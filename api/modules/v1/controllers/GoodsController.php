<?php

namespace api\modules\v1\controllers;

use yii\rest\ActiveController;
use Yii;
class GoodsController extends ActiveController
{

    public $modelClass = 'api\models\Goods';

    public function actionGoodsInfo()
    {
        $headers = Yii::$app->request->headers;
        return $headers;
    }
}
