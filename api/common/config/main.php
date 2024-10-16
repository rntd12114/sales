<?php
$log = require_once __DIR__ . '/log.php';

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=rm-2ze7i98y2z5xxo4r16o.mysql.rds.aliyuncs.com;dbname=db_client_dev',
            'username' => 'rw_user',
            'password' => 'pD2020rntd99#',
            'charset' => 'utf8mb4',
            'attributes' => [
                PDO::ATTR_STRINGIFY_FETCHES => false,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ],
        'log' => $log,
    ],
];
