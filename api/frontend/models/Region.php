<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "region".
 *
 * @property int $id
 * @property int $code 省市区编码
 * @property string $name
 * @property int $parent_id
 * @property int $out_of_range 是否超区 0否 1超过范围
 * @property int $status 状态 1正常 0停用
 * @property int $type 类型 0省 1市 2区 3街道
 */
class Region extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'region';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'parent_id', 'out_of_range', 'status', 'type'], 'integer'],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'parent_id' => 'Parent ID',
            'out_of_range' => 'Out Of Range',
            'status' => 'Status',
            'type' => 'Type',
        ];
    }
}
