<?php

namespace frontend\models;

use Yii;
use yii\data\Pagination;

/**
 * This is the model class for table "client_log".
 *
 * @property int $log_id
 * @property int|null $client_id 客户ID
 * @property string|null $opt_time 操作时间
 * @property int|null $opt_mode 动作 1默认 2上级指派 3自己提取 4释放
 * @property string|null $opt_user 操作人
 * @property string|null $to_user 操作给谁(指派的时候有此项)
 */
class ClientLog extends \yii\db\ActiveRecord
{
    // 1默认
    const OPT_DEFAULT = 1;
    // 2上级指派
    const OPT_ASSIGN = 2;
    // 3自己提取
    const OPT_PICK = 3;
    // 4自己释放
    const OPT_OPEN = 4;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id', 'opt_mode'], 'integer'],
            [['opt_time'], 'safe'],
            [['opt_user', 'to_user'], 'string', 'max' => 20],
            [['log_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'log_id' => 'Log ID',
            'client_id' => 'Client ID',
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
        $model = new ClientLog();
        $model->client_id = $data['client_id'];
        $model->opt_mode = $data['opt_mode'];
        $model->opt_user = $data['opt_user'];

        if ($model->opt_mode == ClientLog::OPT_ASSIGN) {
            $model->to_user = $data['to_user'];
        }
        if ($model->save() > 0) {
            return [
                'error'     => 0,
                'error_msg' => '添加成功',
                'data'      => '',
            ];
        } else {
            return [
                'error'     => 2,
                'error_msg' => json_encode($model->errors),
            ];
        }
    }

    /**
     * 根据时间和所有者获取时间段内提取，释放或被指派的数量
     */
    static public function getClientLog($username, $time = '1', $opt, $current_page = 1, $page_size = 10)
    {
        $time_data = ClueLog::getTime($time);
        $time_start = $time_data['day_start'];
        $time_end = $time_data['day_end'];

        $query = ClientLog::find()
            ->select('o.*,p.opt_time')
            ->alias('p')->leftJoin('client_info o', '`o`.`client_id` = `p`.`client_id`');

        if ($opt == self::OPT_ASSIGN) {
            //被指派
            $query->andWhere(['=', 'p.to_user', $username]);
        } else {
            $query->andWhere(['=', 'p.opt_user', $username]);
        }
        $query->andWhere(['=', 'p.opt_mode', $opt]);
        $query->andWhere(['between', 'p.opt_time', $time_start, $time_end]);
        $count = $query->count();

        $pagination = new Pagination([
            'totalCount' => $count,
            'defaultPageSize' => $page_size,
            'pageSizeLimit' => false
        ]);
        $pagination->setPage((int)$current_page - 1);
        $list = $query->offset($pagination->offset)->limit($pagination->limit)->orderBy('p.opt_time DESC')->asArray()->all();

        return $data = [
            'list' => $list,
            'count' => $count,
        ];
    }
}
