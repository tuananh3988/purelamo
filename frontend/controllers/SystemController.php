<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
/**
 * Site controller
 */
class SystemController extends Controller
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
                        'actions' => ['category'],
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

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionCategory()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'success' => 1,
            'data' => [
                ['id' => 1, 'category_name' => '美容'],
                ['id' => 2, 'category_name' => 'ダイエット'],
                ['id' => 3, 'category_name' => 'ファッション'],
                ['id' => 4, 'category_name' => 'メイク・コスメ'],
                ['id' => 5, 'category_name' => 'ヘアスタイル'],
                ['id' => 6, 'category_name' => 'ライフスタイル'],
            ]
            
        ];
    }
    


}
