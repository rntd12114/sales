<?php

use yii\db\Migration;

/**
 * Class m201019_023812_create_clue_info
 */
class m201019_023812_create_clue_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `clue_info` (
            `clue_id` int(11) NOT NULL AUTO_INCREMENT,
            `agent_id` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            `clue_name` varchar(100) NOT NULL COMMENT '线索名称',
            `contacts` varchar(20) NOT NULL COMMENT '联系人',
            `tel` varchar(20) DEFAULT NULL COMMENT '电话',
            `email` varchar(50) DEFAULT NULL COMMENT '邮箱',
            `qq` varchar(15) DEFAULT NULL COMMENT 'QQ号',
            `wx` varchar(50) DEFAULT NULL COMMENT '微信号',
            `trade` varchar(50) DEFAULT NULL COMMENT '行业',
            `source` varchar(50) DEFAULT NULL COMMENT '来源',
            `area` varchar(50) DEFAULT NULL COMMENT '地域',
            `address` varchar(100) DEFAULT NULL COMMENT '地址',
            `describe` varchar(255) DEFAULT NULL COMMENT '描述概括',
            `add_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
            `add_user` varchar(20) NOT NULL COMMENT '添加人',
            `add_mode` tinyint(2) NOT NULL COMMENT '添加方式 1手动录入 2批量导入',
            `username` varchar(20) DEFAULT NULL COMMENT '负责人',
            `get_mode` tinyint(1) DEFAULT NULL COMMENT '获取方式 1默认 2上级指派 3自己提取',
            `get_time` datetime DEFAULT NULL COMMENT '获取时间',
            `assign_user` varchar(20) DEFAULT NULL COMMENT '指派人',
            `change_state` tinyint(1) DEFAULT '1' COMMENT '是否转化/释放 -1已释放/回收 1默认 2已转化为客户3,无法联系4，无法沟通',
            `change_time` datetime DEFAULT NULL COMMENT '转化时间',
            `change_user` varchar(20) DEFAULT NULL COMMENT '转化/释放人',
            `team_id` int(11) DEFAULT NULL COMMENT '部门id',
            `trace_time` datetime DEFAULT NULL COMMENT '最后联系时间',
            PRIMARY KEY (`clue_id`)
          ) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8 COMMENT='线索表';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201019_023812_create_clue_info cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201019_023812_create_clue_info cannot be reverted.\n";

        return false;
    }
    */
}
