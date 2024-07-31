<?php
return [
    'traceLevel' => YII_DEBUG ? 3 : 0,
    'targets' => [
        [
            'class' => 'yii\log\EmailTarget',
            'levels' => ['error', 'info'],
            'categories' => ['email_log'],      #该email_log 会在日志方法使用时使用到
            'message' => [
                'from' => ['pd@rntd.cn'],
                'to' => ['shixd@rntd.cn'],
                'subject' => 'Database errors at example.com',
            ],
        ],
        [
            'class' => 'yii\log\FileTarget',
            'levels' => ['error', 'warning', 'info'],
            'logVars' => [],
            'logFile' => '@runtime/logs/mysql/' . date('Y-m-d') . '.log',
            'maxFileSize' => 102400,
            'maxLogFiles' => 10,
            //表示以yii\db\或者app\models\开头的分类都会写入这个文件
            'categories' => ['yii\db\*', 'app\models\*'],
            //表示写入到文件
        ],
    ],
];
