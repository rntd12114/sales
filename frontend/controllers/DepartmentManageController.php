<?php

namespace frontend\controllers;

use frontend\models\Team;
use frontend\models\ClientMenu;
use common\framework\web\Controller;
use Yii;

class DepartmentManageController extends Controller
{
    public function actionList()
    {
        $post = Yii::$app->request->post();

        $team = new Team();
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $role = Yii::$app->user->getIdentity()->role;
        $team_id = Yii::$app->user->getIdentity()->team_id;

        $where = [];
        $where['agent_id'] = $agent_id;

        if (!empty($post['name'])) {
            $where['name'] = $post['name'];
        }
        if ($role < 3) {
            $where['team_id'] = $team_id;
        }

        $result = Team::find()->where($where)->all();
        return $this->success($result);
    }

    public function actionAdd()
    {
        $post = Yii::$app->request->post();
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $name = @$post['team_name'] ? $post['team_name'] : null;
        $state = @$post['state'] ? $post['state'] : Team::STAT_OPEN;

        $team_model = new Team();
        $team_model->scenario = Team::SCENARIO_CREATE;

        $team_model->load($post, '');
        if (!$team_model->validate()) {
            return $this->error(1, array_values($team_model->getFirstErrors())[0]);
        }
        $where = [];
        $where['team_name'] = $name;
        $where['agent_id'] = $agent_id;

        $result = Team::find()->where($where)->one();
        if (!empty($result)) {
            return $this->error(2, '部门名称已存在！');
        }
        $team_model->team_name = $name;
        $team_model->state = $state;
        $team_model->agent_id = $agent_id;
        if (!$team_model->save()) {
            return $this->error(3, json_encode($team_model->getErrors()));
        }
        return $this->success();
    }

    public function actionUpdate()
    {
        $post = Yii::$app->request->post();
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $team_id = @$post['team_id'] ? $post['team_id'] : null;
        $team_name = @$post['team_name'] ? $post['team_name'] : null;
        $state = @$post['state'] ? $post['state'] : Team::STAT_OPEN;

        $team_model = new Team();
        $team_model->scenario = Team::SCENARIO_UPDATE;

        $team_model->load($post, '');
        if (!$team_model->validate()) {
            return $this->error(1, array_values($team_model->getFirstErrors())[0]);
        }
        $where = [];
        $where['team_id'] = $team_id;
        $where['agent_id'] = $agent_id;


        $result = Team::find()->where($where)->one();
        if (!$result) {
            return $this->error(2, '此ID非法！');
        }
        $check_result = Team::find()->where(['agent_id' => $agent_id, 'team_name' => $team_name])->andWhere(
            [
                '<>',
                'team_id',
                $team_id,
            ]
        )->one();
        if (!empty($check_result)) {
            return $this->error(3, '该部门已存在');
        }
        $result->team_name = $team_name;
        $result->state = $state;
        if (!$result->save()) {
            return $this->error(3, json_encode($team_model->getErrors()));
        }
        return $this->success();
    }


    public function actionMenu()
    {
        $role = Yii::$app->user->getIdentity()->role;
        $where = [];

        $where['type'] = 1;
        $result = ClientMenu::find()->where($where)->andWhere(['<=', 'level', $role])->all();
        return $this->success($result);
    }
}
