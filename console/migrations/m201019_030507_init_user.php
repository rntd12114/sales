<?php

use yii\db\Migration;

/**
 * Class m201019_030507_init_user
 */
class m201019_030507_init_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("INSERT INTO user (agent_id, username, password, name, role, phone, email, team_id, state) VALUES ('agent26211', 'shixd', 'e10adc3949ba59abbe56e057f20f883e', '石小冬', 1, '18518236830', 'shixd@rntd.cn', 1, 1);
INSERT INTO user (agent_id, username, password, name, role, phone, email, team_id, state) VALUES ('agent26211', '测试', 'e10adc3949ba59abbe56e057f20f883e', '测试', 3, '03128951631', '1394390428@qq.com', null, 1);
INSERT INTO user (agent_id, username, password, name, role, phone, email, team_id, state) VALUES ('agent26211', '宋嘉', 'e10adc3949ba59abbe56e057f20f883e', '宋嘉', 1, '', '测试', 1, 1);
INSERT INTO user (agent_id, username, password, name, role, phone, email, team_id, state) VALUES ('agent26211', '原佳豪', '96e79218965eb72c92a549dd5a330112', '原佳豪', 1, '18801459871', 'yuanjh@rntd.cn', 1, 1);
INSERT INTO user (agent_id, username, password, name, role, phone, email, team_id, state) VALUES ('agent26211', '杨仙', 'e10adc3949ba59abbe56e057f20f883e', '杨仙', 1, '', '', 1, 1);
INSERT INTO user (agent_id, username, password, name, role, phone, email, team_id, state) VALUES ('agent26211', '陈张鹏', 'e10adc3949ba59abbe56e057f20f883e', '陈张鹏', 2, '15286806708', 'chenzhp@rntd.cn', 1, 1);
INSERT INTO user (agent_id, username, password, name, role, phone, email, team_id, state) VALUES ('agent26211', '杨仙2', 'e10adc3949ba59abbe56e057f20f883e', '杨仙2', 1, '', '', 1, 1);
INSERT INTO user (agent_id, username, password, name, role, phone, email, team_id, state) VALUES ('agent26211', '陈小鹏', 'e10adc3949ba59abbe56e057f20f883e', '陈小鹏', 1, '15286806708', '1183626725@qq.com', 1, 1);
INSERT INTO user (agent_id, username, password, name, role, phone, email, team_id, state) VALUES ('agent26211', 'nanyu', '1c63129ae9db9c60c3e8aa94d3e00495', '南宇', 1, '18500310942', 'nanyu@rntd.cn', 4, 1);
");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201019_030507_init_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201019_030507_init_user cannot be reverted.\n";

        return false;
    }
    */
}
