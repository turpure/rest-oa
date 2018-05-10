<?php

namespace api\modules\v1\controllers;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use common\models\User;
use api\models\LoginForm;
use api\models\SignupForm;
use Yii;
use yii\filters\Cors;
use yii\base\UserException;

class UserController extends ActiveController
{
    public $modelClass = 'api\models\User';

    public function behaviors()
        {
            $behaviors = ArrayHelper::merge([
                [
                    'class' => Cors::className(),
                ]
            ],
                parent::behaviors()
            );

            $behaviors['authenticator'] = [
                'class' => CompositeAuth::className(),

                'authMethods' => [
                    ['class' => HttpBasicAuth::className(),'auth' => [$this, 'auth']],
//                    ['class' => HttpBearerAuth::className()],
//                    ['class' => QueryParamAuth::className(),'tokenParam' => 'token',],

                ],
                'optional' =>[
                    'login',
                    'signup'
                ]
            ];
            return $behaviors;
        }

    public function actions()
    {
        $actions =  parent::actions();
        return $actions;
    }
    /**
     * sing-up-test
     */
    /*
    public function actionSignupTest ()
    {
        $request = Yii::$app->request;
        if(!$request->isPost)
        {
            return [
                'code' => '404'
            ];
        }
        $post = $request->post();
        $user = new User();
        $user->generateAuthKey();
        $user->generateApiToken();
        $user->setPassword($post['password']);
        $user->username = $post['username'];
        $user->email = $post['email'];
        $user->save(false);

        return [
            'code' => 0
        ];
    }
*/
    /**
     * sing up
     */
    public function actionSignup ()
    {
        $model = new SignupForm();
        $model->setAttributes(Yii::$app->request->post());
        if($model->signup()){
            return [];
        }
        return $model->errors;
    }
    /**
     * login
     */
    public function actionLogin ()
    {
        $model = new LoginForm;
        $model->setAttributes(Yii::$app->request->post());
        if ($user = $model->login()) {
            if ($user instanceof IdentityInterface) {
                return $user->access_token;
            } else {
                return $user->errors;
            }
        } else {
            return $model->errors;
        }
    }

    /**
     * 获取用户信息
     */
    public function actionUserProfile ()
    {
        // 到这一步，token都认为是有效的了
        // 下面只需要实现业务逻辑即可，下面仅仅作为案例，比如你可能需要关联其他表获取用户信息等o等
        /* get user by token
        $token = Yii::$app->request->get()['token'];
        $user = User::findIdentityByAccessToken($token);
        */
        // get user by authenticating
        $user = $this->authenticate(Yii::$app->user, Yii::$app->request, Yii::$app->response);
        return [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
        ];
    }

    /**
     * 获取用户信息
     */
    public function actionInfo ()
    {

        $user = $this->authenticate(Yii::$app->user, Yii::$app->request, Yii::$app->response);
        return [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
        ];
    }

    public function auth($username, $password)
    {
        $user = User::findByUsername($username);
        if(empty($username) || empty($password) || empty($user)) {
            //return false;
            //OR
            throw new UserException("There is an error!");
        }
        if ($user->validatePassword($password)) {
            return $user;
        }
        //return false;
        //OR
        throw new UserException("Wrong username or password!");
    }
}
