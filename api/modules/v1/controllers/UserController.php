<?php

namespace api\modules\v1\controllers;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;
use yii\filters\auth\QueryParamAuth;
use common\models\User;
use api\models\LoginForm;
use Yii;

class UserController extends ActiveController
{
    public $modelClass = 'api\models\User';

    public function behaviors() {
        return ArrayHelper::merge (parent::behaviors(), [
            'authenticator' => [
                'class' => QueryParamAuth::className(),
                'tokenParam' => 'token',
                'optional' => [
                    'login',
                    'signup-test'
                ],
            ]
        ] );
    }

    /**
     * sing-up-test
     */
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
    public function actionUserProfile ($token)
    {
        // 到这一步，token都认为是有效的了
        // 下面只需要实现业务逻辑即可，下面仅仅作为案例，比如你可能需要关联其他表获取用户信息等等
        $user = User::findIdentityByAccessToken($token);
        return [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
        ];
    }
}
