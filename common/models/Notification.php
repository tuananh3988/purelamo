<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property integer $id
 * @property string $subject
 * @property string $message
 * @property integer $status
 * @property integer $delete_flag
 * @property string $reserve_date
 * @property string $send_begin_date
 * @property string $send_end_date
 * @property string $create_date
 * @property string $last_update_date
 */
class Notification extends \yii\db\ActiveRecord
{
    public static $STATUS = [
        0 => 'Active',
        1 => 'Pushed',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subject', 'message', 'reserve_date'], 'required'],
            [['id', 'status', 'delete_flag'], 'integer'],
            [['message'], 'string'],
            ['status', 'default', 'value' => 0],
            ['delete_flag', 'default', 'value' => 0],
            [['reserve_date'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['reserve_date', 'send_begin_date', 'send_end_date', 'create_date', 'last_update_date'], 'safe'],
            [['subject'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subject' => 'Subject',
            'message' => 'Message',
            'status' => 'Status',
            'delete_flag' => 'Delete Flag',
            'reserve_date' => 'Reserve Date',
            'send_begin_date' => 'Send Begin Date',
            'send_end_date' => 'Send End Date',
            'create_date' => 'Create Date',
            'last_update_date' => 'Last Update Date',
        ];
    }
}
