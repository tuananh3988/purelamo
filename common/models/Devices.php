<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "devices".
 *
 * @property integer $id
 * @property string $device_id
 * @property integer $type
 * @property string $device_token
 * @property string $created_date
 * @property string $updated_date
 */
class Devices extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'devices';
    }
    
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
    public function rules()
    {
        return [
            [['device_id', 'type', 'device_token'], 'required'],
            [['type'], 'integer'],
            [['created_date', 'updated_date'], 'safe'],
            [['device_id', 'device_token'], 'string', 'max' => 255],
            ['type', 'number', 'min'=> 1, 'max'=> 2, 'tooSmall'=> \Yii::t('app', 'Type have to 1 or 2'), 'tooBig'=> \Yii::t('app', 'Type have to 1 or 2')],
            ['type_time_recieve_notify', 'number', 'min'=> 1, 'max'=> 3, 'tooSmall'=> \Yii::t('app', 'Type time recieve notify have to between 1 and 3'), 'tooBig'=> \Yii::t('app', 'Type time recieve notify have to between 1 and 3')],
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
            'type' => 'Type',
            'device_token' => 'Device Token',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }
}
