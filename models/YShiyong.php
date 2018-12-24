<?php

namespace app\models;

use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\Missing404Exception;
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
    private $elasticSearch = [
        'index' => 'free',
        'type' => 'list'
    ];
    private $elastic = null;

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

    /**
     * @return \Elasticsearch\Client
     * @author 伍松
     * @date 2018/12/11 13:40
     */
    private function initElasticsearch()
    {
        if($this->elastic !== null){
            return $this->elastic;
        }
        $config = \Yii::$app->params['elasticsearch'];
        $hots = [
            $config['host'] . ':' . $config['port']
        ];
        $this->elastic = ClientBuilder::create()->setHosts($hots)->build();
        return $this->elastic;
    }
    /**
     * @author 伍松
     * @date 2018/12/11 10:43
     * 创建索引
     */
    public function createIndex()
    {
        try{
            $client = $this->initElasticsearch();
            try{
                $param = [
                    'index' => $this->elasticSearch['index']
                ];
                $client->indices()->get($param);
                $client->indices()->delete($param);
            }catch (Missing404Exception $e){

            }

            $params = [
                'index' => $this->elasticSearch['index'],
                'body' => [
                    'mappings' => [
                        $this->elasticSearch['type'] => [
                            'properties' => [
                                'id' => ['type' => 'integer'],
                                'goods_id' => ['type' => 'keyword'],
                                'goods_title' => ['type' => 'text', 'analyzer' => 'ik_max_word'],
                                'goods_plat' => ['type' => 'keyword'],
                                'goods_apply' => ['type' => 'integer'],
                                'goods_left' => ['type' => 'integer'],
                                'goods_price' => ['type' => 'float'],
                                'goods_image' => ['type' => 'keyword'],
                                'goods_link' => ['type' => 'keyword'],
                                'is_huabei' => ['type' => 'byte'],
                                'business_plat' => ['type' => 'keyword'],
                                'business_grade' => ['type' => 'keyword'],
                                'business_sale' => ['type' => 'keyword'],
                                'business_time' => ['type' => 'date'],
                                'create_time' => ['type' => 'date'],
                                'update_time' => ['type' => 'date'],
                                'business_sock' => ['type' => 'integer'],
                            ]
                        ]
                    ]
                ]
            ];
            $response = $client->indices()->create($params);
            return $response;
        }catch (\Exception $e){
            throw new \Exception('搜索引擎异常:'.$e->getMessage());
        }

    }


    /**
     * @param $info {"id":1,"goods_id":56569,"goods_title":"帆布包 加我QQ","goods_plat":"淘宝","goods_apply":22,"goods_left":0,"goods_price":"19.90","goods_image":null,"goods_link":"http://www.shiyong.com/id/56569.html","is_huabei":0,"business_plat":"淘宝","business_grade":"普通商家","business_sale":"鞋包配饰","business_time":"2018-01-08 11:08:01","business_sock":5,"create_time":"2018-01-03 11:19:31","update_time":"2018-01-04 15:21:27"}
     * @author 伍松
     * @date 2018/12/11 13:34
     * 添加文档
     * @return array
     */
    public function addDocument($info)
    {
        $client = $this->initElasticsearch();
        $params = [
            'index' => $this->elasticSearch['index'],
            'type' => $this->elasticSearch['type'],
            'id' => (int)$info['id'],
            'body' => [
                'id' => (int)$info['id'],
                'goods_id' => (int)$info['goods_id'],
                'goods_title' => $info['goods_title'],
                'goods_plat' => $info['goods_plat'],
                'goods_apply' => $info['goods_apply'],
                'goods_left' => (int)$info['goods_left'],
                'goods_price' => (float)$info['goods_price'],
                'goods_image' => $info['goods_image'],
                'goods_link' => $info['goods_link'],
                'is_huabei' => (int)$info['is_huabei'],
                'business_plat' => $info['business_plat'],
                'business_grade' => $info['business_grade'],
                'business_sale' => $info['business_sale'],
                'business_time' => date('c', strtotime($info['business_time'])),
                'create_time' => date('c', strtotime($info['create_time'])),
                'update_time' => date('c', strtotime($info['update_time'])),
                'business_sock' => (int)$info['business_sock'],
            ]
        ];
        $response = $client->index($params);
        return $response;
    }


    /**
     * @param $goods_title 商品标题
     * @param $goods_plat 平台
     * @param $create_time 创建时间
     * @param $business_time 过期时间
     * @param $business_sale 是否上架
     * @param $page
     * @param $limit
     * @return array
     * @throws \Exception
     * @author 伍松
     * @date 2018/12/11 14:44
     * 搜索列表
     */
    public function listGoods($goods_title, $goods_plat, $create_time, $business_time, $business_sale, $page, $limit, $sort)
    {
        $mustArr = [];
        if($goods_title){
           $mustArr[] = ['match' => ['goods_title' => $goods_title]];
        }
        if($goods_plat){
            $mustArr[] = ['term' => ['goods_plat' => $goods_plat]];
        }
        if($create_time){
            $create_time_l = date('c', strtotime(explode(' - ',$create_time)[0].' 00:00:00'));
            $create_time_r = date('c', strtotime(explode(' - ',$create_time)[1].' 23:59:59'));
            $mustArr[] = ['range' => ['create_time' =>['gte' => $create_time_l, 'lte'=> $create_time_r]]];
        }
        if($business_time === '0'){
            $mustArr[] = ['range' => ['business_time' =>['gt' => date('c')]]];//是否上架
        }else if($business_time === '1'){
            $mustArr[] = ['range' => ['business_time' =>['lte' => date('c')]]];//是否上架
        }
        if($business_sale){
            $mustArr[] = ['term' => ['business_sale' => $business_sale]];
        }

        $order = explode(' ', $sort);
        if($order[0] == 'goods_title'){
            $order = ['_score' => $order[1]];
        }else{
            $order = [$order[0] => $order[1]];
        }
        $client = $this->initElasticsearch();
        try {
            $params = [
                'index' => $this->elasticSearch['index'],
                'type' => $this->elasticSearch['type'],
                'body' => [
                    'from' => ($page - 1) * $limit,
                    'size' => $limit,
                    'query' => [
                        'bool' => [
                            'must' => $mustArr
                        ]
                    ],
                    "highlight" => [
                        "fields" => [
                            "goods_title" => new \stdClass()
                        ]
                    ],
                    'sort' => $order
                ],
            ];
            $response = $client->search($params);
            $dataSource = $response['hits']['hits'];
            $data = [];
            $total = $response['hits']['total'];
            foreach ($dataSource as $index => $item) {
                foreach ($item['_source'] as $key => $value) {
                    if (in_array($key, ['business_time','create_time','update_time'])) {
                        $value = date('Y-m-d H:i:s', strtotime($value));
                    }
                    $data[$index][$key] = $value;
                }
                if (isset($item['highlight'])) {
                    foreach ($item['highlight'] as $hKey => $hValue) {
                        $data[$index][$hKey] = $hValue[0];
                    }
                }
            }
            return [$data,$total];
        } catch (\Exception $e) {
            throw new \Exception('搜索引擎异常：'.$e->getMessage());
        }
    }

}
