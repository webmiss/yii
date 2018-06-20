<?php
// 开启session
session_start() or session_start();

return [
	'id' => 'web',
	'version' => '1.0.0',
	'charset' => 'UTF-8',
	'timeZone' => 'Asia/Shanghai',
	'basePath' => realpath(__DIR__ . '/../'),
	// 默认模块
	'bootstrap' => ['home'],
	// 默认首页
	'defaultRoute' => 'home/index',
	// 默认模板视图
	'layout' => false,
	// 定义模块
	'modules' => [
		'home' => ['class'=>'app\modules\home\Module'],
		'admin' => ['class'=>'app\modules\admin\Module'],
	],
	/* 组件 */
	'components' => [
		 'request' => [
		 	'cookieValidationKey' => 'webmis.vip',
		 ],
		// 数据库
		'db' => [
			'class' => 'yii\db\Connection',
			'dsn' => 'mysql:host=localhost;dbname=mvc',
			'username' => 'webmis',
			'password' => 'webmis',
			'charset' => 'utf8',
		],
	],
];
