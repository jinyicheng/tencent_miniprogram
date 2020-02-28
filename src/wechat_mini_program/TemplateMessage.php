<?php

namespace jinyicheng\tencent_miniprogram\wechat_mini_program;

use jinyicheng\tencent_miniprogram\MiniProgramException;
use jinyicheng\tencent_miniprogram\Request;

/**
 * 模板消息
 * Class TemplateMessage
 * @package jinyicheng\tencent_miniprogram\wechat_mini_program
 */
class TemplateMessage
{
    use CommonTrait;

    /**
     * 组合模板并添加至帐号下的个人模板库（请注意，小程序模板消息接口将于2020年1月10日下线，开发者可使用订阅消息功能）
     * @param $id
     * @param array $keyword_id_list
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/template-message/templateMessage.addTemplate.html
     */
    public function addTemplate($id, array $keyword_id_list)
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        return Request::post(
            'https://api.weixin.qq.com/cgi-bin/wxopen/template/add?access_token=' . $access_token,
            json_encode([
                'id' => $id,
                'keyword_id_list' => $keyword_id_list
            ]),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000,
            [
                '0' => '成功'
            ]
        );
    }

    /**
     * 组合模板并添加至帐号下的个人模板库（请注意，小程序模板消息接口将于2020年1月10日下线，开发者可使用订阅消息功能）
     * @param $id
     * @param array $keyword_id_list
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/template-message/templateMessage.deleteTemplate.html
     */
    public function deleteTemplate($template_id)
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        return Request::post(
            'https://api.weixin.qq.com/cgi-bin/wxopen/template/del?access_token=' . $access_token,
            json_encode([
                'template_id' => $id
            ]),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000,
            [
                '0' => '成功'
            ]
        );
    }

    /**
     * 组合模板并添加至帐号下的个人模板库（请注意，小程序模板消息接口将于2020年1月10日下线，开发者可使用订阅消息功能）
     * @param $id
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/template-message/templateMessage.getTemplateLibraryById.html
     */
    public function getTemplateLibraryById($id)
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        return Request::post(
            'https://api.weixin.qq.com/cgi-bin/wxopen/template/library/get?access_token=' . $access_token,
            json_encode([
                'id' => $id
            ]),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000,
            [
                '0' => '成功'
            ]
        );
    }

    /**
     * 组合模板并添加至帐号下的个人模板库（请注意，小程序模板消息接口将于2020年1月10日下线，开发者可使用订阅消息功能）
     * @param $id
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/template-message/templateMessage.getTemplateLibraryList.html
     */
    public function getTemplateLibraryList($offset, $count)
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        return Request::post(
            'https://api.weixin.qq.com/cgi-bin/wxopen/template/library/list?access_token=' . $access_token,
            json_encode([
                'offset' => $offset,
                'count' => $count
            ]),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000,
            [
                '0' => '成功'
            ]
        );
    }

    /**
     * 组合模板并添加至帐号下的个人模板库（请注意，小程序模板消息接口将于2020年1月10日下线，开发者可使用订阅消息功能）
     * @param $id
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/template-message/templateMessage.getTemplateList.html
     */
    public function getTemplateList($offset, $count)
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        return Request::post(
            'https://api.weixin.qq.com/cgi-bin/wxopen/template/list?access_token=' . $access_token,
            json_encode([
                'offset' => $offset,
                'count' => $count
            ]),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000,
            [
                '0' => '成功'
            ]
        );
    }

    /**
     * 组合模板并添加至帐号下的个人模板库（请注意，小程序模板消息接口将于2020年1月10日下线，开发者可使用订阅消息功能）
     * @param $open_id
     * @param $template_id
     * @param $form_id
     * @param array $extra_params
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/template-message/templateMessage.send.html
     */
    public function send($open_id, $template_id, $form_id, $extra_params = [])
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        $data = [
            'touser' => $open_id,
            'template_id' => $template_id,
            'form_id' => $form_id
        ];

        if (isset($extra_params['page'])) $data['page'] = $extra_params['page'];
        if (isset($extra_params['data'])) $data['data'] = $extra_params['data'];
        if (isset($extra_params['emphasis_keyword'])) $data['emphasis_keyword'] = $extra_params['emphasis_keyword'];

        return Request::post(
            'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=' . $access_token,
            json_encode($data),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000,
            [
                '0' => '成功',
                '40037' => 'template_id不正确',
                '41028' => 'form_id不正确，或者过期',
                '41029' => 'form_id已被使用',
                '41030' => 'page不正确',
                '45009' => '接口调用超过限额（目前默认每个帐号日调用限额为100万）'
            ]
        );
    }
}