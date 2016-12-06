<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "post_view".
 *
 * @property integer $view_id
 * @property integer $post_id
 * @property integer $count
 * @property string $created_date
 * @property string $updated_date
 */
class PostView extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post_view';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['post_id', 'count'], 'integer'],
            [['created_date', 'updated_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'view_id' => 'View ID',
            'post_id' => 'Post ID',
            'count' => 'Count',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }
}
