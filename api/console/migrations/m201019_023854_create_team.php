<?php

use yii\db\Migration;

/**
 * Class m201019_023854_create_team
 */
class m201019_023854_create_team extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `team` (
            `team_id` int(11) NOT NULL AUTO_INCREMENT,
            `agent_id` varchar(20) NOT NULL,
            `team_name` varchar(255) DEFAULT NULL COMMENT '部门名称',
            `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1启用 -1关闭',
            PRIMARY KEY (`team_id`)
          ) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='部门表';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201019_023854_create_team cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201019_023854_create_team cannot be reverted.\n";

        return false;
    }
    */
}
