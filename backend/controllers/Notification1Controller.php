<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;



class Notification1Controller extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['save' ,'index', 'detail', 'search', 'change-pass'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                //'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * List user
     *
     * @date : 28/05/2016
     *
     */
    public function actionIndex() {
        $request = Yii::$app->request;
        $formSearch = new Users();
        $param = $request->queryParams;
        if (!empty($param['Users'])) {
            $formSearch->setAttributes($param['Users']);
        }
        $dataProvider = $formSearch->getData();
        return $this->render('index', ['dataProvider' => $dataProvider, 'formSearch' => $formSearch]);
    }
    /**
     * Detail user
     *
     * @date : 28/05/2016
     *
     */
    public function actionDetail($userId) {
        $model = new Users();
        $userItem = $model->findOne(['id' => $userId]);
        if (empty($userItem)) {
            Yii::$app->response->redirect(['/error/error']);
        }
        return $this->render('detail', ['userItem' => $userItem]);
    }
    /**
     * Action save
     *
     * @date : 27/05/2016
     *
     */
    public function actionSave($userId = null) {
        $request = Yii::$app->request;
        $user = new Users();
        $banks = [
            'bank1' => new Banks(),
            'bank2' => new Banks(),
            'bank3' => new Banks(),
            'bank4' => new Banks(),
        ];
        
        $flag = 0;
        if (!empty($userId)) {
            $bank1 = Banks::findOne(['user_id' => $userId, 'type' => 1]);
            $bank2 = Banks::findOne(['user_id' => $userId, 'type' => 2]);
            $bank3 = Banks::findOne(['user_id' => $userId, 'type' => 3]);
            $bank4 = Banks::findOne(['user_id' => $userId, 'type' => 4]);
            $bank1 = ($bank1) ? $bank1 : new Banks();
            $bank2 = ($bank2) ? $bank2 : new Banks();
            $bank3 = ($bank3) ? $bank3 : new Banks();
            $bank4 = ($bank4) ? $bank4 : new Banks();
            $banks = [
                'bank1' => $bank1,
                'bank2' => $bank2,
                'bank3' => $bank3,
                'bank4' => $bank4,
            ];
            
            $user = Users::findOne(['id' => $userId]);
            if (!$user) {
                return Yii::$app->response->redirect(['error/error']);
            }
            $flag = 1;
        }
        
        if ($request->isPost) {
            $dataPost = $request->Post();
            $user->addUser($dataPost, $banks, $flag);
        }
        return $this->render('save', [
            'user' => $user,
            'banks' => $banks,
            'flag' => $flag
        ]);
    }
    
}
