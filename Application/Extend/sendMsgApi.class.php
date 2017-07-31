<?php
/**
 * Created by PhpStorm.
 * User: yueping
 * Date: 2016/2/2
 * Time: 14:38
 * email:596169733@qq.com
 * 那时快短信接口
 */
namespace Extend;

class sendMsgApi{
    const Baseurl = "http://api5.nashikuai.cn/SendSms.aspx";
    private $account;
    private $passwd;
    public function __construct($account,$password) {
        $this->account = $account;
        $this->passwd = md5($account.$password);
    }

    /**
     * 发送短信
     * @param string $msg 短信内容
     * @param string $mobs 手机号码
     */
    public function sendSMS($msg,$mobs){
        $body = "<?xml version='1.0' encoding='utf-8' ?>
<message>
  <user>{$this->account}</user>
  <passwd>{$this->passwd}</passwd>
  <msg>{$msg}</msg>
  <mobs>{$mobs}</mobs>
  <ts></ts>
  <dtype>1</dtype>
  <extno></extno>
</message>
";
        $body = trim($body);
        $ch = curl_init(self::Baseurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$body);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}