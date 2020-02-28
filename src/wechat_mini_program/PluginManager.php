<?php

namespace jinyicheng\tencent_miniprogram\wechat_mini_program;

use jinyicheng\tencent_miniprogram\Request;
use jinyicheng\tencent_miniprogram\Response;
use jinyicheng\tencent_miniprogram\MiniProgramException;

/**
 * 插件管理
 * Class PluginManager
 * @package jinyicheng\tencent_miniprogram\wechat_mini_program
 */
class PluginManager
{
    private $options;
    private static $instance = [];

    /**
     * PluginManager constructor.
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
     * 向插件开发者发起使用插件的申请
     * @param string $plugin_appid
     * @param string $reason
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/plugin-management/pluginManager.applyPlugin.html
     */
    public function applyPlugin(string $plugin_appid, string $reason = '')
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        $data = [
            'action' => 'apply',
            'plugin_appid' => $plugin_appid
        ];

        if ($reason != '') $data['reason'] = $reason;

        return Request::post(
            'https://api.weixin.qq.com/wxa/plugin?access_token=' . $access_token,
            json_encode($data),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000,
            [
                '0' => '正常',
                '-1' => '系统错误',
                '89236' => '该插件不能申请',
                '89237' => '已经添加该插件',
                '89238' => '申请或使用的插件已经达到上限',
                '89239' => '该插件不存在',
                '89240' => '无法进行此操作，只有“待确认”的申请可操作通过/拒绝',
                '89241' => '无法进行此操作，只有“已拒绝/已超时”的申请可操作删除',
                '89242' => '该appid不在申请列表内',
                '89243' => '“待确认”的申请不可删除',
                '89044' => '不存在该插件appid'
            ]
        );
    }

    /**
     * 获取当前所有插件使用方（供插件开发者调用）
     * @param string $plugin_appid
     * @param string $reason
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/plugin-management/pluginManager.getPluginDevApplyList.html
     */
    public function getPluginDevApplyList(int $page, int $num)
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        $data = [
            'action' => 'dev_apply_list',
            'page' => $page,
            'num' => $num
        ];

        return Request::post(
            'https://api.weixin.qq.com/wxa/devplugin?access_token=' . $access_token,
            json_encode($data),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000,
            [
                '0' => '正常',
                '-1' => '系统错误',
                '89236' => '该插件不能申请',
                '89237' => '已经添加该插件',
                '89238' => '申请或使用的插件已经达到上限',
                '89239' => '该插件不存在',
                '89240' => '无法进行此操作，只有“待确认”的申请可操作通过/拒绝',
                '89241' => '无法进行此操作，只有“已拒绝/已超时”的申请可操作删除',
                '89242' => '该appid不在申请列表内',
                '89243' => '“待确认”的申请不可删除',
                '89044' => '不存在该插件appid'
            ]
        );
    }

    /**
     * 查询已添加的插件
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/plugin-management/pluginManager.getPluginList.html
     */
    public function getPluginList()
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        $data = [
            'action' => 'list'
        ];

        return Request::post(
            'https://api.weixin.qq.com/wxa/plugin?access_token=' . $access_token,
            json_encode($data),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000,
            [
                '0' => '正常',
                '-1' => '系统错误',
                '89236' => '该插件不能申请',
                '89237' => '已经添加该插件',
                '89238' => '申请或使用的插件已经达到上限',
                '89239' => '该插件不存在',
                '89240' => '无法进行此操作，只有“待确认”的申请可操作通过/拒绝',
                '89241' => '无法进行此操作，只有“已拒绝/已超时”的申请可操作删除',
                '89242' => '该appid不在申请列表内',
                '89243' => '“待确认”的申请不可删除',
                '89044' => '不存在该插件appid'
            ]
        );
    }

    /**
     * 修改插件使用申请的状态（供插件开发者调用）
     * @param string $action
     * @param array $extra_params
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/plugin-management/pluginManager.setDevPluginApplyStatus.html
     */
    public function setDevPluginApplyStatus(string $action, array $extra_params = [])
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();

        $data['action'] = $action;
        switch ($action) {
            case 'dev_agree':
                if (!isset($extra_params['appid'])) {
                    throw new InvalidArgumentException('$extra_params["appid"]参数不存在，$extra_params当前传入："' . json_encode($extra_params));
                }
                break;
            case 'dev_refuse':
                break;
            case 'dev_delete':
                if (!isset($extra_params['reason'])) {
                    throw new InvalidArgumentException('$extra_params["reason"]参数不存在，$extra_params当前传入："' . json_encode($extra_params));
                }
                break;
            default:
                throw new InvalidArgumentException('$action参数不合法，该参数允许的合法值为（dev_agree、dev_refuse、dev_delete），当前传入："' . $action . '"');
        }

        return Request::post(
            'https://api.weixin.qq.com/wxa/devplugin?access_token=' . $access_token,
            json_encode($data),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000,
            [
                '0' => '正常',
                '-1' => '系统错误',
                '89236' => '该插件不能申请',
                '89237' => '已经添加该插件',
                '89238' => '申请或使用的插件已经达到上限',
                '89239' => '该插件不存在',
                '89240' => '无法进行此操作，只有“待确认”的申请可操作通过/拒绝',
                '89241' => '无法进行此操作，只有“已拒绝/已超时”的申请可操作删除',
                '89242' => '该appid不在申请列表内',
                '89243' => '“待确认”的申请不可删除',
                '89044' => '不存在该插件appid'
            ]
        );
    }

    /**
     * 修改插件使用申请的状态（供插件开发者调用）
     * @param string $action
     * @param array $extra_params
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/plugin-management/pluginManager.unbindPlugin.html
     */
    public function unbindPlugin(string $action, string $plugin_appid)
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();

        $data = [
            'action' => $action,
            'plugin_appid' => $plugin_appid

        ];
        switch ($action) {
            case 'dev_agree':
                if (!isset($extra_params['appid'])) {
                    throw new InvalidArgumentException('$extra_params["appid"]参数不存在，$extra_params当前传入："' . json_encode($extra_params));
                }
                break;
            case 'dev_refuse':
                break;
            case 'dev_delete':
                if (!isset($extra_params['reason'])) {
                    throw new InvalidArgumentException('$extra_params["reason"]参数不存在，$extra_params当前传入："' . json_encode($extra_params));
                }
                break;
            default:
                throw new InvalidArgumentException('$action参数不合法，该参数允许的合法值为（dev_agree、dev_refuse、dev_delete），当前传入："' . $action . '"');
        }

        return Request::post(
            'https://api.weixin.qq.com/wxa/plugin?access_token=' . $access_token,
            json_encode($data),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000,
            [
                '0' => '正常',
                '-1' => '系统错误',
                '89236' => '该插件不能申请',
                '89237' => '已经添加该插件',
                '89238' => '申请或使用的插件已经达到上限',
                '89239' => '该插件不存在',
                '89240' => '无法进行此操作，只有“待确认”的申请可操作通过/拒绝',
                '89241' => '无法进行此操作，只有“已拒绝/已超时”的申请可操作删除',
                '89242' => '该appid不在申请列表内',
                '89243' => '“待确认”的申请不可删除',
                '89044' => '不存在该插件appid'
            ]
        );
    }
}