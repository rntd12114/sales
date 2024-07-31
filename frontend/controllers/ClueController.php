<?php

/**
 * Created by PhpStorm.
 * User: Apple
 * Date: 2020-09-22
 * Time: 10:37
 */

namespace frontend\controllers;

use frontend\models\User;
use Yii;
use frontend\models\ClueInfo;
use frontend\models\ClueLog;
use frontend\models\ClueTrace;
use frontend\models\ClientInfo;
use frontend\models\Team;
use frontend\models\Agent;
use frontend\models\Region;
use yii\data\Pagination;
use moonland\phpexcel\Excel;
use common\framework\web\Controller;
use yii\web\UploadedFile;
use frontend\models\UploadForm;

class ClueController extends Controller
{

    public function actionIndex()
    {
        return $this->success();
    }

    /**
     * 线索添加及修改
     */
    public function actionSaveClue()
    {

        $username = Yii::$app->user->getIdentity()->username;
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $team_id = Yii::$app->user->getIdentity()->team_id;
        $request = Yii::$app->request->post();
        $clue_name = !empty($request['clue_name']) ? $request['clue_name'] : null;
        $contacts = !empty($request['contacts']) ? $request['contacts'] : null;
        $tel = !empty($request['tel']) ? $request['tel'] : null;
        $email = !empty($request['email']) ? $request['email'] : null;
        $qq = !empty($request['qq']) ? $request['qq'] : null;
        $wx = !empty($request['wx']) ? $request['wx'] : null;
        $source = !empty($request['source']) ? $request['source'] : null;
        $province = isset($request['province']) ? $request['province'] : '';
        $area = !empty($request['area']) ? $request['area'] : null;
        $address = !empty($request['address']) ? $request['address'] : null;
        $describe = !empty($request['describe']) ? $request['describe'] : null;
        $establish_date = isset($request['establish_date']) ? $request['establish_date'] : '';
        $capital = isset($request['capital']) ? $request['capital'] : '';
        $trade = isset($request['trade']) ? $request['trade'] : '';
        if (!$clue_name) {
            return $this->error(-1, '线索名称不能为空');
        }
        if (!$contacts) {
            return $this->error(-1, '联系人不能为空');
        }
        if (!$tel && !$email && !$qq && !$wx) {
            return $this->error(-1, '基本联系方式至少需要一个');
        };
        $add_time = date('Y-m-d H:i:s', time());
        $add_user = $username;
        $clue_id = !empty($request['clue_id']) ? $request['clue_id'] : null;
        $clue_info = ClueInfo::findOne(['tel' => $tel, 'agent_id' => $agent_id]);
        if ($clue_id) {
            $clue = ClueInfo::findOne(['clue_id' => $clue_id]);
            if (!$clue) {
                return $this->error(-1, '该线索不存在');
            }
            //            if ($clue->username != $username) {
            //                return $this->error(-1, '对不起，您没有编辑权限');
            //            }
        } else {
            if ($clue_info) {
                return $this->error(-1, '该线索已存在，请勿重复添加');
            }
            $clue = new ClueInfo();
            $clue->add_time = $add_time;
            $clue->add_user = $username;
            $clue->add_mode = ClueInfo::ADD_MANUAL;
            $clue->get_mode = ClueLog::OPT_PICK;
            $clue->username = $username;
        }
        $clue->agent_id = $agent_id;
        $clue->clue_name = $clue_name;
        $clue->contacts = $contacts;
        $clue->tel = $tel;
        $clue->email = $email;
        $clue->qq = $qq;
        $clue->wx = $wx;
        $clue->establish_date = $establish_date;
        $clue->capital = $capital;
        $clue->source = $source;
        $clue->province = $province;
        $clue->area = $area;
        $clue->address = $address;
        $clue->describe = $describe;
        $clue->team_id = $team_id;
        $clue->trade = $trade;
        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            $result = $clue->save();
            if (!$result) {
                throw new \Exception('线索添加失败');
            }
            if (!$clue_id) {
                $addlog = ClueLog::addlog([
                    'clue_id' => $clue->clue_id,
                    'opt_mode' => ClueLog::OPT_PICK,
                    'opt_user' => $username
                ]);
                if ($addlog['error'] != '0') {
                    throw new \Exception('线索Log添加失败');
                }
            }
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
        }
        return $this->success([]);
    }

    /**
     * 公共线索列表
     */
    public function actionPublicClueList()
    {
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $role = Yii::$app->user->getIdentity()->role;
        $request = Yii::$app->request->post();
        $clue_id = (isset($request['clue_id'])) ? $request['clue_id'] : null;
        $clue_name = (isset($request['clue_name'])) ? $request['clue_name'] : null;
        $province = (!empty($request['province'])) ? $request['province'] : null;
        $city = (!empty($request['city'])) ? $request['city'] : null;
        $clue_mark = (!empty($request['clue_mark'])) ? $request['clue_mark'] : null;
        $change_state = (!empty($request['change_state'])) ? $request['change_state'] : null;
        $current_page = (!empty($request['current_page'])) ? $request['current_page'] : 1;
        $page_size = (!empty($request['page_size'])) ? $request['page_size'] : 10;
        $establish_date_type = !empty($request['establish_date_type']) ? $request['establish_date_type'] : '';
        $trace_time_type = !empty($request['trace_time_type']) ? $request['trace_time_type'] : '';
        $name = (!empty($request['username'])) ? $request['username'] : null;
        if ($name) {
            return $this->error(1, '公共线索池不支持根据负责人进行搜索');
        }
        $query = ClueInfo::find()->where(['IS', 'username', new \yii\db\Expression('NULL')]);
        if ($clue_name) {
            $query->andWhere([
                'or',
                ['like', 'clue_name', $clue_name],
                ['like', 'tel', $clue_name],
                ['like', 'contacts', $clue_name]
            ]);
        }
        if ($clue_id) {
            $query->andWhere([
                '=',
                'clue_id',
                $clue_id,
            ]);
        }
        if ($province) {
            $query->andWhere([
                '=',
                'province',
                $province,
            ]);
        }
        if ($city) {
            $query->andWhere([
                '=',
                'area',
                $city,
            ]);
        }
        if ($establish_date_type) {
            $establish_time = ClueLog::getTime($establish_date_type);
            $query->andWhere([
                'between',
                'establish_date',
                $establish_time['day_start'],
                $establish_time['day_end'],
            ]);
        }
        if ($trace_time_type) {
            $trace_time = ClueLog::getTime($trace_time_type);
            $query->andWhere([
                'between',
                'trace_time',
                $trace_time['day_start'],
                $trace_time['day_end'],
            ]);
        }
        if ($clue_mark) {
            $query->andWhere([
                '=',
                'clue_mark',
                $clue_mark,
            ]);
        }
        if ($change_state) {
            $query->andWhere([
                '=',
                'change_state',
                $change_state,
            ]);
        }

        $query->andWhere(['IS', 'team_id', new \yii\db\Expression('NULL')]);
        $query->andWhere(['=', 'agent_id', $agent_id]);
        $count = $query->count();
        $pagination = new Pagination([
            'totalCount' => $count,
            'defaultPageSize' => $page_size,
            'pageSizeLimit' => false
        ]);
        $pagination->setPage((int)$current_page - 1);
        $list = $query->offset($pagination->offset)->limit($pagination->limit)->orderBy('clue_id DESC')->asArray()->all();
        $list = $this->dealList($list);
        //公共线索池只有总监可以看到手机号码
        foreach ($list as $k => $v) {
            if ($role < 3) {
                $list[$k]['tel'] = '***********';
            }
        }
        return $this->success([
            'list' => $list,
            'count' => $count,
        ]);
    }

    /**
     * 我的线索列表
     */
    public function actionMyClueList()
    {
        $username = Yii::$app->user->getIdentity()->username;
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $team_id = Yii::$app->user->getIdentity()->team_id;
        $request = Yii::$app->request->post();
        $role = Yii::$app->user->getIdentity()->role;
        $clue_name = (isset($request['clue_name'])) ? $request['clue_name'] : null;
        $clue_id = (isset($request['clue_id'])) ? $request['clue_id'] : null;
        $name = (!empty($request['username'])) ? $request['username'] : null;
        $province = (!empty($request['province'])) ? $request['province'] : null;
        $city = (!empty($request['city'])) ? $request['city'] : null;
        $clue_mark = (!empty($request['clue_mark'])) ? $request['clue_mark'] : null;
        $change_state = (!empty($request['change_state'])) ? $request['change_state'] : null;
        $current_page = (!empty($request['current_page'])) ? $request['current_page'] : 1;
        $page_size = (!empty($request['page_size'])) ? $request['page_size'] : 10;
        $establish_date_type = !empty($request['establish_date_type']) ? $request['establish_date_type'] : '';
        $trace_time_type = !empty($request['trace_time_type']) ? $request['trace_time_type'] : '';
        $list_type = !empty($request['list_type']) ? $request['list_type'] : '';

        if ($name && ($username != $name)) {
            return $this->error(1, '我的线索中无法搜索其他负责人的线索');
        }
        $where['username'] = $username;
        $query = ClueInfo::find()->where($where);
        if ($list_type == '2') {//已标记
            $query->andWhere([
                'in',
                'clue_mark',
                ['1', '2', '3'],
            ]);
        } elseif ($list_type == '3') {//已转化
            $query->andWhere(['=', 'change_state', '2']);
        } elseif ($list_type == '1') {
            $query->andWhere([//待联系
                'and',
                ['IS', 'clue_mark', new \yii\db\Expression('NULL')],
                ['!=', 'change_state', '2'],
            ]);
        }
        if ($clue_name) {
            $query->andWhere([
                'or',
                ['like', 'clue_name', $clue_name],
                ['like', 'tel', $clue_name],
                ['like', 'contacts', $clue_name]
            ]);
        }
        if ($clue_id) {
            $query->andWhere([
                '=',
                'clue_id',
                $clue_id,
            ]);
        }
        if ($province) {
            $query->andWhere([
                '=',
                'province',
                $province,
            ]);
        }
        if ($city) {
            $query->andWhere([
                '=',
                'area',
                $city,
            ]);
        }
        if ($establish_date_type) {
            $establish_time = ClueLog::getTime($establish_date_type);
            $query->andWhere([
                'between',
                'establish_date',
                $establish_time['day_start'],
                $establish_time['day_end'],
            ]);
        }
        if ($trace_time_type) {
            $trace_time = ClueLog::getTime($trace_time_type);
            $query->andWhere([
                'between',
                'trace_time',
                $trace_time['day_start'],
                $trace_time['day_end'],
            ]);
        }
        if ($clue_mark) {
            $query->andWhere([
                '=',
                'clue_mark',
                $clue_mark,
            ]);
        }
        if ($change_state) {
            $query->andWhere([
                '=',
                'change_state',
                $change_state,
            ]);
        }
        $count = $query->count();
        $pagination = new Pagination([
            'totalCount' => $count,
            'defaultPageSize' => $page_size,
            'pageSizeLimit' => false
        ]);
        $pagination->setPage((int)$current_page - 1);
        if ($list_type == '1') {
            $list = $query->offset($pagination->offset)->limit($pagination->limit)->orderBy('get_time desc,clue_id DESC')->asArray()->all();
        } else {
            $list = $query->offset($pagination->offset)->limit($pagination->limit)->orderBy('clue_id DESC')->asArray()->all();
        }
        $list = $this->dealList($list);
        return $this->success([
            'list' => $list,
            'count' => $count,
        ]);
    }

    /**
     * 部门线索列表
     */
    public function actionTeamClueList()
    {
        $username = Yii::$app->user->getIdentity()->username;
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $team_id = Yii::$app->user->getIdentity()->team_id;
        $request = Yii::$app->request->post();
        $role = Yii::$app->user->getIdentity()->role;
        $clue_id = (!empty($request['clue_id'])) ? $request['clue_id'] : null;
        $clue_name = (!empty($request['clue_name'])) ? $request['clue_name'] : null;
        $name = (!empty($request['username'])) ? $request['username'] : null;
        $province = (!empty($request['province'])) ? $request['province'] : null;
        $city = (!empty($request['city'])) ? $request['city'] : null;
        $clue_mark = (!empty($request['clue_mark'])) ? $request['clue_mark'] : null;
        $change_state = (!empty($request['change_state'])) ? $request['change_state'] : null;
        $current_page = (!empty($request['current_page'])) ? $request['current_page'] : 1;
        $page_size = (!empty($request['page_size'])) ? $request['page_size'] : 10;
        $establish_date_type = !empty($request['establish_date_type']) ? $request['establish_date_type'] : '';
        $trace_time_type = !empty($request['trace_time_type']) ? $request['trace_time_type'] : '';
        $list_type = !empty($request['list_type']) ? $request['list_type'] : '';

        $query = ClueInfo::find();
        if ($role == 1) {
            $query->andWhere(['=', 'team_id', $team_id]);
            $query->andWhere(['IS', 'username', new \yii\db\Expression('NULL')]);
        } elseif ($role == 2) {
            $query->andWhere(['=', 'team_id', $team_id]);
        } else {
            $query->andWhere(['not', ['team_id' => null]]);
            $query->andWhere(['=', 'agent_id', $agent_id]);
        }

        if ($list_type == '2') {//已标记
            $query->andWhere([
                'in',
                'clue_mark',
                ['1', '2', '3'],
            ]);
        } elseif ($list_type == '3') {//已转化
            $query->andWhere(['=', 'change_state', '2']);
        } elseif ($list_type == '1') {
            $query->andWhere([//待联系
                'and',
                ['IS', 'clue_mark', new \yii\db\Expression('NULL')],
                ['!=', 'change_state', '2'],
            ]);
        }
        if ($clue_name) {
            $query->andWhere([
                'or',
                ['like', 'clue_name', $clue_name],
                ['like', 'tel', $clue_name],
                ['like', 'contacts', $clue_name]
            ]);
        }
        if ($clue_id) {
            $query->andWhere([
                '=',
                'clue_id',
                $clue_id,
            ]);
        }
        if ($name) {
            $query->andWhere([
                'like',
                'username',
                $name,
            ]);
        }
        if ($province) {
            $query->andWhere([
                '=',
                'province',
                $province,
            ]);
        }
        if ($city) {
            $query->andWhere([
                '=',
                'area',
                $city,
            ]);
        }
        if ($establish_date_type) {
            $establish_time = ClueLog::getTime($establish_date_type);
            $query->andWhere([
                'between',
                'establish_date',
                $establish_time['day_start'],
                $establish_time['day_end'],
            ]);
        }
        if ($trace_time_type) {
            $trace_time = ClueLog::getTime($trace_time_type);
            $query->andWhere([
                'between',
                'trace_time',
                $trace_time['day_start'],
                $trace_time['day_end'],
            ]);
        }
        if ($clue_mark) {
            $query->andWhere([
                '=',
                'clue_mark',
                $clue_mark,
            ]);
        }
        if ($change_state) {
            $query->andWhere([
                '=',
                'change_state',
                $change_state,
            ]);
        }
        $count = $query->count();
        $pagination = new Pagination([
            'totalCount' => $count,
            'defaultPageSize' => $page_size,
            'pageSizeLimit' => false
        ]);
        $pagination->setPage((int)$current_page - 1);
        $list = $query->offset($pagination->offset)->limit($pagination->limit)->orderBy('clue_id DESC')->asArray()->all();
        $list = $this->dealList($list);
        //部门线索池经理和总监可以看到手机号码
        foreach ($list as $k => $v) {
            if ($role < 2 && $v['username'] != $username) {
                $list[$k]['tel'] = '***********';
            }
            $team = Team::findOne(['team_id' => $v['team_id']]);
            $list[$k]['team_str'] = $team->team_name;
        }
        return $this->success([
            'list' => $list,
            'count' => $count,
        ]);
    }

    /**
     * 获取特定时间内线索列表
     */
    public function actionTimeClueList()
    {
        $username = Yii::$app->user->getIdentity()->username;
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $team_id = Yii::$app->user->getIdentity()->team_id;
        $request = Yii::$app->request->post();
        $role = Yii::$app->user->getIdentity()->role;
        $clue_name = (isset($request['clue_name'])) ? $request['clue_name'] : null;
        $name = (isset($request['username'])) ? $request['username'] : null;
        $time = (isset($request['time'])) ? $request['time'] : '1';
        $opt = (isset($request['opt'])) ? $request['opt'] : 1;
        $current_page = (isset($request['current_page'])) ? $request['current_page'] : 1;
        $page_size = (isset($request['page_size'])) ? $request['page_size'] : 10;
        if ($name) {
            $username = $name;
        }

        //权限验证
        $canSee = user::userRights($username);
        if (!$canSee) {
            return $this->error(1, '无权查看该用户信息');
        }

        $data = ClueLog::getClueLog($username, $time, $opt, $current_page, $page_size);
        $list = $this->dealList($data['list']);
        $count = $data['count'];
        return $this->success([
            'list' => $list,
            'count' => $count,
        ]);
    }

    /**
     * 获取特定时间内联系线索列表
     */
    public function actionCallClueList()
    {
        $username = Yii::$app->user->getIdentity()->username;
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $team_id = Yii::$app->user->getIdentity()->team_id;
        $request = Yii::$app->request->post();
        $role = Yii::$app->user->getIdentity()->role;
        $clue_name = (isset($request['clue_name'])) ? $request['clue_name'] : null;
        $name = (isset($request['username'])) ? $request['username'] : null;
        $time = (isset($request['time'])) ? $request['time'] : '1';
        $current_page = (isset($request['current_page'])) ? $request['current_page'] : 1;
        $page_size = (isset($request['page_size'])) ? $request['page_size'] : 10;
        if ($name) {
            $username = $name;
        }

        //权限验证
        $canSee = user::userRights($username);
        if (!$canSee) {
            return $this->error(1, '无权查看该用户信息');
        }

        $data = ClueInfo::getCallClueList($username, $time, $current_page, $page_size);
        $list = $this->dealList($data['list']);
        $count = $data['count'];
        return $this->success([
            'list' => $list,
            'count' => $count,
        ]);
    }

    /**
     * 一定时间内标记线索数
     *
     */
    public function actionMarkClueList()
    {
        $username = Yii::$app->user->getIdentity()->username;
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $request = Yii::$app->request->post();
        $clue_name = (isset($request['clue_name'])) ? $request['clue_name'] : null;
        $name = (isset($request['username'])) ? $request['username'] : null;
        $time = (isset($request['time'])) ? $request['time'] : '1';
        $mark = (isset($request['mark'])) ? $request['mark'] : null;
        $current_page = (isset($request['current_page'])) ? $request['current_page'] : 1;
        $page_size = (isset($request['page_size'])) ? $request['page_size'] : 10;
        if ($name) {
            $username = $name;
        }

        //权限验证
        $canSee = user::userRights($username);
        if (!$canSee) {
            return $this->error(1, '无权查看该用户信息');
        }

        if (!$mark) {
            return $this->error(-1, '请选择标记状态');
        }
        $data = ClueInfo::getClueMarkList($username, $time, $mark, $current_page, $page_size);
        $list = $this->dealList($data['list']);
        $count = $data['count'];
        return $this->success([
            'list' => $list,
            'count' => $count,
        ]);
    }

    public function dealList($list)
    {
        foreach ($list as $k => &$v) {
            //添加方式
            if ($v['add_mode'] == ClueInfo::ADD_MANUAL) {
                $v['add_mode_str'] = '手动录入';
            } elseif ($v['add_mode'] == ClueInfo::ADD_BATCH) {
                $v['add_mode_str'] = '批量导入';
            }
            //获取方式
            if ($v['get_mode'] == ClueInfo::GET_DEFAULT) {
                $v['get_mode_str'] = '';
            } elseif ($v['get_mode'] == ClueInfo::GET_PICK) {
                $v['get_mode_str'] = '自己提取';
            } elseif ($v['get_mode'] == ClueInfo::GET_ASSIGN) {
                $v['get_mode_str'] = '上级指派';
            }
            //线索状态
            switch ($v['change_state']) {
                case ClueInfo::STATUS_DEFAULT:
                    $v['change_state_str'] = '';
                    break;
                case ClueInfo::STATUS_TURN:
                    $v['change_state_str'] = '已转化';
                    break;
                case ClueInfo::STATUS_NO_CONTACT:
                    $v['change_state_str'] = '被释放(无法联系)';
                    break;
                case ClueInfo::STATUS_NO_TALK:
                    $v['change_state_str'] = '被释放(无法沟通)';
                    break;
                case ClueInfo::STATUS_OPEN:
                    $v['change_state_str'] = '被释放';
                    break;
                default:
                    $v['change_state_str'] = '';
            }
            //线索标记
            switch ($v['clue_mark']) {
                case ClueInfo::MARK_EMPTY:
                    $v['clue_mark_str'] = '空号';
                    break;
                case ClueInfo::MARK_BREAK:
                    $v['clue_mark_str'] = '挂断';
                    break;
                case ClueInfo::MARK_MISSED:
                    $v['clue_mark_str'] = '未接听';
                    break;
                default:
                    $v['clue_mark_str'] = '';
            }
        }
        return $list;
    }

    /**
     * 查看线索详情
     */
    public function actionClueDetail()
    {
        $request = Yii::$app->request->post();
        $clue_id = (isset($request['clue_id'])) ? $request['clue_id'] : "";
        if (!$clue_id) {
            return $this->error(-1, '参数错误');
        }
        //获取线索信息
        $clue_info = ClueInfo::find()->where(['clue_id' => $clue_id])->asArray()->one();
        //添加方式
        if ($clue_info['add_mode'] == ClueInfo::ADD_MANUAL) {
            $clue_info['add_mode_str'] = '手动录入';
        } elseif ($clue_info['add_mode'] == ClueInfo::ADD_BATCH) {
            $clue_info['add_mode_str'] = '批量导入';
        }
        //获取方式
        if ($clue_info['get_mode'] == ClueInfo::GET_DEFAULT) {
            $clue_info['get_mode_str'] = '';
        } elseif ($clue_info['get_mode'] == ClueInfo::GET_PICK) {
            $clue_info['get_mode_str'] = '自己提取';
        } elseif ($clue_info['get_mode'] == ClueInfo::GET_ASSIGN) {
            $clue_info['get_mode_str'] = '上级指派';
        }
        return $this->success($clue_info);
    }

    /**
     * 线索指派
     */
    public function actionClueAssign()
    {
        $username = Yii::$app->user->getIdentity()->username;
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $team_id = Yii::$app->user->getIdentity()->team_id;
        $request = Yii::$app->request->post();
        $clue_id = (isset($request['clue_id'])) ? $request['clue_id'] : "";
        $to_user = (isset($request['to_user'])) ? $request['to_user'] : "";

        if (empty($to_user)) {
            return $this->error(1, '负责人不能为空');
        }
        $to_userinfo = User::findOne(['agent_id' => $agent_id, 'username' => $to_user]);
        if (!$to_userinfo) {
            return $this->error(1, '指派负责人不存在');
        }
        foreach ($clue_id as $k => $v) {
            $query = $clueinfo = ClueInfo::find()->where([
                'agent_id' => $agent_id,
                'clue_id' => $v,
            ]);
            //        $query->andWhere([
            //            'or',
            //            ['change_state' => ClueInfo::STATUS_OPEN],
            //            ['change_state' => ClueInfo::STATUS_DEFAULT],
            //        ]);
            $clueinfo = $query->one();
            if (empty($clueinfo)) {
                return $this->error(1, '线索不存在');
            }
            $clueinfo->username = $to_user;
            $clueinfo->team_id = $to_userinfo->team_id;
            $clueinfo->get_mode = ClueInfo::GET_ASSIGN;
            $clueinfo->get_time = date('Y-m-d H:i:s', time());
            $clueinfo->assign_user = $username;
            $clueinfo->change_time = '';
            $clueinfo->change_user = '';
            if ($clueinfo->save() > 0) {
                ClueLog::addlog([
                    'clue_id' => $v,
                    'opt_mode' => ClueLog::OPT_ASSIGN,
                    'opt_user' => $username,
                    'to_user' => $to_user
                ]);
            } else {
                return $this->error(1, '指派失败');
            }
        }
        return $this->success('指派成功');
    }

    /**
     * 提取线索
     */
    public function actionCluePick()
    {
        $username = Yii::$app->user->getIdentity()->username;
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $team_id = Yii::$app->user->getIdentity()->team_id;
        $request = Yii::$app->request->post();
        $clue_id = (isset($request['clue_id'])) ? $request['clue_id'] : "";
        $pick = ClueLog::getClueLog($username, '1', ClueLog::OPT_PICK);
        $pick_num = $pick['count']; //今日提取线索数
        $count = count($clue_id);
        $agent_info = Agent::findOne(['agent_id' => $agent_id]);
        $pick_limit = $agent_info->clue_num;
        if ($pick_num >= $pick_limit) {
            return $this->error(1, '今日提取线索数已超过限制');
        }
        if (($pick_num + $count) > $pick_limit) {
            $msg = '今日你只能再提取' . ($pick_limit - $pick_num) . '条线索，现已超过目标数量，请重新选择后提交';
            return $this->error(1, $msg);
        }
        $err = 0;
        $errMsg = [];
        foreach ($clue_id as $k => $v) {
            $query = ClueInfo::find()->where([
                'clue_id' => $v,
            ]);
            $query->andWhere([
                ' != ',
                'change_state',
                ClueInfo::STATUS_TURN
            ]);
            $clueinfo = $query->one();
            if (empty($clueinfo)) {
                $err++;
                $errMsg[] = '第' . $k . '条数据提取失败，该线索已转化为客户或数据不存在';
                continue;
            }
            $clueinfo->username = $username;
            $clueinfo->team_id = $team_id;
            $clueinfo->get_mode = ClueInfo::GET_PICK;
            $clueinfo->get_time = date('Y-m-d H:i:s', time());
            $clueinfo->change_time = '';
            $clueinfo->change_user = '';
            if ($clueinfo->save() > 0) {
                ClueLog::addlog(['clue_id' => $v, 'opt_mode' => ClueLog::OPT_PICK, 'opt_user' => $username]);
            } else {
                $err++;
                $errMsg[] = '第' . $k . '条数据提取失败';
                continue;
            }
        }
        if ($err > 0) {
            $errMsg = empty($errMsg) ? '' : implode("<br />", array_unique($errMsg));
            return $this->error(1, '提取失败：' . $errMsg);
        } else {
            return $this->success('提取成功');
        }
    }

    /**
     * 释放线索
     */
    public function actionClueChange()
    {
        $username = Yii::$app->user->getIdentity()->username;
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $team_id = Yii::$app->user->getIdentity()->team_id;
        $request = Yii::$app->request->post();
        $clue_id = (isset($request['clue_id'])) ? $request['clue_id'] : null;
        $change_state = (isset($request['change_state'])) ? $request['change_state'] : ClueInfo::STATUS_OPEN;
        $change_type = (isset($request['change_type'])) ? $request['change_type'] : '1';
        $clueinfo = ClueInfo::find()->where([
            'clue_id' => $clue_id,
        ])->one();
        if (empty($clueinfo)) {
            return $this->error(1, '数据不存在');
        }
        //1,释放到部门2，释放到公共
        if ($change_type == '1') {
            $clueinfo->team_id = $team_id;
        } else {
            $clueinfo->team_id = null;
        }
        $clueinfo->username = null;
        $clueinfo->get_mode = ClueInfo::GET_DEFAULT;
        $clueinfo->get_time = '';
        $clueinfo->assign_user = '';
        $clueinfo->change_state = $change_state;
        $clueinfo->change_time = date('Y-m-d H:i:s', time());
        $clueinfo->change_user = $username;
        if ($clueinfo->save() > 0) {
            ClueLog::addlog(['clue_id' => $clue_id, 'opt_mode' => ClueLog::OPT_OPEN, 'opt_user' => $username]);
            return $this->success();
        } else {
            return $this->error(1, '释放失败');
        }
    }

    /**
     * 线索转化为客户
     */
    public function actionClueTurn()
    {
        $username = Yii::$app->user->getIdentity()->username;
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $request = Yii::$app->request->post();
        $clue_id = (isset($request['clue_id'])) ? $request['clue_id'] : null;

        $clueinfo = ClueInfo::findOne(['clue_id' => $clue_id]);
        $request['add_mode'] = ClientInfo::ADD_DEFAULT;
        $request['add_user'] = $username;
        $request['agent_id'] = $agent_id;
        $request['team_id'] = Yii::$app->user->getIdentity()->team_id;
        $request['username1'] = $username;
        $clueinfo->change_state = ClueInfo::STATUS_TURN;
        $clueinfo->change_time = date('Y-m-d H:i:s', time());
        $clueinfo->change_user = $username;


        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            //添加客户
            $add = ClientInfo::addclient($request);
            if ($add['error'] != '0') {
                throw new \Exception($add['error_msg']);
            }
            $res = $clueinfo->save();
            if (!$res) {
                throw new \Exception('客户转化失败');
            }
            $addlog = ClueLog::addlog([
                'clue_id' => $clue_id,
                'opt_mode' => ClueLog::OPT_TURN,
                'opt_user' => $username
            ]);
            if ($addlog['error'] != '0') {
                throw new \Exception('线索Log添加失败');
            }
            $transaction->commit();
            return $this->success();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return $this->error('1', $e->getMessage());
        }
    }

    /**
     * 添加跟进记录
     */
    public function actionTraceAdd()
    {
        $username = Yii::$app->user->getIdentity()->username;
        $request = Yii::$app->request->post();
        $clue_id = (isset($request['clue_id'])) ? $request['clue_id'] : null;
        $trace_mode = (isset($request['trace_mode'])) ? $request['trace_mode'] : null;
        $content = (isset($request['content'])) ? $request['content'] : null;
        if (!$clue_id) {
            return $this->error(1, '参数错误');
        }
        $clue_info = ClueInfo::findOne(['clue_id' => $clue_id]);
        if (!$clue_info) {
            return $this->error(1, '数据错误');
        }
        $model = new ClueTrace();
        $model->clue_id = $clue_id;
        $model->trace_mode = $trace_mode;
        $model->content = $content;
        $model->username = $username;

        if ($model->save() > 0) {
            $clue_info->trace_time = date('Y-m-d H:i:s', time());
            $clue_info->save();
            return $this->success();
        } else {
            return $this->error(1, '添加失败');
        }
    }

    /**
     * 获取所有跟进记录
     */
    public function actionGetTraceInfo()
    {
        $request = Yii::$app->request->post();
        $clue_id = (isset($request['clue_id'])) ? $request['clue_id'] : null;

        $traceinfo = ClueTrace::find()->where([
            'clue_id' => $clue_id
        ])
            ->asArray()
            ->all();

        return $this->success($traceinfo);
    }

    /**
     * 获取所有变更记录
     */
    public function actionGetLogInfo()
    {
        $request = Yii::$app->request->post();
        $clue_id = (isset($request['clue_id'])) ? $request['clue_id'] : null;

        $loginfo = ClueLog::find()->where([
            'clue_id' => $clue_id
        ])
            ->asArray()
            ->all();
        foreach ($loginfo as $k => $v) {
            $opt_user = User::findOne(['username' => $v['opt_user']]);
            if (!$opt_user) {
                $loginfo[$k]['role'] = '1';
            } else {
                $loginfo[$k]['role'] = $opt_user->role;
            }
        }
        return $this->success($loginfo);
    }

    /**
     * 标记线索
     */
    public function actionMarkClue()
    {
        $username = Yii::$app->user->getIdentity()->username;
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $team_id = Yii::$app->user->getIdentity()->team_id;
        $request = Yii::$app->request->post();
        $clue_id = (isset($request['clue_id'])) ? $request['clue_id'] : null;
        $mark = (isset($request['mark'])) ? $request['mark'] : null;
        $clue_info = ClueInfo::findOne(['clue_id' => $clue_id]);
        if (!$clue_info) {
            return $this->error(1, '数据错误');
        }
        $clue_info->clue_mark = $mark;
        $clue_info->mark_time = date("Y-m-d H:i:s", time());
        $res = $clue_info->save();
        if ($res) {
            return $this->success();
        } else {
            return $this->error(1, '标记失败');
        }
    }

    /**
     * 删除线索
     */
    public function actionDelClue()
    {
        $username = Yii::$app->user->getIdentity()->username;
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $team_id = Yii::$app->user->getIdentity()->team_id;
        $request = Yii::$app->request->post();
        $role = Yii::$app->user->getIdentity()->role;
        $clue_id = (isset($request['clue_id'])) ? $request['clue_id'] : null;
        if ($role != '3') {
            return $this->error(1, '您没有删除权限');
        }
        $clue_info = ClueInfo::findOne(['clue_id' => $clue_id]);
        if (!$clue_info) {
            return $this->error(1, '数据错误');
        }
        $result = $clue_info->delete();
        if (!$result) {
            return $this->error(1, '线索删除失败');
        }
        return $this->success('线索删除成功');
    }

    /**
     * 获取区域
     * @return array
     */
    public function actionGetRegion()
    {
        $request = Yii::$app->request->post();

        $parent_id = isset($request['parent_id']) ? $request['parent_id'] : 0;
        $region = (new \yii\db\Query())
            ->select(['id', 'name', 'code'])
            ->from('region')
            ->where(['parent_id' => $parent_id, 'status' => 1])
            ->orderBy('code asc')
            ->all();
        if (!empty($region)) {
            return $this->success($region);
        } else {
            return $this->error(0, '没有数据');
        }
    }

    public function actionExcelToClue()
    {
        $username = Yii::$app->user->getIdentity()->username;
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $request = Yii::$app->request->get();
        $type = isset($request['type']) ? $request['type'] : 1;
        if ($type == '2') {
            $name = null;
        } else {
            $name = $username;
        }
        $model = new UploadForm();
        $file = UploadedFile::getInstanceByName('file');
        $ext = substr(strrchr($file->name, '.'), 1);
        if ($ext != 'csv') {
            return $this->error(1, '请上传.csv文件');
        }
        $model->file = $file;
        $files = $model->upload();
        if (!$files) {
            return $this->error(-1, '上传失败');
        }
        if ($file) {
            $filename = 'upload/Files/' . $files;
            $file->saveAs($filename);
        }
        // $filedata = file($filename);
        $file = fopen($filename, 'r');
        while ($data = fgetcsv($file)) {
            $filedata[] = $data;
        }
        $code = $this->checkCode($filedata[0][0]);
        foreach ($filedata as $key => $value) {
            if ($code != 'UTF-8') {
                $filedata[$key] = $this->arrayToGbk($value);
            }
        }
        $filedata = array_splice($filedata, 1);
        if (empty($filedata)) {
            return $this->error(1, 'excel文件为空，请重新选择文件');
        }
        $total = count($filedata);
        if ($total > 100000) {
            return $this->error(1, '文件数据量太大，为避免导入失败，建议您分成多个文件导入');
        }
        $user = User::findOne(['agent_id' => $agent_id, 'username' => trim($name)]);
        if ($user) {
            $team_id = $user->team_id;
        } else {
            $team_id = null;
        }
        $chunkData = array_chunk($filedata, 5000, true); // 将这个10W+ 的数组分割成5000一个的小数组。这样就一次批量插入5000条数据。mysql 是支持的。
        $count = count($chunkData);
        $err = 0;
        $errMsg = [];
        $total_success = 0;
        //当前文件内手机号集合
        $telInfoThis = [];
        //获取当前代理所有线索手机号
        $query = ClueInfo::find()->where(['agent_id' => $agent_id]);
        $tel_database = [];
        foreach ($query->each() as $clues) {
            $tel_database[$clues['tel']] = $clues['tel'];
        }
        for ($i = 0; $i < $count; $i++) {
            $csv = $chunkData[$i];
            $data = [];
            foreach ($csv as $k => $v) {
                $contact = isset($v[1]) ? $v[1] : '';
                $clue_name = isset($v[0]) ? $v[0] : '';
                $tel = isset($v[2]) ? $v[2] : '';

                if (!$clue_name || !$contact || !$tel) {
                    $err++;
                    $errMsg[] = '第 <font color = \'red\'>' . ($k + 2) . '</font>条导入失败，数据缺失';
                    continue;
                }
                if (!is_numeric($tel)) {
                    $err++;
                    $errMsg[] = '第 <font color = \'red\'>' . ($k + 2) . '</font>条导入失败，电话格式错误';
                    continue;
                }
                if (isset($tel_database[$tel]) || isset($telInfoThis[$tel])) {
                    $err++;
                    $errMsg[] = '第 <font color = \'red\'>' . ($k + 2) . '</font>条导入失败:' . $contact . '的手机号 ' . '<font color=\'red\'>' . $tel . '</font> 已存在，请勿重复添加';
                    continue;
                }

                $data[$k]['add_mode'] = ClueInfo::ADD_BATCH;
                $data[$k]['add_user'] = $username;
                $data[$k]['agent_id'] = $agent_id;
                $data[$k]['team_id'] = $team_id;
                $data[$k]['clue_name'] = $clue_name;
                $data[$k]['contacts'] = $contact;
                $data[$k]['tel'] = $tel;
                $data[$k]['email'] = isset($v[3]) ? trim($v[3]) : "";
                $data[$k]['qq'] = isset($v[4]) ? trim($v[4]) : "";
                $data[$k]['establish_date'] = isset($v[5]) ? trim($v[5]) : "";
                $data[$k]['capital'] = isset($v[6]) ? trim($v[6]) : "";
                $data[$k]['trade'] = isset($v[7]) ? trim($v[7]) : "";
                $data[$k]['source'] = isset($v[8]) ? trim($v[8]) : "";
                $data[$k]['province'] = isset($v[9]) ? trim($v[9]) : "";
                $data[$k]['area'] = isset($v[10]) ? trim($v[10]) : "";
                $data[$k]['address'] = isset($v[11]) ? trim($v[11]) : "";
                $data[$k]['describe'] = isset($v[12]) ? trim($v[12]) : "";
                $data[$k]['add_time'] = date('Y-m-d H:i:s', time());
                $data[$k]['username'] = $name;

                $telInfoThis[$tel] = $tel;
            }
            $sql = Yii::$app->db->getQueryBuilder()->batchInsert(ClueInfo::tableName(), ClueInfo::getFields(), $data);
            $res = Yii::$app->db->createCommand($sql)->execute();
            $total_success += $res;
        }
        if ($err > 0) {
            $success = '成功导入' . $total_success . '条。';
            $total_fail = $total - $total_success;
            $errMsg = empty($errMsg) ? '' : implode("<br />", array_unique($errMsg));
            unlink($filename);
            return $this->error(1, $success . "<br />" . '导入失败' . $total_fail . '条：' . "<br />" . $errMsg);
        } else {
            unlink($filename);
            return $this->success('导入成功' . $total_success . '条');
        }
    }

    public function checkCode($data)
    {
        $list = array('GBK', 'UTF-8', 'GB2312');
        foreach ($list as $item) {
            $tmp = mb_convert_encoding($data, $item, $item);
            if (md5($tmp) == md5($data)) {
                return $item;
            }
        }
    }

    public function arrayToGbk($array)
    {
        $array = array_map(function ($value) {
            return mb_convert_encoding(trim(strip_tags($value)), 'utf-8', 'gbk');
        }, $array);

        return $array;
    }
}
