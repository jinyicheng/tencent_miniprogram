<?php

namespace jinyicheng\tencent_miniprogram\wechat_mini_program;

use InvalidArgumentException;
use jinyicheng\tencent_miniprogram\MiniProgramException;
use jinyicheng\tencent_miniprogram\Request;
use jinyicheng\tencent_miniprogram\Response;

/**
 * 订阅消息
 * Class SubscribeMessage
 * @package jinyicheng\tencent_miniprogram\wechat_mini_program
 */
class SubscribeMessage
{
    use CommonTrait;

    /**
     * 组合模板并添加至帐号下的个人模板库
     * @param string $tid
     * @param array $kidList
     * @param string $sceneDesc
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.addTemplate.html
     */
    public function addTemplate(string $tid, array $kidList, string $sceneDesc = '')
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        $data = [
            'tid' => $tid,
            'kidList' => $kidList
        ];

        if ($sceneDesc != '') $data['sceneDesc'] = $sceneDesc;

        return Request::post(
            'https://api.weixin.qq.com/wxaapi/newtmpl/addtemplate?access_token=' . $access_token,
            json_encode($data),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000,
            [
                '200014' => '模版 tid 参数错误',
                '200020' => '关键词列表 kidList 参数错误',
                '200021' => '场景描述 sceneDesc 参数错误',
                '200011' => '此账号已被封禁，无法操作',
                '200013' => '此模版已被封禁，无法选用',
                '200012' => '个人模版数已达上限，上限25个'
            ]
        );
    }

    /**
     * 删除帐号下的个人模板
     * @param string $tid
     * @param array $kidList
     * @param string $sceneDesc
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.deleteTemplate.html
     */
    public function deleteTemplate(string $priTmplId)
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();

        return Request::post(
            'https://api.weixin.qq.com/wxaapi/newtmpl/deltemplate?access_token=' . $access_token,
            json_encode([
                'priTmplId' => $priTmplId
            ]),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000
        );
    }

    /**
     * 获取小程序账号的类目
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.getCategory.html
     */
    public function getCategory()
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();

        return Request::post(
            'https://api.weixin.qq.com/wxaapi/newtmpl/getcategory',
            [
                'access_token' => $access_token
            ],
            [],
            2000
        );
    }

    /**
     * 获取模板标题下的关键词列表
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.getPubTemplateKeyWordsById.html
     */
    public function getPubTemplateKeyWordsById(string $tid)
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();

        return Request::get(
            'https://api.weixin.qq.com/wxaapi/newtmpl/getpubtemplatekeywords',
            [
                'access_token' => $access_token,
                'tid' => $tid
            ],
            [],
            2000
        );
    }

    /**
     * 获取帐号所属类目下的公共模板标题
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.getPubTemplateTitleList.html
     */
    public function getPubTemplateTitleList(string $ids, int $start, int $limit)
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        if ($limit > 30) $limit = 30;
        return Request::get(
            'https://api.weixin.qq.com/wxaapi/newtmpl/getpubtemplatetitles',
            [
                'access_token' => $access_token,
                'ids' => $ids,
                'start' => $start,
                'limit' => $limit
            ],
            [],
            2000,
            [
                '200016' => 'start 参数错误',
                '200017' => 'limit 参数错误',
                '200018' => '类目 ids 缺失',
                '200019' => '类目 ids 不合法',
                '0' => '成功'
            ]
        );
    }

    /**
     * 获取帐号所属类目下的公共模板标题
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.getPubTemplateTitleList.html
     */
    public function getTemplateList(string $ids, int $start, int $limit)
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        if ($limit > 30) $limit = 30;
        return Request::get(
            'https://api.weixin.qq.com/wxaapi/newtmpl/gettemplate',
            [
                'access_token' => $access_token
            ],
            [],
            2000
        );
    }

    /**
     * 发送订阅消息
     * @param string $touser
     * @param string $template_id
     * @param array $data
     * @param array $extra_params
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/template-message/templateMessage.send.html
     */
    public function send(string $touser, string $template_id, array $data, array $extra_params = [])
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        $data = [
            'touser' => $touser,
            'template_id' => $template_id,
            'data' => $data
        ];

        if (isset($extra_params['page'])) $data['page'] = $extra_params['page'];
        if (isset($extra_params['miniprogramState'])) {
            if (!in_array($extra_params['miniprogramState'], ['developer', 'trial', 'formal'])) {
                throw new InvalidArgumentException('$extra_params["miniprogramState"]参数不合法，该参数允许的合法值为（developer、trial、formal），当前传入："' . $extra_params["miniprogramState"] . '"');
            } else {
                $data['miniprogramState'] = $extra_params['miniprogramState'];
            }
        }
        if (isset($extra_params['lang'])) {
            if (!in_array($extra_params['lang'], ['zh_CN', 'en_US', 'zh_HK', 'zh_TW'])) {
                throw new InvalidArgumentException('$extra_params["lang"]参数不合法，该参数允许的合法值为（zh_CN、en_US、zh_HK、zh_TW），当前传入："' . $extra_params["lang"] . '"');
            } else {
                $data['lang'] = $extra_params['lang'];
            }
        }

        return Request::post(
            'https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=' . $access_token,
            json_encode($data),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000,
            [
                '0' => '成功',
                '40003' => 'touser字段openid为空或者不正确',
                '40037' => '订阅模板id为空不正确',
                '43101' => '用户拒绝接受消息，如果用户之前曾经订阅过，则表示用户取消了订阅关系',
                '47003' => '模板参数不准确，可能为空或者不满足规则，errmsg会提示具体是哪个字段出错',
                '41030' => 'page路径不正确，需要保证在现网版本小程序中存在，与app.json保持一致'
            ]
        );
    }
}