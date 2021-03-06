<?php

namespace jinyicheng\tencent_miniprogram\wechat_mini_program;

use InvalidArgumentException;
use jinyicheng\tencent_miniprogram\MiniProgramException;
use jinyicheng\tencent_miniprogram\Request;

/**
 * 客服消息
 * Class CustomerServiceMessage
 * @package jinyicheng\tencent_miniprogram\wechat_mini_program
 */
class CustomerServiceMessage
{
    use CommonTrait;

    /**
     * 获取客服消息内的临时素材
     * @param $media_id
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/customer-message/customerServiceMessage.getTempMedia.html
     */
    public function getTempMedia($media_id)
    {
        //TODO:当前方法未经过实测仅根据文档编写，还需实测；
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        /**
         * 请求接口
         */
        return Request::get(
            "https://api.weixin.qq.com/cgi-bin/media/get",
            [
                'access_token' => $access_token,
                'media_id' => $media_id
            ],
            [],
            2000,
            [
                '40007' => '无效媒体文件ID'
            ]
        );
    }

    /**
     * 发送客服消息给用户
     * @param $type
     * @param $begin_date
     * @param $end_date
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/customer-message/customerServiceMessage.send.html
     */
    public function send($open_id, $message_type, $params = [])
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();

        $data = [
            'touser' => $open_id,
            'msgtype' => $message_type
        ];

        switch ($message_type) {
            case 'text':
                if (isset($params['content'])) {
                    $data['text']['content'] = $params['content'];
                } else {
                    throw new InvalidArgumentException('缺少参数$params["content"]');
                }
                break;
            case 'image':
                if (isset($params['media_id'])) {
                    $data['image']['media_id'] = $params['media_id'];
                } else {
                    throw new InvalidArgumentException('缺少参数$params["media_id"]');
                }
                break;
            case 'link':
                if (isset($params['title'])) {
                    $data['link']['title'] = $params['title'];
                } else {
                    throw new InvalidArgumentException('缺少参数$params["title"]');
                }
                if (isset($params['description'])) {
                    $data['link']['description'] = $params['description'];
                } else {
                    throw new InvalidArgumentException('缺少参数$params["description"]');
                }
                if (isset($params['url'])) {
                    $data['link']['url'] = $params['url'];
                } else {
                    throw new InvalidArgumentException('缺少参数$params["url"]');
                }
                if (isset($params['thumb_url'])) {
                    $data['link']['thumb_url'] = $params['thumb_url'];
                } else {
                    throw new InvalidArgumentException('缺少参数$params["thumb_url"]');
                }
                break;
            case 'miniprogrampage':
                if (isset($params['title'])) {
                    $data['miniprogrampage']['title'] = $params['title'];
                } else {
                    throw new InvalidArgumentException('缺少参数$params["title"]');
                }
                if (isset($params['pagepath'])) {
                    $data['miniprogrampage']['pagepath'] = $params['pagepath'];
                } else {
                    throw new InvalidArgumentException('缺少参数$params["pagepath"]');
                }
                if (isset($params['thumb_media_id'])) {
                    $data['miniprogrampage']['thumb_media_id'] = $params['thumb_media_id'];
                } else {
                    throw new InvalidArgumentException('缺少参数$params["thumb_media_id"]');
                }
                break;
            default:
                throw new InvalidArgumentException('$message_type参数值无效，微信小程序仅支持：text、image、link、miniprogrampage这4种类型');
        }
        return Request::post(
            'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $access_token,
            json_encode($data),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000,
            [
                '-1' => '系统繁忙，此时请开发者稍候再试',
                '0' => '请求成功',
                '40001' => '获取 access_token 时 AppSecret 错误，或者 access_token 无效。请开发者认真比对 AppSecret 的正确性，或查看是否正在为恰当的小程序调用接口',
                '40002' => '不合法的凭证类型',
                '40003' => '不合法的 OpenID，请开发者确认 OpenID 是否是其他小程序的 OpenID',
                '45015' => '回复时间超过限制',
                '45047' => '客服接口下行条数超过上限',
                '48001' => 'API 功能未授权，请确认小程序已获得该接口'
            ]
        );
    }

    /**
     * 下发客服当前输入状态给用户
     * @param $touser
     * @param bool $status
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/customer-message/customerServiceMessage.setTyping.html
     */
    public function setTyping($touser, bool $status = true)
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        $data = [
            'touser' => $touser,
            'command' => ($status) ? 'Typing' : 'CancelTyping'
        ];
        return Request::post(
            'https://api.weixin.qq.com/cgi-bin/message/custom/typing?access_token=' . $access_token,
            json_encode($data),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000,
            [
                '0' => '成功',
                '45072' => 'command字段取值不对',
                '45080' => '下发输入状态，需要之前30秒内跟用户有过消息交互',
                '45081' => '已经在输入状态，不可重复下发'
            ]
        );
    }

    /**
     * 把媒体文件上传到微信服务器
     * @param $image
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/customer-message/customerServiceMessage.uploadTempMedia.html
     */
    public function uploadTempMedia($image)
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        return Request::post(
            'https://api.weixin.qq.com/cgi-bin/media/upload?access_token=' . $access_token . '&type=image',
            json_encode([
                'media' => [
                    'contentType' => (new finfo(FILEINFO_MIME_TYPE))->file($image),
                    'value' => file_get_contents($image)
                ]
            ]),
            2000,
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            [
                '0' => '成功',
                '40004' => '无效媒体文件类型'
            ]
        );
    }
}