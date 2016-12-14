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
        return [
            'success' => 1,
            'data' => [
                ['post_id' => '12769' , 'title' => '結婚するならどんな人？結婚生活で不幸にならない男性の選び方', 'categories' => [1, 2, 3], 'thumbnail' => 'http://purelamo.com/wp-content/uploads/wordpress-popular-posts/12757-featured-75x75.jpg', 'views' => 3000, 'favourite_flag' => true],
                ['post_id' => '12740' , 'title' => '【ハロウィンの季節到来】ハロウィン仮装のご紹介＼(^o^)／♡', 'categories' => [1, 2], 'thumbnail' => 'http://purelamo.com/wp-content/uploads/wordpress-popular-posts/11354-featured-75x75.jpg', 'views' => 3100, 'favourite_flag' => false],
                ['post_id' => '12600' , 'title' => '女性も使える！気軽に髪色チェンジ♡『エマジニーヘアカラーアートワックス』', 'categories' => [3], 'thumbnail' => 'http://purelamo.com/wp-content/uploads/wordpress-popular-posts/5801-featured-75x75.jpg',  'views' => 200, 'favourite_flag' => true],
            ]
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
