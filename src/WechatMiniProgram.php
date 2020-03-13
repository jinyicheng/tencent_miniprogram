<?php

namespace jinyicheng\tencent_miniprogram;

use jinyicheng\redis\Redis;
use jinyicheng\tencent_miniprogram\wechat_mini_program\Analysis;
use jinyicheng\tencent_miniprogram\wechat_mini_program\Auth;
use jinyicheng\tencent_miniprogram\wechat_mini_program\CommonTrait;
use jinyicheng\tencent_miniprogram\wechat_mini_program\CustomerServiceMessage;
use jinyicheng\tencent_miniprogram\wechat_mini_program\Operation;
use jinyicheng\tencent_miniprogram\wechat_mini_program\PluginManager;
use jinyicheng\tencent_miniprogram\wechat_mini_program\Search;
use jinyicheng\tencent_miniprogram\wechat_mini_program\ServiceMarket;
use jinyicheng\tencent_miniprogram\wechat_mini_program\Soter;
use jinyicheng\tencent_miniprogram\wechat_mini_program\SubscribeMessage;
use jinyicheng\tencent_miniprogram\wechat_mini_program\TemplateMessage;
use jinyicheng\tencent_miniprogram\wechat_mini_program\UniformMessage;
use jinyicheng\tencent_miniprogram\wechat_mini_program\Wxacode;
use OSS\OssClient;

class WechatMiniProgram
{
    use CommonTrait;

    /**
     * 解密
     * @param $session_key
     * @param $encrypted_data
     * @param $iv
     * @param $raw_data
     * @param $signature
     * @return array
     * @throws MiniProgramException
     */
    public static function decrypt($session_key, $encrypted_data, $iv, $raw_data, $signature)
    {
        if ($signature == sha1($raw_data . $session_key)) {
            $result = openssl_decrypt(base64_decode($encrypted_data), "AES-128-CBC", base64_decode($session_key), 1, base64_decode($iv));
            $dataObj = json_decode($result, true);
            if (is_array($dataObj) && !empty($dataObj)) {
                return $dataObj;
            } else {
                throw new MiniProgramException('解密失败，解密结果为空', -41003);
            }
        } else {
            throw new MiniProgramException('解密失败，数据非法', -41004);
        }
    }

    /**
     * 登录/用户信息/接口调用凭证
     * @return Auth
     */
    public function auth()
    {
        return Auth::getInstance($this->options);
    }

    /**
     * 数据分析
     * @return Analysis
     */
    public function analysis()
    {
        return Analysis::getInstance($this->options);
    }

    /**
     * 客服消息
     * @return CustomerServiceMessage
     */
    public function customerServiceMessage()
    {
        return CustomerServiceMessage::getInstance($this->options);
    }

    /**
     * 模板消息
     * @return TemplateMessage
     */
    public function templateMessage()
    {
        return TemplateMessage::getInstance($this->options);
    }

    /**
     * 统一服务消息
     * @return UniformMessage
     */
    public function uniformMessage()
    {
        return UniformMessage::getInstance($this->options);
    }

    /**
     * 插件管理
     * @return PluginManager
     */
    public function pluginManager()
    {
        return PluginManager::getInstance($this->options);
    }

    /**
     * 附近的小程序
     */

    /**
     * 小程序码
     * @return Wxacode
     */
    public function wxacode(){
        return Wxacode::getInstance($this->options);
    }

    /**
     * 内容安全
     */

    /**
     * 广告
     */

    /**
     * 图像处理
     */

    /**
     * 即时配送
     */

    /**
     * 物流助手
     */

    /**
     * OCR
     */

    /**
     * 运维中心
     * @return Operation
     */
    public function operation()
    {
        return Operation::getInstance($this->options);
    }

    /**
     * 小程序搜索
     * @return Search
     */
    public function search()
    {
        return Search::getInstance($this->options);
    }

    /**
     * 服务市场
     * @return ServiceMarket
     */
    public function serviceMarket()
    {
        return ServiceMarket::getInstance($this->options);
    }

    /**
     * 导购助手
     */

    /**
     * 生物认证
     * @return Soter
     */
    public function soter()
    {
        return Soter::getInstance($this->options);
    }

    /**
     * 订阅消息
     * @return SubscribeMessage
     */
    public function subscribeMessage()
    {
        return SubscribeMessage::getInstance($this->options);
    }
}