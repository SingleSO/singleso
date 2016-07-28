<?php

// Comment out the following two lines when deployed to production.
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
	'id' => 'sso-web',
	'name' => isset($params['applicationName']) ? $params['applicationName'] : null,
	'basePath' => dirname(__DIR__),
	'bootstrap' => ['log'],
	'components' => [
		'request' => [
			// Replace with secret key if blank.
			'cookieValidationKey' => '',
		],
		'cache' => [
			'class' => 'yii\caching\FileCache',
		],
		'errorHandler' => [
			'errorAction' => 'site/error',
		],
		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
			// True use file, false send mail.
			'useFileTransport' => true,
			// Use mail by default, or configure for SMPT.
			// 'transport' => [
			// 	'class' => 'Swift_SmtpTransport',
			// 	'host' => 'localhost',
			// 	'username' => 'username',
			// 	'password' => 'password',
			// 	'port' => '587',
			// 	'encryption' => 'tls',
			// ],
		],
		'session' => [
			// Use database for sessions.
			'class' => 'yii\web\DbSession',
			'name' => 'SSOSESSION',
			'timeout' => 60 * 60 * 24, // 24 hours unless remember me checked.
		],
		'log' => [
			'traceLevel' => (defined('YII_DEBUG') && YII_DEBUG) ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
				],
			],
		],
		'db' => $db,
		'urlManager' => [
			'class' => 'yii\web\UrlManager',
			// true keeps index.php, false removes but requires server config.
			'showScriptName' => false,
			// true uses path directly, false uses r=/path
			'enablePrettyUrl' => true,
			'rules' => [
				// Alias the controller bases.
				'<alias:user(/?)>' => 'user/settings',
				'<alias:admin(/?)>' => '<alias>',
				'<alias:oauth2(/?)>' => '<alias>',
				'<alias:oauth2-client(/?)>' => '<alias>',
				// Default to site controller.
				'<action:[\w-]+(/?)>' => 'site/<action>',
			],
		],
		'view' => [
			'class' => '\rmrevin\yii\minify\View',
			'enableMinify' => !(defined('YII_DEBUG') && YII_DEBUG),
			// Path alias to web base
			'web_path' => '@web',
			// Path alias to web base.
			'base_path' => '@webroot',
			// Path alias to save minify result.
			'minify_path' => '@webroot/assets/min',
			// Charset, otherwise will use all of the files found charset.
			'force_charset' => 'UTF-8',
			// Compress result html page.
			'compress_output' => false,
			'theme' => [
				'pathMap' => [
					// Override the users views.
					'@dektrium/user/views' => '@app/views/user',
				]
			]
		],
		'authClientCollection' => [
			'class' => 'app\models\authclient\Collection',
		],
		'user' => [
			'identityCookie' => ['name' => 'SSOID', 'httpOnly' => true],
		],
	],
	'modules' => [
		'user' => [
			'class' => 'dektrium\user\Module',
			'modelMap' => [
				'User' => 'app\models\user\User',
				'LoginForm' => 'app\models\user\LoginForm',
				'SettingsForm' => 'app\models\user\SettingsForm',
				'RegistrationForm' => 'app\models\user\RegistrationForm',
			],
			'controllerMap' => [
				'security' => 'app\controllers\user\SecurityController'
			],
		],
	],
	'params' => $params,
];

if (
	(defined('YII_ENV_DEV') && YII_ENV_DEV) ||
	(defined('YII_ENV') && YII_ENV === 'dev')
) {
	// Configuration adjustments for the 'dev' environment.
	$config['components']['assetManager']['forceCopy'] = true;
	$config['bootstrap'][] = 'debug';
	$config['modules']['debug'] = [
		'class' => 'yii\debug\Module',
	];
	$config['bootstrap'][] = 'gii';
	$config['modules']['gii'] = [
		'class' => 'yii\gii\Module',
	];
}

return $config;
