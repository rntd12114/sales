<?php

namespace frontend\models;

use Yii;
use yii\data\Pagination;

/**
 * This is the model class for table "client_info".
 *
 * @property int $client_id
 * @property string $agent_id
 * @property string $client_name 客户姓名
 * @property string $contacts 联系人
 * @property string $duty 部门&职务
 * @property string $weight 角色权重 经办人/决策人/关键人/其他
 * @property string|null $tel 电话
 * @property string|null $email 邮箱
 * @property string|null $qq QQ号
 * @property string|null $wx 微信号
 * @property string|null $establish_date 成立日期
 * @property string|null $capital 注册资本
 * @property string|null $trade 行业
 * @property string|null $source 来源
 * @property string|null $province 省份
 * @property string|null $area 地域
 * @property string|null $address 地址
 * @property string|null $describe 描述概括
 * @property string $add_time 添加时间
 * @property string $add_user 添加人
 * @property int $add_mode 添加方式 1线索转化 2手动录入 3批量导入
 * @property string|null $username1 第一负责人
 * @property string|null $username2 第二负责人
 * @property int|null $get_mode 获取方式 1默认 2上级指派 3自己提取
 * @property string|null $get_time 获取时间
 * @property string|null $assign_user 指派人
 * @property int|null $clue_id 线索id
 * @property int|null $change_state 是否转化/释放  -2难成单 -1已释放/回收 1默认 2首开 3二开
 * @property string|null $change_time 释放时间
 * @property string|null $change_user 释放人
 * @property int|null $team_id 部门ID
 * @property int|null $orders 成单次数
 * @property string|null $trace_time 最后跟进时间
 */
class ClientInfo extends \yii\db\ActiveRecord
{
    // 未开通
    const STATUS_DEFAULT = 1;
    // 已释放/回收
    const STATUS_OPEN = -1;

    // 1线索转化
    const ADD_DEFAULT = 1;
    // 2手动录入
    const ADD_MANUAL = 2;
    // 3批量导入
    const ADD_BATCH = 3;

    // 1默认
    const GET_DEFAULT = 1;
    // 2上级指派
    const GET_ASSIGN = 2;
    // 3自己提取
    const GET_PICK = 3;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['agent_id', 'client_name', 'contacts', 'tel', 'add_user', 'add_mode'], 'required', 'message' => '{attribute}不能为空'],
            [['establish_date', 'add_time', 'get_time', 'change_time', 'trace_time'], 'safe'],
            [['describe'], 'string'],
            [['add_mode', 'get_mode', 'clue_id', 'change_state', 'team_id', 'orders'], 'integer', 'message' => '{attribute}必须是数字'],
            [['agent_id', 'tel', 'capital', 'add_user', 'username1', 'username2', 'assign_user', 'change_user'], 'string', 'max' => 20, 'tooLong' => '{attribute}最大长度是20个字符'],
            [['client_name', 'contacts', 'address'], 'string', 'max' => 255, 'tooLong' => '{attribute}最大长度是255个字符'],
            [['duty', 'email', 'wx', 'trade', 'source', 'area'], 'string', 'max' => 50, 'tooLong' => '{attribute}最大长度是50个字符'],
            [['weight', 'province'], 'string', 'max' => 10, 'tooLong' => '{attribute}最大长度是10个字符'],
            [['qq'], 'string', 'max' => 15, 'tooLong' => '{attribute}最大长度是15个字符'],
            [['address'], 'string', 'max' => 100, 'tooLong' => '{attribute}最大长度是100个字符'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'client_id' => 'Client ID',
            'agent_id' => '代理号',
            'client_name' => '客户名称',
            'contacts' => '联系人',
            'duty' => '部门&职务',
            'weight' => '职称',
            'tel' => '电话',
            'email' => '邮箱',
            'qq' => 'QQ',
            'wx' => 'Wx',
            'establish_date' => '成立日期',
            'capital' => '注册资本',
            'trade' => '行业',
            'source' => '来源',
            'province' => '省份',
            'area' => '地区',
            'address' => '地址',
            'describe' => '描述',
            'add_time' => 'Add Time',
            'add_user' => 'Add User',
            'add_mode' => 'Add Mode',
            'username1' => '第一负责人',
            'username2' => '第二负责人',
            'get_mode' => 'Get Mode',
            'get_time' => 'Get Time',
            'assign_user' => 'Assign User',
            'clue_id' => 'Clue ID',
            'change_state' => 'Change State',
            'change_time' => 'Change Time',
            'change_user' => 'Change User',
            'team_id' => 'Team ID',
            'orders' => 'Orders',
            'trace_time' => 'Trace Time',
        ];
    }

    /**
     * 添加客户
     * 必传：'agent_id', 'client_name', 'contacts', 'duty', 'weight', 'tel', 'add_user', 'add_mode'
     */
    static public function addclient($data)
    {
        $agent_id = $data['agent_id'];
        $client_name = trim($data['client_name']);
        $contacts = isset($data['contacts']) ?  trim($data['contacts']) : '';
        $tel = isset($data['tel']) ? trim($data['tel']) : '';
        if (empty($agent_id)) {
            return [
                'error'     => 1,
                'error_msg' => '代理号不能为空',
            ];
        }
        if (empty($client_name)) {
            return [
                'error'     => 1,
                'error_msg' => '客户名称不能为空',
            ];
        }

        // 判断重复
        $ishave = ClientInfo::find()->where([
            'agent_id' => $agent_id,
            'contacts' => $contacts,
            'tel' => $tel
        ])
            ->one();

        if (!empty($ishave)) {
            return [
                'error'     => 1,
                'error_msg' => '该客户联系方式已存在，请勿重复添加',
            ];
        }

        $model = new ClientInfo();
        $model->agent_id = $agent_id;
        $model->client_name = $client_name;
        $model->contacts = $contacts;
        $model->duty = isset($data['duty']) ? trim($data['duty']) : '';
        $model->weight = isset($data['weight']) ? trim($data['weight']) : '';
        $model->tel = isset($data['tel']) ? trim($data['tel']) : '';
        $model->email = isset($data['email']) ? trim($data['email']) : '';
        $model->qq = isset($data['qq']) ? trim($data['qq']) : '';
        $model->wx = isset($data['wx']) ? $data['wx'] : '';
        $model->establish_date = isset($data['establish_date']) ? $data['establish_date'] : '';
        $model->capital = isset($data['capital']) ? $data['capital'] : '';
        $model->trade = isset($data['trade']) ? $data['trade'] : '';
        $model->source = isset($data['source']) ? $data['source'] : '';
        $model->province = isset($data['province']) ? $data['province'] : '';
        $model->area = isset($data['area']) ? $data['area'] : '';
        $model->address = isset($data['address']) ? $data['address'] : '';
        $model->describe = isset($data['describe']) ? $data['describe'] : '';
        $model->add_user = $data['add_user'];
        $model->add_mode = $data['add_mode'];
        $model->add_time = date('Y-m-d H:i:s');
        $model->team_id = isset($data['team_id']) ? $data['team_id'] : '';
        $model->trace_time = isset($data['trace_time']) ? $data['trace_time'] : '';

        //转化的
        if ($data['add_mode'] == ClientInfo::ADD_DEFAULT) {
            $model->clue_id = $data['clue_id'];
            $model->username1 = $data['username1'];
            $model->get_mode =  ClientInfo::GET_PICK;
            $model->get_time = date('Y-m-d H:i:s');
        }
        //导入的
        if ($data['add_mode'] == ClientInfo::ADD_BATCH && !empty($data['username1'])) {
            $model->username1 = $data['username1'];
            $model->get_mode =  ClientInfo::GET_DEFAULT;
            $model->get_time = date('Y-m-d H:i:s');
        } elseif ($data['add_mode'] == ClientInfo::ADD_BATCH && empty($data['username1'])) {
            //没负责人暂归公共池
            $model->change_state = ClientInfo::STATUS_OPEN;
        }

        //手动添加的直接获取为自己客户
        if ($data['add_mode'] == ClientInfo::ADD_MANUAL) {
            $model->username1 = $data['add_user'];
            $model->get_mode =  ClientInfo::GET_PICK;
            $model->get_time = date('Y-m-d H:i:s');
        }


        if ($model->save() > 0) {
            return [
                'error'     => 0,
                'error_msg' => '添加成功',
                'data'      => $model->attributes['client_id'],
            ];;
        } else {
            $err = implode('；', $model->firstErrors);
            return [
                'error'     => 2,
                'error_msg' => $err,
            ];
        }
    }

    /**
     * 跟踪客户数
     *
     */
    static public function clientTrace($username, $time = '1', $current_page = 1, $page_size = 10)
    {
        $time_data = ClueLog::getTime($time);
        $time_start = $time_data['day_start'];
        $time_end = $time_data['day_end'];
        $query = self::find()->alias('o')
            ->select('o.*')
            ->distinct()
            ->leftJoin('client_trace p', '`o`.`client_id` = `p`.`client_id`');
        $query->andWhere(['=', 'p.username', $username]);
        $query->andWhere(['between', 'p.trace_time', $time_start, $time_end]);
        $count = $query->count();
        $pagination = new Pagination([
            'totalCount' => $count,
            'defaultPageSize' => $page_size,
            'pageSizeLimit' => false
        ]);
        $pagination->setPage((int)$current_page - 1);
        $list = $query->offset($pagination->offset)->limit($pagination->limit)->orderBy('p.trace_time DESC')->asArray()->all();
        return $data = [
            'list' => $list,
            'count' => $count,
        ];
    }

    /**
     * 时间区间返回
     * $type today/yesterday/week/month
     */
    static public function timepick($type)
    {
        $start = '';
        $end = '';
        if ($type == 'today') {
            //今天开始时间
            $start = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
            //今天结束时间
            $end = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);
        } elseif ($type == 'yesterday') {
            //昨天开始时间
            $start = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));
            //昨天结束时间
            $end = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1);
        } elseif ($type == 'week') {
            //本周开始时间
            $start = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") - date("w") + 1, date("Y")));
            //本周结束时间
            $end = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d") - date("w") + 7, date("Y")));
        } elseif ($type == 'month') {
            //本月开始时间
            $start = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), 1, date('Y')));
            //本月结束时间
            $end = date("Y-m-d H:i:s", mktime(23, 59, 59, date('m'), date('t'), date('Y')));
        }
        return ['start' => $start, 'end' => $end];
    }
}
