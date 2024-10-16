<?php

use yii\db\Migration;

/**
 * Class m200925_091632_add_column_to_client_menu
 */
class m200925_091632_add_column_to_client_menu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('client_menu', 'type', $this->tinyInteger(1)->defaultValue(1)->comment('类型 1菜单 2权限'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200925_091632_add_column_to_client_menu cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200925_091632_add_column_to_client_menu cannot be reverted.\n";

        return false;
    }
    */
}
