<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "client_menu".
 *
 * @property int $id
 * @property string $name 菜单名称
 * @property string $route 菜单路由
 * @property int|null $level 级别，1员工 2经理 3总监
 */
class ClientMenu extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_menu';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'route'], 'required'],
            [['level'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['route'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'route' => 'Route',
            'level' => 'Level',
        ];
    }
}
