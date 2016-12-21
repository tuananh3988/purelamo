<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use common\models\Notification;
use common\models\Devices;
/**
 * Site controller
 */
class NoticeController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['list', 'setting'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'setting' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionList()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $getData = $request->get();
        $limit = empty($getData['limit']) ? Yii::$app->params['notice_limit'] : $getData['limit'];
        $offset = empty($getData['offset']) ? Yii::$app->params['offset'] : $getData['offset'];
        
        $notify = Notification::find()->where(['status' => 1, 'delete_flag' => 0])->limit($limit)->offset($offset)->all();
        $count = Notification::find()->where(['status' => 1, 'delete_flag' => 0])->count();
        $data = [];
        foreach ($notify as $n) {
            $data[] = [
                'notice_id' => $n['id'],
                'subject' => $n['subject'],
                'message' => $n['message'],
                'date' => $n['reserve_date'],
            ];
        }
        
        return [
            'success' => 1,
            'count' => $count,
            'offset' => $offset + (count($notify) < $limit ? count($notify) : $limit),
            'data' => $data
        ];
    }
    
    public function actionSetting()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $postData = $request->post();
        if (empty($postData['type']) || empty($postData['device_id']) || empty($postData['type_notify'])) {
            return [
                'success' => 0,
                'mgs' => 'Param invalid.'
            ];
        }
        
        $device = Devices::findOne(['type' => $postData['type'], 'device_id' => $postData['device_id']]);
        
        if (!$device) {           
            return [
                'success' => 0,
                'mgs' => 'Device id not register.'
            ];
        }
        else {
            $device->type_time_recieve_notify = $postData['type_notify'];
            if ($device->save()) {
                return [
                    'success' => 1,
                ];
            }

            return [
                'success' => 0,
                'mgs' => 'Have error validate'
            ];
        }
        
    }


}
