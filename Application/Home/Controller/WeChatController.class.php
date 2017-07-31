<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;

use Think\Controller;
use Extend\Wechat;

class WeChatController extends Controller{
    /**
     * 微信消息接口入口
     * 所有发送到微信的消息都会推送到该操作
     * 所以，微信公众平台后台填写的api地址则为该操作的访问地址
     */
    protected $token;
    protected $appid;
    protected $appsecret;
    protected $encodingaeskey;
    protected $options;
    protected $wechat;

    public function __construct(){
        parent::__construct();
        $this->token = C("WX_TOKEN"); 
        $this->appid = C("WX_APPID"); 
        $this->appsecret = C("WX_APPSECRET"); 
        $this->encodingaeskey = C("WX_ENCODINGAESKEY");
        //配置
        $this->options = array(
        'token'=>$this->token, //填写你设定的key
        'encodingaeskey'=>$this->encodingaeskey,//填写加密用的EncodingAESKey，如接口为明文模式可忽略
        'appid'=>$this->appid, //填写高级调用功能的app id
        'appsecret'=>$this->appsecret //填写高级调用功能的密钥
        );
        $this->wechat = new Wechat($this->options);
    }

    public function index($id = ''){

        $this->wechat->valid();//明文或兼容模式可以在接口验证通过后注释此句，但加密模式一定不能注释，否则会验证失败
        $type = $this->wechat->getRev()->getRevType();
        switch($type) {
            case Wechat::MSGTYPE_TEXT:
                $domain = C('WX_DOMAIN');
                $url = $domain."/WeChat/get_openid";
                $redirect = $this->wechat->getOauthRedirect($url);
                $url = $this->get_authorize_url();
                $this->wechat->text("<a href=\"$url\">点击绑定帐号</a>,其他功能建设中....")->reply();
                exit;
                    break;
            case Wechat::MSGTYPE_LOCATION:
                $localtion = $this->wechat->getRevGeo();
                $this->wechat->text(json_encode($localtion))->reply();
                exit;
                break;
            case Wechat::EVENT_SUBSCRIBE:
                $this->wechat->text("谢谢关注")->reply();
                exit;
                break;
            case Wechat::MSGTYPE_IMAGE:
                $this->wechat->text("...")->reply();
                break;
            default:
                $this->wechat->text("欢迎来到小宝课堂，小宝课堂开课辣")->reply();
        }
        $newmenu =  array(
           "button"=>
               array(
                   array('type'=>'view','name'=>'小宝课堂','url'=>'http://www.123bcb.com'),
                   array('type'=>'view','name'=>'绑定帐号','url'=>$this->get_authorize_url()),
                   )
           );
        $result = $this->wechat->createMenu($newmenu);

        $this->wechat->getMenu();
    }


    
    /**
     * 微信登陆DEMO
     * @return [type] [description]
     */
    public function getCode()
    {
        $data=array();
        $wechat = new Wechat($this->options);
        $wxdata = $wechat->getOauthAccessToken();
        
        dd($wxdata);

    }

    public function inform_grocery(){
        $url = 'www.123bcb.com/user/login';
        $data = array(
            'touser'=>$_GET['open_id'],
            'msgtype'=>'text',
            'text'=>array(
                'content'=>'您在小宝课堂有一个新的订单已付款，订单号为'.$_GET['out_trade_no'].",请及时<a href=$url>登录</a>小宝课堂商户管理中心处理！"
            )
        );
        var_dump($this->wechat->sendCustomMessage($data));
    }

    /**
     * 授权链接
     */
    public function get_authorize_url(){
        $url = "http://www.123bcb.com/WeChat/get_openid";
        return $this->wechat->getOauthRedirect($url);
    }

    /**
     * 获取用户open_id
     */
    public function get_openid(){
        $user_info = $this->wechat->getOauthAccessToken();
        $this->assign('openid',$user_info['openid']);
        $this->display();
    }

    public function success(){
        $where['mphone'] = $_POST['tel'];
        $where['type'] = 3;
        $data['openid'] = $_POST['openid'];
        if($_POST['active_code'] == $_SESSION['wechat']['code'] && (time() - $_SESSION['wechat']['validTime']) < 1800){
            if($_POST['type'] == 1){
                M('branch_account')->where($where)->save($data);
            }else{
                M('member')->where($where)->save($data);
            }
        }

        $this->display();
    }

    /**
     * 发送绑定动态码
     */
    public function sendMsg(){
        $send = new \Extend\sendMsgApi('fyxx','fyxx0108');
        $to = $_GET['to'];
        $code = mt_rand(100000,999999);
        $_SESSION['wechat']['code'] = $code;
        $_SESSION['wechat']['validTime'] = time();
        $content = '您正在绑定微信，您的验证码为'.$code.'，有效期为半个小时！';
        $_SESSION['wechat']['mphone'] = $to;
        $result = $send->sendSMS($content,$to);
        $this->ajaxReturn($code);
    }

    /**
     * 发送客服消息
     * @param array $data
     * @return array|bool
     */
    public function send_order_msg($data){
        $result = $this->wechat->sendTemplateMessage($data);
        return $result;
    }

    /**
     * 获取JS签名
     * @param $id
     * @return array|bool
     */
    public function getJsSign($id){
        $url = C('WX_DOMAIN').'/article/details/id/'.$id.'.html';
        return $this->wechat->getJsSign($url);
    }

    /**
     * 检查是否是分校管理员
     */
    public function check_branch_account(){
        $where['mphone'] = $_POST['to'];
        $branch_account = M('branch_account')
            ->where($where)
            ->find();

        $data = 0;
        if($branch_account){
            $data = 1;
        }

        $this->ajaxReturn($data);
    }
}
