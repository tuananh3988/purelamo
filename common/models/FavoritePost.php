<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "favorite_post".
 *
 * @property integer $id
 * @property integer $device_id
 * @property integer $post_id
 * @property integer $favorite_flag
 * @property string $unfavorite_date
 * @property string $created_date
 * @property string $update_date
 */
class FavoritePost extends \yii\db\ActiveRecord
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
        return 'favorite_post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['device_id', 'post_id', 'favorite_flag'], 'required'],
            [['device_id', 'post_id', 'favorite_flag'], 'integer'],
            [['unfavorite_date', 'created_date', 'updated_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'device_id' => 'Device ID',
            'post_id' => 'Post ID',
            'favorite_flag' => 'Favorite Flag',
            'unfavorite_date' => 'Unfavorite Date',
            'created_date' => 'Created Date',
            'updated_date' => 'Update Date',
        ];
    }
    
    public static function isFavorited($postId, $deviceId) {
        $favorite = FavoritePost::find()->where([
            'device_id' => $postId,
            'post_id' => $deviceId,
            'favorite_flag' => 1
        ])->one();
        
        if ($favorite) {
            return true;
        }
        
        return false;
    }
}
