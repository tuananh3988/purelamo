<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use common\models\Utility;

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
            AND wp32_posts.post_status = 'publish'";
        
        if(!empty($get['category_id'])) {
            $where .= " AND wp32_terms.term_id = :category_id";
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
        
        if ($isTop) {
            $count['count'] = 11;
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
                'views' => 0,
                //'favourite_flag' => 0,
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
        //
        $sql = "SELECT * FROM post_view WHERE post_id = :post_id";
        
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
        //update view count
        $sqlView = "";
        return [
            'success' => 1,
            'data' => [
                'detail' => [
                    'post_id' => $query['ID'],
                    'thumbnail' => Yii::$app->params['domainImg'] . $query['meta_value'],
                    'created_date' => $query['post_date'],
                    'title' => $query['post_title'],
                    'categories' => Utility::getNewsCategories($query['ID']),
                    'views' => 0,
                    //'favourite_flag' => true,
                    'contents' => $query['post_content'],
                    'author_id' => $query['author_id'],
                    'author_name' => $query['display_name'],
                ]
            ]
        ];
    }
    
    public function actionRelate()
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
    
    public function actionRecommend()
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
    
    public function actionRanking()
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
    
    public function actionSearch()
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

}
