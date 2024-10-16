<?php

use yii\db\Migration;

/**
 * Class m201019_023909_create_user
 */
class m201019_023909_create_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `user` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `agent_id` varchar(20) NOT NULL,
            `username` varchar(20) NOT NULL COMMENT '用户名',
            `password` varchar(100) NOT NULL COMMENT '密码',
            `name` varchar(20) NOT NULL COMMENT '员工姓名',
            `role` tinyint(1) NOT NULL COMMENT '角色/级别  1员工 2经理 3总监',
            `phone` varchar(20) DEFAULT NULL COMMENT '手机号',
            `email` varchar(50) DEFAULT NULL COMMENT '邮箱',
            `team_id` int(11) DEFAULT NULL COMMENT '部门id',
            `state` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1启用 -1关闭',
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='代理商人员表';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201019_023909_create_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201019_023909_create_user cannot be reverted.\n";

        return false;
    }
    */
}
