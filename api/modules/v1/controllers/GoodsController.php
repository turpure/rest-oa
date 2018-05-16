<?php

namespace api\modules\v1\controllers;

use yii\rest\ActiveController;
use Yii;
use api\modules\v1\controllers\AdminController;
class GoodsController extends AdminController
{

    public $modelClass = 'api\models\Goods';

    public function actionGoodsInfo()
    {
        $headers = Yii::$app->request->headers;
        return $headers;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        $user = $this->authenticate(Yii::$app->user, Yii::$app->request, Yii::$app->response);
        $userId = $user?$user->getId():'';
        if ($userId == 6)
        {
            throw new \yii\web\ForbiddenHttpException("No permiession!");
        }
    }
}
