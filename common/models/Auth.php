<?php
/**
 * @desc PhpStorm.
 * @author: Administrator
 * @since: 2018-05-11 11:27
 */

namespace common\models;
use filsh\yii2\oauth2server\Module;
use OAuth2\Storage\UserCredentialsInterface;
use common\models\User;
use Yii;
use yii\web\IdentityInterface;

class Auth extends User implements UserCredentialsInterface
{

    /**
     * Implemented for Oauth2 Interface
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        /** @var \filsh\yii2\oauth2server\Module $module */
        $module = Yii::$app->getModule('oauth2');
        $token = $module->getServer()->getResourceController()->getToken();
        return !empty($token['user_id'])
            ? static::findIdentity($token['user_id'])
            : null;
    }

    /**
     * Implemented for Oauth2 Interface
     */
    public function checkUserCredentials($username, $password)
    {
        $user = static::findByUsername($username);
        if (empty($user)) {
            return false;
        }
        return $user->validatePassword($password);
    }

    /**
     * Implemented for Oauth2 Interface
     */
    public function getUserDetails($username)
    {
        $user = static::findByUsername($username);
        return ['user_id' => $user->getId()];
    }
}