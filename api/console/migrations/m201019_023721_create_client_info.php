<?php

use yii\db\Migration;

/**
 * Class m201019_023721_create_client_info
 */
class m201019_023721_create_client_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `client_info` (
            `client_id` int(11) NOT NULL AUTO_INCREMENT,
            `agent_id` varchar(20) NOT NULL,
            `client_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '客户名称',
            `contacts` varchar(20) NOT NULL COMMENT '联系人',
            `duty` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '部门&职务',
            `weight` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '角色权重 经办人/决策人/关键人/其他',
            `tel` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '电话',
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
            `add_mode` tinyint(2) NOT NULL COMMENT '添加方式 1线索转化 2手动录入 3批量导入',
            `username1` varchar(20) DEFAULT NULL COMMENT '第一负责人',
            `username2` varchar(20) DEFAULT NULL COMMENT '第二负责人',
            `get_mode` tinyint(1) DEFAULT NULL COMMENT '获取方式  1默认 2上级指派 3自己提取',
            `get_time` datetime DEFAULT NULL COMMENT '获取时间',
            `assign_user` varchar(20) DEFAULT NULL COMMENT '指派人',
            `clue_id` int(11) DEFAULT NULL COMMENT '线索id',
            `change_state` tinyint(1) DEFAULT '1' COMMENT '是否转化/释放  -1正常释放/回收 1默认 ',
            `change_time` datetime DEFAULT NULL COMMENT '释放时间',
            `change_user` varchar(20) DEFAULT NULL COMMENT '释放人',
            `team_id` int(11) DEFAULT NULL COMMENT '部门ID',
            `orders` int(2) DEFAULT '0' COMMENT '成单次数 -1难成单 默认0次 ',
            `trace_time` datetime DEFAULT NULL COMMENT '最后跟进时间',
            PRIMARY KEY (`client_id`)
          ) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COMMENT='客户表';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201019_023721_create_client_info cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201019_023721_create_client_info cannot be reverted.\n";

        return false;
    }
    */
}
