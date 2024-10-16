<?php

use yii\db\Migration;

/**
 * Class m201021_082039_add_establish_date_to_clue_info
 */
class m201021_082039_add_establish_date_to_clue_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('clue_info', 'establish_date', $this->dateTime()->defaultValue(null)->comment('成立日期')->after('wx'));
        $this->addColumn('clue_info', 'capital', $this->string(20)->comment('注册资本')->after('establish_date'));
        $this->addColumn('clue_info', 'province', $this->string(10)->comment('省份')->after('source'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201021_082039_add_establish_date_to_clue_info cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201021_082039_add_establish_date_to_clue_info cannot be reverted.\n";

        return false;
    }
    */
}
