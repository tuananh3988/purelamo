<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wp32_popularpostsdata".
 *
 * @property string $postid
 * @property string $day
 * @property string $last_viewed
 * @property string $pageviews
 */
class Wp32Popularpostsdata extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wp32_popularpostsdata';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['postid'], 'required'],
            [['postid', 'pageviews'], 'integer'],
            [['day', 'last_viewed'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'postid' => 'Postid',
            'day' => 'Day',
            'last_viewed' => 'Last Viewed',
            'pageviews' => 'Pageviews',
        ];
    }
}
