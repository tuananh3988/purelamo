<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "favorite_sumary".
 *
 * @property integer $id
 * @property integer $post_id
 * @property integer $total_favorite
 * @property string $created_date
 * @property string $updated_date
 */
class FavoriteSumary extends \yii\db\ActiveRecord
{
    
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                          ActiveRecord::EVENT_BEFORE_INSERT => ['created_date'],
                          ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_date'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'favorite_sumary';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['post_id', 'total_favorite'], 'required'],
            [['post_id', 'total_favorite'], 'integer'],
            [['created_date', 'updated_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'post_id' => 'Post ID',
            'total_favorite' => 'Total Favorite',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }
    
    public static function getCountFavorite($postId) {
        $favorite = FavoriteSumary::find()->where([
            'post_id' => $postId,
        ])->one();
        
        if ($favorite) {
            return $favorite->total_favorite;
        }
        
        return 0;
    }
}
