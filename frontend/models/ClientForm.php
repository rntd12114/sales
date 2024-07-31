<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "client_form".
 *
 * @property int $form_id
 * @property int $client_id 客户id
 * @property string $level 客户等级 A/B/C/D
 * @property string|null $operate 经营产品
 * @property string|null $property 经营性质： 厂家、代理、经销商、准代
 * @property string|null $undergo 经历、体验行业知识 ：未体验、体验成功、体验失败
 * @property int|null $years 成立年限
 * @property string|null $shop 是否有店/情况：网络推广/TOP/好久无销量
 * @property string|null $wx_content 朋友圈状态：炫富/文学/激励/业务/激励/其他
 * @property string|null $appeal 欲望、诉求：想做/试水/投资/鄙视
 * @property int|null $age 年龄
 * @property string|null $like 兴趣爱好
 * @property string|null $doubt 顾虑点：名誉/暴富/决策/赔了咋办
 * @property string|null $traits 性格特点：鹰/鸽/狼
 * @property string $username 跟进人
 * @property string $last_time 填入时间、最后一次编辑的时间
 */
class ClientForm extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_form';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id', 'username', 'last_time'], 'required'],
            [['client_id', 'years', 'age'], 'integer', 'message' => '{attribute}必须是数字'],
            [['last_time'], 'safe'],
            [['level'], 'string', 'max' => 2],
            [['operate', 'property', 'shop'], 'string', 'max' => 50, 'tooLong' => '{attribute}最大长度是50个字符'],
            [['undergo', 'wx_content', 'like', 'doubt', 'username'], 'string', 'max' => 20, 'tooLong' => '{attribute}最大长度是20个字符'],
            [['appeal', 'traits'], 'string', 'max' => 10, 'tooLong' => '{attribute}最大长度是10个字符'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'form_id' => 'Form ID',
            'client_id' => 'Client ID',
            'level' => 'Level',
            'operate' => '经营产品',
            'property' => '经营性质',
            'undergo' => '体验行业',
            'years' => '成立年限',
            'shop' => 'Shop',
            'wx_content' => '朋友圈状态',
            'appeal' => '欲望',
            'age' => '年龄',
            'like' => '兴趣爱好',
            'doubt' => 'Doubt',
            'traits' => 'Traits',
            'username' => 'Username',
            'last_time' => 'Last Time',
        ];
    }
}
