<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute'=>'login/index',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'qaz123wsx852edc745!@we',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport', //使用的类
                'host' => 'smtp.qq.com', //邮箱服务一地址
                'username' => '3423929165@qq.com',//邮箱地址，发送的邮箱
                'password' => 'xkctwhwaquredbgb',  //自己填写邮箱密码
                'port' => '25',  //服务器端口
                'encryption' => 'tls', //加密方式
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class'=>'yii\log\FileTarget',
                    'levels'=>["warning","error"],
                    'categories'=>['edm'],
                    'logFile'=>'@app/runtime/logs/edm.log',
                ],
            ],

        ],
        'db' => require(__DIR__ . '/db.php'),
        'db2'=> require(__DIR__ . '/db2.php'),
        'db3'=> require(__DIR__ . '/db3.php')
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
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
