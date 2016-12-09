<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "staffs".
 *
 * @property integer $id
 * @property string $password
 * @property integer $auth_key
 */
class Utility extends \yii\base\Model
{
    public static function getNewsCategories($postId) {
        $query = \yii::$app->db->createCommand("SELECT wp32_terms.term_id, wp32_terms.name FROM wp32_term_relationships
        INNER JOIN wp32_term_taxonomy ON wp32_term_relationships.term_taxonomy_id = wp32_term_taxonomy.term_taxonomy_id
        INNER JOIN wp32_terms ON wp32_terms.term_id = wp32_term_taxonomy.term_id
        WHERE wp32_term_relationships.object_id = :post_id
        AND wp32_term_taxonomy.taxonomy = 'category'")
        ->bindValues([':post_id' => $postId])
        ->queryAll();
        
        $data = [];
        foreach ($query as $d) {
            $data[] = [
                'id' => $d['term_id'],
                'category_name' => $d['name']
            ];
        }
        
        return $data;
    }
    
    public static function getPostView($postId) {
        $postView = $query = \yii::$app->db->createCommand("SELECT postid, pageviews FROM wp32_popularpostsdata WHERE postid = :post_id")
        ->bindValues([':post_id' => $postId])
        ->queryOne();
        return empty($postView['pageviews']) ? 0 : $postView['pageviews'];
    }
    
    public static function getRelated($categoryId) {
        
        if (empty($categoryId)) {
            return [];
        }
        
        $sql = "SELECT wp32_posts.ID, wp32_posts.post_title, wp32_posts.post_date, pm2.meta_value FROM wp32_posts
            INNER JOIN wp32_postmeta AS pm1 ON wp32_posts.ID = pm1.post_id
            INNER JOIN wp32_postmeta AS pm2 ON pm1.meta_value = pm2.post_id
            INNER JOIN wp32_term_relationships ON wp32_term_relationships.object_id = wp32_posts.ID
            INNER JOIN wp32_term_taxonomy ON wp32_term_relationships.term_taxonomy_id = wp32_term_taxonomy.term_taxonomy_id
            INNER JOIN wp32_terms ON wp32_terms.term_id = wp32_term_taxonomy.term_id
            WHERE pm1.meta_key = '_thumbnail_id'
            AND pm2.meta_key = '_wp_attached_file'
            AND wp32_posts.post_status = 'publish'
            AND wp32_posts.post_type = 'post'
            AND wp32_terms.term_id = :category_id
            ORDER BY RAND()
            LIMIT :limit";
            
        $query = \yii::$app->db->createCommand($sql);
        $query = $query->bindValues([':category_id' => $categoryId, ':limit' => Yii::$app->params['related_limit']]);
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
        
        return $data;
    }
        
}
