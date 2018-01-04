<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'timeZone' => 'Asia/Shanghai',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'mH8jRxCgb8SmfW5EMHAZFySGdI1Q_B1o',
        ],
        'cache' => [
//            'class' => 'yii\caching\FileCache',
            'class'=>'yii\caching\MemCache',
            'servers'=>array(
                array(
                    'host'=>'127.0.0.1',
                    'port'=>11211,
                )
            ),
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        // 自定义公共方法
        'helper' => [
            'class' => 'app\models\Helper',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
//            'viewPath' => '@common/mail',
            'useFileTransport' => false,    //这里一定要改成false，不然邮件不会发送，true只是生成邮件在runtime文件夹下，不发邮件
            'transport' => [
                'class' => 'Swift_SmtpTransport', //使用的类
                'host' => 'smtp.qq.com', //邮箱服务一地址
                'username' => '877183659@qq.com',//邮箱地址，发送的邮箱
                'password' => 'dohyvliikqijbdba',  //自己填写邮箱密码
                'port' => '465',  //服务器端口
                'encryption' => 'ssl', //加密方式
            ],
            'messageConfig'=>[
                'charset'=>'UTF-8',
                'from'=>['877183659@163.com'=>'系统邮箱']
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
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning','info'],
                    'logVars'=>[],
                    //表示以yii\db\或者app\models\开头的分类都会写入这个文件
                    'categories'=>['yii\db\*','app\models\*'],
                    //表示写入到文件
                    'logFile'=>'@runtime/../runtime/logs/YIISQL_'.date('y_m_d').'.log',
                ],

            ],
        ],
        'db' => $db,
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
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
