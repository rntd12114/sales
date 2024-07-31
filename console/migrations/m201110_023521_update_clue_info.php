<?php

use yii\db\Migration;

/**
 * Class m201110_023521_update_clue_info
 */
class m201110_023521_update_clue_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('clue_info', 'clue_name', $this->string(255)->notNull()->comment("线索名称"));
        $this->alterColumn('clue_info', 'contacts', $this->string(255)->notNull()->comment("联系人"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201110_023521_update_clue_info cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201110_023521_update_clue_info cannot be reverted.\n";

        return false;
    }
    */
}
