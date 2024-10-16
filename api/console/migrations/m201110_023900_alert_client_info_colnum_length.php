<?php

use yii\db\Migration;

/**
 * Class m201110_023900_alert_client_info_colnum_length
 */
class m201110_023900_alert_client_info_colnum_length extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('client_info', 'client_name', $this->string(255)->notNull()->comment("客户名称"));
        $this->alterColumn('client_info', 'contacts', $this->string(255)->notNull()->comment("联系人"));
        $this->alterColumn('client_info', 'address', $this->string(255)->comment("地址"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201110_023900_alert_client_info_colnum_length cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201110_023900_alert_client_info_colnum_length cannot be reverted.\n";

        return false;
    }
    */
}
