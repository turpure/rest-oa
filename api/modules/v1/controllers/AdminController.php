<?php
/**
 * @desc PhpStorm.
 * @author: turpure
 * @since: 2018-05-10 17:32
 */

namespace api\modules\v1\controllers;

use common\models\User;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\Cors;
use yii\base\UserException;


class AdminController extends ActiveController
{
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
                ['class' => HttpBearerAuth::className()],
                ['class' => QueryParamAuth::className(),'tokenParam' => 'token',],

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

    /*
     * basic-auth auth
     */
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