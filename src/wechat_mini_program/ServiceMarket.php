<?php

namespace jinyicheng\tencent_miniprogram\wechat_mini_program;

use jinyicheng\tencent_miniprogram\Request;
use jinyicheng\tencent_miniprogram\MiniProgramException;

/**
 * 服务市场
 * Class ServiceMarket
 * @package jinyicheng\tencent_miniprogram\wechat_mini_program
 */
class ServiceMarket
{
    private $options;
    private static $instance = [];

    /**
     * ServiceMarket constructor.
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
     * 调用服务平台提供的服务
     * @param string $openid
     * @param string $json_string
     * @param string $json_signature
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/service-market/serviceMarket.invokeService.html
     */
    public function verifySignature(string $service,string $api,string $data,string $client_msg_id)
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        $data = [
            'service' => $service,
            'api' => $api,
            'data' => $data,
            'client_msg_id' => $client_msg_id
        ];

        return Request::post(
            'https://api.weixin.qq.com/wxa/servicemarket?access_token=' . $access_token,
            json_encode($data),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000
        );
    }
}