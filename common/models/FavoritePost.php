<?php

namespace common\models;

use Yii;

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
            [['unfavorite_date', 'created_date', 'update_date'], 'safe'],
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
            'update_date' => 'Update Date',
        ];
    }
}
