<?php

use yii\db\Migration;

/**
 * Class m201105_024921_add_colnum_clue_info
 */
class m201105_024921_add_colnum_clue_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('clue_info', 'mark_time', $this->dateTime()->comment('线索标记时间'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201105_024921_add_colnum_clue_info cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201105_024921_add_colnum_clue_info cannot be reverted.\n";

        return false;
    }
    */
}
