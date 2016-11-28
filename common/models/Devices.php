<?php

namespace common\models;

use Yii;

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
