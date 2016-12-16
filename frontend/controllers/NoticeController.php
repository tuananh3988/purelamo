<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use common\models\Notification;
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
                        'actions' => ['list', 'detail'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'category' => ['post'],
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
    
    public function actionDetail()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'success' => 1,
        ];
    }


}
