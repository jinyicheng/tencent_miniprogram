<?php

namespace jinyicheng\tencent_miniprogram\wechat_mini_program;

use jinyicheng\redis\Redis;
use jinyicheng\tencent_miniprogram\Datetime;
use jinyicheng\tencent_miniprogram\MiniProgramException;
use jinyicheng\tencent_miniprogram\Request;

/**
 * 数据分析
 * Class Analysis
 * @package jinyicheng\tencent_miniprogram\wechat_mini_program
 */
class Analysis
{
    use CommonTrait;

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
}