<?php


use think\facade\Env;

return [
    // +----------------------------------------------------------------------
    // | 腾讯对接配置信息
    // +----------------------------------------------------------------------
    'app_name' => Env::get('app_name','微信小程序'),//填写小游戏名称，一旦上线谨慎修改，曾经调取过此参数的记录将不做变更，仅对更新后版本有效
    'app_id' => Env::get('app_id','xxx'),//请从官方获取
    'app_secret' => Env::get('app_secret','xxx'),//请从官方获取
    'app_redis_cache_db_number' => Env::get('app_redis_cache_db_number',1),//缓存到redis的DB编号
    'app_redis_cache_key_prefix' => Env::get('app_redis_cache_key_prefix','wechat:miniprogram:client'),//缓存到redis时所有key的前缀

    'app_qrcode_cache_type' => 'oss',//存储小程序码的方式：oss（阿里云oss）、local（本地）
    'app_qrcode_cache_relative_dir_path' => 'wechat/minigame/quanmincaigewang',//小程序码存储的相对目录（对访问域名而言）
    'app_qrcode_request_url_prefix' => '//upload.oss-cn-shanghai.aliyuncs.com',//小程序码存储的相对目录（对访问域名而言）
    /**
     * 以下使用阿里云oss方式存储小程序码必须配置
     */
    'app_qrcode_cache_oss_access_key_id'=>'xxxxxxx',//请从阿里云oss官方获取access_key_id
    'app_qrcode_cache_oss_access_key_secret'=>'xxxxxxxxxxxxx',//请从阿里云oss官方获取access_key_secret
    'app_qrcode_cache_oss_end_point'=>'oss-cn-shanghai.aliyuncs.com',//请从阿里云oss官方获取end_point
    'app_qrcode_cache_oss_bucket'=>'xxxx',//请从阿里云oss官方获取bucket
    /**
     * 以下使用本地方式存储小程序码必须配置
     */
    'app_qrcode_cache_real_dir_path'=>'/home/wwwroot/xxxxxx/public/cache/miniprogram/client',//小程序码实际生成后存放的文件夹路径
];