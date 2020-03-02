<?php

namespace jinyicheng\tencent_miniprogram\wechat_mini_program;

use InvalidArgumentException;
use jinyicheng\tencent_miniprogram\MiniProgramException;
use jinyicheng\tencent_miniprogram\Request;

/**
 * 生物认证
 * Class Operation
 * @package jinyicheng\tencent_miniprogram\wechat_mini_program
 */
class Operation
{
    use CommonTrait;

    /**
     * 实时日志查询
     * @param string $date
     * @param int $begintime
     * @param int $endtime
     * @param array $extra_params
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/operation/operation.realtimelogSearch.html
     */
    public function realtimelogSearch(string $date, int $begintime, int $endtime,array $extra_params=[])
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        $data = [
            'access_token' => $access_token,
            'date'=>$date,
            'begintime'=>$begintime,
            'endtime'=>$endtime,
            'start'=>isset($extra_params['start'])? $extra_params['start']:0,
            'limit'=>isset($extra_params['limit'])? $extra_params['limit']:20
        ];
        if (isset($extra_params['traceId'])) $data['traceId'] = $extra_params['traceId'];
        if (isset($extra_params['url'])) $data['url'] = $extra_params['url'];
        if (isset($extra_params['id'])) $data['id'] = $extra_params['id'];
        if (isset($extra_params['filterMsg'])) $data['filterMsg'] = $extra_params['filterMsg'];
        if (isset($extra_params['level'])) {
            if (!in_array($extra_params['level'], [2,4,8])) {
                throw new InvalidArgumentException('$extra_params["level"]参数不合法，该参数允许的合法值为（2、4、8），其中level的定义为2（Info）、4（Warn）、8（Error）当前传入："' . $extra_params["level"] . '"');
            } else {
                $data['level'] = $extra_params['level'];
            }
        }

        return Request::post(
            'https://api.weixin.qq.com/wxaapi/userlog/userlog_search',
            $data,
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000
        );
    }
}