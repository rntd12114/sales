<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "agent".
 *
 * @property int $id
 * @property string $agent_id 代理号
 * @property string $agent_name 代理名称
 * @property int $state 1启用 -1关闭
 * @property int $clue_num 每日可提取线索数量
 * @property int $client_num 每日可提取客户数量
 */
class Agent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agent';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['agent_id', 'agent_name'], 'required'],
            [['state'], 'integer'],
            [['agent_id'], 'string', 'max' => 20],
            [['agent_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agent_id' => 'Agent ID',
            'agent_name' => 'Agent Name',
            'state' => 'State',
            'clue_num' => 'Clue Num',
            'client_num' => 'Client Num',
        ];
    }
}
