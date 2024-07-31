<?php

use yii\db\Migration;

/**
 * Class m201021_095530_alert_describe_to_client_info
 */
class m201021_095530_alert_describe_to_client_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('client_info', 'establish_date', $this->date()->defaultValue(null)->comment('成立日期'));
        $this->alterColumn('client_info', 'describe', $this->text()->defaultValue(null)->comment('经营范围'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201021_095530_alert_describe_to_client_info cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201021_095530_alert_describe_to_client_info cannot be reverted.\n";

        return false;
    }
    */
}
