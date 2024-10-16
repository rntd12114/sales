<?php

use yii\db\Migration;

/**
 * Class m201019_030929_init_team
 */
class m201019_030929_init_team extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("INSERT INTO team (agent_id, team_name, state) VALUES ('agent26211', '产品部', 1);
INSERT INTO team (agent_id, team_name, state) VALUES ('agent26211', '测试部门', 1);
INSERT INTO team (agent_id, team_name, state) VALUES ('agent26211', '技术部门-php', 1);
INSERT INTO team (agent_id, team_name, state) VALUES ('agent26211', '北方一区', 1);
INSERT INTO team (agent_id, team_name, state) VALUES ('agent26211', '北方二区', 1);
INSERT INTO team (agent_id, team_name, state) VALUES ('agent26211', '运维部', 1);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201019_030929_init_team cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201019_030929_init_team cannot be reverted.\n";

        return false;
    }
    */
}
