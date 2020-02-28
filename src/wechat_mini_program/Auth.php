<?php

namespace jinyicheng\tencent_miniprogram\wechat_mini_program;

use jinyicheng\redis\Redis;
use jinyicheng\tencent_miniprogram\Request;
use jinyicheng\tencent_miniprogram\MiniProgramException;

/**
 * 登录/用户信息/接口调用凭证
 * Class Auth
 * @package jinyicheng\tencent_miniprogram\wechat_mini_program
 */
class Auth
{
    private $options;
    private static $instance = [];

    /**
     * Auth constructor.
     * @param array $options
     */
    private function __construct($options = [])
    {
        $this->options = $options;
        if (!extension_loaded('redis')) throw new BadFunctionCallException('Redis扩展不支持');
    }

    /**
     * @param array $options
     * @return mixed
     */
    public static function getInstance($options = [])
    {
        if ($options === []) $options = config('wechat_mini_program');
        if ($options === false || $options === []) throw new InvalidArgumentException('配置不存在');
        if (!isset($options['app_id'])) throw new InvalidArgumentException('配置下没有找到app_id设置');
        if (!isset($options['app_secret'])) throw new InvalidArgumentException('配置下没有找到app_secret设置');
        //if (!isset($options['app_token'])) throw new InvalidArgumentException('配置下没有找到app_token设置');
        if (!isset($options['app_redis_cache_db_number'])) throw new InvalidArgumentException('配置下没有找到app_redis_cache_db_number设置');
        if (!isset($options['app_redis_cache_key_prefix'])) throw new InvalidArgumentException('配置下没有找到app_redis_cache_key_prefix设置');
        if (!isset($options['app_qrcode_cache_type'])) throw new InvalidArgumentException('配置下没有找到app_qrcode_cache_type设置');
        if (!in_array($options['app_qrcode_cache_type'], ['oss', 'local'])) throw new InvalidArgumentException('配置下app_qrcode_cache_type参数无效仅支持：oss或local');
        if ($options['app_qrcode_cache_type'] == 'oss') {
            if (!isset($options['app_qrcode_cache_oss_access_key_id'])) throw new InvalidArgumentException('配置下没有找到app_qrcode_cache_oss_access_key_id设置');
            if (!isset($options['app_qrcode_cache_oss_access_key_secret'])) throw new InvalidArgumentException('配置下没有找到app_qrcode_cache_oss_access_key_secret设置');
            if (!isset($options['app_qrcode_cache_oss_end_point'])) throw new InvalidArgumentException('配置下没有找到app_qrcode_cache_oss_end_point设置');
            if (!isset($options['app_qrcode_cache_oss_bucket'])) throw new InvalidArgumentException('配置下没有找到app_qrcode_cache_oss_bucket设置');
        }
        if (!is_dir($options['app_qrcode_cache_real_dir_path'])) throw new InvalidArgumentException('配置下app_qrcode_cache_real_dir_path路径无效');
        if (!isset($options['app_qrcode_cache_relative_dir_path'])) throw new InvalidArgumentException('配置下app_qrcode_cache_relative_dir_path路径无效');
        if (!isset($options['app_qrcode_request_url_prefix'])) throw new InvalidArgumentException('配置下没有找到app_qrcode_request_url_prefix设置');
        $hash = md5(json_encode($options));
        if (!isset(self::$instance[$hash])) {
            self::$instance[$hash] = new self($options);
        }
        return self::$instance[$hash];
    }

    /**
     * 获取小程序全局唯一后台接口调用凭据（access_token）。
     * @return array|mixed|string
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/access-token/auth.getAccessToken.html
     */
    public function getAccessToken()
    {
        /**
         * 尝试从redis中获取access_token
         */
        $redis = Redis::db($this->options['app_redis_cache_db_number']);
        $access_token_key = $this->options['app_redis_cache_key_prefix'] . ':access_token:' . $this->options['app_id'];
        $access_token = $redis->get($access_token_key);
        if ($access_token !== false) {
            return $access_token;
        } else {
            /**
             * 请求接口
             */
            $response = Request::get(
                "https://api.weixin.qq.com/cgi-bin/token",
                [
                    'appid' => $this->options['app_id'],
                    'secret' => $this->options['app_secret'],
                    'grant_type' => 'client_credential'
                ],
                [],
                2000,
                [
                    '-1' => '系统繁忙，此时请开发者稍候再试',
                    '0' => '请求成功',
                    '40001' => 'AppSecret 错误或者 AppSecret 不属于这个小程序，请开发者确认 AppSecret 的正确性',
                    '40002' => '请确保 grant_type 字段值为 client_credential',
                    '40013' => '不合法的 AppID，请开发者检查 AppID 的正确性，避免异常字符，注意大小写'
                ]
            );
            if ($response['status']) {
                //在redis中保存access_token
                $redis->set($access_token_key, $response['data']['access_token'], $response['data']['expires_in']);
                return $response['data']['access_token'];
            } else {
                throw new MiniProgramException($response['message'], $response['code']);
            }
        }
    }


    /**
     * 登录凭证校验
     * @param $js_code
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/login/auth.code2Session.html
     */
    public function code2Session($js_code)
    {
        /**
         * 请求接口
         */
        return Request::get(
            "https://api.weixin.qq.com/sns/jscode2session",
            [
                'appid' => $this->options['app_id'],
                'secret' => $this->options['app_secret'],
                'js_code' => $js_code,
                'grant_type' => 'authorization_code'
            ],
            [],
            2000,
            [
                '-1' => '系统繁忙，此时请开发者稍候再试',
                '0' => '请求成功',
                '40029' => 'code 无效',
                '45011' => '频率限制，每个用户每分钟100次	'
            ]
        );
    }

    /**
     * 用户支付完成后，获取该用户的 UnionId，无需用户授权。
     * @param $open_id
     * @param array $extra_params
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/user-info/auth.getPaidUnionId.html
     */
    public function getPaidUnionId($open_id, $extra_params = [])
    {
        //TODO:当前方法未经过实测仅根据文档编写，还需实测；
        $access_token = $this->getAccessToken();
        /**
         * 请求接口
         */
        $data = [
            'access_token' => $access_token,
            'open_id' => $open_id
        ];
        if (isset($extra_params['transaction_id'])) {
            $data['transaction_id'] = $extra_params['transaction_id'];
        } else if (isset($extra_params['mch_id']) && isset($extra_params['out_trade_no'])) {
            $data['mch_id'] = $extra_params['mch_id'];
            $data['out_trade_no'] = $extra_params['out_trade_no'];
        } else {
            throw new InvalidArgumentException('参数无效，微信小程序仅支持2种传参方式：（方式1、微信支付订单号（transaction_id）；方式2、微信支付商户订单号和微信支付商户号out_trade_no 及 mch_id）');
        }
        return Request::get(
            "https://api.weixin.qq.com/wxa/getpaidunionid",
            $data,
            [],
            2000,
            [
                '-1' => '系统繁忙，此时请开发者稍候再试',
                '0' => '请求成功',
                '40003' => 'openid 错误',
                '89002' => '没有绑定开放平台帐号',
                '89300' => '订单无效'
            ]
        );
    }
}