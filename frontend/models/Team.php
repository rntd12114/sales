<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "team".
 *
 * @property int $team_id
 * @property string|null $agent_id
 * @property string|null $team_name 部门名称
 * @property int $state 1启用 -1关闭
 */
class Team extends \yii\db\ActiveRecord
{
    const STAT_OPEN = 1;
    const STAT_CLOSED = -1;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['team_name', 'state'];
        $scenarios[self::SCENARIO_UPDATE] = ['team_id', 'team_name', 'state'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'team';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['agent_id', 'required', 'message' => '代理号必填', 'on' => self::SCENARIO_CREATE],
            [
                'team_name',
                'required',
                'message' => '部门名称必填',
                'on' => self::SCENARIO_CREATE
            ],
            ['state', 'required', 'message' => '部门名称状态必选', 'on' => self::SCENARIO_CREATE],
            ['team_id', 'required', 'message' => '部门ID必传', 'on' => self::SCENARIO_UPDATE],


            ['state', 'in', 'range' => [1, -1], 'message' => '状态值错误，必须是1或者-1'],
            ['agent_id', 'string', 'max' => 100],
            [
                'team_name',
                'string',
                'max' => 20,
                'min' => 2,
                'tooShort' => '部门名称最少两个字符',
                'tooLong' => '部门名称最长20个字符'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'team_id' => '部门ID',
            'agent_id' => '代理号',
            'team_name' => '部门名称',
            'state' => '部门状态',
        ];
    }
}
