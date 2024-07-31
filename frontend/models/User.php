<?php

namespace frontend\models;

use Yii;
use yii\db\Exception;
use frontend\models\Team;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $agent_id
 * @property string $username 用户名
 * @property string $password 密码
 * @property string $name 员工姓名
 * @property int $role 角色/级别  1员工 2经理 3总监
 * @property string|null $phone 手机号
 * @property string|null $email 邮箱
 * @property int|null $team_id 部门id
 * @property int $state 1启用 -1关闭
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    public $errors;
    public $enableAutoLogin = true;
    public $authKey = '';

    const  state = 1; //启用状态

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['username', 'role', 'team_id', 'state', 'phone', 'email', 'password'];
        $scenarios[self::SCENARIO_UPDATE] = ['id', 'team_id', 'role', 'state', 'phone', 'email', 'password'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', 'required', 'message' => 'id不能为空'],
            ['username', 'required', 'message' => '用户名不能为空', 'on' => self::SCENARIO_CREATE],
            ['password', 'required', 'message' => '密码不能为空', 'on' => self::SCENARIO_CREATE],
            ['role', 'required', 'message' => '必选级别', 'on' => self::SCENARIO_CREATE],
            ['team_id', 'required', 'message' => '必须选部门', 'on' => self::SCENARIO_CREATE],

            [['role', 'team_id', 'state'], 'integer'],
            [
                'agent_id',
                'string',
                'max' => 20,
                'min' => 5,
                'tooLong' => '代理号长度不能超过20个字符',
                'tooShort' => '代理号长度不能少于5个字符'
            ],
            [
                'username',
                'string',
                'max' => 20,
                'min' => 2,
                'tooLong' => '用户名长度不能超过20个字符',
                'tooShort' => '用户名长度不能少于2个字符'
            ],
            [
                'phone',
                'string',
                'max' => 11,
                'min' => 6,
                'tooLong' => '电话长度不能超过11个字符',
                'tooShort' => '电话长度最少需要6个字符',
            ],
            [
                'password',
                'string',
                'max' => 100,
                'min' => 6,
                'tooLong' => '密码长度不能超过100个字符',
                'tooShort' => '密码长度最少需要6个字符',
            ],
            ['email', 'email', 'message' => '邮箱格式错误'],
            ['role', 'in', 'range' => [1, 2, 3, 4], 'message' => '级别值错误'],
            ['state', 'in', 'range' => [1, -1], 'message' => '启用值错误'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '用户ID',
            'agent_id' => '代理号',
            'username' => '用户名',
            'password' => '密码',
            'role' => '级别',
            'phone' => '电话',
            'email' => '邮箱',
            'team_id' => '部门ID',
            'state' => '状态',
        ];
    }

    public function authLogin($password)
    {
        if (is_null($this->username)) {
            return false;
        }
        $userinfo = self::find()->where(['username' => $this->username, 'state' => self::state])->one();
        if (is_null($userinfo)) {
            return false;
        }
        if ($userinfo->password != md5($password)) {
            return false;
        }

        return $userinfo;
    }

    public function createUser($username, $password, $role, $team_id, $state, $phone, $email, $agent = null)
    {
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        if ($agent) {
            $agent_id = $agent;
        }
        //检测账号重复
        $check_username = self::find()->where(['username' => $username])->one();
        if (!empty($check_username)) {
            $this->errors = '账号已存在！';
            return false;
        }
        //检测team_id
        $check_team_id = Team::find()->where(['team_id' => $team_id, 'agent_id' => $agent_id])->one();
        if (empty($check_team_id)) {
            $this->errors = '部门不存在！';
            return false;
        }

        $this->username = $username;
        $this->password = md5($password);
        $this->agent_id = $agent_id;
        $this->role = $role;
        $this->team_id = $team_id;
        $this->state = $state;
        $this->phone = $phone;
        $this->email = $email;
        if (!self::insert()) {
            $this->errors = json_encode($this->getErrors());
            return false;
        }
        return true;
    }

    public function updateUser(
        $id,
        $password = '',
        $role = '',
        $phone = '',
        $email = '',
        $state = ''
    ) {
        $agent_id = Yii::$app->user->getIdentity()->agent_id;
        $user_info = self::find()->where(['id' => $id, 'agent_id' => $agent_id])->one();
        $user_info->scenario = self::SCENARIO_UPDATE;
        if (empty($user_info)) {
            $this->errors = '非法id';
            return false;
        }

        if (!empty($email)) {
            $user_info->email = $email;
        }
        if (!empty($phone)) {
            $user_info->phone = $phone;
        }
        if (!empty($role)) {
            $user_info->role = $role;
        }

        if (!empty($password) && $password != $user_info->password) {
            $user_info->password = md5($password);
        }
        if (!empty($state)) {
            $user_info->state = $state;
        }

        //        if (!empty($username) && ($username != $user_info->username)) //检测账号重复
        //        {
        //            $check_username = self::find()->where(['username' => $username])->one();
        //            if (!empty($check_username)) {
        //                $this->errors = '账号已存在！';
        //                return false;
        //            }
        //            $user_info->username = $username;
        //        }

        //        if (!empty($name) && ($name != $user_info->name)) {
        //            //检测员工名字
        //            $check_name = self::find()->where(['name' => $name, 'agent_id' => $agent_id])->one();
        //            if (!empty($check_name)) {
        //                $this->errors = '员工名字已存在！';
        //                return false;
        //            }
        //            $user_info->name = $name;
        //        }
        //        if (!empty($team_id) && ($team_id != $user_info->team_id)) {
        //            //检测team_id
        //            $check_team_id = Team::find()->where(['team_id' => $team_id, 'agent_id' => $agent_id])->one();
        //            if (empty($check_team_id)) {
        //                $this->errors = '部门不存在！';
        //                return false;
        //            }
        //            if ($check_team_id->state != Team::STAT_OPEN) {
        //                if (empty($check_team_id)) {
        //                    $this->errors = '部门状态为' . $check_team_id->state;
        //                    return false;
        //                }
        //            }
        //            $user_info->team_id = $team_id;
        //        }

        if (!$user_info->save()) {
            $this->errors = json_encode($this->getErrors());
            return false;
        }
        return true;
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw  new Exception('no token');
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * 用户数据查看权限验证
     * 员工看自己
     * 经理看部门
     * 总监看全部
     * username 要查看的用户名
     */
    public static function userRights($username = '')
    {
        $usernameMy = Yii::$app->user->getIdentity()->username; //登录用户
        $role = Yii::$app->user->getIdentity()->role; //1员工 2经理 3总监
        $team_id = Yii::$app->user->getIdentity()->team_id; //部门ID

        //获取查询员工信息
        $userinfo = self::findOne(['username' => $username]);
        if (empty($userinfo)) {
            return false;
        }

        if ($role == 1 && $usernameMy !== $username) {
            return false;
        } elseif ($role == 2 && $userinfo->team_id != $team_id) {
            return false;
        } else {
            return true;
        }
    }
}
