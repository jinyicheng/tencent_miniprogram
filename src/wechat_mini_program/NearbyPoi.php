<?php

namespace jinyicheng\tencent_miniprogram\wechat_mini_program;

use jinyicheng\tencent_miniprogram\MiniProgramException;
use jinyicheng\tencent_miniprogram\Request;

/**
 * 附近的小程序
 * Class NearbyPoi
 * @package jinyicheng\tencent_miniprogram\wechat_mini_program
 */
class NearbyPoi
{
    use CommonTrait;

    /**
     * 实时日志查询
     * @param array $pic_list
     * @param array $service_infos
     * @param string $store_name
     * @param string $hour
     * @param string $credential
     * @param string $address
     * @param string $company_name
     * @param string $qualification_list
     * @param array $kf_info
     * @param string $poi_id
     * @return array
     * @throws MiniProgramException
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/nearby-poi/nearbyPoi.add.html
     */
    public function add(array $pic_list, array $service_infos, string $store_name, string $hour, string $credential, string $address, string $company_name, string $qualification_list, array $kf_info, string $poi_id)
    {
        /**
         * 获取access_token
         */
        $access_token = Auth::getInstance($this->options)->getAccessToken();
        $data = [
            'is_comm_nearby' => '1',
            'kf_info' => json_encode($kf_info),
            'pic_list' => json_encode([
                'list' => $pic_list
            ]),
            'service_infos' => json_encode([
                'service_infos' => $service_infos
            ]),
            'store_name' => $store_name,
            'hour' => $hour,
            'credential' => $credential,
            'address' => $address,
            'company_name' => $company_name,
            'qualification_list' => $qualification_list,
            'poi_id' => $poi_id
        ];
        return Request::post(
            'https://api.weixin.qq.com/wxa/addnearbypoi?access_token=' . $access_token,
            $data,
            [
                'Content-Type:application/json;charset=utf-8'
            ],
            2000
        );
    }
}