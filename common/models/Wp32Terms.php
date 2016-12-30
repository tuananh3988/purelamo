<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wp32_terms".
 *
 * @property string $term_id
 * @property string $name
 * @property string $slug
 * @property string $term_group
 * @property integer $term_order
 */
class Wp32Terms extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wp32_terms';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['term_group', 'term_order'], 'integer'],
            [['name', 'slug'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'term_id' => 'Term ID',
            'name' => 'Name',
            'slug' => 'Slug',
            'term_group' => 'Term Group',
            'term_order' => 'Term Order',
        ];
    }
}
