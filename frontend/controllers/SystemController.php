<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use common\models\Devices;
use common\models\Wp32Terms;
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
                        'actions' => ['category', 'register', 'info'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'register' => ['post'],
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
    public function actionCategoryOld()
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
    
    public function actionCategory()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $category = Wp32Terms::find()->where(['in', 'term_id', Yii::$app->params['categories']])
                ->orderBy([new \yii\db\Expression('FIELD (term_id, ' . implode(',', Yii::$app->params['categories']) . ')')])
                ->all();
        
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
    
    public function actionRegister()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $postData = $request->post();
        if (empty($postData['type']) || empty($postData['device_id']) || empty($postData['device_token'])) {
            return [
                'success' => 0,
                'mgs' => 'Param invalid.'
            ];
        }
        
        $device = Devices::findOne(['type' => $postData['type'], 'device_id' => $postData['device_id']]);
        
        if (!$device) {
            $device = new Devices();
            $device->device_id = $postData['device_id'];
            $device->device_token = $postData['device_token'];
            $device->type = $postData['type'];
            $device->type_time_recieve_notify = 1;
            
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
        else {
            $device->device_token = $postData['device_token'];
            $device->save();
            return [
                'success' => 1,
            ];
        }

    }

    public function actionInfo()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $getData = $request->get();
        if (empty($getData['type']) || empty($getData['device_id'])) {
            return [
                'success' => 0,
                'mgs' => 'Param invalid.'
            ];
        }
        
        $device = Devices::findOne(['type' => $getData['type'], 'device_id' => $getData['device_id']]);
        
        if ($device) {
            return [
                'success' => 1,
                'data' => [
                    'notice_setting' => $device->type_time_recieve_notify,
                ]
            ];
        }
        
        return [
                'success' => 0,
                'mgs' => 'Have error validate'
            ];

    }

}
