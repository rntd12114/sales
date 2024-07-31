<?php

use yii\db\Migration;

/**
 * Class m201019_023840_create_clue_trace
 */
class m201019_023840_create_clue_trace extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `clue_trace` (
            `trace_id` int(11) NOT NULL AUTO_INCREMENT,
            `clue_id` int(11) NOT NULL COMMENT '线索id',
            `trace_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '跟进时间',
            `trace_mode` varchar(10) NOT NULL COMMENT '跟进方式 电话 /拜访 /其他',
            `content` varchar(255) DEFAULT NULL COMMENT '文字表述',
            `username` varchar(20) NOT NULL COMMENT '跟进人',
            PRIMARY KEY (`trace_id`)
          ) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='线索跟进记录表';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201019_023840_create_clue_trace cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201019_023840_create_clue_trace cannot be reverted.\n";

        return false;
    }
    */
}
