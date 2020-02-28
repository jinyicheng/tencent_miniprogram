<?php

namespace jinyicheng\tencent_miniprogram\wechat_mini_program;

use jinyicheng\tencent_miniprogram\Request;
use jinyicheng\tencent_miniprogram\MiniProgramException;

/**
 * 模板消息
 * Class TemplateMessage
 * @package jinyicheng\tencent_miniprogram\wechat_mini_program
 */
class TemplateMessage
{
    private $options;
    private static $instance = [];

    /**
     * TemplateMessage constructor.
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
     * 组合模板并添加至帐号下的个人模板库（请注意，小程序模板消息接口将于2020年1月10日下线，开发者可使用订阅消息功能）
     * @param $id
     * @param array $keyword_id_list
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/template-message/templateMessage.addTemplate.html
     */
    public function addTemplate($id,array $keyword_id_list)
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
    public function getTemplateLibraryList($offset,$count)
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
    public function getTemplateList($offset,$count)
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
    public function send($open_id,$template_id,$form_id,$extra_params=[])
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

        if (isset($extra_params['page']))$data['page'] = $extra_params['page'];
        if (isset($extra_params['data']))$data['data'] = $extra_params['data'];
        if (isset($extra_params['emphasis_keyword']))$data['emphasis_keyword'] = $extra_params['emphasis_keyword'];

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