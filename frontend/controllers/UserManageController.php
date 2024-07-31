<?php

namespace frontend\controllers;

use Yii;
use frontend\models\User;
use frontend\models\ClientMenu;
use common\framework\web\Controller;
use yii\data\Pagination;
use frontend\models\Team;
use frontend\models\Agent;
use frontend\models\ClientInfo;
use frontend\models\ClientLog;
use frontend\models\ClueInfo;
use frontend\models\ClueLog;

class UserManageController extends Controller
{
    public function actionList()
    {
        $post = Yii::$app->request->post();
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $role = Yii::$app->user->getIdentity()->role;
        $page_size = @$post['page_size'] ? $post['page_size'] : 10;
        $current_page = @$post['current_page'] ? $post['current_page'] : 1;
        $team_id = Yii::$app->user->getIdentity()->team_id;
        $post_role = @$post['role'] ? $post['role'] : null;
        $state = @$post['state'] ? $post['state'] : 1;

        $where = [];
        $where['a.agent_id'] = $agent_id;
        $field = 'a.id,a.username,a.state,a.password,a.role,a.phone,a.email,a.team_id,b.team_name';


        if (!empty(@$post['team_id'])) {
            $where['a.team_id'] = $post['team_id'];
        }
        if ($post_role) {
            $where['a.role'] = $post_role;
        }
        if ($state) {
            $where['a.state'] = $state;
        }

        //总监以下的人只能查看当前部门列表
        if ($role < 3) {
            $where['a.team_id'] = $team_id;
        }

        $query = User::find()->from('user a')->where($where)->join(
            'LEFT JOIN',
            'team b',
            'a.team_id=b.team_id'
        )->select($field);
        $query->andWhere(
            [
                '<=',
                'a.role',
                $role,
            ]
        );
        if (!empty(@$post['username'])) {
            $query->andWhere(['like', 'a.username', $post['username']]);
        }


        $count = $query->count();
        $pagination = new Pagination(
            [
                'defaultPageSize' => $page_size,
                'totalCount' => $count,
            ]
        );

        $pagination->setPage((int)$current_page - 1);

        $list = $query->offset($pagination->offset)->limit($pagination->limit)->orderBy(
            'a.role desc,a.id desc'
        )->asArray()->all();

        return $this->success(
            [
                'list' => $list,
                'totalCount' => intval($count),
            ]
        );
    }

    public function actionAdd()
    {
        $post = Yii::$app->request->post();
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $username = @$post['username'] ? $post['username'] : null; //登录账号
        $password = @$post['password'] ? strval($post['password']) : null; //登录密码
        $phone = @$post['phone'] ? $post['phone'] : '';
        $email = @$post['email'] ? $post['email'] : '';
        $role = @$post['role'] ? intval($post['role']) : 1; //级别
        $team_id = @$post['team_id'] ? $post['team_id'] : null; //部门ID
        $state = @$post['state'] ? $post['state'] : Team::STAT_OPEN;

        $user_model = new User();
        $user_model->scenario = User::SCENARIO_CREATE;

        $user_model->load($post, '');

        if (!$user_model->validate()) {
            return $this->error(5, array_values($user_model->getFirstErrors())[0]);
        }
        $result = $user_model->createUser($username, $password, $role, $team_id, $state, $phone, $email);
        if (!$result) {
            return $this->error(2, $user_model->errors);
        }
        return $this->success();
    }

    /**
     * 获取单个用户的资料
     */
    public function actionGet()
    {
        $post = Yii::$app->request->post();
        $login_user_id = Yii::$app->user->getIdentity()->id;
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $id = @$post['id'] ? $post['id'] : $login_user_id;

        if (!$id) {
            return $this->error(1, 'id 空');
        }
        $field = 'a.agent_id,a.id,a.username,a.email,a.phone,a.role,a.team_id,a.state,b.team_name';
        $result = User::find()->from("user a")->leftJoin(
            'team b',
            'a.team_id=b.team_id'
        )->where(['a.id' => $id, 'a.agent_id' => $agent_id])->select($field)->asArray()->one();
        return $this->success($result);
    }


    public function actionUpdatePass()
    {
        $post = Yii::$app->request->post();
        $login_user_id = Yii::$app->user->getIdentity()->id;
        $old_pass = @$post['old_pass'] ? $post['old_pass'] : null;
        $new_pass = @$post['new_pass'] ? $post['new_pass'] : null;
        if (empty($old_pass) || empty($new_pass)) {
            return $this->error(1, '参数错误');
        }


        $user = user::find()->where(['id' => $login_user_id])->one();
        //        echo $user->password;
        //        echo '----';
        //        echo md5($old_pass);die;
        if (md5($old_pass) != $user->password) {
            return $this->error(1, '旧密码错误');
        }
        $user_model = new User();

        $result = $user_model->updateUser($login_user_id, $new_pass);
        if (!$result) {
            return $this->error(2, $user_model->errors);
        }
        return $this->success();
    }

    public function actionUpdate()
    {
        $post = Yii::$app->request->post();
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $login_user_id = Yii::$app->user->getIdentity()->id;
        $login_user_role = Yii::$app->user->getIdentity()->role;
        $login_team_id = Yii::$app->user->getIdentity()->team_id;

        $id = @$post['id'] ? $post['id'] : null;
        $password = @$post['password'] ? $post['password'] : null;
        $role = @$post['role'] ? $post['role'] : 1;
        $email = @$post['email'] ? $post['email'] : null;
        $phone = @$post['phone'] ? $post['phone'] : null;
        //$team_id = @$post['team_id'] ? $post['team_id'] : null;

        $state = @$post['state'] ? $post['state'] : Team::STAT_OPEN;

        if (!$id) {
            return $this->error(1, 'id 参数缺失');
        }
        //员工只能改自己的
        if ($login_user_role == 1) {
            $id = $login_user_id;
            $role = $login_user_role;
            //$team_id = $login_team_id;
        }
        $user_model = new User();
        $user_model->scenario = User::SCENARIO_UPDATE;
        $user_model->load($post, '');

        if (!$user_model->validate()) {
            return $this->error(5, array_values($user_model->getFirstErrors())[0]);
        }
        $result = $user_model->updateUser($id, $password, $role, $phone, $email, $state);
        if (!$result) {
            return $this->error(2, $user_model->errors);
        }
        return $this->success();
    }

    public function actionMenu()
    {
        $role = Yii::$app->user->getIdentity()->role;
        $where = [];

        $where['type'] = 2;
        $result = ClientMenu::find()->where($where)->andWhere(['<=', 'level', $role])->all();
        return $this->success($result);
    }

    /*
     * 26211直接给某公司添加员工
     */
    public function actionAddAgentUser()
    {
        $post = Yii::$app->request->post();
        $agent_id = @$post['agent_id'] ? $post['agent_id'] : null; //选择公司;
        $username = @$post['username'] ? $post['username'] : null; //登录账号
        $password = @$post['password'] ? $post['password'] : null; //登录密码
        $phone = @$post['phone'] ? $post['phone'] : '';
        $email = @$post['email'] ? $post['email'] : '';
        $role = @$post['role'] ? intval($post['role']) : 1; //级别
        $team_id = @$post['team_id'] ? $post['team_id'] : null; //部门ID
        $state = @$post['state'] ? $post['state'] : Team::STAT_OPEN;

        $user_model = new User();
        $user_model->scenario = User::SCENARIO_CREATE;
        $user_model->load($post, '');

        if (!$user_model->validate()) {
            return $this->error(5, array_values($user_model->getFirstErrors())[0]);
        }
        $result = $user_model->createUser($username, $password, $role, $team_id, $state, $phone, $email, $agent_id);
        if (!$result) {
            return $this->error(3, $user_model->errors);
        }
        return $this->success();
    }

    /*
    * 公司代理号列表
    */
    public function actionAgentList()
    {
        $list = Agent::find()->where(['state' => 1])->asArray()->all();
        return $this->success($list);
    }

    /**
     * 公司代理号列表
     */
    public function actionAgentListAll()
    {
        $request = Yii::$app->request->post();
        $pagenum = isset($request['current_page']) ? ($request['current_page'] - 1) : 0; // 0页开始
        $page_size = isset($request['page_size']) ? $request['page_size'] : 10; // 0页开始
        $agent_id = isset($request['agent_id']) ? $request['agent_id'] : '';
        $state = isset($request['state']) ? $request['state'] : '';

        $query = Agent::find();
        if ($agent_id) {
            $query->andWhere(['agent_id' => $agent_id]);
        }

        if ($state) {
            $query->andWhere(['state' => $state]);
        }

        // 得到总数（但是还没有从数据库取数据）
        $count = $query->count();
        // 使用总数来创建一个分页对象
        $pagination = new Pagination(
            [
                'defaultPageSize' => $page_size,
                'totalCount' => $count,
            ]
        );

        $pagination->setPage($pagenum); // 设置页数，从0开始

        // 使用分页对象来填充 limit 子句并取得数据
        $ininfo = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy('id desc')
            ->asArray()
            ->all();

        return $this->success(
            [
                'info' => $ininfo,
                'totalCount' => intval($count),
            ]
        );
    }

    /**
     * 新增代理
     */
    public function actionAddAgent()
    {
        $request = Yii::$app->request->post();
        $agent_id = isset($request['agent_id']) ? trim($request['agent_id']) : '';
        $agent_name = isset($request['agent_name']) ? trim($request['agent_name']) : '';

        // 去重
        $model = Agent::find()->where(
            [
                'agent_id' => $agent_id,
            ]
        )
            ->one();

        if (!empty($model)) {
            return $this->error(1, '代理号已存在');
        }

        $model = Agent::find()->where(
            [
                'agent_name' => $agent_name,
            ]
        )
            ->one();

        if (!empty($model)) {
            return $this->error(1, '代理名称已存在');
        }

        $model = new Agent();
        $model->agent_id = $agent_id;
        $model->agent_name = $agent_name;

        if ($model->save() > 0) {
            return $this->success('添加成功');
        } else {
            return $this->error(1, json_encode($model->errors));
        }
    }

    /**
     * 修改代理
     */
    public function actionUpAgent()
    {
        $request = Yii::$app->request->post();
        $agent_id = isset($request['agent_id']) ? trim($request['agent_id']) : '';

        // 获取客户信息
        $model = Agent::find()->where(
            [
                'agent_id' => $agent_id,
            ]
        )
            ->one();

        if (empty($model)) {
            return $this->error(1, '数据不存在');
        }
        if (!empty($request['agent_name'])) {
            $model->agent_name = trim($request['agent_name']);
        }
        if (!empty($request['state'])) {
            $model->state = trim($request['state']);
        }
        if (!empty($request['clue_num'])) {
            $model->clue_num = trim($request['clue_num']);
        }
        if (!empty($request['client_num'])) {
            $model->client_num = trim($request['client_num']);
        }
        if ($model->save() > 0) {
            return $this->success('修改成功');
        } else {
            return $this->error(1, json_encode($model->errors));
        }
    }

    /**
     * 获取代理部门信息
     */
    public function actionAgentTeam()
    {
        $request = Yii::$app->request->post();
        $agent_id = isset($request['agent_id']) ? trim($request['agent_id']) : '';
        $list = Team::find()->where(['agent_id' => $agent_id, 'state' => 1])->asArray()->all();
        return $this->success($list);
    }

    /**
     * 统计数
     */
    public function actionTotalNum()
    {
        $request = Yii::$app->request->post();
        $username = isset($request['username']) ? $request['username'] : Yii::$app->user->getIdentity()->username;
        $timetype = isset($request['timetype']) ? $request['timetype'] : '1'; //默认当天
        //权限验证
        $canSee = user::userRights($username);
        if (!$canSee) {
            return $this->error(1, '无权查看该用户信息');
        }

        //线索相关
        $empty = ClueInfo::getClueMarkList($username, $timetype, ClueInfo::MARK_EMPTY);
        $break = ClueInfo::getClueMarkList($username, $timetype, ClueInfo::MARK_BREAK);
        $miss = ClueInfo::getClueMarkList($username, $timetype, ClueInfo::MARK_MISSED);
        $empty_num = $empty['count']; //空号数
        $break_num = $break['count']; //挂断数
        $miss_num = $miss['count']; //未接听

        $pick = ClueLog::getClueLog($username, $timetype, ClueLog::OPT_PICK);
        $assign = ClueLog::getClueLog($username, $timetype, ClueLog::OPT_ASSIGN);
        $open = ClueLog::getClueLog($username, $timetype, ClueLog::OPT_OPEN);
        $turn = ClueLog::getClueLog($username, $timetype, ClueLog::OPT_TURN);
        $trace = ClueInfo::getCallClueList($username, $timetype);
        $pick_num = $pick['count']; //提取数
        $assign_num = $assign['count']; //指派数
        $open_num = $open['count']; //释放数
        $turn_num = $turn['count']; //线索转化为客户数
        $ctrace_num = $trace['count']; //联系线索数
        $clue_total = ClueInfo::find()->where(['username' => $username])->count(); //已有线索数

        //客户相关
        $cTrace = ClientInfo::clientTrace($username, $timetype); //已联系客户
        $trace_num = $cTrace['count'];
        $cPick = ClientLog::getClientLog($username, $timetype, ClientLog::OPT_PICK); //已提取客户
        $cpick_num = $cPick['count'];
        $cAssign = ClientLog::getClientLog($username, $timetype, ClientLog::OPT_ASSIGN); //已被指派客户
        $cassign_num = $cAssign['count'];
        $cOpen = ClientLog::getClientLog($username, $timetype, ClientLog::OPT_OPEN); //已释放客户
        $copen_num = $cOpen['count'];
        $client_total = ClientInfo::find()->where(
            [
                'or',
                ['username1' => $username],
                ['username2' => $username],
            ]
        )->count(); //已有客户数

        $totalNum = [
            'pick_num' => $pick_num,
            'assign_num' => $assign_num,
            'open_num' => $open_num,
            'turn_num' => $turn_num,
            'empty_num' => $empty_num,
            'break_num' => $break_num,
            'miss_num' => $miss_num,
            'trace_num' => $trace_num,
            'cpick_num' => $cpick_num,
            'cassign_num' => $cassign_num,
            'copen_num' => $copen_num,
            'ctrace_num' => $ctrace_num,
            'clue_total' => $clue_total,
            'client_total' => $client_total
        ];
        return $this->success($totalNum);
    }
}
