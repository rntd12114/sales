<?php

use yii\db\Migration;

/**
 * Class m201019_023759_create_client_trace
 */
class m201019_023759_create_client_trace extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `client_trace` (
            `trace_id` int(11) NOT NULL AUTO_INCREMENT,
            `client_id` int(11) NOT NULL COMMENT '客户id',
            `trace_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '跟进时间',
            `trace_mode` varchar(10) NOT NULL COMMENT '跟进方式 电话 /拜访 /其他',
            `phase` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '阶段 立项/谈判/报价/合同/收款/资质/执行/交付/扩单',
            `content` varchar(255) DEFAULT NULL COMMENT '文字表述',
            `work` varchar(10) DEFAULT NULL COMMENT '标准事务 发案例/发合同/回传合同/寄发票或收据/寄合同原件/回传合同原件',
            `product` varchar(50) DEFAULT NULL COMMENT '售卖产品',
            `username` varchar(20) NOT NULL COMMENT '跟进人',
            `next_time` datetime DEFAULT NULL COMMENT '下次联系时间',
            `next_mode` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '下次跟进方式 电话 /拜访 /其他',
            `next_content` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '下次联系内容',
            PRIMARY KEY (`trace_id`)
          ) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='客户跟进记录表';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201019_023759_create_client_trace cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201019_023759_create_client_trace cannot be reverted.\n";

        return false;
    }
    */
}
