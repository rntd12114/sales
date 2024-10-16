<?php
return [
    'components' => [
        'db' => [
            'dsn' => 'mysql:host=rm-2ze7i98y2z5xxo4r190130.mysql.rds.aliyuncs.com;dbname=db_client_dev',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];
