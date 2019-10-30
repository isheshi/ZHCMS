<?php
return array(
    //'配置项'=>'配置值'
	'URL_MODEL' => 2,//设置路由模式为重写模式
	'DEFAULT_MODULE' => 'Home',//设置默认模块
	'MODULE_ALLOW_LIST' => array('Home','Admin'),//设置容许访访问的模块
    'TMPL_PARSE_STRING' => array(
        '__PUBLIC_ADMIN__' => '/Public/Admin',
        '__PUBLIC_HOME__' => '/Public/Home',

    ),

    'DB_TYPE' => 'mysql',       //数据库类型
    'DB_HOST' => 'localhost',   //host地址
    'DB_USER' => 'root',        //数据库用户名
    'DB_PWD' => 'root',          //数据库密码
    'DB_NAME' => 'zhcms',  //数据库名
    'DB_PREFIX' => 'zh_',      //表前缀
);