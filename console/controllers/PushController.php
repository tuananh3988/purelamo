<?php

namespace console\controllers;
use Yii;
use common\models\Notification;
use common\components\Firebase;
use common\models\Devices;

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
            
            //send aos
            $aos = Devices::find()->where(['type' => 2])->all();
            $regisIds = [];
            foreach ($aos as $a) {
                $regisIds[] = $a['device_token'];
            }
            
            
            $firebase = new Firebase();
            $result = $firebase->sendMultiple($regisIds, $res);
        }
        
        
        var_dump($result);
        echo 'a';
        return 0;
    }

}