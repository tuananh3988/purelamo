<?php

namespace common\models;

use Yii;

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
            [['id', 'post_id', 'total_favorite'], 'required'],
            [['id', 'post_id', 'total_favorite'], 'integer'],
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
}
