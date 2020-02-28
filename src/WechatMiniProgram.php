<?php

namespace jinyicheng\tencent_miniprogram;

use BadFunctionCallException;
use Exception;
use InvalidArgumentException;
use jinyicheng\redis\Redis;
use jinyicheng\tencent_miniprogram\wechat_mini_program\Analysis;
use jinyicheng\tencent_miniprogram\wechat_mini_program\Auth;
use jinyicheng\tencent_miniprogram\wechat_mini_program\CustomerServiceMessage;
use jinyicheng\tencent_miniprogram\wechat_mini_program\TemplateMessage;
use jinyicheng\tencent_miniprogram\wechat_mini_program\UniformMessage;
use jinyicheng\tencent_miniprogram\wechat_mini_program\PluginManager;
use jinyicheng\tencent_miniprogram\wechat_mini_program\ServiceMarket;
use jinyicheng\tencent_miniprogram\wechat_mini_program\Soter;
use jinyicheng\tencent_miniprogram\wechat_mini_program\SubscribeMessage;
use OSS\OssClient;
use UnexpectedValueException;

class WechatMiniProgram
{

    private $options;
    private static $instance = [];

    /**
     * WechatMiniProgram constructor.
     * @param array $options
     */
    private function __construct($options = [])
    {
        $this->options = $options;
        if (!extension_loaded('redis')) throw new BadFunctionCallException('Redis扩展不支持');
    }

    /**
     * @param array $options
     * @return self
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
    public function analysis(){
        return Analysis::getInstance($this->options);
    }

    /**
     * 客服消息
     * @return CustomerServiceMessage
     */
    public function customerServiceMessage(){
        return CustomerServiceMessage::getInstance($this->options);
    }

    /**
     * 模板消息
     * @return TemplateMessage
     */
    public function templateMessage(){
        return TemplateMessage::getInstance($this->options);
    }

    /**
     * 统一服务消息
     * @return UniformMessage
     */
    public function uniformMessage(){
        return UniformMessage::getInstance($this->options);
    }

    /**
     * 插件管理
     * @return PluginManager
     */
    public function pluginManager(){
        return PluginManager::getInstance($this->options);
    }

    /**
     * 附近的小程序
     */

    /**
     * 小程序码
     */

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
     */

    /**
     * 小程序搜索
     */

    /**
     * 服务市场
     * @return ServiceMarket
     */
    public function serviceMarket(){
        return ServiceMarket::getInstance($this->options);
    }

    /**
     * 导购助手
     */

    /**
     * 生物认证
     * @return Soter
     */
    public function soter(){
        return Soter::getInstance($this->options);
    }

    /**
     * 订阅消息
     * @return SubscribeMessage
     */
    public function subscribeMessage(){
        return SubscribeMessage::getInstance($this->options);
    }

    

//    /**
//     * @param $open_id
//     * @param $session_key
//     * @return array
//     * @throws Exception
//     * @throws Exception
//     */
//    public function checkSessionKey($open_id, $session_key)
//    {
//        $access_token = $this->getAccessToken();
//        dump($access_token);
//        /**
//         * 请求接口
//         */
//        $getResult = self::get(
//            "https://api.weixin.qq.com/wxa/checksession",
//            [
//                'access_token' => $access_token,
//                'openid' => $open_id,
//                'signature' => hash_hmac('sha256', $session_key, $this->options['app_secret']),
//                'sig_method' => 'hmac_sha256'
//            ],
//            [],
//            2000
//        );
//        /**
//         * 处理返回结果
//         */
//        //返回状态：不成功，抛出异常
//        if ($getResult['errcode'] != 0) {
//            throw new MiniProgramException($getResult['errmsg'], $getResult['errcode']);
//        }
//        return true;
//    }


    
    /**
     * 客服消息
     */

//    /**
//     * @param $open_id
//     * @param $template_id
//     * @param $page
//     * @param $data
//     * @return array
//     * @throws Exception
//     * @throws Exception
//     */
//    public function sendTemplateMessage($open_id, $template_id, $page, $data)
//    {
//        /**
//         * 获取access_token
//         */
//        $access_token = $this->getAccessToken();
//        /**
//         * 请求接口
//         */
//        $postResult = parent::post(
//            "https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=" . $access_token,
//            json_encode([
//                'touser' => $open_id,
//                'template_id' => $template_id,
//                'page' => $page,
//                'data' => $data
//            ]),
//            [
//                'Content-Type:application/json;charset=utf-8'
//            ],
//            2000
//        );
//        //返回状态：不成功，抛出异常
//        if ($postResult['errcode'] != 0) {
//            throw new MiniProgramException($postResult['errmsg'], $postResult['errcode']);
//        }
//        return true;
//    }
//
//    /**
//     * @param string $path
//     * @param int $width
//     * @param bool $auto_color
//     * @param array $line_color
//     * @param bool $is_hyaline
//     * @return string
//     * @throws Exception
//     * @throws Exception
//     * @throws Exception
//     */
//    public function getQRCode($path, $width = 430, $auto_color = false, $line_color = ["r" => 0, "g" => 0, "b" => 0], $is_hyaline = false)
//    {
//        /**
//         * 获取access_token
//         */
//        $access_token = $this->getAccessToken();
//        /**
//         * 请求接口
//         */
//        $postResult = self::post(
//            "https://api.weixin.qq.com/wxa/getwxacode?access_token=" . $access_token,
//            [
//                'path' => $path,
//                'width' => (int)$width,
//                'auto_color' => (bool)$auto_color,
//                'line_color' => $line_color,
//                'is_hyaline' => (bool)$is_hyaline
//            ],
//            [],
//            2000
//        );
//        //返回状态：不成功，抛出异常
//        if ($postResult['errcode'] != 0) {
//            throw new MiniProgramException($postResult['errmsg'], $postResult['errcode']);
//        }
//        switch ($postResult['contentType']) {
//            case 'image/jpeg':
//                $ext = '.jpg';
//                break;
//            case 'image/png':
//            case 'application/x-png':
//                $ext = 'png';
//                break;
//            case 'image/gif':
//                $ext = '.gif';
//                break;
//            case 'image/vnd.wap.wbmp':
//                $ext = '.wbmp';
//                break;
//            case 'image/x-icon':
//                $ext = '.ico';
//                break;
//            case 'image/vnd.rn-realpix':
//                $ext = '.rp';
//                break;
//            case 'image/tiff':
//                $ext = '.tiff';
//                break;
//            case 'image/pnetvue':
//                $ext = '.net';
//                break;
//            case 'image/fax':
//                $ext = '.fax';
//                break;
//            default:
//                throw new UnexpectedValueException('未知类型文件' . $postResult['contentType'] . '无法确定存储文件后缀');
//        }
//        $filename = md5($postResult['buffer']) . $ext;
//
//        $relative_file_path = $this->options['app_qrcode_cache_relative_dir_path'] . DIRECTORY_SEPARATOR . $filename;
//        switch ($this->options['app_qrcode_cache_type']) {
//            case 'oss':
//                /**
//                 * 执行数据量到oss的远程文件生成
//                 */
//                $ossClient = new OssClient(
//                    $this->options['access_key_id'],
//                    $this->options['access_key_secret'],
//                    $this->options['end_point']
//                );
//                $ossClient->putObject($this->config['bucket'], $relative_file_path, $postResult['buffer']);
//                break;
//            case 'local':
//                /**
//                 * 执行数据流到本地文件的生成
//                 */
//                $real_file_path = $this->options['app_qrcode_cache_real_dir_path'] . DIRECTORY_SEPARATOR . $filename;
//                if (file_put_contents($real_file_path, $postResult['buffer']) === false) {
//                    throw new MiniProgramException('文件：' . $real_file_path . '写入失败');
//                }
//                break;
//        }
//        return $this->options['app_qrcode_request_url_prefix'] . DIRECTORY_SEPARATOR . $relative_file_path;
//    }
}