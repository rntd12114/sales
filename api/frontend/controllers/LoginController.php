<?php
/**
 * User: chenzhp
 * Date: 2019-11-27
 * Time: 13:22
 */

namespace frontend\controllers;

use frontend\models\Team;
use Yii;
use common\framework\web\Controller;
use frontend\models\User;
use frontend\models\Agent;

class LoginController extends Controller
{
    public $enableCsrfValidation = false;

    const REDIS_QR_PRE = 'pd_login_qr_';


    public function actionLogin()
    {
        $param = Yii::$app->request->post();

        $username = (isset($param['username'])) ? $param['username'] : "";
        $password = isset($param['password']) ? trim($param['password']) : "";

        if (empty($username) || empty($password)) {
            return $this->error(1, '参数空');
        }
        $userModel = new User();
        $userModel->setAttribute('username', $username);
        $user_info = $userModel->authLogin($password);
        if (!$user_info) {
            return $this->error('-1', '用户名密码错误');
        }
        Yii::$app->user->login($user_info, true);
        $team_result = Team::find()->where(['team_id' => $user_info->team_id])->one();
        $agent_result = Agent::find()->where(['agent_id'=>$user_info->agent_id])->one();
        $user_info = $user_info->toArray();
        $user_info['team_name'] = @$team_result->team_name;
        $user_info['agent_name'] = @$agent_result->agent_name;
        return $this->success(['user_info' => $user_info]);
    }


    public function actionOut()
    {
        Yii::$app->user->logout();
        return $this->success();
    }

    public function actionTestCreate()
    {
        $request = Yii::$app->request->post();
        $name = $request['username'];
        $passwd = $request['password'];
        $userModel = new User();
        $result = $userModel->createUser($name, $passwd);
        if (!$result) {
            return $this->error(1, $userModel->errors);
        }
        return $this->success();

    }
}
