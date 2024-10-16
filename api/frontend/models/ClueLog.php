<?php

namespace frontend\models;

use Yii;
use yii\data\Pagination;

/**
 * This is the model class for table "clue_log".
 *
 * @property int $log_id
 * @property int|null $clue_id 线索ID
 * @property string|null $opt_time 操作时间
 * @property int|null $opt_mode 动作 1默认 2上级指派 3自己提取 3释放 4转化
 * @property string|null $opt_user 操作人
 * @property string|null $to_user 操作给谁(指派的时候有此项)
 */
class ClueLog extends \yii\db\ActiveRecord
{
    // 1默认
    const OPT_DEFAULT = 1;
    // 2上级指派
    const OPT_ASSIGN = 2;
    // 3自己提取
    const OPT_PICK = 3;
    // 4释放
    const OPT_OPEN = 4;
    // 5转化
    const OPT_TURN = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clue_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['clue_id', 'opt_mode'], 'integer'],
            [['opt_time'], 'safe'],
            [['opt_user', 'to_user'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'log_id' => 'Log ID',
            'clue_id' => 'Clue ID',
            'opt_time' => 'Opt Time',
            'opt_mode' => 'Opt Mode',
            'opt_user' => 'Opt User',
            'to_user' => 'To User',
        ];
    }

    /**
     * 添加操作日志
     */
    static public function addlog($data)
    {
        $model = new ClueLog();
        $model->clue_id = $data['clue_id'];
        $model->opt_mode = $data['opt_mode'];
        $model->opt_user = $data['opt_user'];

        if ($model->opt_mode == ClueLog::OPT_ASSIGN) {
            $model->to_user = $data['to_user'];
        }
        if ($model->save() > 0) {
            return [
                'error' => 0,
                'error_msg' => '添加成功',
                'data' => '',
            ];
        } else {
            return [
                'error' => 2,
                'error_msg' => json_encode($model->errors),
            ];
        }
    }

    /**
     * 根据时间和所有者获取时间段内提取，释放或被指派的数量
     */
    static public function getClueLog($username, $time = '1', $opt, $current_page = 1, $page_size = 10)
    {
        $time_data = self::getTime($time);
        $time_start = $time_data['day_start'];
        $time_end = $time_data['day_end'];
        $query = self::find()->alias('o')
            ->select('p.*')
            ->leftJoin('clue_info p', '`o`.`clue_id` = `p`.`clue_id`');
        $query->andWhere(['not', ['p.clue_id' => null]]);
        if ($opt == self::OPT_ASSIGN) {
            $query->andWhere(['=', 'o.to_user', $username]);
        } else {
            $query->andWhere(['=', 'o.opt_user', $username]);
        }
        $query->andWhere(['=', 'o.opt_mode', $opt]);
        $query->andWhere(['between', 'o.opt_time', $time_start, $time_end]);
        $count = $query->count();
        $pagination = new Pagination([
            'totalCount' => $count,
            'defaultPageSize' => $page_size,
            'pageSizeLimit' => false
        ]);
        $pagination->setPage((int)$current_page - 1);
        $list = $query->offset($pagination->offset)->limit($pagination->limit)->orderBy('o.opt_time DESC')->asArray()->all();
        return $data = [
            'list' => $list,
            'count' => $count,
        ];
    }

    static public function getTime($time)
    {
        //昨日
        if ($time == '2') {
            $day = date('Y-m-d', strtotime('yesterday'));
            $day_start = $day . " 00:00:01";
            $day_end = $day . " 23:59:59";
        } elseif ($time == '3') { //本周
            $now = date('Y-m-d H:i:s', time());
            $w = date('w', strtotime($now));
            //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
            $day_start = date('Y-m-d', strtotime("$now -" . ($w ? $w - 1 : 6) . ' days')) . " 00:00:01";
            //本周结束日期
            $day_end = date('Y-m-d', strtotime("$day_start +6 days")) . " 23:59:59";
        } elseif ($time == '4') { //本月
            $timestamp = mktime(0, 0, 0, date('m'), 1, date('Y'));
            $day_start = date('Y-m-d H:i:s', $timestamp);
            $timeend = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
            $day_end = date('Y-m-d H:i:s', $timeend);
        } elseif ($time == '5') {
            //上月
            $day_start = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));
            $day_end = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), 0, date("Y")));
        } elseif ($time == '1year') {
            //近一年
            $day_start = date("Y-m-d", strtotime("-1 year"));
            $day_end = date('Y-m-d H:i:s', time());
        } elseif ($time == '3year') {
            //近一年到3年
            $day_start = date("Y-m-d", strtotime("-3 year"));
            $day_end = date("Y-m-d", strtotime("-1 year"));
        } elseif ($time == '5year') {
            //3-5年
            $day_start = date("Y-m-d", strtotime("-5 year"));
            $day_end = date("Y-m-d", strtotime("-3 year"));
        } elseif ($time == '10year') {
            //5-10年
            $day_start = date("Y-m-d", strtotime("-10 year"));
            $day_end = date("Y-m-d", strtotime("-5 year"));
        } elseif ($time == '10years') {
            //10年以上
            $day_start = '';
            $day_end = date("Y-m-d", strtotime("-10 year"));
        } elseif ($time == '1week') {
            //近一周
            $day_start = date("Y-m-d", strtotime("-1 week"));
            $day_end = date('Y-m-d H:i:s', time());
        } elseif ($time == '1month') {
            //近一月
            $day_start = date("Y-m-d", strtotime("-1 month"));
            $day_end = date('Y-m-d H:i:s', time());
        } elseif ($time == '3month') {
            //近3月
            $day_start = date("Y-m-d", strtotime("-3 month"));
            $day_end = date('Y-m-d H:i:s', time());
        } elseif ($time == '3months') {
            //3月之前
            $day_start = '';
            $day_end = date("Y-m-d", strtotime("-3 month"));
        } else { //今日
            $day = date('Y-m-d', time());
            $day_start = $day . " 00:00:01";
            $day_end = $day . " 23:59:59";
        }
        $time_data = [
            'day_start' => $day_start,
            'day_end' => $day_end
        ];
        return $time_data;
    }
}
