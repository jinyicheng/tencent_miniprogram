<?php

namespace jinyicheng\tencent_miniprogram\wechat_mini_program;

use InvalidArgumentException;
use jinyicheng\tencent_miniprogram\MiniProgramException;
use jinyicheng\tencent_miniprogram\Request;

/**
 * 动态消息
 * Class UpdatableMessage
 * @package jinyicheng\tencent_miniprogram\wechat_mini_program
 */
class UpdatableMessage
{
    use CommonTrait;

    /**
     * 修改被分享的动态消息
     * @param string $activity_id
     * @param int $target_state
     * @param array $parameter_list
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/updatable-message/updatableMessage.setUpdatableMsg.html
     */
    public function setUpdatableMsg(string $activity_id, int $target_state, array $parameter_list)
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        if (!in_array($target_state, [0, 1])) {
            throw new InvalidArgumentException('$target_state参数值不合法，该参数允许的合法值为（0、1），当前传入："' . $target_state . '"');
        }
        foreach ($parameter_list as $key => $parameter) {
            if (!isset($parameter['name'])) {
                throw new InvalidArgumentException('$parameter_list[' . $key . ']["name"]参数不存在，当前传入："' . json_encode($parameter_list));
            } else {
                if (!in_array(['member_count', 'room_limit', 'path', 'version_type'], $parameter['name'])) {
                    throw new InvalidArgumentException('$parameter_list[' . $key . ']["name"]值不合法，该参数允许的合法值为（member_count、room_limit、path、version_type），当前传入："' . $parameter_list[$key]['name'] . '"');
                }
            }
            if (!isset($parameter['value'])) {
                throw new InvalidArgumentException('$parameter_list[' . $key . ']["value"]参数不存在，当前传入："' . json_encode($parameter_list));
            }
        }

        return Request::post(
            'https://api.weixin.qq.com/cgi-bin/message/wxopen/updatablemsg/send?access_token=' . $access_token,
            json_encode([
                'activity_id' => $activity_id,
                'target_state' => $target_state,
                'template_info' => [
                    'parameter_list' => $parameter_list
                ]
            ]),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000,
            [
                '0' => '请求成功',
                '-1' => '系统繁忙。此时请开发者稍候再试',
                '42001' => 'access_token过期',
                '44002' => 'post数据为空',
                '47001' => 'post数据中参数缺失',
                '47501' => '参数activity_id错误',
                '47502' => '参数target_state错误',
                '47503' => '参数version_type错误',
                '47504' => 'activity_id过期'
            ]
        );
    }

    /**
     * 创建被分享动态消息的 activity_id
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