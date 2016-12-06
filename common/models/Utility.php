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
            $data[$d['term_id']] = $d['name'];
        }
        
        return $data;
    }
    
    public static function getPostView($postId) {
        $postView = PostView::findOne(['post_id' => $postId]);
        return empty($postView['count']) ? 0 : $postView['count'];
    }
}
