<?php

namespace jinyicheng\tencent_miniprogram\wechat_mini_program;

use jinyicheng\tencent_miniprogram\MiniProgramException;
use jinyicheng\tencent_miniprogram\Request;

/**
 * 小程序搜索
 * Class Soter
 * @package jinyicheng\tencent_miniprogram\wechat_mini_program
 */
class Search
{
    use CommonTrait;

    /**
     * 小程序开发者可以通过本接口提交小程序页面url及参数信息，让微信可以更及时的收录到小程序的页面信息，开发者提交的页面信息将可能被用于小程序搜索结果展示。
     * @param array $pages
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/soter/soter.verifySignature.html
     */
    public function submitPages(array $pages)
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        return Request::post(
            'https://api.weixin.qq.com/wxa/search/wxaapi_submitpages?access_token=' . $access_token,
            json_encode([
                'pages' => $pages
            ]),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000,
            [
                '0'=>'成功',
                '-1'=>'系统繁忙，此时请开发者稍候再试',
                '40066'=>'递交的页面被sitemap标记为拦截，具体查看errmsg提示。',
                '40212'=>'pages 当中存在不合法的query，query格式遵循URL标准，即k1=v1&k2=v2',
                '40219'=>'pages不存在或者参数为空',
                '47001'=>'http请求包不是合法的JSON',
                '47004'=>'每次提交的页面数超过1000（备注：每次提交页面数应小于或等于1000）',
                '47006'=>'当天提交页面数达到了配额上限，请明天再试',
                '85091'=>'小程序的搜索开关被关闭。请访问设置页面打开开关再重试',
                '85083'=>'小程序的搜索功能被禁用',
            ]
        );
    }
}