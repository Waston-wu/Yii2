<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "y_shiyong".
 *
 * @property integer $id
 * @property integer $goods_id
 * @property string $goods_title
 * @property string $goods_plat
 * @property integer $goods_apply
 * @property integer $goods_left
 * @property string $goods_price
 * @property string $goods_link
 * @property string $business_plat
 * @property string $business_grade
 * @property string $business_sale
 * @property integer $business_sock
 * @property string $create_time
 * @property string $update_time
 */
class YShiyong extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'y_shiyong';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'goods_apply', 'goods_left', 'business_sock'], 'integer'],
            [['goods_price'], 'number'],
            [['create_time', 'update_time'], 'safe'],
            [['goods_title', 'goods_link', 'business_grade'], 'string', 'max' => 64],
            [['goods_plat', 'business_plat'], 'string', 'max' => 8],
            [['business_sale'], 'string', 'max' => 16],
            [['goods_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => 'Goods ID',
            'goods_title' => 'Goods Title',
            'goods_plat' => 'Goods Plat',
            'goods_apply' => 'Goods Apply',
            'goods_left' => 'Goods Left',
            'goods_price' => 'Goods Price',
            'goods_link' => 'Goods Link',
            'business_plat' => 'Business Plat',
            'business_grade' => 'Business Grade',
            'business_sale' => 'Business Sale',
            'business_sock' => 'Business Sock',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
