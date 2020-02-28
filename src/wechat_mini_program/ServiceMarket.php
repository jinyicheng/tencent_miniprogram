<?php

namespace jinyicheng\tencent_miniprogram\wechat_mini_program;

use jinyicheng\tencent_miniprogram\MiniProgramException;
use jinyicheng\tencent_miniprogram\Request;

/**
 * 服务市场
 * Class ServiceMarket
 * @package jinyicheng\tencent_miniprogram\wechat_mini_program
 */
class ServiceMarket
{
    use CommonTrait;

    /**
     * 调用服务平台提供的服务
     * @param string $openid
     * @param string $json_string
     * @param string $json_signature
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/service-market/serviceMarket.invokeService.html
     */
    public function verifySignature(string $service, string $api, string $data, string $client_msg_id)
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