<?php

namespace jinyicheng\tencent_miniprogram\wechat_mini_program;

use jinyicheng\tencent_miniprogram\MiniProgramException;
use jinyicheng\tencent_miniprogram\Request;

/**
 * 统一服务消息
 * Class UniformMessage
 * @package jinyicheng\tencent_miniprogram\wechat_mini_program
 */
class UniformMessage
{
    use CommonTrait;

    /**
     * 下发小程序和公众号统一的服务消息
     * @param $open_id
     * @param array $mp_template_msg
     * @param null $weapp_template_msg
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/uniform-message/uniformMessage.send.html
     */
    public function send($open_id, array $mp_template_msg, $weapp_template_msg = null)
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        $data = [
            'touser' => $open_id,
            'mp_template_msg' => $mp_template_msg
        ];

        if (!is_null($weapp_template_msg)) $data['weapp_template_msg'] = $weapp_template_msg;

        return Request::post(
            'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/uniform_send?access_token=' . $access_token,
            json_encode($data),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000,
            [
                '0' => '成功',
                '40037' => '模板id不正确，weapp_template_msg.template_id或者mp_template_msg.template_id',
                '41028' => 'weapp_template_msg.form_id过期或者不正确',
                '41029' => 'weapp_template_msg.form_id已被使用',
                '41030' => 'weapp_template_msg.page不正确',
                '45009' => '接口调用超过限额（目前默认每个帐号日调用限额为100万）',
                '40003' => 'touser不是正确的openid',
                '40013' => 'appid不正确，或者不符合绑定关系要求'
            ]
        );
    }
}