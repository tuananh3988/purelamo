<?php

namespace console\controllers;
use Yii;
use common\models\Notification;

class PushController extends \yii\console\Controller
{
    
    /*
     * Calculating revenue for user
     */
    public function actionIndex()
    {
        $notify = Notification::find()->where(['status' => 1, 'delete_flag' => 0])->andWhere('reserve_date > ' . date())
        echo 'a';
        return 0;
    }

}