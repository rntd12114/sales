<?php

use yii\db\Migration;

/**
 * Class m201019_023616_create_agent
 */
class m201019_023616_create_agent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `agent` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `agent_id` varchar(20) NOT NULL COMMENT '代理号',
            `agent_name` varchar(100) NOT NULL COMMENT '代理名称',
            `state` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1启用 -1关闭',
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='代理商表';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201019_023616_create_agent cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201019_023616_create_agent cannot be reverted.\n";

        return false;
    }
    */
}
