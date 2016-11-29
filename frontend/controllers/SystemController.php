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
        $category = \yii::$app->db->createCommand("SELECT wp32_terms.term_id, wp32_terms.name FROM wp32_term_relationships
            INNER JOIN wp32_postmeta ON wp32_term_relationships.object_id = wp32_postmeta.post_id
            INNER JOIN wp32_terms on wp32_terms.term_id = wp32_postmeta.meta_value
            WHERE wp32_term_relationships.term_taxonomy_id = :category_id
            AND meta_key = '_menu_item_object_id'")
            ->bindValues([':category_id' => Yii::$app->params['category_id']])
            ->queryAll();
        
        $data = [];
        foreach ($category as $c) {
            $data[] = [
                'id' => $c['term_id'],
                'category_name' => $c['name'],
            ];
        }
        
        return [
            'success' => 1,
            'data' => $data
        ];
    }
    


}
