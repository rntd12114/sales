<?php

namespace frontend\controllers;

use frontend\models\ClientForm;
use frontend\models\ClientInfo;
use frontend\models\ClientLog;
use frontend\models\ClientTrace;
use moonland\phpexcel\Excel;
use Yii;
use common\framework\web\Controller;
use frontend\models\Agent;
use frontend\models\ClueInfo;
use frontend\models\ClueLog;
use frontend\models\UploadForm;
use frontend\models\User;
// use yii\web\Controller;
use yii\data\Pagination;
use yii\web\Response;
use yii\web\UploadedFile;

class ClientController extends Controller
{
    /**
     * 客户统计数列表
     */
    public function actionNumList()
    {
        $request = Yii::$app->request->post();
        $username = isset($request['username']) ? $request['username'] : Yii::$app->user->getIdentity()->username;
        $timetype = isset($request['timetype']) ? $request['timetype'] : '1'; //默认当天
        $optype = isset($request['optype']) ? $request['optype'] : ''; //默认当天
        $current_page = isset($request['current_page']) ? ($request['current_page'] - 1) : 0; // 0页开始
        $page_size = isset($request['page_size']) ? $request['page_size'] : 10; // 0页开始

        //权限验证
        $canSee = user::userRights($username);
        if (!$canSee) {
            return $this->error(1, '无权查看该用户信息');
        }

        //客户相关
        if ($optype == '1') {
            $listInfo = ClientInfo::clientTrace($username, $timetype, $current_page, $page_size); //已联系客户
        } else {
            $listInfo = ClientLog::getClientLog($username, $timetype, $optype, $current_page, $page_size); //已提取客户
        }

        return $this->success($listInfo);
    }

    /**
     * 客户列表
     */
    public function actionList()
    {
        $role = Yii::$app->user->getIdentity()->role; //1员工 2经理 3总监
        $team_id = Yii::$app->user->getIdentity()->team_id; //部门ID
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $usernamemy = Yii::$app->user->getIdentity()->username;
        $request = Yii::$app->request->post();
        $pagenum = isset($request['current_page']) ? ($request['current_page'] - 1) : 0; // 0页开始
        $page_size = isset($request['page_size']) ? $request['page_size'] : 10; // 0页开始
        $created_date_star = isset($request['creationDateStar']) ? $request['creationDateStar'] : '';
        $created_date_end = isset($request['creationDateEnd']) ? $request['creationDateEnd'] : '';
        // $trace_time_star = isset($request['traceTimeStar']) ? $request['traceTimeStar'] : '';
        // $trace_time_end = isset($request['traceTimeEnd']) ? $request['traceTimeEnd'] : '';
        $name = isset($request['name']) ? $request['name'] : ''; //客户姓名
        $username = isset($request['username']) ? $request['username'] : ''; //负责人
        $ltype = isset($request['ltype']) ? $request['ltype'] : 'my'; //我的
        $orders = isset($request['orders']) ? $request['orders'] : ''; //成单次数
        $client_id = isset($request['client_id']) ? $request['client_id'] : '';
        $province = isset($request['province']) ? $request['province'] : '';
        $area = isset($request['area']) ? $request['area'] : '';
        $contacts = isset($request['contacts']) ? $request['contacts'] : ''; //联系人
        $tel = isset($request['tel']) ? $request['tel'] : ''; //电话

        $establish_date_type = isset($request['establish_date_type']) ? $request['establish_date_type'] : ''; //成立时间区间
        $establish_date = ClueLog::getTime($establish_date_type);
        $establish_date_star = $establish_date['day_start'];
        $establish_date_end = $establish_date['day_end'];

        $trace_time_type = isset($request['trace_time_type']) ? $request['trace_time_type'] : ''; //最近联系时间区间
        $trace_time = ClueLog::getTime($trace_time_type);
        $trace_time_star = $trace_time['day_start'];
        $trace_time_end = $trace_time['day_end'];

        $query = ClientInfo::find()->where([
            'agent_id' => $agent_id,
        ]);

        if ($client_id) {
            $query->andWhere(['client_id' => $client_id]);
        }

        if ($province) {
            $query->andWhere(['province' => $province]);
        }

        if ($area) {
            $query->andWhere(['area' => $area]);
        }

        if ($tel) {
            $query->andWhere(['tel' => $tel]);
        }

        if ($username) {
            $query->andWhere([
                'or',
                [
                    'like',
                    'username1',
                    "{$username}",
                ],
                [
                    'like',
                    'username2',
                    "{$username}",
                ],
            ]);
        }
        if ($ltype == 'my') {
            //我的
            $query->andWhere([
                'or',
                ['username1' => $usernamemy],
                ['username2' => $usernamemy],
            ]);
        } elseif ($ltype == 'team') {
            //部门池
            if ($role == 1) {
                //员工看部门释放的
                $query->andWhere([
                    'team_id' => $team_id,
                    'change_state' => ClientInfo::STATUS_OPEN
                ]);
            } elseif ($role == 2) {
                //经理看部门的
                $query->andWhere([
                    'team_id' => $team_id,
                ]);
            } elseif ($role == 3) {
                //总监看全部的
                $query->andWhere(['!=', 'team_id', '']);
            }
        } else {
            //公共池
            $query->andWhere(['team_id' => null, 'change_state' => ClientInfo::STATUS_OPEN]);
        }

        if ($orders !== '') {
            if ($orders > 1) {
                $query->andWhere(['>=', 'orders', $orders]);
            } elseif ($orders <= 1) {
                $query->andWhere(['=', 'orders', $orders]);
            }
        }

        if ($name) {
            // $query->andWhere([
            //     'like',
            //     'client_name',
            //     "{$name}",
            // ]);

            $query->andWhere([
                'or',
                [
                    'like',
                    'client_name',
                    "{$name}",
                ],
                [
                    'like',
                    'contacts',
                    "{$name}",
                ],
                [
                    'like',
                    'tel',
                    "{$name}",
                ]
            ]);
        }

        if ($contacts) {
            $query->andWhere([
                'like',
                'contacts',
                "{$contacts}",
            ]);
        }

        if ($created_date_star) {
            $query->andWhere([
                '>=',
                'add_time',
                $created_date_star,
            ]);
        }
        if ($created_date_end) {
            $query->andWhere([
                '<=',
                'add_time',
                $created_date_end,
            ]);
        }

        if ($trace_time_type) {
            if ($trace_time_star) {
                $query->andWhere([
                    '>=',
                    'trace_time',
                    $trace_time_star,
                ]);
            }
            if ($trace_time_end) {
                $query->andWhere([
                    '<=',
                    'trace_time',
                    $trace_time_end,
                ]);
            }
        }

        if ($establish_date_type) {
            if ($establish_date_star) {
                $query->andWhere([
                    '>=',
                    'establish_date',
                    $establish_date_star,
                ]);
            }
            if ($establish_date_end) {
                $query->andWhere([
                    '<=',
                    'establish_date',
                    $establish_date_end,
                ]);
            }
        }




        // 得到总数（但是还没有从数据库取数据）
        $count = $query->count();
        // 使用总数来创建一个分页对象
        $pagination = new Pagination([
            'defaultPageSize' => $page_size,
            'totalCount' => $count,
            'pageSizeLimit' => false
        ]);

        $pagination->setPage($pagenum); // 设置页数，从0开始

        // 使用分页对象来填充 limit 子句并取得数据
        $ininfo = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy('client_id desc')
            ->asArray()
            ->all();
        foreach ($ininfo as $k => &$v) {
            //电话显示限制
            if ($ltype == 'my' || $role == 3) {
                //我的或总监都可见
                $v['tel'] = $v['tel'];
            } elseif ($ltype == 'team' && $role > 1) {
                //部门池
                $v['tel'] = $v['tel'];
            } else {
                $v['tel'] = '***********';
            }

            //添加方式
            if ($v['add_mode'] == ClientInfo::ADD_DEFAULT) {
                $v['add_mode_str'] = '线索转化';
            } elseif ($v['add_mode'] == ClientInfo::ADD_MANUAL) {
                $v['add_mode_str'] = '手动录入';
            } elseif ($v['add_mode'] == ClientInfo::ADD_BATCH) {
                $v['add_mode_str'] = '批量导入';
            }
            //获取方式
            if ($v['get_mode'] == ClientInfo::GET_DEFAULT) {
                $v['get_mode_str'] = '导入';
            } elseif ($v['get_mode'] == ClientInfo::GET_PICK) {
                $v['get_mode_str'] = '自己提取';
            } elseif ($v['get_mode'] == ClientInfo::GET_ASSIGN) {
                $v['get_mode_str'] = '上级指派';
            }

            if ($v['change_state'] == ClientInfo::STATUS_OPEN) {
                $v['change_state_str'] = empty($v['change_user']) ? '' : '已释放</br>' . $v['change_user'] . '<br/>' . $v['change_time'];
            } elseif ($v['change_state'] == ClientInfo::STATUS_DEFAULT) {
                $v['change_state_str'] = '已获取</br>' . $v['username1'] . ' ' . $v['username2'] . $v['get_mode_str'] . '<br/>' . $v['get_time'];
            }
        }

        return $this->success([
            'cinfo' => $ininfo,
            'totalCount' => intval($count),
        ]);
    }

    /**
     * 指派客户
     */
    public function actionClientAssign()
    {
        $username = Yii::$app->user->getIdentity()->username;
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $request = Yii::$app->request->post();
        $id = $request['id'];
        $to_user = isset($request['to_user']) ? $request['to_user'] : '';
        $to_user2 = isset($request['to_user2']) ? $request['to_user2'] : '';

        if (empty($to_user) && empty($to_user2)) {
            return [
                'error'     => 1,
                'error_msg' => '负责人不能为空',
            ];
        }

        $cinfo = ClientInfo::find()->where([
            'agent_id' => $agent_id,
            'client_id' => $id,
            // 'change_state' => ClientInfo::STATUS_OPEN
        ])->one();
        if (empty($cinfo)) {
            return $this->error(1, '数据不存在');
        }
        $cinfo->username1 = !empty($to_user) ? $to_user : $cinfo->username1;
        $cinfo->username2 = !empty($to_user2) ? $to_user2 : $cinfo->username2;
        //获取第一负责人部门
        if (!empty($cinfo->username1)) {
            $userinfo = User::find()->where(['username' => $cinfo->username1])->one();
            if (empty($userinfo)) {
                return $this->error(1, $cinfo->username1 . '用户数据不存在');
            }
            $cinfo->team_id = $userinfo->team_id;
        } elseif (empty($cinfo->username1) && !empty($cinfo->username2)) {
            $userinfo2 = User::find()->where(['username' => $cinfo->username2])->one();
            if (empty($userinfo2)) {
                return $this->error(1, $cinfo->username2 . '用户数据不存在');
            }
            $cinfo->team_id = $userinfo2->team_id;
        }

        $cinfo->get_mode = ClientInfo::GET_ASSIGN;
        $cinfo->get_time = date('Y-m-d H:i:s');
        $cinfo->assign_user = $username;
        $cinfo->change_state = ClientInfo::STATUS_DEFAULT;
        $cinfo->change_time = '';
        $cinfo->change_user = '';

        if ($cinfo->save() > 0) {
            ClientLog::addlog(['client_id' => $id, 'opt_mode' => ClientLog::OPT_ASSIGN, 'opt_user' => $username, 'to_user' => $to_user . $to_user2]);
            return $this->success('指派成功');
        } else {
            return $this->error(1, json_encode($cinfo->errors));
        }
    }

    /**
     * 提取客户
     */
    public function actionClientPick()
    {
        $username = Yii::$app->user->getIdentity()->username;
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $request = Yii::$app->request->post();
        $id = $request['id'];
        $AgentInfo = Agent::findOne(['agent_id' => $agent_id]);
        $client_num = $AgentInfo->client_num; //最高提取数
        $cnum = ClientLog::getClientLog($username, '1', ClientInfo::GET_PICK); //已提取
        $cnum = $cnum['count'];
        if ($cnum >= $client_num) {
            return $this->error(1, '提取客户次数已达最高' . $client_num . '次');
        }

        $cinfo = ClientInfo::find()->where([
            'agent_id' => $agent_id,
            'client_id' => $id,
            'change_state' => ClientInfo::STATUS_OPEN
        ])->one();
        if (empty($cinfo)) {
            return $this->error(1, '数据不存在');
        }
        $cinfo->username1 = $username;
        $cinfo->get_mode = ClientInfo::GET_PICK;
        $cinfo->get_time = date('Y-m-d H:i:s');
        $cinfo->change_state = ClientInfo::STATUS_DEFAULT;
        $cinfo->change_time = '';
        $cinfo->change_user = '';
        $cinfo->team_id = Yii::$app->user->getIdentity()->team_id;
        if ($cinfo->save() > 0) {
            ClientLog::addlog(['client_id' => $id, 'opt_mode' => ClientLog::OPT_PICK, 'opt_user' => $username]);
            return $this->success('提取成功');
        } else {
            return $this->error(1, json_encode($cinfo->errors));
        }
    }

    /**
     * 释放客户
     */
    public function actionClientChange()
    {
        $role = Yii::$app->user->getIdentity()->role; //1员工 2经理 3总监
        $username = Yii::$app->user->getIdentity()->username;
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $request = Yii::$app->request->post();
        $id = $request['id'];
        $orders = isset($request['orders']) ? $request['orders'] : 0; //成单数
        $place = isset($request['place']) ? $request['place'] : ''; //释放到哪 默认到部门

        $cinfo = ClientInfo::find()->where([
            'agent_id' => $agent_id,
            'client_id' => $id,
            // 'change_state' => ClientInfo::STATUS_DEFAULT
        ])->one();
        if (empty($cinfo)) {
            return $this->error(1, '数据不存在');
        }

        if ($role == 1) {
            //员工释放
            if ($cinfo->username1 == $username) {
                $cinfo->username1 = '';
            } elseif ($cinfo->username2 == $username) {
                $cinfo->username2 = '';
            }
        } else {
            //领导回收
            $cinfo->username1 = '';
            $cinfo->username2 = '';
        }

        $cinfo->get_mode = empty($cinfo->username1) && empty($cinfo->username1) ? '' : $cinfo->get_mode;
        $cinfo->get_time = empty($cinfo->username1) && empty($cinfo->username1) ? '' : $cinfo->get_time;
        $cinfo->assign_user = '';
        $cinfo->change_state =  empty($cinfo->username1) && empty($cinfo->username1) ? ClientInfo::STATUS_OPEN : ClientInfo::STATUS_DEFAULT;
        $cinfo->change_time = date('Y-m-d H:i:s');
        $cinfo->change_user = $username;

        if ($place == 'public') {
            //释放到公共池
            $cinfo->team_id = '';
        }

        if ($orders > 0) {
            $cinfo->orders = $cinfo->orders < 0 ? 1 : $cinfo->orders + 1; //成单次数+1
        } elseif ($orders < 0 && $cinfo->orders == 0) {
            $cinfo->orders = -1; //难成单
        }

        if ($cinfo->save() > 0) {
            ClientLog::addlog(['client_id' => $id, 'opt_mode' => ClientLog::OPT_OPEN, 'opt_user' => $username]);
            return $this->success('释放成功');
        } else {
            return $this->error(1, json_encode($cinfo->errors));
        }
    }

    /**
     * 添加跟进记录
     */
    public function actionTraceAdd()
    {
        $username = Yii::$app->user->getIdentity()->username;
        $request = Yii::$app->request->post();

        $model = new ClientTrace();
        $model->client_id = $request['client_id'];
        $model->trace_mode = $request['trace_mode'];
        $model->phase = $request['phase'];
        $model->content = $request['content'];
        $model->work = $request['work'];
        $model->product = $request['product'];
        $model->username = $username;
        $model->next_time = $request['next_time'];
        $model->next_mode = $request['next_mode'];
        $model->next_content = $request['next_content'];

        if ($model->save() > 0) {
            //更新客户表跟进时间
            $cmodel = ClientInfo::find()->where([
                'client_id' => $request['client_id']
            ])
                ->one();
            if (empty($cmodel)) {
                return $this->error(1, '客户数据不存在');
            }
            $cmodel->trace_time = date('Y-m-d H:i:s');
            $cmodel->save();

            return $this->success('添加成功');
        } else {
            $err = $model->firstErrors;
            return $this->error(1, implode('；', $err));
        }
    }


    /**
     * 获取所有跟进记录
     */
    public function actionTraceInfo()
    {
        $request = Yii::$app->request->post();
        $id = $request['client_id'];

        $cinfo = ClientTrace::find()->where([
            'client_id' => $id
        ])
            ->orderBy('trace_id desc')
            ->asArray()
            ->all();

        return $this->success($cinfo);
    }

    /**
     * 获取所有跟进计划
     */
    public function actionTraceNextInfo()
    {
        $username = Yii::$app->user->getIdentity()->username;
        $request = Yii::$app->request->post();
        $current_page = isset($request['current_page']) ? ($request['current_page'] - 1) : 0; // 0页开始
        $page_size = isset($request['page_size']) ? $request['page_size'] : 5; // 0页开始

        $query = ClientTrace::find()->alias('t')
            ->select([
                't.username',
                't.next_time',
                't.next_mode',
                't.next_content',
                'i.client_name'
            ])
            ->leftJoin('client_info i', 't.client_id = i.client_id')
            ->where([
                'or',
                ['i.username1' => $username],
                ['i.username2' => $username],
            ])->andWhere([
                '>=',
                't.next_time',
                date('Y-m-d H:i:s'),
            ]);

        $count = $query->count();
        $pagination = new Pagination([
            'totalCount' => $count,
            'defaultPageSize' => $page_size,
        ]);
        $pagination->setPage((int)$current_page);
        $list = $query->offset($pagination->offset)->limit($pagination->limit)->orderBy('next_time asc')->asArray()->all();

        return $this->success([
            'list' => $list,
            'count' => $count,
        ]);
    }

    /**
     *  获取客户转换记录
     */
    public function actionClientLogList()
    {
        $request = Yii::$app->request->post();
        $id = $request['client_id'];

        $cinfo = ClientLog::find()->where([
            'client_id' => $id
        ])
            ->orderBy('log_id desc')
            ->asArray()
            ->all();

        foreach ($cinfo as $k => &$v) {
            $userinfo = User::find()->where(['username' => $v['opt_user']])->one();
            $role = $userinfo->role;
            //获取方式
            if ($v['opt_mode'] == ClientLog::OPT_OPEN) {
                $v['opt_mode_str'] = $role == 1 ? '释放' : '回收';
            } elseif ($v['opt_mode'] == ClientLog::OPT_PICK) {
                $v['opt_mode_str'] = '自己提取';
            } elseif ($v['opt_mode'] == ClientLog::OPT_ASSIGN) {
                $v['opt_mode_str'] = '指派';
            } else {
                $v['opt_mode_str'] = '其他';
            }
        }

        return $this->success($cinfo);
    }



    /**
     * 添加画像
     */
    public function actionFormAdd()
    {
        $username = Yii::$app->user->getIdentity()->username;
        $request = Yii::$app->request->post();

        $model = new ClientForm();
        $model->client_id = isset($request['client_id']) ?  trim($request['client_id']) : '';
        $model->level = isset($request['level']) ?  trim($request['level']) : '';
        $model->operate = isset($request['operate']) ?  trim($request['operate']) : '';
        $model->property = isset($request['property']) ?  trim($request['property']) : '';
        $model->undergo = isset($request['undergo']) ?  trim($request['undergo']) : '';
        $model->years = isset($request['years']) ?  trim($request['years']) : '';
        $model->shop = isset($request['shop']) ?  trim($request['shop']) : '';
        $model->wx_content = isset($request['wx_content']) ?  trim($request['wx_content']) : '';
        $model->appeal = isset($request['appeal']) ?  trim($request['appeal']) : '';
        $model->age = isset($request['age']) ?  trim($request['age']) : '';
        $model->like = isset($request['like']) ?  trim($request['like']) : '';
        $model->doubt = isset($request['doubt']) ?  trim($request['doubt']) : '';
        $model->traits = isset($request['traits']) ?  trim($request['traits']) : '';
        $model->username = $username;
        $model->last_time = date('Y-m-d H:i:s');

        if ($model->save() > 0) {
            return $this->success('添加成功');
        } else {
            $err = $model->firstErrors;
            return $this->error(1, implode('；', $err));
        }
    }

    /**
     * 获取客户画像信息
     */
    public function actionFormInfo()
    {
        $request = Yii::$app->request->post();
        $id = $request['client_id'];

        // 获取客户画像信息
        $cinfo = ClientForm::find()->where([
            'client_id' => $id
        ])
            ->asArray()
            ->one();

        // if (empty($cinfo)) {
        //     return $this->error(1, '数据不存在');
        // }
        return $this->success($cinfo);
    }

    /**
     * 修改画像
     */
    public function actionFormUp()
    {
        $username = Yii::$app->user->getIdentity()->username;
        $request = Yii::$app->request->post();
        $id = $request['client_id'];
        if (empty($id)) {
            return $this->error(1, '客户ID不能为空');
        }

        // 获取客户画像信息
        $model = ClientForm::find()->where([
            'client_id' => $id
        ])
            ->one();

        if (empty($model)) {
            $model = new ClientForm();
            $model->client_id = $id;
        }

        if (!empty($request['level'])) {
            $model->level = trim($request['level']);
        }
        if (!empty($request['operate'])) {
            $model->operate = trim($request['operate']);
        }
        if (!empty($request['property'])) {
            $model->property = trim($request['property']);
        }
        if (!empty($request['undergo'])) {
            $model->undergo = trim($request['undergo']);
        }
        if (!empty($request['years'])) {
            $model->years = intval(trim($request['years']));
        }
        if (!empty($request['shop'])) {
            $model->shop = trim($request['shop']);
        }
        if (!empty($request['wx_content'])) {
            $model->wx_content = trim($request['wx_content']);
        }
        if (!empty($request['appeal'])) {
            $model->appeal = trim($request['appeal']);
        }
        if (!empty($request['age'])) {
            $model->age = trim($request['age']);
        }
        if (!empty($request['like'])) {
            $model->like = trim($request['like']);
        }
        if (!empty($request['doubt'])) {
            $model->doubt = trim($request['doubt']);
        }
        if (!empty($request['traits'])) {
            $model->traits = trim($request['traits']);
        }

        $model->username = $username;
        $model->last_time = date('Y-m-d H:i:s');

        if ($model->save() > 0) {
            return $this->success('修改成功');
        } else {
            $err = $model->firstErrors;
            return $this->error(1, implode('；', $err));
        }
    }


    /**
     * 获取客户信息
     */
    public function actionClientInfo()
    {
        $role = Yii::$app->user->getIdentity()->role; //1员工 2经理 3总监
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $request = Yii::$app->request->post();
        $id = $request['id'];
        $ltype = isset($request['ltype']) ? $request['ltype'] : 'my'; //我的

        // 获取客户信息
        $cinfo = ClientInfo::find()->where([
            'agent_id' => $agent_id,
            'client_id' => $id
        ])
            ->asArray()
            ->one();

        if (empty($cinfo)) {
            return $this->error(1, '数据不存在');
        }

        //电话显示限制
        if ($ltype == 'my' || $role == 3) {
            //我的或总监都可见
            $cinfo['tel'] = $cinfo['tel'];
        } elseif ($ltype == 'team' && $role > 1) {
            //部门池
            $cinfo['tel'] = $cinfo['tel'];
        } else {
            $cinfo['tel'] = '***********';
        }
        return $this->success($cinfo);
    }

    /**
     * 修改客户信息
     */
    public function actionClientUp()
    {
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $request = Yii::$app->request->post();
        $id = $request['id'];

        // 获取客户信息
        $model = ClientInfo::find()->where([
            'agent_id' => $agent_id,
            'client_id' => $id
        ])
            ->one();

        if (empty($model)) {
            return $this->error(1, '数据不存在');
        }
        if (!empty($request['contacts'])) {
            $model->contacts = trim($request['contacts']);
        }
        if (!empty($request['duty'])) {
            $model->duty = trim($request['duty']);
        }
        if (!empty($request['weight'])) {
            $model->weight = trim($request['weight']);
        }
        if (!empty($request['tel'])) {
            $model->tel = trim($request['tel']);
        }
        if (!empty($request['email'])) {
            $model->email = trim($request['email']);
        }
        if (!empty($request['qq'])) {
            $model->qq = trim($request['qq']);
        }
        if (!empty($request['wx'])) {
            $model->wx = trim($request['wx']);
        }
        if (!empty($request['establish_date'])) {
            $model->establish_date = trim($request['establish_date']);
        }
        if (!empty($request['capital'])) {
            $model->capital = trim($request['capital']);
        }
        if (!empty($request['trade'])) {
            $model->trade = trim($request['trade']);
        }
        if (!empty($request['source'])) {
            $model->source = trim($request['source']);
        }
        if (!empty($request['province'])) {
            $model->province = trim($request['province']);
        }
        if (!empty($request['area'])) {
            $model->area = trim($request['area']);
        }
        if (!empty($request['address'])) {
            $model->address = trim($request['address']);
        }
        if (!empty($request['describe'])) {
            $model->describe = trim($request['describe']);
        }

        if ($model->save() > 0) {
            return $this->success('修改成功');
        } else {
            $err = $model->firstErrors;
            return $this->error(1, implode('；', $err));
        }
    }



    /**
     * 手动添加客户
     */
    public function actionClientAdd()
    {
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $username = Yii::$app->user->getIdentity()->username;
        $request = Yii::$app->request->post();
        $request['add_mode'] = ClientInfo::ADD_MANUAL;
        $request['add_user'] = $username;
        $request['agent_id'] = $agent_id;
        $request['team_id'] = Yii::$app->user->getIdentity()->team_id;

        //添加客户
        $add = ClientInfo::addclient($request);
        if ($add['error'] > 0) {
            return $this->error($add['error'], $add['error_msg']);
        } else {
            ClientLog::addlog(['client_id' => $add['data'], 'opt_mode' => ClientLog::OPT_PICK, 'opt_user' => $username]);
            return $this->success('添加成功');
        }
    }

    /**
     * 批量导入客户
     */
    public function actionClientImportOld()
    {
        $role = Yii::$app->user->getIdentity()->role; //1员工 2经理 3总监
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $username = Yii::$app->user->getIdentity()->username;
        $team_idme = Yii::$app->user->getIdentity()->team_id;
        $request = Yii::$app->request->post();

        $file = $_FILES['file'];
        $extension = explode('.', $file['name']);
        $ext = $extension[1];
        if (!in_array($ext, array('xls', 'xlsx'))) {
            return $this->error(1, '请上传excel文件');
        }

        $filedata = Excel::import($file['tmp_name'], [
            'setFirstRecordAsKeys' => true,
            'setIndexSheetByName' => true,
            'getOnlySheet' => 'Sheet1',
        ]);

        $err = 0;
        $errMsg = [];
        $getUserInfo = $this->getUserInfo();
        $usernameArr = $getUserInfo['usernameArr'];
        $userTeamArr = $getUserInfo['userTeamArr'];
        $telInfo = $this->getTelInfo();

        $newData = [];
        $telInfoThis = [];
        foreach ($filedata as $k => $v) {
            $data = [];
            $data[] = $agent_id;
            $data[] = $v['客户名称（必填）'];
            $data[] = trim($v['联系人（必填）']);
            $data[] = trim($v['电话（必填）']);
            $data[] = trim($v['邮箱']);
            $data[] = trim($v['QQ号码']);
            $data[] = !empty($v['成立日期']) ? date('Y-m-d', strtotime(trim($v['成立日期']))) : '';
            $data[] = trim($v['注册资本']);
            $data[] = trim($v['所属行业']);
            $data[] = trim($v['客户来源']);
            $data[] = trim($v['省份']);
            $data[] = trim($v['地区']);
            $data[] = trim($v['地址']);
            $data[] = trim($v['经营范围']);
            $data[] = $username;
            $data[] = ClientInfo::ADD_BATCH;
            $data[] =  date('Y-m-d H:i:s');

            //判断电话重复
            $tel = trim($v['电话（必填）']);
            if (in_array($tel, $telInfo) || in_array($tel, $telInfoThis)) {
                $err++;
                $errMsg[] = $v['客户名称（必填）'] . '的手机号<font color="red">' . $tel . '</font>已存在，请勿重复添加';
                continue;
            }

            //获取负责人部门
            $usernamethis = trim($v['负责人（用户名）']);
            if (!empty($usernamethis)) {
                if (!in_array($usernamethis, $usernameArr)) {
                    $err++;
                    $errMsg[] = '负责人：<font color="red">' . $usernamethis . '</font>用户名不存在';
                    continue;
                }
                $team_id = $userTeamArr[$usernamethis];
                $get_mode =  ClientInfo::GET_DEFAULT;
                $get_time = date('Y-m-d H:i:s');
                $change_state =  ClientInfo::STATUS_DEFAULT;
            } else {
                //员工导给自己，经理导给部门，总监导给公共
                $team_id = $team_idme;
                $usernamethis = $role > 1 ? '' : $username;
                $get_mode =  $role > 1 ? '' : ClientInfo::GET_DEFAULT;
                $get_time =  $role > 1 ? '' : date('Y-m-d H:i:s');
                $change_state =  $role > 1 ?  ClientInfo::STATUS_OPEN : ClientInfo::STATUS_DEFAULT;
            }

            $data[] = $usernamethis;
            $data[] = $get_mode;
            $data[] = $get_time;
            $data[] = $change_state;
            $data[] = $team_id;
            $data[] = date('Y-m-d H:i:s', strtotime($v['联系时间（格式必须为yyyy/MM/dd）']));
            $newData[$k] = $data;
            $telInfoThis[$k] =  $tel;

            //逐条添加
            // $data['team_id'] = $team_id;
            // $data['add_mode'] = ClientInfo::ADD_BATCH;
            // $data['add_user'] = $username;
            // $data['agent_id'] = $agent_id;
            // $data['client_name'] = $v['客户名称（必填）'];
            // $data['contacts'] = trim($v['联系人（必填）']);
            // $data['tel'] = trim($v['电话（必填）']);
            // $data['email'] = trim($v['邮箱']);
            // $data['qq'] = trim($v['QQ号码']);
            // $data['establish_date'] = !empty($v['成立日期']) ? date('Y-m-d', strtotime(trim($v['成立日期']))) : '';;
            // $data['capital'] = trim($v['注册资本']);
            // $data['trade'] = trim($v['所属行业']);
            // $data['source'] = trim($v['客户来源']);
            // $data['province'] = trim($v['省份']);
            // $data['area'] = trim($v['地区']);
            // $data['address'] = trim($v['地址']);
            // $data['describe'] = trim($v['经营范围']);
            // $data['username1'] = !empty(trim($v['负责人（用户名）'])) ? trim($v['负责人（用户名）']) : $username;
            // $data['trace_time'] = !empty($v['联系时间（格式必须为yyyy/MM/dd）']) ? date('Y-m-d H:i:s', strtotime($v['联系时间（格式必须为yyyy/MM/dd）'])) : '';
            // $data['team_id'] = $team_id;

            // //添加客户
            // $add = ClientInfo::addclient($data);
            // if ($add['error'] > 0) {
            //     $err++;
            //     $errMsg .= $data['client_name'] . $add['error_msg'] . '\n';
            //     continue;
            // }
            // //添加负责人 添加客户记录
            // if (!empty($data['username1'])) {
            //     $model = new ClientTrace();
            //     $model->client_id = $add['data'];
            //     $model->trace_mode = $v['跟进方式'];
            //     // $model->phase = $request['phase'];
            //     $model->content = $v['最新联系内容'];
            //     // $model->work = $request['work'];
            //     $model->username = $data['username1'];
            //     // $model->next_time = $request['next_time'];
            //     // $model->next_mode = $request['next_mode'];
            //     // $model->next_content = $request['next_content'];
            //     $model->trace_time = date('Y-m-d H:i:s', strtotime($v['联系时间（格式必须为yyyy/MM/dd）']));
            //     $model->save();
            // }
        }
        // echo 1;
        // die;
        $col = [
            'agent_id', 'client_name', 'contacts', 'tel', 'email', 'qq', 'establish_date', 'capital', 'trade', 'source', 'province', 'area', 'address', 'describe',
            'add_user', 'add_mode', 'add_time', 'username1', 'get_mode', 'get_time', 'change_state', 'team_id', 'trace_time',
        ];
        $num = Yii::$app->db->createCommand()->batchInsert('client_info', $col, $newData)->execute();
        //$num['error-info']
        // var_dump($num);
        // die;
        if ($err > 0) {
            $successmsg = $num > 0 ? '添加成功<font color="red">' . $num . '</font>条;<br/>' : '';
            $errMsg = empty($errMsg) ? '' : implode('<br/>', array_unique($errMsg));
            return $this->error(1, $successmsg . '导入失败：<br/>' . $errMsg);
        } else {
            return $this->success('添加成功' . $num . '条');
        }
    }

    public function actionClientImport()
    {
        $role = Yii::$app->user->getIdentity()->role; //1员工 2经理 3总监
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $username = Yii::$app->user->getIdentity()->username;
        $team_idme = Yii::$app->user->getIdentity()->team_id;

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

        $code = $this->checkCode($filedata[0][0]);
        $chunkData = array_chunk($filedata, 5000, true); // 将这个10W+ 的数组分割成5000一个的小数组。这样就一次批量插入5000条数据。mysql 是支持的。
        $count = count($chunkData);

        //获取用户信息
        $userTeamArr = $this->getUserInfo();
        $telInfo = $this->getTelInfo();
        $telInfoThis = [];

        $err = 0;
        $errMsg = [];
        $total_success = 0;
        for ($i = 0; $i < $count; $i++) {
            $csv = $chunkData[$i];
            //当前文件内手机号集合
            $newData = [];
            foreach ($csv as $k => $v) {
                $contact = isset($v[1]) ? $v[1] : '';
                $clue_name = isset($v[0]) ? $v[0] : '';
                $tel = isset($v[2]) ? $v[2] : '';

                if (!$clue_name || !$contact || !$tel) {
                    $err++;
                    $errMsg[] = "第 <font color = \'red\'>" . ($k + 2) . "</font>条导入失败，数据缺失" . "/";
                    continue;
                }

                //判断电话重复
                if (isset($telInfo[$tel]) || isset($telInfoThis[$tel])) {
                    $err++;
                    $errMsg[] = $clue_name . '的手机号<font color="red">' . $tel . '</font>已存在，请勿重复添加';
                    continue;
                }

                //获取负责人部门
                $usernamethis = empty($v[13]) ? '' : trim($v[13]);
                if (!empty($usernamethis)) {
                    if (!isset($userTeamArr[$usernamethis])) {
                        $err++;
                        $errMsg[] = $clue_name . '的负责人：<font color="red">' . $usernamethis . '</font>用户名不存在';
                        continue;
                    }
                    $team_id = $userTeamArr[$usernamethis];
                    $get_mode =  ClientInfo::GET_DEFAULT;
                    $get_time = date('Y-m-d H:i:s');
                    $change_state =  ClientInfo::STATUS_DEFAULT;
                } else {
                    //员工导给自己，经理导给部门，总监导给公共
                    $team_id = $team_idme;
                    $usernamethis = $role > 1 ? '' : $username;
                    $get_mode =  $role > 1 ? '' : ClientInfo::GET_DEFAULT;
                    $get_time =  $role > 1 ? '' : date('Y-m-d H:i:s');
                    $change_state =  $role > 1 ?  ClientInfo::STATUS_OPEN : ClientInfo::STATUS_DEFAULT;
                }

                $data = [];
                $data[] = $agent_id;
                $data[] = $clue_name;
                $data[] = $contact;
                $data[] = $tel;
                $data[] = isset($v[3]) ? trim($v[3]) : "";
                $data[] = isset($v[4]) ? trim($v[4]) : "";
                $data[] = !empty($v[5]) ? date('Y-m-d', strtotime(trim($v[5]))) : '';;
                $data[] = isset($v[6]) ? trim($v[6]) : "";
                $data[] = isset($v[7]) ? trim($v[7]) : "";
                $data[] = isset($v[8]) ? trim($v[8]) : "";
                $data[] = isset($v[9]) ? trim($v[9]) : "";
                $data[] = isset($v[10]) ? trim($v[10]) : "";
                $data[] = isset($v[11]) ? trim($v[11]) : "";
                $data[] = isset($v[12]) ? trim($v[12]) : "";
                $data[] = $username;
                $data[] = ClientInfo::ADD_BATCH;
                $data[] = date('Y-m-d H:i:s');
                $data[] = $usernamethis;
                $data[] = $get_mode;
                $data[] = $get_time;
                $data[] = $change_state;
                $data[] = $team_id;
                $data[] = !empty($v[14]) ? date('Y-m-d H:i:s', strtotime($v[14])) : '';
                $newData[$k] = $data;
                $telInfoThis[$tel] = $tel;
            }

            $col = [
                'agent_id', 'client_name', 'contacts', 'tel', 'email', 'qq', 'establish_date', 'capital', 'trade', 'source', 'province', 'area', 'address', 'describe',
                'add_user', 'add_mode', 'add_time', 'username1', 'get_mode', 'get_time', 'change_state', 'team_id', 'trace_time',
            ];
            $num = Yii::$app->db->createCommand()->batchInsert('client_info', $col, $newData)->execute();
            $total_success += $num;
        }
        if ($err > 0) {
            $success = '成功导入' . $total_success . '条，';
            $errMsg = empty($errMsg) ? '' : implode("<br />", array_unique($errMsg));
            unlink($filename);
            return $this->error(1, $success . '导入失败：' . $errMsg);
        } else {
            unlink($filename);
            return $this->success('导入成功' . $total_success . '条');
        }
    }

    /**
     * 一次获取当前代理用户信息
     */
    private function getUserInfo()
    {
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $userinfo = User::find()->where(['agent_id' => $agent_id])->asArray()->all();
        $userTeamArr = [];
        foreach ($userinfo as $k => $v) {
            $userTeamArr[$v['username']] = $v['team_id'];
        }
        return $userTeamArr;
    }

    /**
     * 一次获取当前代理客户电话信息
     */
    private function getTelInfo()
    {
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $userinfo = ClientInfo::find()->select('tel')->where(['agent_id' => $agent_id])->asArray()->all();
        $telArr = [];
        foreach ($userinfo as $k => $v) {
            $telArr[$v['tel']] = $v['tel'];
        }
        return $telArr;
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
