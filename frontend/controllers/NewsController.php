<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use common\models\Utility;
use common\models\WpFormat;
use common\models\PostView;
use common\models\SearchSumary;
use common\models\Wp32Popularpostsdata;
use common\models\Wp32Popularpostssummary;
use common\models\FavoritePost;
use common\models\FavoriteSumary;
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
                        'actions' => ['list', 'detail', 'ranking', 'auto-complete', 'favorite', 'unfavorite'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'favorite' => ['post'],
                    'unfavorite' => ['post'],
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
        
        if (empty($get['device_id'])) {
            return [
                'success' => 0,
                'mgs' => 'Device id is required.'
            ];
        }
        
        $selectCount = "SELECT count(*) as count FROM wp32_posts";
        $select = "SELECT wp32_posts.ID, wp32_posts.post_title, wp32_posts.post_date, pm2.meta_value, wp32_users.ID as author_id, wp32_users.display_name FROM wp32_posts";
        $join = " INNER JOIN wp32_postmeta AS pm1 ON wp32_posts.ID = pm1.post_id
            INNER JOIN wp32_postmeta AS pm2 ON pm1.meta_value = pm2.post_id
            INNER JOIN wp32_users ON wp32_users.ID = wp32_posts.post_author";
        
        if(!empty($get['category_id'])) {
            $join .= " INNER JOIN wp32_term_relationships ON wp32_term_relationships.object_id = wp32_posts.ID
            INNER JOIN wp32_term_taxonomy ON wp32_term_relationships.term_taxonomy_id = wp32_term_taxonomy.term_taxonomy_id
            INNER JOIN wp32_terms ON wp32_terms.term_id = wp32_term_taxonomy.term_id";
        }
        
        if(!empty($get['list_favorite'])) {
            $join .= " INNER JOIN favorite_post ON favorite_post.post_id = wp32_posts.ID AND favorite_flag = 1 AND device_id ='" . $get['device_id'] . "'";
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
        
        if(!empty($get['author_id'])) {
            $where .= " AND wp32_posts.post_author = :author_id";
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
        
        if(!empty($get['ids'])) {
            $ids = $get['ids'];
            $where .= " AND wp32_posts.ID in ($ids)";
            $limitStr = '';
            $offsetStr = '';
            $order = " ORDER BY FIELD(wp32_posts.ID, $ids)";
        }
        
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
            $searchSumary = SearchSumary::findOne(['keyword' => $get['keyword']]);
            if ($searchSumary) {
                $searchSumary->count++;
                $searchSumary->save();
            }  else {
                $searchSumary = new SearchSumary();
                $searchSumary->keyword = $get['keyword'];
                $searchSumary->count = 1;
                $searchSumary->save();
            }
            
        }
        
        if (!empty($get['author_id'])) {
            $query = $query->bindValues([':author_id' => $get['author_id']]);
            $queryCount = $queryCount->bindValues([':author_id' => $get['author_id']]);
            $isTop = false;
        }
        
        if (!empty($get['ids'])) {
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
                'author_id' => $q['author_id'],
                'author_name' => $q['display_name'],
                'isFavorite' => FavoritePost::isFavorited($q['ID'], $get['device_id']),
                'total_favorite' => FavoriteSumary::getCountFavorite($q['ID']),
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
        
        if (empty($get['device_id'])) {
            return [
                'success' => 0,
                'mgs' => 'Device id is required.'
            ];
        }
        
        //update count view
        $currentDate = date("Y-m-d");
        $viewSummary = Wp32Popularpostssummary::findOne(['postid' => $get['post_id'], 'view_date' => $currentDate]);
       
        if (!$viewSummary) {
            $viewSummary = new Wp32Popularpostssummary();
            $viewSummary->postid = $get['post_id'];
            $viewSummary->view_date = $currentDate;
            $viewSummary->pageviews = 0;
        }
        
        $viewSummary->pageviews = $viewSummary->pageviews + 1;
        $viewSummary->last_viewed = date('Y-m-d H:i:s');
        $viewSummary->save();
        
        $viewData = Wp32Popularpostsdata::findOne(['postid' => $get['post_id']]);
        if (!$viewData) {
            $viewData = new Wp32Popularpostsdata();
            $viewData->postid = $get['post_id'];
            $viewData->day = date('Y-m-d H:i:s');
            $viewData->pageviews = 0;
        }
        
        $viewData->pageviews = $viewData->pageviews + 1;
        $viewData->last_viewed = date('Y-m-d H:i:s');
        $viewData->save();
        
        $sql = "SELECT wp32_posts.ID, wp32_posts.post_title, wp32_posts.post_name, wp32_posts.post_content, wp32_posts.post_date, pm2.meta_value, wp32_users.ID as author_id, wp32_users.display_name FROM wp32_posts"
                . " INNER JOIN wp32_postmeta AS pm1 ON wp32_posts.ID = pm1.post_id
                    INNER JOIN wp32_postmeta AS pm2 ON pm1.meta_value = pm2.post_id
                    INNER JOIN wp32_users ON wp32_users.ID = wp32_posts.post_author"
                . " WHERE pm1.meta_key = '_thumbnail_id'
                    AND pm2.meta_key = '_wp_attached_file'
                    AND wp32_posts.post_status = 'publish' AND wp32_posts.ID = :post_id";

        $query = \yii::$app->db->createCommand($sql);
        $query = $query->bindValues([':post_id' => $get['post_id']]);
        $query = $query->queryOne();
        //get categories
        $categories = Utility::getNewsCategories($query['ID']);
        $categoryRelated = empty($categories[0]['id']) ? 0 : $categories[0]['id'];
        return [
            'success' => 1,
            'data' => [
                'detail' => [
                    'post_id' => $query['ID'],
                    'thumbnail' => Yii::$app->params['domainImg'] . $query['meta_value'],
                    'url_detail' => Yii::$app->params['domain'] . $query['post_name'],
                    'created_date' => $query['post_date'],
                    'title' => $query['post_title'],
                    'categories' => $categories,
                    'views' => Utility::getPostView($query['ID']),
                    'contents' => Utility::renderPostContent($query['post_content']),
                    'author_id' => $query['author_id'],
                    'author_name' => $query['display_name'],
                    'is_favorite' => FavoritePost::isFavorited($query['ID'], $get['device_id']),
                    'total_favorite' => FavoriteSumary::getCountFavorite($query['ID']),
                ],
                'author' => Utility::getAuthorInfo($query['author_id']),
                'related' => Utility::getRelated($categoryRelated),
                'recommend' => Utility::getRecommend(),
                
                
            ]
        ];
    }
    
    public function actionRanking()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();
        
        if (empty($get['device_id'])) {
            return [
                'success' => 0,
                'mgs' => 'Device id is required.'
            ];
        }
        
        $date = date("Y-m-d", strtotime("-2 days", strtotime(date("Y-m-d"))));
        $sql = "SELECT wp32_posts.ID, wp32_posts.post_title, wp32_posts.post_date, pm2.meta_value, viewsumary.suma, wp32_users.ID as author_id, wp32_users.display_name FROM wp32_posts
            INNER JOIN wp32_postmeta AS pm1 ON wp32_posts.ID = pm1.post_id
            INNER JOIN wp32_postmeta AS pm2 ON pm1.meta_value = pm2.post_id
            INNER JOIN wp32_users ON wp32_users.ID = wp32_posts.post_author
            INNER JOIN (
                    SELECT postid, sum(pageviews) suma
                    FROM wp32_popularpostssummary
                    WHERE view_date >= '$date'
                    GROUP BY postid
                    ORDER BY suma DESC) viewsumary 
            ON viewsumary.postid = wp32_posts.ID
            WHERE pm1.meta_key = '_thumbnail_id'
            AND pm2.meta_key = '_wp_attached_file'
            AND wp32_posts.post_status = 'publish'
            AND wp32_posts.post_type = 'post'
            ORDER BY viewsumary.suma DESC
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
                'author_id' => $q['author_id'],
                'author_name' => $q['display_name'],
                'isFavorite' => FavoritePost::isFavorited($q['ID'], $get['device_id']),
                'total_favorite' => FavoriteSumary::getCountFavorite($q['ID']),
            ];
        }
        
        return [
            'success' => 1,
            'data' => $data
        ];
    
    }
    
    public function actionAutoComplete()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();
        $data = [];
        $where = '';
        if (!empty($get['keyword'])) {
            $where = ['like', 'keyword', $get['keyword']];
        }
        
        $searchSumary = SearchSumary::find()->where($where)->orderBy('count DESC')->limit(Yii::$app->params['keyword_search_limit'])->all();
        
        foreach ($searchSumary as $s) {
            $data[] = $s['keyword'];
        }
        
        return [
            'success' => 1,
            'data' => $data
        ];
    }
    
    public function actionFavorite()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $post = $request->post();
        
        if (empty($post['post_id']) || empty($post['device_id'])) {
            return [
                'success' => 0,
                'mgs' => 'Params invalid.'
            ];
        }
        
        $favorite = FavoritePost::find()->where([
            'device_id' => $post['device_id'],
            'post_id' => $post['post_id'],
            'favorite_flag' => 1
        ])->one();
        
        if (!$favorite) {
            $favorite = new FavoritePost();
            $favorite->device_id = $post['device_id'];
            $favorite->post_id = $post['post_id'];
            $favorite->favorite_flag = 1;
            $favorite->save();
            
            $favoriteSumary = FavoriteSumary::find()->where(['post_id' => $post['post_id']])->one();
            if ($favoriteSumary) {
                $favoriteSumary->total_favorite++;
                $favoriteSumary->save();
            }
            else {
                $favoriteSumary = new FavoriteSumary();
                $favoriteSumary->post_id = $post['post_id'];
                $favoriteSumary->total_favorite = 1;
                $favoriteSumary->save();
            }
            
        }
        
        return [
            'success' => 1,
        ];
        
    }
    
    public function actionUnfavorite()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $post = $request->post();
        
        if (empty($post['post_id']) || empty($post['device_id'])) {
            return [
                'success' => 0,
                'mgs' => 'Params invalid.'
            ];
        }
        
        $favorite = FavoritePost::find()->where([
            'device_id' => $post['device_id'],
            'post_id' => $post['post_id'],
            'favorite_flag' => 1
        ])->one();
        
        if (!$favorite) {
            return [
                'success' => 0,
                'mgs' => 'Device id not favorite the post.'
            ];
        }
        
        $favorite->unfavorite_date = date('Y-m-d H:i:s');
        $favorite->favorite_flag = 0;
        $favorite->save();
        
        $favoriteSumary = FavoriteSumary::find()->where(['post_id' => $post['post_id']])->one();
        if ($favoriteSumary) {
            $favoriteSumary->total_favorite--;
            $favoriteSumary->save();
        }
        
        return [
            'success' => 1,
        ];
        
    }

}
