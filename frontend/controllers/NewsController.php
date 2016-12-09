<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use common\models\Utility;
use common\models\PostView;
/**
 * Site controller
 */
class NewsController extends Controller
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
                        'actions' => ['list', 'detail', 'relate', 'recommend', 'ranking', 'search'],
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
        $get = $request->get();
        $isTop = true;
        $selectCount = "SELECT count(*) as count FROM wp32_posts";
        $select = "SELECT wp32_posts.ID, wp32_posts.post_title, wp32_posts.post_date, pm2.meta_value FROM wp32_posts";
        $join = " INNER JOIN wp32_postmeta AS pm1 ON wp32_posts.ID = pm1.post_id
            INNER JOIN wp32_postmeta AS pm2 ON pm1.meta_value = pm2.post_id";
        
        if(!empty($get['category_id'])) {
            $join .= " INNER JOIN wp32_term_relationships ON wp32_term_relationships.object_id = wp32_posts.ID
            INNER JOIN wp32_term_taxonomy ON wp32_term_relationships.term_taxonomy_id = wp32_term_taxonomy.term_taxonomy_id
            INNER JOIN wp32_terms ON wp32_terms.term_id = wp32_term_taxonomy.term_id";
        }
        
        $where = " WHERE pm1.meta_key = '_thumbnail_id'
            AND pm2.meta_key = '_wp_attached_file'
            AND wp32_posts.post_status = 'publish'
            AND wp32_posts.post_type = 'post'";
            
        
        if(!empty($get['category_id'])) {
            $where .= " AND wp32_terms.term_id = :category_id";
        }
        
        if (!empty($get['keyword'])) {
            $where .= " AND (wp32_posts.post_title LIKE :keyword OR wp32_posts.post_content LIKE :keyword)";
        }
        
        $order = " ORDER BY wp32_posts.post_date DESC";

        
        if (!empty($get['limit']) && ctype_digit($get['limit'])) {
            $limit = $get['limit'];
            $isTop = false;
        }
        else {
            $limit = Yii::$app->params['numberOfPage'];
            
        }
        
        $limitStr = " LIMIT " . $limit;
        
        if (!empty($get['offset']) && ctype_digit($get['offset'])) {
            $offset = $get['offset'];
            $isTop = false;
        }
        else {
            $offset = Yii::$app->params['offset'];
        }
        
        $offsetStr = " OFFSET " . $offset;
        
        $sql = $select . $join . $where . $order . $limitStr . $offsetStr;
        $sqlCount = $selectCount . $join . $where . $order;
        
        $query = \yii::$app->db->createCommand($sql);
        $queryCount = \yii::$app->db->createCommand($sqlCount);
        if(!empty($get['category_id'])) {
            $query = $query->bindValues([':category_id' => $get['category_id']]);
            $queryCount = $queryCount->bindValues([':category_id' => $get['category_id']]);
            $isTop = false;
        }
        
        if (!empty($get['keyword'])) {
            $query = $query->bindValues([':keyword' => "%" . $get['keyword'] . "%" ]);
            $queryCount = $queryCount->bindValues([':keyword' => "%" . $get['keyword'] . "%" ]);
            $isTop = false;
        }
        
        if ($isTop) {
            $count['count'] = Yii::$app->params['numberOfPage'];
        }
        else {
            $count = $queryCount->queryOne();
        }
        
        $query = $query->queryAll();
        
        $data = [];
        foreach ($query as $q) {
            $data[] = [
                'post_id' => $q['ID'],
                'title' => $q['post_title'],
                'categories' => Utility::getNewsCategories($q['ID']),
                'thumbnail' => Yii::$app->params['domainImg'] . $q['meta_value'],
                'created_date' => $q['post_date'],
                'views' => Utility::getPostView($q['ID']),
            ];
        }
        return [
            'success' => 1,
            'count' => $count['count'],
            'offset' => $offset + $limit,
            'data' => $data
        ];
    }
    
    public function actionDetail()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();
        $data = [];
        
        if (empty($get['post_id'])) {
            return [
                'success' => 0,
                'mgs' => 'Post id is required.'
            ];
        }
        //select postview
//        $postView = PostView::findOne(['post_id' => $get['post_id']]);
//        if (!$postView) {
//            //create postview
//            $postView = new PostView();
//            $postView->post_id = $get['post_id'];
//            $postView->count = 0;
//            $postView->created_date = date("Y-m-d H:i:s");
//            $postView->save();
//        }
        
        $sql = "SELECT wp32_posts.ID, wp32_posts.post_title, wp32_posts.post_content, wp32_posts.post_date, pm2.meta_value, wp32_users.ID as author_id, wp32_users.display_name FROM wp32_posts"
                . " INNER JOIN wp32_postmeta AS pm1 ON wp32_posts.ID = pm1.post_id
                    INNER JOIN wp32_postmeta AS pm2 ON pm1.meta_value = pm2.post_id
                    INNER JOIN wp32_users ON wp32_users.ID = wp32_posts.post_author"
                . " WHERE pm1.meta_key = '_thumbnail_id'
                    AND pm2.meta_key = '_wp_attached_file'
                    AND wp32_posts.post_status = 'publish' AND wp32_posts.ID = :post_id";

        $query = \yii::$app->db->createCommand($sql);
        $query = $query->bindValues([':post_id' => $get['post_id']]);
        $query = $query->queryOne();
        //update postview count
//        $postView->count++;
//        $postView->updated_date = date("Y-m-d H:i:s");
//        $postView->save();
        //get categories
        $categories = Utility::getNewsCategories($query['ID']);
        $categoryRelated = empty($categories[0]['id']) ? 0 : $categories[0]['id'];
        return [
            'success' => 1,
            'data' => [
                'detail' => [
                    'post_id' => $query['ID'],
                    'thumbnail' => Yii::$app->params['domainImg'] . $query['meta_value'],
                    'created_date' => $query['post_date'],
                    'title' => $query['post_title'],
                    'categories' => $categories,
                    'views' => Utility::getPostView($query['ID']),
                    //'favourite_flag' => true,
                    'contents' => $query['post_content'],
                    'author_id' => $query['author_id'],
                    'author_name' => $query['display_name'],
                ],
                'related' => Utility::getRelated($categoryRelated),
                'recommend' => [],
                
            ]
        ];
    }
    
    public function actionRanking()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $sql = "SELECT wp32_posts.ID, wp32_posts.post_title, wp32_posts.post_date, pm2.meta_value FROM wp32_posts
            INNER JOIN wp32_postmeta AS pm1 ON wp32_posts.ID = pm1.post_id
            INNER JOIN wp32_postmeta AS pm2 ON pm1.meta_value = pm2.post_id
            INNER JOIN wp32_popularpostsdata ON wp32_popularpostsdata.postid = wp32_posts.ID
            WHERE pm1.meta_key = '_thumbnail_id'
            AND pm2.meta_key = '_wp_attached_file'
            AND wp32_posts.post_status = 'publish'
            AND wp32_posts.post_type = 'post'
            ORDER BY wp32_popularpostsdata.pageviews DESC
            LIMIT :limit";
            
        $query = \yii::$app->db->createCommand($sql);
        $query = $query->bindValues([':limit' => Yii::$app->params['ranking_limit']]);
        $query = $query->queryAll();
        
        $data = [];
        foreach ($query as $q) {
            $data[] = [
                'post_id' => $q['ID'],
                'title' => $q['post_title'],
                'categories' => Utility::getNewsCategories($q['ID']),
                'thumbnail' => Yii::$app->params['domainImg'] . $q['meta_value'],
                'created_date' => $q['post_date'],
                'views' => Utility::getPostView($q['ID']),
            ];
        }
        
        return [
            'success' => 1,
            'data' => $data
        ];
    
    }

}
