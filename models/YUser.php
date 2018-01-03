<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "y_user".
 *
 * @property integer $id
 * @property string $createtime
 * @property string $email
 * @property string $password
 * @property string $head_img
 * @property string $nick_name
 * @property string $last_login_time
 * @property integer $last_login_ip_address
 * @property integer $deleted
 * @property string $updatetime
 */
class YUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'y_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['createtime', 'last_login_time', 'updatetime'], 'safe'],
            [['email', 'password'], 'required'],
            [['last_login_ip_address', 'deleted'], 'integer'],
            [['email', 'head_img'], 'string', 'max' => 64],
            [['password', 'nick_name'], 'string', 'max' => 32],
            [['email'], 'unique'],
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
            'email' => 'Email',
            'password' => 'Password',
            'head_img' => 'Head Img',
            'nick_name' => 'Nick Name',
            'last_login_time' => 'Last Login Time',
            'last_login_ip_address' => 'Last Login Ip Address',
            'deleted' => 'Deleted',
            'updatetime' => 'Updatetime',
        ];
    }
}
