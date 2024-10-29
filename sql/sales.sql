/*
 Navicat Premium Data Transfer

 Source Server         : 8.148.4.161
 Source Server Type    : MySQL
 Source Server Version : 101108
 Source Host           : 8.148.4.161:3306
 Source Schema         : sales

 Target Server Type    : MySQL
 Target Server Version : 101108
 File Encoding         : 65001

 Date: 29/10/2024 11:17:19
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for agent
-- ----------------------------
DROP TABLE IF EXISTS `agent`;
CREATE TABLE `agent`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `agent_id` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '代理号',
  `agent_name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '代理名称',
  `state` tinyint NOT NULL DEFAULT 1 COMMENT '1启用 -1关闭',
  `clue_num` int NOT NULL DEFAULT 300 COMMENT '每日可提取线索数量',
  `client_num` int NOT NULL DEFAULT 30 COMMENT '每日可提取客户数量',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci COMMENT = '代理商表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of agent
-- ----------------------------
INSERT INTO `agent` VALUES (1, 'agent26211', 'agent26211', 1, 300, 30);

-- ----------------------------
-- Table structure for client_form
-- ----------------------------
DROP TABLE IF EXISTS `client_form`;
CREATE TABLE `client_form`  (
  `form_id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL COMMENT '客户id',
  `level` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '客户等级 A/B/C/D',
  `operate` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '经营产品',
  `property` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '经营性质： 厂家、代理、经销商、准代',
  `undergo` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '经历、体验行业知识 ：未体验、体验成功、体验失败',
  `years` tinyint NULL DEFAULT NULL COMMENT '成立年限',
  `shop` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '是否有店/情况：网络推广/TOP/好久无销量',
  `wx_content` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '朋友圈状态：炫富/文学/激励/业务/激励/其他',
  `appeal` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '欲望、诉求：想做/试水/投资/鄙视',
  `age` tinyint NULL DEFAULT NULL COMMENT '年龄',
  `like` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '兴趣爱好',
  `doubt` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '顾虑点：名誉/暴富/决策/赔了咋办',
  `traits` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '性格特点：鹰/鸽/狼',
  `username` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '跟进人',
  `last_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '填入时间、最后一次编辑的时间',
  PRIMARY KEY (`form_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci COMMENT = '客户画像表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of client_form
-- ----------------------------

-- ----------------------------
-- Table structure for client_info
-- ----------------------------
DROP TABLE IF EXISTS `client_info`;
CREATE TABLE `client_info`  (
  `client_id` int NOT NULL AUTO_INCREMENT,
  `agent_id` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `client_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '客户名称',
  `contacts` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '联系人',
  `duty` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '部门&职务',
  `weight` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '角色权重 经办人/决策人/关键人/其他',
  `tel` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '电话',
  `email` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '邮箱',
  `qq` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT 'QQ号',
  `wx` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '微信号',
  `establish_date` date NULL DEFAULT NULL COMMENT '成立日期',
  `capital` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '注册资本',
  `trade` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '行业',
  `source` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '来源',
  `province` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '省份',
  `area` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '地域',
  `address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '地址',
  `describe` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL COMMENT '经营范围',
  `add_time` datetime NOT NULL DEFAULT current_timestamp COMMENT '添加时间',
  `add_user` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '添加人',
  `add_mode` tinyint NOT NULL COMMENT '添加方式 1线索转化 2手动录入 3批量导入',
  `username1` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '第一负责人',
  `username2` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '第二负责人',
  `get_mode` tinyint(1) NULL DEFAULT NULL COMMENT '获取方式  1默认 2上级指派 3自己提取',
  `get_time` datetime NULL DEFAULT NULL COMMENT '获取时间',
  `assign_user` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '指派人',
  `clue_id` int NULL DEFAULT NULL COMMENT '线索id',
  `change_state` tinyint(1) NULL DEFAULT 1 COMMENT '是否转化/释放  -1正常释放/回收 1默认 ',
  `change_time` datetime NULL DEFAULT NULL COMMENT '释放时间',
  `change_user` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '释放人',
  `team_id` int NULL DEFAULT NULL COMMENT '部门ID',
  `orders` int NULL DEFAULT 0 COMMENT '成单次数 -1难成单 默认0次 ',
  `trace_time` datetime NULL DEFAULT NULL COMMENT '最后跟进时间',
  PRIMARY KEY (`client_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 26 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci COMMENT = '客户表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of client_info
-- ----------------------------

-- ----------------------------
-- Table structure for client_log
-- ----------------------------
DROP TABLE IF EXISTS `client_log`;
CREATE TABLE `client_log`  (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NULL DEFAULT NULL COMMENT '客户ID',
  `opt_time` datetime NULL DEFAULT current_timestamp COMMENT '操作时间',
  `opt_mode` tinyint(1) NULL DEFAULT NULL COMMENT '动作 1默认 2上级指派 3自己提取 4释放',
  `opt_user` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '操作人',
  `to_user` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '操作给谁(指派的时候有此项)',
  PRIMARY KEY (`log_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 42 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci COMMENT = '客户log' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of client_log
-- ----------------------------

-- ----------------------------
-- Table structure for client_menu
-- ----------------------------
DROP TABLE IF EXISTS `client_menu`;
CREATE TABLE `client_menu`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '菜单名称',
  `route` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '菜单路由',
  `level` tinyint(1) NULL DEFAULT 1 COMMENT '级别，1员工 2经理 3总监',
  `type` tinyint(1) NULL DEFAULT 1 COMMENT '类型 1菜单 2权限',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 61 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '员工菜单表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of client_menu
-- ----------------------------
INSERT INTO `client_menu` VALUES (1, '部门列表接口', 'department-manage/list', 2, 2);
INSERT INTO `client_menu` VALUES (2, '菜单列表', 'department-manage/menu', 1, 2);
INSERT INTO `client_menu` VALUES (3, '客户列表', 'client/list', 1, 1);
INSERT INTO `client_menu` VALUES (4, '添加部门', 'department-manage/add', 3, 2);
INSERT INTO `client_menu` VALUES (5, '公共线索列表', 'clue/public-clue-list', 1, 2);
INSERT INTO `client_menu` VALUES (6, 'excel导入线索', 'clue/excel-to-clue', 1, 2);
INSERT INTO `client_menu` VALUES (7, '更新部门', 'department-manage/update', 3, 2);
INSERT INTO `client_menu` VALUES (8, '手动添加线索', 'clue/save-clue', 1, 2);
INSERT INTO `client_menu` VALUES (9, '指派客户', 'client/client-assign', 2, 2);
INSERT INTO `client_menu` VALUES (10, '提取客户', 'client/client-pick', 1, 2);
INSERT INTO `client_menu` VALUES (11, '释放、回收客户', 'client/client-change', 1, 2);
INSERT INTO `client_menu` VALUES (12, '手动添加客户', 'client/client-add', 1, 2);
INSERT INTO `client_menu` VALUES (13, '获取客户信息', 'client/client-info', 1, 2);
INSERT INTO `client_menu` VALUES (14, '修改客户信息', 'client/client-up', 1, 2);
INSERT INTO `client_menu` VALUES (15, '添加客户画像', 'client/form-up', 1, 2);
INSERT INTO `client_menu` VALUES (16, '获取画像信息', 'client/form-info', 1, 2);
INSERT INTO `client_menu` VALUES (17, '修改画像', 'client/form-up', 1, 2);
INSERT INTO `client_menu` VALUES (18, '添加客户跟进记录', 'client/trace-add', 1, 2);
INSERT INTO `client_menu` VALUES (19, '获取跟进记录', 'client/trace-info', 1, 2);
INSERT INTO `client_menu` VALUES (20, '我的线索池列表', 'clue/my-clue-list', 1, 2);
INSERT INTO `client_menu` VALUES (21, '导入客户', 'client/client-import', 1, 2);
INSERT INTO `client_menu` VALUES (22, '用户列表接口', 'user-manage/list', 2, 2);
INSERT INTO `client_menu` VALUES (23, '线索跟进记录添加', 'clue/trace-add', 1, 2);
INSERT INTO `client_menu` VALUES (24, '线索指派', 'clue/clue-assign', 2, 2);
INSERT INTO `client_menu` VALUES (25, '线索提取', 'clue/clue-pick', 1, 2);
INSERT INTO `client_menu` VALUES (26, '线索释放', 'clue/clue-change', 1, 2);
INSERT INTO `client_menu` VALUES (27, '获取线索跟进记录', 'clue/get-trace-info', 1, 2);
INSERT INTO `client_menu` VALUES (28, '获取线索变更记录', 'clue/get-log-info', 1, 2);
INSERT INTO `client_menu` VALUES (29, '获取线索详情', 'clue/clue-detail', 1, 2);
INSERT INTO `client_menu` VALUES (30, '创建用户接口', 'user-manage/add', 2, 2);
INSERT INTO `client_menu` VALUES (31, '获取单个用户信息接口', 'user-manage/get', 1, 2);
INSERT INTO `client_menu` VALUES (32, '更新用户资料', 'user-manage/update', 1, 2);
INSERT INTO `client_menu` VALUES (33, '我的线索池', '/myCluespool', 1, 1);
INSERT INTO `client_menu` VALUES (34, '公共线索池', '/publicCluespool', 1, 1);
INSERT INTO `client_menu` VALUES (35, '员工设置', '/staffSet', 2, 1);
INSERT INTO `client_menu` VALUES (36, '个人设置', '/setting', 1, 1);
INSERT INTO `client_menu` VALUES (37, '公共的客户池', '/publicClientpool', 1, 1);
INSERT INTO `client_menu` VALUES (38, '我的客户池', '/myClientpool', 1, 1);
INSERT INTO `client_menu` VALUES (39, '线索转化', 'clue/clue-turn', 1, 2);
INSERT INTO `client_menu` VALUES (40, '部门线索', 'clue/team-clue-list', 1, 2);
INSERT INTO `client_menu` VALUES (41, '一周访问线索列表', 'clue/week-clue-list', 1, 2);
INSERT INTO `client_menu` VALUES (42, '线索删除', 'clue/del-clue', 1, 2);
INSERT INTO `client_menu` VALUES (43, '客户转化记录', 'client/client-log-list', 1, 1);
INSERT INTO `client_menu` VALUES (44, '我的任务', 'client/trace-next-info', 1, 1);
INSERT INTO `client_menu` VALUES (45, '我的数据', 'client/index', 1, 1);
INSERT INTO `client_menu` VALUES (46, '代理列表', 'user-manage/agent-list', 3, 1);
INSERT INTO `client_menu` VALUES (47, '添加代理', 'user-manage/add-agent', 3, 1);
INSERT INTO `client_menu` VALUES (48, '修改代理', 'user-manage/up-agent', 3, 1);
INSERT INTO `client_menu` VALUES (49, '添加代理员工', 'user-manage/add-agent-user', 3, 1);
INSERT INTO `client_menu` VALUES (50, '代理列表全部', 'user-manage/agent-list-all', 3, 1);
INSERT INTO `client_menu` VALUES (51, '修改密码', 'user-manage/update-pass', 1, 1);
INSERT INTO `client_menu` VALUES (53, '线索标记', 'clue/mark-clue', 1, 2);
INSERT INTO `client_menu` VALUES (54, '省市列表', 'clue/get-region', 1, 2);
INSERT INTO `client_menu` VALUES (55, '标记线索列表', 'clue/mark-clue-list', 1, 2);
INSERT INTO `client_menu` VALUES (56, '操作线索列表', 'clue/time-clue-list', 1, 2);
INSERT INTO `client_menu` VALUES (57, '用户各种数据统计', 'user-manage/total-num', 1, 1);
INSERT INTO `client_menu` VALUES (58, '客户统计数列表', 'client/num-list', 1, 1);
INSERT INTO `client_menu` VALUES (59, '联系线索列表', 'clue/call-clue-list', 1, 2);
INSERT INTO `client_menu` VALUES (60, '获取部门', 'user-manage/agent-team', 1, 2);

-- ----------------------------
-- Table structure for client_trace
-- ----------------------------
DROP TABLE IF EXISTS `client_trace`;
CREATE TABLE `client_trace`  (
  `trace_id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL COMMENT '客户id',
  `trace_time` datetime NOT NULL DEFAULT current_timestamp COMMENT '跟进时间',
  `trace_mode` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '跟进方式 电话 /拜访 /其他',
  `phase` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '阶段 立项/谈判/报价/合同/收款/资质/执行/交付/扩单',
  `content` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '文字表述',
  `work` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '标准事务 发案例/发合同/回传合同/寄发票或收据/寄合同原件/回传合同原件',
  `product` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '售卖产品',
  `username` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '跟进人',
  `next_time` datetime NULL DEFAULT NULL COMMENT '下次联系时间',
  `next_mode` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '下次跟进方式 电话 /拜访 /其他',
  `next_content` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '下次联系内容',
  PRIMARY KEY (`trace_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci COMMENT = '客户跟进记录表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of client_trace
-- ----------------------------

-- ----------------------------
-- Table structure for clue_info
-- ----------------------------
DROP TABLE IF EXISTS `clue_info`;
CREATE TABLE `clue_info`  (
  `clue_id` int NOT NULL AUTO_INCREMENT,
  `agent_id` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `clue_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '线索名称',
  `contacts` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '联系人',
  `tel` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '电话',
  `email` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '邮箱',
  `qq` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT 'QQ号',
  `wx` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '微信号',
  `establish_date` date NULL DEFAULT NULL COMMENT '成立日期',
  `capital` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '注册资本',
  `trade` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '行业',
  `source` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '来源',
  `province` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '省份',
  `area` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '地域',
  `address` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '地址',
  `describe` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL COMMENT '经营范围',
  `add_time` datetime NOT NULL DEFAULT current_timestamp COMMENT '添加时间',
  `add_user` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '添加人',
  `add_mode` tinyint NOT NULL COMMENT '添加方式 1手动录入 2批量导入',
  `username` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '负责人',
  `get_mode` tinyint(1) NULL DEFAULT NULL COMMENT '获取方式 1默认 2上级指派 3自己提取',
  `get_time` datetime NULL DEFAULT NULL COMMENT '获取时间',
  `assign_user` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '指派人',
  `change_state` tinyint(1) NULL DEFAULT 1 COMMENT '是否转化/释放 -1已释放/回收 1默认 2已转化为客户3,无法联系4，无法沟通',
  `change_time` datetime NULL DEFAULT NULL COMMENT '转化时间',
  `change_user` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '转化/释放人',
  `team_id` int NULL DEFAULT NULL COMMENT '部门id',
  `trace_time` datetime NULL DEFAULT NULL COMMENT '最后联系时间',
  `clue_mark` tinyint(1) NULL DEFAULT NULL COMMENT '跟踪标记 1空号 2挂断 3未接听',
  `mark_time` datetime NULL DEFAULT NULL COMMENT '线索标记时间',
  PRIMARY KEY (`clue_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 45 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci COMMENT = '线索表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of clue_info
-- ----------------------------

-- ----------------------------
-- Table structure for clue_log
-- ----------------------------
DROP TABLE IF EXISTS `clue_log`;
CREATE TABLE `clue_log`  (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `clue_id` int NULL DEFAULT NULL COMMENT '线索ID',
  `opt_time` datetime NULL DEFAULT current_timestamp COMMENT '操作时间',
  `opt_mode` tinyint(1) NULL DEFAULT NULL COMMENT '动作 1默认 2上级指派 3自己提取 4释放5转化',
  `opt_user` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '操作人',
  `to_user` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '操作给谁(指派的时候有此项)',
  PRIMARY KEY (`log_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 75 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci COMMENT = '线索log' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of clue_log
-- ----------------------------

-- ----------------------------
-- Table structure for clue_trace
-- ----------------------------
DROP TABLE IF EXISTS `clue_trace`;
CREATE TABLE `clue_trace`  (
  `trace_id` int NOT NULL AUTO_INCREMENT,
  `clue_id` int NOT NULL COMMENT '线索id',
  `trace_time` datetime NOT NULL DEFAULT current_timestamp COMMENT '跟进时间',
  `trace_mode` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '跟进方式 电话 /拜访 /其他',
  `content` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '文字表述',
  `username` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '跟进人',
  PRIMARY KEY (`trace_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci COMMENT = '线索跟进记录表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of clue_trace
-- ----------------------------

-- ----------------------------
-- Table structure for migration
-- ----------------------------
DROP TABLE IF EXISTS `migration`;
CREATE TABLE `migration`  (
  `version` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `apply_time` int NULL DEFAULT NULL,
  PRIMARY KEY (`version`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of migration
-- ----------------------------
INSERT INTO `migration` VALUES ('m000000_000000_base', 1729563130);
INSERT INTO `migration` VALUES ('m200925_074901_create_table_client_menu', 1729563134);
INSERT INTO `migration` VALUES ('m200925_091632_add_column_to_client_menu', 1729563135);
INSERT INTO `migration` VALUES ('m201019_023616_create_agent', 1729563135);
INSERT INTO `migration` VALUES ('m201019_023704_create_client_form', 1729563135);
INSERT INTO `migration` VALUES ('m201019_023721_create_client_info', 1729563136);
INSERT INTO `migration` VALUES ('m201019_023736_create_client_log', 1729563136);
INSERT INTO `migration` VALUES ('m201019_023759_create_client_trace', 1729563136);
INSERT INTO `migration` VALUES ('m201019_023812_create_clue_info', 1729563136);
INSERT INTO `migration` VALUES ('m201019_023826_create_clue_log', 1729563137);
INSERT INTO `migration` VALUES ('m201019_023838_init_table_client_menu', 1729563137);
INSERT INTO `migration` VALUES ('m201019_023840_create_clue_trace', 1729574079);
INSERT INTO `migration` VALUES ('m201019_023854_create_team', 1729574079);
INSERT INTO `migration` VALUES ('m201019_023909_create_user', 1729574079);
INSERT INTO `migration` VALUES ('m201019_030507_init_user', 1729574240);
INSERT INTO `migration` VALUES ('m201019_030929_init_team', 1729574310);
INSERT INTO `migration` VALUES ('m201021_082021_add_establish_date_to_client_info', 1729574311);
INSERT INTO `migration` VALUES ('m201021_082039_add_establish_date_to_clue_info', 1729574312);
INSERT INTO `migration` VALUES ('m201021_095512_alert_describe_to_clue_info', 1729574312);
INSERT INTO `migration` VALUES ('m201021_095530_alert_describe_to_client_info', 1729574313);
INSERT INTO `migration` VALUES ('m201104_071736_update_clue_info', 1729574314);
INSERT INTO `migration` VALUES ('m201104_080939_add_column_agent', 1729574314);
INSERT INTO `migration` VALUES ('m201104_082402_create_region', 1729574315);
INSERT INTO `migration` VALUES ('m201105_024921_add_colnum_clue_info', 1729574316);
INSERT INTO `migration` VALUES ('m201110_023521_update_clue_info', 1729574316);
INSERT INTO `migration` VALUES ('m201110_023900_alert_client_info_colnum_length', 1729574317);

-- ----------------------------
-- Table structure for region
-- ----------------------------
DROP TABLE IF EXISTS `region`;
CREATE TABLE `region`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` int NOT NULL DEFAULT 0 COMMENT '省市区编码',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `parent_id` int NOT NULL DEFAULT 0,
  `out_of_range` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否超区 0否 1超过范围',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 1正常 0停用',
  `type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '类型 0省 1市 2区 3街道',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of region
-- ----------------------------

-- ----------------------------
-- Table structure for team
-- ----------------------------
DROP TABLE IF EXISTS `team`;
CREATE TABLE `team`  (
  `team_id` int NOT NULL AUTO_INCREMENT,
  `agent_id` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `team_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '部门名称',
  `state` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1启用 -1关闭',
  PRIMARY KEY (`team_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci COMMENT = '部门表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of team
-- ----------------------------
INSERT INTO `team` VALUES (1, 'agent26211', '产品部', 1);

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `agent_id` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `username` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '用户名',
  `password` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '密码',
  `name` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '员工姓名',
  `role` tinyint(1) NOT NULL COMMENT '角色/级别  1员工 2经理 3总监',
  `phone` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '手机号',
  `email` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '邮箱',
  `team_id` int NULL DEFAULT NULL COMMENT '部门id',
  `state` tinyint NOT NULL DEFAULT 1 COMMENT '1启用 -1关闭',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci COMMENT = '代理商人员表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES (1, 'agent26211', 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'admin', 3, '03128951631', 'admin@qq.com', NULL, 1);

SET FOREIGN_KEY_CHECKS = 1;
