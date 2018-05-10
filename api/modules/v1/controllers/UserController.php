<?php

namespace api\modules\v1\controllers;

use api\models\LoginForm;
use api\models\SignupForm;
use Yii;
use api\modules\v1\controllers\AdminController;

class UserController extends AdminController
{
    public $modelClass = 'api\models\User';


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


}
