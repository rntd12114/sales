<?php

use yii\db\Migration;

/**
 * Class m201104_080939_add_column_agent
 */
class m201104_080939_add_column_agent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('agent', 'clue_num', $this->integer()->notNull()->defaultValue(300)->comment('每日可提取线索数量'));
        $this->addColumn('agent', 'client_num', $this->integer()->notNull()->defaultValue(30)->comment('每日可提取客户数量'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201104_080939_add_column_agent cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201104_080939_add_column_agent cannot be reverted.\n";

        return false;
    }
    */
}
