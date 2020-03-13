<?php

namespace jinyicheng\tencent_miniprogram\wechat_mini_program;

use jinyicheng\tencent_miniprogram\MiniProgramException;
use jinyicheng\tencent_miniprogram\Request;

/**
 * 小程序码
 * Class Soter
 * @package jinyicheng\tencent_miniprogram\wechat_mini_program
 */
class Wxacode
{
    use CommonTrait;

    /**
     * 获取小程序二维码，适用于需要的码数量较少的业务场景。通过该接口生成的小程序码，永久有效，有数量限制，详见获取二维码。
     * @param string $path
     * @param int $width
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/qr-code/wxacode.createQRCode.html
     */
    public function createQRCode(string $path, int $width = 430)
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        return Request::post(
            'https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=' . $access_token,
            json_encode([
                'path' => $path,
                'width' => $width
            ]),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000,
            [
                '0' => '成功',
                '45029' => '生成码个数总和到达最大个数限制'
            ]
        );
    }

    /**
     * 获取小程序码，适用于需要的码数量较少的业务场景。通过该接口生成的小程序码，永久有效，有数量限制，详见获取二维码。
     * @param string $path
     * @param array $extra_params
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/qr-code/wxacode.get.html
     */
    public function get(string $path, $extra_params = [])
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        $data['path'] = $path;
        if (isset($extra_params['width'])) $data['width'] = $extra_params['width'];
        if (isset($extra_params['auto_color'])){
            $data['auto_color'] = $extra_params['auto_color'];
            if($data['auto_color']===false){
                if (isset($extra_params['line_color'])) $data['line_color'] = $extra_params['line_color'];
            }
        }
        if (isset($extra_params['is_hyaline'])) $data['is_hyaline'] = $extra_params['is_hyaline'];
        return Request::post(
            'https://api.weixin.qq.com/wxa/getwxacode?access_token=' . $access_token,
            json_encode($data),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000,
            [
                '0' => '成功',
                '45029' => '生成码个数总和到达最大个数限制'
            ]
        );
    }

    /**
     * 获取小程序码，适用于需要的码数量极多的业务场景。通过该接口生成的小程序码，永久有效，数量暂无限制。 更多用法详见 获取二维码。
     * @param string $scene
     * @param array $extra_params
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/qr-code/wxacode.getUnlimited.html
     */
    public function getUnlimited(string $scene, $extra_params = [])
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        $data['scene'] = $scene;
        if (isset($extra_params['page'])) $data['page'] = $extra_params['page'];
        if (isset($extra_params['width'])) $data['width'] = $extra_params['width'];
        if (isset($extra_params['auto_color'])){
            $data['auto_color'] = $extra_params['auto_color'];
            if($data['auto_color']===false){
                if (isset($extra_params['line_color'])) $data['line_color'] = $extra_params['line_color'];
            }
        }
        if (isset($extra_params['is_hyaline'])) $data['is_hyaline'] = $extra_params['is_hyaline'];
        return Request::post(
            'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=' . $access_token,
            json_encode($data),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000,
            [
                '0' => '成功',
                '45009' => '调用分钟频率受限(目前5000次/分钟，会调整)，如需大量小程序码，建议预生成。',
                '41030' => '所传page页面不存在，或者小程序没有发布'
            ]
        );
    }
}