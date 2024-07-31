<?php

use yii\db\Migration;

/**
 * Class m201019_023838_init_table_client_menu
 */
class m201019_023838_init_table_client_menu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("INSERT INTO client_menu (id, name, route, level, type) VALUES (1, '部门列表接口', 'department-manage/list', 2, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (2, '菜单列表', 'department-manage/menu', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (3, '客户列表', 'client/list', 1, 1);
INSERT INTO client_menu (id, name, route, level, type) VALUES (4, '添加部门', 'department-manage/add', 3, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (5, '公共线索列表', 'clue/public-clue-list', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (6, 'excel导入线索', 'clue/excel-to-clue', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (7, '更新部门', 'department-manage/update', 3, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (8, '手动添加线索', 'clue/save-clue', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (9, '指派客户', 'client/client-assign', 2, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (10, '提取客户', 'client/client-pick', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (11, '释放、回收客户', 'client/client-change', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (12, '手动添加客户', 'client/client-add', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (13, '获取客户信息', 'client/client-info', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (14, '修改客户信息', 'client/client-up', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (15, '添加客户画像', 'client/form-up', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (16, '获取画像信息', 'client/form-info', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (17, '修改画像', 'client/form-up', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (18, '添加客户跟进记录', 'client/trace-add', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (19, '获取跟进记录', 'client/trace-info', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (20, '我的线索池列表', 'clue/my-clue-list', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (21, '导入客户', 'client/client-import', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (22, '用户列表接口', 'user-manage/list', 2, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (23, '线索跟进记录添加', 'clue/trace-add', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (24, '线索指派', 'clue/clue-assign', 2, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (25, '线索提取', 'clue/clue-pick', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (26, '线索释放', 'clue/clue-change', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (27, '获取线索跟进记录', 'clue/get-trace-info', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (28, '获取线索变更记录', 'clue/get-log-info', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (29, '获取线索详情', 'clue/clue-detail', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (30, '创建用户接口', 'user-manage/add', 2, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (31, '获取单个用户信息接口', 'user-manage/get', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (32, '更新用户资料', 'user-manage/update', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (33, '我的线索池', '/myCluespool', 1, 1);
INSERT INTO client_menu (id, name, route, level, type) VALUES (34, '公共线索池', '/publicCluespool', 1, 1);
INSERT INTO client_menu (id, name, route, level, type) VALUES (35, '员工设置', '/staffSet', 2, 1);
INSERT INTO client_menu (id, name, route, level, type) VALUES (36, '个人设置', '/setting', 1, 1);
INSERT INTO client_menu (id, name, route, level, type) VALUES (37, '公共的客户池', '/publicClientpool', 1, 1);
INSERT INTO client_menu (id, name, route, level, type) VALUES (38, '我的客户池', '/myClientpool', 1, 1);
INSERT INTO client_menu (id, name, route, level, type) VALUES (39, '线索转化', 'clue/clue-turn', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (40, '部门线索', 'clue/team-clue-list', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (41, '一周访问线索列表', 'clue/week-clue-list', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (42, '线索删除', 'clue/del-clue', 1, 2);
INSERT INTO client_menu (id, name, route, level, type) VALUES (43, '客户转化记录', 'client/client-log-list', 1, 1);
INSERT INTO client_menu (id, name, route, level, type) VALUES (44, '我的任务', 'client/trace-next-info', 1, 1);
INSERT INTO client_menu (id, name, route, level, type) VALUES (45, '我的数据', 'client/index', 1, 1);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201019_023838_init_table_client_menu cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201019_023838_init_table_client_menu cannot be reverted.\n";

        return false;
    }
    */
}
