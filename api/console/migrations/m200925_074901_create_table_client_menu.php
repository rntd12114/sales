<?php

use yii\db\Migration;

/**
 * Class m200925_074901_create_table_client_menu
 */
class m200925_074901_create_table_client_menu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('client_menu', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull()->comment('菜单名称'),
            'route' => $this->string(100)->notNull()->comment('菜单路由'),
            'level' => $this->tinyInteger(1)->defaultValue(1)->comment('级别，1员工 2经理 3总监'),
        ]);

        $this->addCommentOnTable('client_menu', '员工菜单表');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200925_074901_create_table_client_menu cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200925_074901_create_table_client_menu cannot be reverted.\n";

        return false;
    }
    */
}
