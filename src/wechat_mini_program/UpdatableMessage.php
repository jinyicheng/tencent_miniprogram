<?php

namespace jinyicheng\tencent_miniprogram\wechat_mini_program;

use jinyicheng\tencent_miniprogram\Request;
use jinyicheng\tencent_miniprogram\MiniProgramException;

/**
 * 动态消息
 * Class UpdatableMessage
 * @package jinyicheng\tencent_miniprogram\wechat_mini_program
 */
class UpdatableMessage
{
    private $options;
    private static $instance = [];

    /**
     * UpdatableMessage constructor.
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
     * 修改被分享的动态消息
     * @param string $activity_id
     * @param int $target_state
     * @param array $template_info
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/updatable-message/updatableMessage.setUpdatableMsg.html
     */
    public function setUpdatableMsg(string $activity_id,int $target_state,array $parameter_list)
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        if(!in_array($target_state,[0,1])){
            throw new InvalidArgumentException('$target_state参数值不合法，该参数允许的合法值为（0、1），当前传入："'.$target_state.'"');
        }
        foreach ($parameter_list as $key=>$parameter){
            if(!isset($parameter['name'])){
                throw new InvalidArgumentException('$parameter_list['.$key.']["name"]参数不存在，当前传入："'.json_encode($parameter_list));
            }else{
                if(!in_array(['member_count','room_limit','path','version_type'],$parameter['name'])){
                    throw new InvalidArgumentException('$parameter_list['.$key.']["name"]值不合法，该参数允许的合法值为（member_count、room_limit、path、version_type），当前传入："'.$parameter_list[$key]['name'].'"');
                }
            }
            if(!isset($parameter['value'])){
                throw new InvalidArgumentException('$parameter_list['.$key.']["value"]参数不存在，当前传入："'.json_encode($parameter_list));
            }
        }

        return Request::post(
            'https://api.weixin.qq.com/cgi-bin/message/wxopen/updatablemsg/send?access_token=' . $access_token,
            json_encode([
                'activity_id' => $activity_id,
                'target_state' => $target_state,
                'template_info' => [
                    'parameter_list'=>$parameter_list
                ]
            ]),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000,
            [
                '0'=>'请求成功',
                '-1'=>'系统繁忙。此时请开发者稍候再试',
                '42001'=>'access_token过期',
                '44002'=>'post数据为空',
                '47001'=>'post数据中参数缺失',
                '47501'=>'参数activity_id错误',
                '47502'=>'参数target_state错误',
                '47503'=>'参数version_type错误',
                '47504'=>'activity_id过期'
            ]
        );
    }

    /**
     * 创建被分享动态消息的 activity_id
     * @param $media_id
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/updatable-message/updatableMessage.createActivityId.html
     */
    public function createActivityId()
    {
        //TODO:当前方法未经过实测仅根据文档编写，还需实测；
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        /**
         * 请求接口
         */
        return Request::get(
            "https://api.weixin.qq.com/cgi-bin/message/wxopen/activityid/create",
            [
                'access_token' => $access_token
            ],
            [],
            2000,
            [
                '-1' => '系统繁忙。此时请开发者稍候再试',
                '0' => '请求成功',
                '42001' => 'access_token 过期'
            ]
        );
    }
}