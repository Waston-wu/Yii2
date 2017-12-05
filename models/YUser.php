<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "y_user".
 *
 * @property integer $id
 * @property string $email
 * @property string $password
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
            [['email', 'password'], 'required'],
            [['email'], 'string', 'max' => 64],
            [['password'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'password' => 'Password',
        ];
    }
}
