<?php
/**
 * Link:
 * Created by PhpStorm.
 * User: shizj
 * DateTime: 2019/11/27 2:30 下午
 * Describe:
 */

namespace common\framework\web;


use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use frontend\models\ClientMenu;

class Controller extends \yii\web\Controller
{

    public $enableCsrfValidation = false;

    /**
     * @param $action
     *
     * @return bool
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $arr = [
            'login',
            'register',
            'bill-pay',
            'site',
        ];

        if (!parent::beforeAction($action)) {
            return false;
        }

        if (!in_array(Yii::$app->controller->id, $arr)) {
            if (Yii::$app->getUser()->getIsGuest()) {
                Yii::$app->getResponse()->data = $this->needLogin();
                return false;
            }
            $request_path = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
            $menu_result = ClientMenu::find()->where(['route' => $request_path])->one();
            if (empty($menu_result)) {
                Yii::$app->getResponse()->data = $this->error(1, '未找到路由');
                return false;
            }

            if (Yii::$app->user->getIdentity()->role < $menu_result->level) {
                Yii::$app->getResponse()->data = $this->error(2000, '无权访问');
                return false;
            }

            return true;
        }
        return true;
    }


    public function error($code, $message = '')
    {
        return [
            'error' => $code,
            'error_msg' => $message,
            'data' => '',
        ];
    }

    public function success($data = [])
    {
        return [
            'error' => 0,
            'error_msg' => '',
            'data' => $data,
        ];
    }

    public function needLogin()
    {
        return $this->error(1000, '请登录');
    }

}
