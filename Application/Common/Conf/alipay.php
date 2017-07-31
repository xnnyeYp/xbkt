<?php
return array(
//    'PARTNER' => '2088221353429535',
//    'SELLER_ID' => 'waxiang767@163.com',
//    'KEY' => 'mom1y7np0674j6wswvz8g21mx8xyvnxm',
//    'NOTIFY_URL' => __ROOT__.'',
//    'RETURN_URL' => __ROOT__.'',
//    'SIGN_TYPE' => strtoupper('MD5'),
//    'INPUT_CHARSET' => strtolower('utf-8'),
//    'CACERT' => getcwd().'\\cacert.pem',
//    'TRANSPORT' => 'http',
//    'PAYMENT_TYPE' => 1,
//    'SERVICE' => 'alipay.wap.create.direct.pay.by.user',
    'alipay_config'=>array(
        //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        //合作身份者id，以2088开头的16位纯数字//
        'partner'		=> '2088221353429535',
        'seller_id'     => 'waxiang767@163.com',
        //安全检验码，以数字和字母组成的32位字符//
        'key'			=> 'mom1y7np0674j6wswvz8g21mx8xyvnxm',

        //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

        //签名方式 不需修改
        'sign_type'    => strtoupper('MD5'),

        //字符编码格式 目前支持 gbk 或 utf-8
        'input_charset'=> strtolower('utf-8'),

        //ca证书路径地址，用于curl中ssl校验
        //请保证cacert.pem文件在当前文件夹目录中
        'cacert'    => getcwd().'\\cacert.pem',

        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        'transport'    => 'http',

         'service' => 'alipay.wap.create.direct.pay.by.user',
        'notify_url' =>'http://'.$_SERVER['SERVER_NAME'].'/Pay/notify_url',
        'return_url' =>'http://'.$_SERVER['SERVER_NAME'].'/Pay/return_url',
        'payment_type' => 1,
        'seller_email' => 'waxiang767@163.com'
    ),
    /**************************请求参数配置**************************/
    'alipay'=>array(
        //支付类型
        'payment_type' => 1,
        //必填，不能修改
        //服务器异步通知页面路径
        'notify_url' => 'http://'.$_SERVER['SERVER_NAME'].'/Pay/notify_url',
        //需http://格式的完整路径，不能加?id=123这类自定义参数

        //页面跳转同步通知页面路径
        'return_url' => 'http://'.$_SERVER['SERVER_NAME'].'/Pay/return_url',
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

        //卖家支付宝帐户
        'seller_email' => 'waxiang767@163.com'
        //必填
        /************************************************************/
    ),
    //支付宝退款配置
    'tuikuan' => array(
        'partner' => '2088221353429535',
        'seller_user_id' => '2088221353429535',
        'key' => 'mom1y7np0674j6wswvz8g21mx8xyvnxm',
        'notify_url' => 'http://'.$_SERVER['SERVER_NAME'].'/tuikuan/notify_url',
        'sign_type' => strtoupper('MD5'),
        'refund_date' => date("Y-m-d H:i:s",time()),
        'service' => 'refund_fastpay_by_platform_pwd',
        'input_charset' => strtolower('utf-8'),
        'cacert' => getcwd().'\\cacert.pem',
        'transport' => 'http',
    ),
);