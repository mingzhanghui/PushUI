<?php
return array(
	//'配置项'=>'配置值'
    /* sqlite数据库配置 */
    'DB_TYPE'               =>  'sqlite',     // 数据库类型
    'DB_NAME'               =>  './db3/MBIS_Server.db3',          // 数据库名
    'DB_PREFIX'             =>  'MBIS_Server_',    // 数据库表前缀
    'DB_CHARSET'            =>  'utf8',

    'LOG_RECORD' => true, // 开启日志记录
    'LOG_LEVEL'  =>'EMERG,ALERT,CRIT,ERR,NOTICE', // 记录EMERG ALERT CRIT ERR NOTICE 错误
    'LOG_TYPE'   =>  'File',

    // 显示页面Trace信息
    'SHOW_PAGE_TRACE' =>true,
);