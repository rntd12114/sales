<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=rm-2ze7i98y2z5xxo4r16o.mysql.rds.aliyuncs.com;dbname=db_client',
            'username' => 'rw_user',
            'password' => 'pD2020rntd99#',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
];
