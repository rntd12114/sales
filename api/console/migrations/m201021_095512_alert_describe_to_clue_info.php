<?php

use yii\db\Migration;

/**
 * Class m201021_095512_alert_describe_to_clue_info
 */
class m201021_095512_alert_describe_to_clue_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('clue_info', 'establish_date', $this->date()->defaultValue(null)->comment('成立日期')->after('wx'));
        $this->alterColumn('clue_info', 'describe', $this->text()->defaultValue(null)->comment('经营范围'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201021_095512_alert_describe_to_clue_info cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201021_095512_alert_describe_to_clue_info cannot be reverted.\n";

        return false;
    }
    */
}
