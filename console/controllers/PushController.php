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
        $notify = Notification::find()->where(['status' => 0, 'delete_flag' => 0])->andWhere('reserve_date <= "' . date('Y-m-d H:i:s') . '"')->all();
        foreach ($notify as $n) {
            $res = [
                'data' => [
                    'title' => $n['subject'],
                    'message' => $n['message'],
                    'timestamp' => date('Y-m-d G:i:s'),
                ]
            ];
        }
        var_dump($notify[0]->id);
        echo 'a';
        return 0;
    }

}