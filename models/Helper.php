<?php
/**
 * Created by PhpStorm.
 * User: banma
 * Date: 2017/12/4
 * Time: 13:58
 */

namespace app\models;
use Yii;


class Helper
{

    public function test()
    {
        return 'test';
    }

    // curl请求
    public function curlRequest($url, $data = null, $header = null)
    {
        //初始化
        $curl = curl_init();
        //设置url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置https
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        //如果传递了数据，则使用POST请求
        if (!is_null($data)) {
            //开启post模式
            curl_setopt($curl, CURLOPT_POST, 1);
            //设置post数据
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        if (!is_null($header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        //结果返回成字符串  如果是0  则是直接输出
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //执行
        $output = curl_exec($curl);
        //释放资源
        curl_close($curl);
        return $output;
    }

    // 验证人机验证码是否正确 https://luosimao.com/docs/api/56
    public function luosimao_validate($code = null)
    {
        $api_key = Yii::$app->params['luosimao_api_key'];
        $url = Yii::$app->params['luosimao_url'];
        if(!$api_key || !$url)return ['code'=>1,'message'=>'无法获取配置信息','data'=>[]];
        if($code == null) return ['code'=>2,'message'=>'入参错误！','data'=>[]];
        // 发送post请求
        $param = ['api_key'=>$api_key,'response'=>$code];
        $res = json_decode($this->curlRequest($url,$param));
        if($res->error == 0)
            return json_encode(['code'=>0,'message'=>'验证成功','data'=>[]]);
        else
            return json_encode(['code'=>$res->error,'message'=>$res->msg,'data'=>[]]);
    }

    // 验证参数(验证这些key是否都是存在的，并且不为空 )
    public function param_validate($param,$keys = array())
    {
        foreach ($keys as $key)
        {
            if(!isset($param[$key]) || !$param[$key])
            {
                return $key.'参数不能为空！';
            }
        }
        return false;
    }

    // 发送邮箱验证码(参数：收件人)
    public function mail_code_send($to)
    {
        if(!$to)return false;
        $code = mt_rand(1000,9999); // 验证码
        $expire = Yii::$app->params['mail_code_expire'];    // 过期时间
        $res = Yii::$app->cache->set(trim($to), $code, ($expire*60*60));
        if(!$res)return false;
        $mail = Yii::$app->mailer->compose()
            ->setFrom(['877183659@qq.com' => '系统邮箱'])
            ->setTo($to)
            ->setSubject('验证码')
            ->setHtmlBody("<br><h1>您本次操作的验证码是：<span style='color:red;'>".$code."</span>，有效期".($expire)."小时。</h1>")    //发布可以带html标签的文本
            ->send();
        return $mail;
    }

    // 验证邮箱验证码（参数：邮箱、验证码）
    public function mail_code_validate($email,$code)
    {
        if(!$email || !$code)return json_encode(['code'=>11,'message'=>'入参非法','data'=>[]]);
        // 获取缓存中的验证码
        $cache_code = Yii::$app->cache->get(trim($email));
        if(trim($code) != $cache_code)
        {
            return json_encode(['code'=>2,'message'=>'验证码错误','data'=>[]]);
        }else
        {
            return json_encode(['code'=>0,'message'=>'验证码正确','data'=>[]]);
        }
    }

    // 获取客户端ip地址
    public function getIP()
    {
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if(getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if(getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else
            $ip = "0";
        return $ip;
    }

    // 获取随机字符串
    function getRandomString($length = 10)
    {
        $str = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
        str_shuffle($str);
        $name = substr(str_shuffle($str), 0, $length);
        return $name;
    }
}