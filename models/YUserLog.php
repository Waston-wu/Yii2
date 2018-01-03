<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "y_user_log".
 *
 * @property integer $id
 * @property string $createtime
 * @property integer $userid
 * @property integer $ip_address
 * @property string $action
 * @property string $old_data
 * @property string $new_data
 * @property string $remark
 */
class YUserLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'y_user_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['createtime'], 'safe'],
            [['userid'], 'required'],
            [['userid', 'ip_address'], 'integer'],
            [['action', 'remark'], 'string', 'max' => 32],
            [['old_data', 'new_data'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'createtime' => 'Createtime',
            'userid' => 'Userid',
            'ip_address' => 'Ip Address',
            'action' => 'Action',
            'old_data' => 'Old Data',
            'new_data' => 'New Data',
            'remark' => 'Remark',
        ];
    }
}
