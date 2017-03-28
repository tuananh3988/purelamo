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
            $reserveDate = date("H:i:s", strtotime($n->reserve_date));
            
            $timeType = 3;
            if ($reserveDate < "12:00:00") {
                $timeType = 2;
            }
            
            Yii::info('Notification id: ' . $n->id . ' start', 'push'); 
            $res = [
                'data' => [
                    'title' => $n['subject'],
                    'message' => $n['message'],
                    'timestamp' => date('Y-m-d G:i:s'),
                ]
            ];
            
            $devices = Devices::find()->where()->all();
            $regisAosIds = [];
            $regisIosIds = [];
            foreach ($devices as $d) {
                if ($d['type_time_recieve_notify'] != 1 &&  $d['type_time_recieve_notify'] != $timeType) {
                    continue;
                }
                
                if ($d['type'] === 1) {
                    if (preg_match('~^[a-f0-9]{64}$~i', $i['device_token'])) {
                        $regisIosIds[] = $i['device_token'];
                    }
                }
                else {
                    $regisAosIds[] = $a['device_token'];
                }
            }
            
            //send aos
            Yii::info('send aos start', 'push');
            if (!empty($regisIds)) {
                $firebase = new Firebase();
                $result = $firebase->sendMultiple($regisIds, $res);
            }

            //send ios
            Yii::info('send ios start', 'push');
            if (!empty($regisIds)) {
                $apns = Yii::$app->apns;
                $mgs = $apns->sendMulti($regisIds, $n['message'],
                    [
                      'customProperty' => 'Hello',
                    ],
                    [
                      'sound' => 'default',
                      'badge' => 1
                    ]
                );

                Yii::info('log ios: ', 'push');
                Yii::info($mgs, 'push');
            }
            
            //update status notification.status = 1
            $n->status = 1;
            $n->last_update_date = date('Y-m-d H:i:s');
            $n->save(false);
            Yii::info('update notification.status = 1, done', 'push');
        }
        
        return 0;
    }

}