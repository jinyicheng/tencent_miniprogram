<?php

namespace jinyicheng\tencent_miniprogram\wechat_mini_program;

use jinyicheng\redis\Redis;
use jinyicheng\tencent_miniprogram\Request;
use jinyicheng\tencent_miniprogram\MiniProgramException;

/**
 * 数据分析
 * Class Analysis
 * @package jinyicheng\tencent_miniprogram\wechat_mini_program
 */
class Analysis
{
    private $options;
    private static $instance = [];

    /**
     * Analysis constructor.
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
    public static function getInstance($options=[])
    {
        if($options===[])$options=config('wechat_mini_program');
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
     * 数据分析
     * @param $type
     * @param $begin_date
     * @param $end_date
     * @return array
     * @throws MiniProgramException
     */
    private function getDatacube($type, $begin_date, $end_date)
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        /**
         * 请求接口
         */
        switch ($type) {
            /**
             * 留存
             */
            case 'getDailyRetain':
                $url = "https://api.weixin.qq.com/datacube/getweanalysisappiddailyretaininfo?access_token=" . $access_token;
                break;
            case 'getMonthlyRetain':
                $url = "https://api.weixin.qq.com/datacube/getweanalysisappidmonthlyretaininfo?access_token=" . $access_token;
                break;
            case 'getWeeklyRetain':
                $url = "https://api.weixin.qq.com/datacube/getweanalysisappidweeklyretaininfo?access_token=" . $access_token;
                break;
            /**
             * 访问趋势
             */
            case 'getDailyVisitTrend':
                $url = "https://api.weixin.qq.com/datacube/getweanalysisappiddailyvisittrend?access_token=" . $access_token;
                break;
            case 'getMonthlyVisitTrend':
                $url = "https://api.weixin.qq.com/datacube/getweanalysisappidmonthlyvisittrend?access_token=" . $access_token;
                break;
            case 'getWeeklyVisitTrend':
                $url = "https://api.weixin.qq.com/datacube/getweanalysisappidweeklyvisittrend?access_token=" . $access_token;
                break;
            /**
             * 其它
             */
            case 'getDailySummary':
                $url = "https://api.weixin.qq.com/datacube/getweanalysisappiddailysummarytrend?access_token=" . $access_token;
                break;
            case 'getUserPortrait':
                $url = "https://api.weixin.qq.com/datacube/getweanalysisappiduserportrait?access_token=" . $access_token;
                break;
            case 'getVisitDistribution':
                $url = "https://api.weixin.qq.com/datacube/getweanalysisappidvisitdistribution?access_token=" . $access_token;
                break;
            case 'getVisitPage':
                $url = "https://api.weixin.qq.com/datacube/getweanalysisappidvisitpage?access_token=" . $access_token;
                break;
        }

        return Request::post(
            $url,
            json_encode([
                'begin_date' => Datetime::format($begin_date),
                'end_date' => Datetime::format($end_date)
            ]),
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000
        );
    }

    /**
     * 获取用户访问小程序日留存
     * @param $begin_date
     * @param $end_date
     * @return mixed
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/data-analysis/visit-retain/analysis.getDailyRetain.html
     */
    public function getDailyRetain($begin_date, $end_date)
    {
        return $this->getDatacude('getDailyRetain', $begin_date, $end_date);
    }

    /**
     * 获取用户访问小程序月留存
     * @param $begin_date
     * @param $end_date
     * @return mixed
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/data-analysis/visit-retain/analysis.getMonthlyRetain.html
     */
    public function getMonthlyRetain($begin_date, $end_date)
    {
        return $this->getDatacude('getMonthlyRetain', $begin_date, $end_date);
    }

    /**
     * 获取用户访问小程序周留存
     * @param $begin_date
     * @param $end_date
     * @return mixed
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/data-analysis/visit-retain/analysis.getWeeklyRetain.html
     */
    public function getWeeklyRetain($begin_date, $end_date)
    {
        return $this->getDatacude('getWeeklyRetain', $begin_date, $end_date);
    }

    /**
     * 获取用户访问小程序数据概况
     * @param $begin_date
     * @param $end_date
     * @return mixed
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/data-analysis/analysis.getDailySummary.html
     */
    public function getDailySummary($begin_date, $end_date)
    {
        return $this->getDatacude('getDailySummary', $begin_date, $end_date);
    }

    /**
     * 获取用户访问小程序数据日趋势
     * @param $begin_date
     * @param $end_date
     * @return mixed
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/data-analysis/visit-trend/analysis.getDailyVisitTrend.html
     */
    public function getDailyVisitTrend($begin_date, $end_date)
    {
        return $this->getDatacude('getDailyVisitTrend', $begin_date, $end_date);
    }

    /**
     * 获取用户访问小程序数据月趋势
     * @param $begin_date
     * @param $end_date
     * @return mixed
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/data-analysis/visit-trend/analysis.getMonthlyVisitTrend.html
     */
    public function getMonthlyVisitTrend($begin_date, $end_date)
    {
        return $this->getDatacude('getMonthlyVisitTrend', $begin_date, $end_date);
    }

    /**
     * 获取用户访问小程序数据周趋势
     * @param $begin_date
     * @param $end_date
     * @return mixed
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/data-analysis/visit-trend/analysis.getWeeklyVisitTrend.html
     */
    public function getWeeklyVisitTrend($begin_date, $end_date)
    {
        return $this->getDatacude('getWeeklyVisitTrend', $begin_date, $end_date);
    }

    /**
     * 获取小程序新增或活跃用户的画像分布数据
     * @param $begin_date
     * @param $end_date
     * @return mixed
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/data-analysis/analysis.getUserPortrait.html
     */
    public function getUserPortrait($begin_date, $end_date)
    {
        return $this->getDatacude('getUserPortrait', $begin_date, $end_date);
    }

    /**
     * 获取用户小程序访问分布数据
     * @param $begin_date
     * @param $end_date
     * @return mixed
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/data-analysis/analysis.getVisitDistribution.html
     */
    public function getVisitDistribution($begin_date, $end_date)
    {
        return $this->getDatacude('getVisitDistribution', $begin_date, $end_date);
    }

    /**
     * 获取访问页面。目前只提供按 page_visit_pv 排序的 top200
     * @param $begin_date
     * @param $end_date
     * @return mixed
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/data-analysis/analysis.getVisitPage.html
     */
    public function getVisitPage($begin_date, $end_date)
    {
        return $this->getDatacude('getVisitPage', $begin_date, $end_date);
    }
}