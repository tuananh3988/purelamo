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
        $sql = "SELECT wp32_posts.ID, wp32_posts.post_title, wp32_posts.post_date, pm2.meta_value FROM wp32_posts";
        $join = " INNER JOIN wp32_postmeta AS pm1 ON wp32_posts.ID = pm1.post_id
            INNER JOIN wp32_postmeta AS pm2 ON pm1.meta_value = pm2.post_id";
        
        if(!empty($get['category_id'])) {
            $join .= " INNER JOIN wp32_term_relationships ON wp32_term_relationships.object_id = wp32_posts.ID
            INNER JOIN wp32_term_taxonomy ON wp32_term_relationships.term_taxonomy_id = wp32_term_taxonomy.term_taxonomy_id
            INNER JOIN wp32_terms ON wp32_terms.term_id = wp32_term_taxonomy.term_id";
        }
        
        $sql .= $join;
        
        $where = " WHERE pm1.meta_key = '_thumbnail_id'
            AND pm2.meta_key = '_wp_attached_file'
            AND wp32_posts.post_status = 'publish'";
        
        if(!empty($get['category_id'])) {
            $where .= " AND wp32_terms.term_id = :category_id";
        }
        
        $sql .= $where;
        
        $order = " ORDER BY wp32_posts.post_date DESC";
        $sql .= $order;
        
        $limit = !empty($get['limit']) && ctype_digit($get['limit']) ? $get['limit'] : Yii::$app->params['numberOfPage'];
        $sql .= " LIMIT " . $limit;
        
        $offset = !empty($get['offset']) && ctype_digit($get['offset']) ? $get['offset'] : Yii::$app->params['offset'];
        $sql .= " OFFSET " . $offset;
        
        $query = \yii::$app->db->createCommand($sql);
        if(!empty($get['category_id'])) {
            $query = $query->bindValues([':category_id' => $get['category_id']]);
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
                'favourite_flag' => 0,
            ];
        }
        return [
            'success' => 1,
            'data' => $data
        ];
    }
    
    public function actionDetail()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'success' => 1,
            'data' => [
                'post_id' => '12769',
                'created_date' => '2016-09-19',
                'title' => '結婚するならどんな人？結婚生活で不幸にならない男性の選び方',
                'categories' => [1, 2, 3],
                'views' => 1242,
                'favourite_flag' => true,
                'contents' => '<h3 class="spam">エマジニーは男性だけでなく女性にもおすすめ♡</h3><p><a href="http://purelamo.com/wp-content/uploads/2016/05/emaona.jpg" rel="attachment wp-att-7628"><img src="http://purelamo.com/wp-content/uploads/2016/05/emaona.jpg" alt="emaona" width="679" height="320" class="alignnone size-full wp-image-7628" srcset="http://purelamo.com/wp-content/uploads/2016/05/emaona-300x141.jpg 300w, http://purelamo.com/wp-content/uploads/2016/05/emaona.jpg 679w, http://purelamo.com/wp-content/uploads/2016/05/emaona-300x141@2x.jpg 600w" sizes="(max-width: 679px) 100vw, 679px"></a></p><p>女性の中で個性あふれたファッションや髪型が好きな人はいるでしょう。ファッションが好き、いろんなことに挑戦するのが好きという女性もたくさんいます。</p>',
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
