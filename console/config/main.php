<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'components' => [
        'apns' => [
		'class' => 'bryglen\apnsgcm\Apns',
		'environment' => \bryglen\apnsgcm\Apns::ENVIRONMENT_PRODUCTION,
		'pemFile' => dirname(__FILE__).'/apnssert/apns-purelamo-dis-cert.pem',
		// 'retryTimes' => 3,
		'options' => [
			'sendRetryTimes' => 5
		]
	],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['push'],
                    'logFile' => '@app/runtime/logs/push.log',
                ],
            ],
        ],
    ],
    'params' => $params,
];
