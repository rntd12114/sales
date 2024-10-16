<?php

use yii\db\Migration;

/**
 * Class m201019_023704_create_client_form
 */
class m201019_023704_create_client_form extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `client_form` (
            `form_id` int(11) NOT NULL AUTO_INCREMENT,
            `client_id` int(11) NOT NULL COMMENT '客户id',
            `level` varchar(2) DEFAULT NULL COMMENT '客户等级 A/B/C/D',
            `operate` varchar(50) DEFAULT NULL COMMENT '经营产品',
            `property` varchar(50) DEFAULT NULL COMMENT '经营性质： 厂家、代理、经销商、准代',
            `undergo` varchar(20) DEFAULT NULL COMMENT '经历、体验行业知识 ：未体验、体验成功、体验失败',
            `years` tinyint(2) DEFAULT NULL COMMENT '成立年限',
            `shop` varchar(50) DEFAULT NULL COMMENT '是否有店/情况：网络推广/TOP/好久无销量',
            `wx_content` varchar(20) DEFAULT NULL COMMENT '朋友圈状态：炫富/文学/激励/业务/激励/其他',
            `appeal` varchar(10) DEFAULT NULL COMMENT '欲望、诉求：想做/试水/投资/鄙视',
            `age` tinyint(2) DEFAULT NULL COMMENT '年龄',
            `like` varchar(20) DEFAULT NULL COMMENT '兴趣爱好',
            `doubt` varchar(20) DEFAULT NULL COMMENT '顾虑点：名誉/暴富/决策/赔了咋办',
            `traits` varchar(10) DEFAULT NULL COMMENT '性格特点：鹰/鸽/狼',
            `username` varchar(20) NOT NULL COMMENT '跟进人',
            `last_time` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '填入时间、最后一次编辑的时间',
            PRIMARY KEY (`form_id`)
          ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='客户画像表';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201019_023704_create_client_form cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201019_023704_create_client_form cannot be reverted.\n";

        return false;
    }
    */
}
