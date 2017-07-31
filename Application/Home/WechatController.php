<?php
/**
 * Created by PhpStorm.
 * User: yueping
 * Date: 2016/5/16
 * Time: 21:58
 * email:596169733@qq.com
 * 微信通知
 */
namespace Think\Controller;

use Think\Controller;

class WechatController extends Controller {
    private $Wechat = '';
    private $appId = 'wx41313960c99c7ff6';
    private $appSecret = '6d2e8bf921a4a5c1e97fd8f72c39eece';
    private $accessToken = 'xiaobaowx';

    public function __construct(){
        $this->Wechat = new \Extend\WechatAuth($this->appid, $this->appSecret, $this->accessToken);
    }

    public function index(){
        $this->valid();
        echo $this->accessToken;
    }

    public function inform_grocery(){

    }

    /**
     * 验证token
     */
    public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = $this->accessToken;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}