<?php

return [
    'adminEmail' => 'admin@example.com',
    'mail_code_expire'=>24,   // 发送邮件验证码的过期时间 单位小时
    'mail_code_down'=>120,    // 发送邮件验证码的倒计时，时间间隔 单位秒
    'luosimao_site_key' => 'd734b186a1ec834ea534e338ebd0c0ca',    // 人机验证的site_key（前端使用）
    'luosimao_api_key' => 'a22ab2bce321bc7910dfb4aa4b49971d',    // 人机验证的api_key(后端使用)
    'luosimao_url' => 'https://captcha.luosimao.com/api/site_verify',   // 人机验证的地址
    'redis' => [
        'scheme' => 'tcp',
        'host' => '127.0.0.1',
        'port' => 6379,
        'auth' => '1',
        'dbId' => 0
    ],
];
