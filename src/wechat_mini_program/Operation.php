<?php

namespace jinyicheng\tencent_miniprogram\wechat_mini_program;

use jinyicheng\tencent_miniprogram\MiniProgramException;
use jinyicheng\tencent_miniprogram\Request;

/**
 * 生物认证
 * Class Operation
 * @package jinyicheng\tencent_miniprogram\wechat_mini_program
 */
class Operation
{
    use CommonTrait;

    /**
     * SOTER 生物认证秘钥签名验证
     * @param string $openid
     * @param string $json_string
     * @param string $json_signature
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/soter/soter.verifySignature.html
     */
    public function realtimelogSearch(string $openid, string $json_string, string $json_signature)
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        $data = [
            'access_token' => $access_token,
        ];

        return Request::post(
            'https://api.weixin.qq.com/wxaapi/userlog/userlog_search',
            $data,
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000
        );
    }
}