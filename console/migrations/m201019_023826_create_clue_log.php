<?php

use yii\db\Migration;

/**
 * Class m201019_023826_create_clue_log
 */
class m201019_023826_create_clue_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `clue_log` (
            `log_id` int(11) NOT NULL AUTO_INCREMENT,
            `clue_id` int(11) DEFAULT NULL COMMENT '线索ID',
            `opt_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '操作时间',
            `opt_mode` tinyint(1) DEFAULT NULL COMMENT '动作 1默认 2上级指派 3自己提取 4释放5转化',
            `opt_user` varchar(20) DEFAULT NULL COMMENT '操作人',
            `to_user` varchar(20) DEFAULT NULL COMMENT '操作给谁(指派的时候有此项)',
            PRIMARY KEY (`log_id`)
          ) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8 COMMENT='线索log';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201019_023826_create_clue_log cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201019_023826_create_clue_log cannot be reverted.\n";

        return false;
    }
    */
}
