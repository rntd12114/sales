<?php

use yii\db\Migration;

/**
 * Class m201104_071736_update_clue_info
 */
class m201104_071736_update_clue_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('clue_info', 'clue_mark',
            $this->tinyInteger(1)->defaultValue(null)->comment('跟踪标记 1空号 2挂断 3未接听'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201104_071736_update_clue_info cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201104_071736_update_clue_info cannot be reverted.\n";

        return false;
    }
    */
}
