<?php

namespace frontend\models;

use Yii;
use yii\data\Pagination;

/**
 * This is the model class for table "clue_info".
 *
 * @property int $clue_id
 * @property string $agent_id
 * @property string $clue_name 线索名称
 * @property string $contacts 联系人
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
 * @property int $add_mode 添加方式 1手动录入 2批量导入
 * @property string|null $username 负责人
 * @property int|null $get_mode 获取方式 1默认 2上级指派 3自己提取
 * @property string|null $get_time 获取时间
 * @property string|null $assign_user 指派人
 * @property int|null $change_state 是否转化/释放 -1已释放/回收 1默认 2已转化为客户
 * @property string|null $change_time 转化时间
 * @property string|null $change_user 转化/释放人
 * @property string|null $team_id 部门id
 * @property string|null $trace_time 最后跟进时间
 * @property int|null $clue_mark 线索跟踪标记 1空号 2挂断 3未接听
 * @property string $mark_time 添加时间
 */
class ClueInfo extends \yii\db\ActiveRecord
{
    // 未开通
    const STATUS_DEFAULT = 1;
    //已转化为客户
    const STATUS_TURN = 2;
    //标记为无法联系
    const STATUS_NO_CONTACT = 3;
    //标记为无法沟通
    const STATUS_NO_TALK = 4;
    // 已释放/回收
    const STATUS_OPEN = -1;


    // 1手动录入
    const ADD_MANUAL = 1;
    // 2批量导入
    const ADD_BATCH = 2;

    // 1默认
    const GET_DEFAULT = 1;
    // 2上级指派
    const GET_ASSIGN = 2;
    // 3自己提取
    const GET_PICK = 3;

    // 1空号
    const MARK_EMPTY = 1;
    // 2挂断
    const MARK_BREAK = 2;
    // 3未接听
    const MARK_MISSED = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clue_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['agent_id', 'clue_name', 'contacts', 'add_user', 'add_mode'], 'required'],
            [['agent_id'], 'string'],
            [['add_time', 'get_time', 'change_time'], 'safe'],
            [['add_mode', 'get_mode', 'change_state'], 'integer'],
            [['clue_name', 'address'], 'string', 'max' => 255],
            [['contacts', 'tel', 'add_user', 'username', 'assign_user', 'change_user'], 'string', 'max' => 255],
            [['email', 'wx', 'trade', 'source', 'area'], 'string', 'max' => 50],
            [['qq'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'clue_id' => 'Clue ID',
            'agent_id' => 'Agent ID',
            'clue_name' => 'Clue Name',
            'contacts' => 'Contacts',
            'tel' => 'Tel',
            'email' => 'Email',
            'qq' => 'Qq',
            'wx' => 'Wx',
            'establish_date' => 'Establish Date',
            'capital' => 'Capital',
            'trade' => 'Trade',
            'source' => 'Source',
            'province' => 'Province',
            'area' => 'Area',
            'address' => 'Address',
            'describe' => 'Describe',
            'add_time' => 'Add Time',
            'add_user' => 'Add User',
            'add_mode' => 'Add Mode',
            'username' => 'Username',
            'get_mode' => 'Get Mode',
            'get_time' => 'Get Time',
            'assign_user' => 'Assign User',
            'change_state' => 'Change State',
            'change_time' => 'Change Time',
            'change_user' => 'Change User',
            'team_id' => 'Team ID',
            'trace_time' => 'Trace Time',
            'clue_mark' => 'Clue Mark',
            'mark_time' => 'Mark Time',
        ];
    }

    /**
     * 添加线索
     * 必传：'agent_id', 'clue_name', 'contacts', 'add_user', 'add_mode'
     */
    static public function addclue($data)
    {
        $agent_id = $data['agent_id'];
        $clue_name = trim($data['clue_name']);
        $tel = $data['tel'];
        $contacts = $data['contacts'];
        if (empty($agent_id)) {
            return [
                'error' => 1,
                'error_msg' => '代理号不能为空',
            ];
        }
        if (empty($clue_name)) {
            return [
                'error' => 1,
                'error_msg' => '线索名称不能为空',
            ];
        }

        // 判断重复
        $ishave = ClueInfo::find()->where([
            'agent_id' => $agent_id,
            'contacts' => $contacts,
            'tel' => $tel
        ])
            ->one();

        if (!empty($ishave)) {
            return [
                'error' => 1,
                'error_msg' => '该客户已存在，请勿重复添加',
            ];
        }

        $model = new ClueInfo();
        $model->agent_id = $agent_id;
        $model->clue_name = $clue_name;
        $model->contacts = isset($data['contacts']) ? trim($data['contacts']) : '';
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
        $model->username = isset($data['username']) ? trim($data['username']) : null;
        $model->team_id = isset($data['team_id']) ? trim($data['team_id']) : null;
        $model->add_time = isset($data['add_time']) && !empty($data['add_time']) ? $data['add_time'] : date('Y-m-d H:i:s');


        //手动添加的直接获取为自己线索
        if ($data['add_mode'] == ClueInfo::ADD_MANUAL) {
            $model->get_mode = ClueInfo::GET_PICK;
            $model->get_time = date('Y-m-d H:i:s');
        }


        if ($model->save() > 0) {
            return [
                'error' => 0,
                'error_msg' => '添加成功',
                'data' => $model->attributes['clue_id'],
            ];;
        } else {
            return [
                'error' => 2,
                'error_msg' => json_encode($model->errors),
            ];
        }
    }

    /**
     * 某时间内联系记录线索数
     *
     */
    static public function getCallClueList($username, $time = '1', $current_page = 1, $page_size = 10)
    {
        $time_data = ClueLog::getTime($time);
        $time_start = $time_data['day_start'];
        $time_end = $time_data['day_end'];

        $query = ClueInfo::find()->alias('o')
            ->select('o.clue_id')
            ->distinct()
            ->select('o.*')
            ->leftJoin('clue_trace p', '`o`.`clue_id` = `p`.`clue_id`');
        $query->andWhere(['=', 'o.username', $username]);
        $query->andWhere(['not', ['o.clue_id' => null]]);
        $query->andWhere(['between', 'p.trace_time', $time_start, $time_end]);
        $count = $query->count();
        $pagination = new Pagination([
            'totalCount' => $count,
            'defaultPageSize' => $page_size,
            'pageSizeLimit' => false
        ]);
        $pagination->setPage((int)$current_page - 1);
        $list = $query->offset($pagination->offset)->limit($pagination->limit)->orderBy('p.trace_time DESC')->asArray()->all();
        $data = [
            'list' => $list,
            'count' => $count,
        ];
        return $data;
    }

    static public function getClueMarkList($username, $time = '1', $mark, $current_page = 1, $page_size = 10)
    {
        $time_data = ClueLog::getTime($time);
        $time_start = $time_data['day_start'];
        $time_end = $time_data['day_end'];
        $query = self::find();
        $query->andWhere(['=', 'username', $username]);
        $query->andWhere(['=', 'clue_mark', $mark]);
        $query->andWhere(['between', 'mark_time', $time_start, $time_end]);
        $count = $query->count();
        $pagination = new Pagination([
            'totalCount' => $count,
            'defaultPageSize' => $page_size,
            'pageSizeLimit' => false
        ]);
        $pagination->setPage((int)$current_page - 1);
        $list = $query->offset($pagination->offset)->limit($pagination->limit)->orderBy('mark_time DESC')->asArray()->all();
        $data = [
            'list' => $list,
            'count' => $count,
        ];
        return $data;
    }

    static public function getFields()
    {
        return [
            'add_mode',
            'add_user',
            'agent_id',
            'team_id',
            'clue_name',
            'contacts',
            'tel',
            'email',
            'qq',
            'establish_date',
            'capital',
            'trade',
            'source',
            'province',
            'area',
            'address',
            'describe',
            'add_time',
            'username',
        ];
    }
}
