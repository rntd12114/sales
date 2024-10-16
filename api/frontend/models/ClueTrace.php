<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "clue_trace".
 *
 * @property int $trace_id
 * @property int $clue_id 线索id
 * @property string $trace_time 跟进时间
 * @property string $trace_mode 跟进方式 电话 /拜访 /其他
 * @property string|null $content 文字表述
 * @property string $username 跟进人
 */
class ClueTrace extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clue_trace';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['clue_id', 'trace_mode', 'username'], 'required'],
            [['clue_id'], 'integer'],
            [['trace_time'], 'safe'],
            [['trace_mode'], 'string', 'max' => 10],
            [['content'], 'string', 'max' => 255],
            [['username'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'trace_id' => 'Trace ID',
            'clue_id' => 'Clue ID',
            'trace_time' => 'Trace Time',
            'trace_mode' => 'Trace Mode',
            'content' => 'Content',
            'username' => 'Username',
        ];
    }
}
