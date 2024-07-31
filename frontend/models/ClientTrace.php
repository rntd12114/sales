<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "client_trace".
 *
 * @property int $trace_id
 * @property int $client_id 客户id
 * @property string $trace_time 跟进时间
 * @property string $trace_mode 跟进方式 电话 /拜访 /其他
 * @property string $phase 阶段 立项/谈判/报价/合同/收款/资质/执行/交付/扩单
 * @property string|null $content 文字表述
 * @property string|null $work 标准事务 发案例/发合同/回传合同/寄发票或收据/寄合同原件/回传合同原件
 * @property string|null $product 售卖产品
 * @property string $username 跟进人
 * @property string $next_time 下次联系时间
 * @property string $next_mode 下次跟进方式 电话 /拜访 /其他
 * @property string $next_content 下次联系内容
 */
class ClientTrace extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_trace';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id', 'trace_mode', 'username'], 'required'],
            [['client_id'], 'integer'],
            [['trace_time', 'next_time'], 'safe'],
            [['trace_mode', 'phase', 'work', 'next_mode'], 'string', 'max' => 10],
            [['content', 'next_content'], 'string', 'max' => 255, 'tooLong' => '{attribute}最大长度是255个字符'],
            [['product'], 'string', 'max' => 50, 'tooLong' => '{attribute}最大长度是50个字符'],
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
            'client_id' => 'Client ID',
            'trace_time' => 'Trace Time',
            'trace_mode' => 'Trace Mode',
            'phase' => 'Phase',
            'content' => '跟进内容',
            'work' => 'Work',
            'product' => '售卖产品',
            'username' => 'Username',
            'next_time' => 'Next Time',
            'next_mode' => 'Next Mode',
            'next_content' => '下次跟进内容',
        ];
    }
}
