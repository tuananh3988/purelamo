<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wp32_popularpostssummary".
 *
 * @property string $ID
 * @property string $postid
 * @property string $pageviews
 * @property string $view_date
 * @property string $last_viewed
 */
class Wp32Popularpostssummary extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wp32_popularpostssummary';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['postid'], 'required'],
            [['postid', 'pageviews'], 'integer'],
            [['view_date', 'last_viewed'], 'safe'],
            [['postid', 'view_date'], 'unique', 'targetAttribute' => ['postid', 'view_date'], 'message' => 'The combination of Postid and View Date has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'postid' => 'Postid',
            'pageviews' => 'Pageviews',
            'view_date' => 'View Date',
            'last_viewed' => 'Last Viewed',
        ];
    }
}
