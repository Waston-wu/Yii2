<?php

namespace app\controllers;

use Elasticsearch\ClientBuilder;
use yii\web\Controller;
use Yii;

class ElasticController extends Controller
{
    private $es;

    public function init()
    {
        $this->es = ClientBuilder::create()->build();
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        echo 'Welcome to Elastic Search';
    }
    public function index($param)
    {
        return $this->es->index($param);
    }

    // 多条搜索
    public function actionSearch()
    {
//        $params = [
//            'index' =>  'user_index',   //['my_index1', 'my_index2'],可以通过这种形式进行跨库查询
//            'type' => 'normal_type',//['my_type1', 'my_type2'],同理跨表查询
//            'body' => [
//                "size"=> 20,
//                "from"=> 0,
//                "query"=> [
//                    "bool"=> [
//                        "must"=> [
//                            [
//                                "term"=> [
//                                    "username"=> "2NTCF"
//                                ]
//                            ]
//                        ]
//                    ]
//                ],
//                "sort"=> [
//                    "uid"=> "desc"
//                ]
//            ]
//        ];
        // 嵌套查询
//        $params = [
//            'index' =>  'user_index',   //['my_index1', 'my_index2'],可以通过这种形式进行跨库查询
//            'type' => 'normal_type',//['my_type1', 'my_type2'],同理跨表查询
//            'body' => [
//                "size"=> 20,
//                "from"=> 0,
//                "query"=> [
//                    "bool"=> [
//                        "must"=> [],
//                        "must_not" => [
//                            "range" => [
//                                "uid" => [
//                                    "gt" => 80072
//                                ]
//                            ]
//                        ],
//                        "should" => [
//                            [
//                                "term" => ["sex" => 2],
//                            ],[
//                                "term" => ["username" => "2NTCF" ],
//                            ]
//                        ],
//                    ]
//                ],
//                "sort"=> [
//                    "uid"=> "desc"
//                ]
//            ]
//        ];
        // 多个精确值
//        $params = [
//            'index' =>  'user_index',   //['my_index1', 'my_index2'],可以通过这种形式进行跨库查询
//            'type' => 'normal_type',//['my_type1', 'my_type2'],同理跨表查询
//            'body' => [
//                "size"=> 20,
//                "from"=> 0,
//                "query"=> [
//                    "bool" => [
//                        "must" => [
//                            "terms" => [
//                                "sex" => [1,0]
//                            ]
//                        ]
//                    ]
//                ],
//                "sort"=> [
//                    "uid"=> "desc"
//                ]
//            ]
//        ];
        $params = [
            'index' =>  'user_index',   //['my_index1', 'my_index2'],可以通过这种形式进行跨库查询
            'type' => 'normal_type',//['my_type1', 'my_type2'],同理跨表查询
            'body' => [
                "size"=> 20,
                "from"=> 0,
                "query"=> [
                    "bool" => [
                        "must" => [
                            "query_string" => [
                                "default_field" => "remark",
                                "query" => "中小学交通"
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $res = $this->es->search($params);
        return \yii\helpers\Json::encode($res);
    }

    // 单条搜索
    public function actionGetdata()
    {
        $params = [
            'index' => 'user_index',
            'type' => 'normal_type',
            'id' => '67486',
            'client' => [
                'ignore' => 404
            ]
        ];
        $res = $this->es->get($params);
        return \yii\helpers\Json::encode($res);
    }

    // 批量插入数据
    public function actionBulk()
    {
        for ($i = 0 ; $i < 1; $i ++){
            $params['body'][] = [
                'index' => [
                    '_index' => 'user_index',
                    '_type' => 'normal_type',
                    '_id'  => mt_rand(10000, 99999)
                ]
            ];
            $params['body'][] = [
                'uid' => mt_rand(10000, 99999),
                'username' => Yii::$app->helper->getRandomString(5),
                'platform' => Yii::$app->helper->getRandomString(2),
                'mobile' =>mt_rand(10000000, 99999999),
                'province' => Yii::$app->helper->getRandomString(2),
                'city' => Yii::$app->helper->getRandomString(2),
                'sex' => mt_rand(0,2),
                'source' => mt_rand(0,99),
                'remark' => '特别是前往虹桥枢纽乘坐火车或飞机的旅客，如需驾车通过交通管制区，要做好比平时提前1小时出行的准备，避免因道路交通拥堵而误点。'
            ];
        }
        $res = $this->es->bulk($params);
        return \yii\helpers\Json::encode($res);
    }

    // 删除单条数据
    public function actionDeletedata()
    {
        $params = [
            'index' => 'user_index',
            'type' => 'normal_type',
            'id' => '674816',
            'client' => [
                'ignore' => 404
            ]
        ];
        $res = $this->es->delete($params);
        return \yii\helpers\Json::encode($res);
    }

    // 删除索引
    public function actionDeleteindex()
    {
        $params = [
            'index' => 'my_index',
            'client' => [
                'ignore' => 404
            ]
        ];
        $res = $this->es->indices()->delete($params);
        return \yii\helpers\Json::encode($res);
    }

    // 更新数据
    public function actionUpdate()
    {
        $params = [
            'index' => 'user_index',
            'type' => 'normal_type',
            'id' => '80072',
            'body' => [
                'doc' => [  // 必须带上这个.表示是文档操作
                    'remark' => '双十一调整为工作日，剁手一族要不开心了。'
                ]
            ],
            'client' => [
                'ignore' => 404
            ]
        ];
        $res = $this->es->update($params);
        return \yii\helpers\Json::encode($res);
    }

    // 添加/修改mapping信息(注意事项:已经建立好的字段类型是不能更改的!!)
    public function actionPutmapping()
    {
        $params = [
            'index' => 'user_index',  //索引名（相当于mysql的数据库）
            'type'  => 'normal_type',
            'body'  =>  [
                'normal_type' => [
                    'properties'    =>[
                        'remark'   =>[
                            'type'  => 'text'
                        ]
                    ]
                ]
            ]
        ];
        $res = $this->es->indices()->putMapping($params);
        return \yii\helpers\Json::encode($res);
    }

    // 获取mapping信息
    public function actionGetmapping()
    {
        $params = [
            'index' => 'user_index',
            'client' => [
                'ignore' => 404
            ]
        ];
        $res = $this->es->indices()->getMapping($params);
        return \yii\helpers\Json::encode($res);
    }

    // 获取索引
    public function actionGetsettings()
    {
        $params = [
            'index' => 'user_index',
            'client' => [
                'ignore' => 404 // 找不到的情况不报异常错误
            ]
        ];
        $res = $this->es->indices()->getSettings($params);
        return \yii\helpers\Json::encode($res);
    }

    // 新增索引
    public function actionCreateindex()
    {
        // 创建索引
        $params = [
            'index' => 'user_index',  //索引名（相当于mysql的数据库）
            'body' => [
                'mappings' => [
                    'normal_type' => [ //类型名（相当于mysql的表）
                        '_all'=>[   //  是否开启所有字段的检索
                            'enabled' => 'false'
                        ],
                        'properties' => [ //文档类型设置（相当于mysql的数据类型）
                            'uid' => [
                                'type' => 'integer' // 字段类型为整型
                            ],
                            'username' => [
                                'type' => 'keyword' // 字段类型为关键字,如果需要全文检索,则修改为text,注意keyword字段为整体查询,不能作为模糊搜索
                            ],

                            'platform' => [
                                'type' => 'keyword'
                            ],
                            'mobile' => [
                                'type' => 'integer'
                            ],
                            'sex' => [
                                'type' => 'integer'
                            ],
                            'source' => [
                                'type' => 'keyword'
                            ],
                            'province' => [
                                'type' => 'keyword'
                            ],
                            'city' => [
                                'type' => 'keyword'
                            ],
                            'tag' =>   [
                                'properties'    =>  [
                                    'tagName'   =>[
                                        'type' => 'text'
                                    ],
                                    'tagWeight'   => [
                                        'type' => 'integer',
                                    ]
                                ]
                            ],
                        ]
                    ]
                ]
            ]
        ];
        $res = $this->es->indices()->create($params);
        return \yii\helpers\Json::encode($res);
    }
}
