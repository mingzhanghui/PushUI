<?php
return array(
    //'配置项'=>'配置值'
    //====================ThinkPHP配置============================//
    /* sqlite数据库配置 */
    'DB_TYPE'               =>  'sqlite',     // 数据库类型
    'DB_NAME'               =>  './db3/MBIS_Server.db3',          // 数据库名
    'DB_PREFIX'             =>  'MBIS_Server_',    // 数据库表前缀
    'DB_CHARSET'            =>  'utf8',

    'LOG_RECORD' => true, // 开启日志记录
    'LOG_LEVEL'  =>'EMERG,ALERT,CRIT,ERR,WARN', // 记录EMERG ALERT CRIT ERR NOTICE 错误
    'LOG_TYPE'   =>'File',

    // 显示页面Trace信息
    'SHOW_PAGE_TRACE' =>false,

    // SQL解析缓存
    'DB_SQL_BUILD_CACHE' => false,
    // 'DB_SQL_BUILD_LENGTH' => 20, // SQL 缓存的队列长度

    //====================自定义配置============================//
    // The sitewide hashkey, do not change this because it's used for passwords!
    // This is for other hash keys... Not sure yet
    'HASH_GENERAL_KEY' => 'MixitUp200',

    // This is for database passwords only
    'HASH_PASSWORD_KEY' => 'catsFLYhigh2000miles',

    'DAILY_RECORD_DIR'  => './Log',   // 开启Configure表 daily_record 功能log保存目录

    // 生成二维码服务器url前缀
    // 'QR_URL'      => 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx4e8458aa6433be6a',
    'QR_URL' => 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxc335d36999732aa6',
    // 'QR_REDIRECT' => 'http://b5312762.ngrok.io',
    'QR_REDIRECT' => 'http://www.funintv.com',
    'QR_SUFFIX'   => '&response_type=code&scope=snsapi_base&state=1#wechat_redirect'
);