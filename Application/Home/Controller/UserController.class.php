<?php
/**
 * Created by PhpStorm.
 * User: yueping
 * Date: 2016/1/31
 * Time: 20:19
 * email:596169733@qq.com
 * 用户登录模块
 */
namespace Home\Controller;

use Think\Controller;

class UserController extends BaseController {
    /**
     * 登录
     */
    public function login(){
        $error = 0;
        if($_POST){
            $Member = M('member');
            $accout = trim(I('account'));
            $password = md5(I('password'));
            $where['username'] = $accout;
            $where['mphone'] = $accout;
            $where['_logic'] = 'OR';
            $user = $Member->where($where)->find();
            if($password == $user['password'] && $user['status'] == 1){
                session('userid',$user['id']);
                $sql = "update member set login_ip = '".GetIP()."' where id = ".$user['id'];
                /*if(date("Ymd") != date("Ymd",$user['last_login_time'])){
                    $point = M('point')->where('id = 1')->find();
                    $data['message'] = "每天第一次登录";
                    $data['user_id'] = $user['id'];
                    $data['create_time'] = time();
                    $data['type'] = 1;
                    $data['point'] = $point['point'];
                    M('point_log')->add($data);

                    $sql = "update member set point = point + {$point['point']},last_login_time = ".time().",login_ip = '".GetIP()."' where id = ".$user['id'];
                }else{
                    $sql = "update member set login_ip = '".GetIP()."' where id = ".$user['id'];
                }*/
                $Member->execute($sql);
                $this->redirect('Choice/index');
            }elseif($user['status'] == 0){
               $error = 2;
            }elseif($password != $user['password']){
                $error = 1;
            }
        }
        $this->assign('mphone',I('mphone'));
        $this->assign('error',$error);
        $this->display();
    }

    /**
     * 注册第一步
     */
    public function registerf(){
        $_SESSION['inviteCode'] = I('inviteCode');
        $this->display();
    }

    /**
     * 注册第二步
     */
    public function registers() {
        if ($_POST) {
            $password1 = I('password1');
            $password2 = I('password2');
            $inviteCode = $_SESSION['inviteCode'];
            if($password1 == $password2){
                $point = M('point')->where('id = 2')->find();
                $m = M('member');
                $data['point'] = $point['point'];
                $data['mphone'] = $_SESSION['mphone'];
                $data['password'] = md5($password1);
                $data['create_at'] = time();
                $maxid = $m->max('id');
                $data['numbering'] = 'SH-GC-'.$maxid;
                $data['inviteCode'] = 'GCE'.mt_rand(1000000,9999999);
                $data['headProtrait'] = '/Public/uploads/home/default.jpeg';
                $url = 'http://'.$_SERVER['SERVER_NAME'].U('user/registerf',array('inviteCode'=>$data['inviteCode']), 'html',false,true);
                $qr = new \Extend\QRcode();
                $img = '/Public/home/images/QRcode/'.md5($data['inviteCode'].time()).'.png';
                $qr->png($url,'.'.$img,'L',13,1);

                $data['qrcode'] = $img;
                $result = $m->data($data)->add();
                if($result){
                    if(!empty($inviteCode)){
                        $where['inviteCode'] = $inviteCode;
                        $point = M('point')->where('id = 7')->find();
                        M('member')->where($where)-setInc('point',$point['point']);
                        $point = M('point')->where('id = 1')->find();

                        $user = M('member')->where($where)->find();
                        $data_p['message'] = "邀请新用户";
                        $data_p['user_id'] = $user['id'];
                        $data_p['create_time'] = time();
                        $data_p['type'] = 1;
                        $data_p['point'] = $point['point'];
                        M('point_log')->add($data_p);
                    }
                    $this->redirect('user/login');
                }else{
                    $this->error($m->getLastSql());
                }
            }
        }
        $this->display();
    }

    /**
     * 获取注册码
     */
    public function register_code(){
        $code = mt_rand(100000,999999);
        $_SESSION['code'] = $code;
        $_SESSION['validTime'] = time();
        $content = '欢迎您来到小宝课堂，您的注册验证码为'.$code.'，有效期为半个小时！';
        $to = $_GET['to'];
        $_SESSION['mphone'] = $to;
        $result = $this->sendMsg($to, $content);
        $this->ajaxReturn($code);
    }

    /**
     * @param $to
     * @param $content
     * @return mixed
     * ajax请求发送短信
     */
    private function sendMsg($to,$content){
        $send = new \Extend\sendMsgApi('fyxx','fyxx0108');
        $resutl = $send->sendSMS($content,$to);
        return $resutl;
    }

    /**
     * 退出登录
     */
    public function logout(){
        session('userid',null);
        $this->redirect('user/login');
    }

    /**
     * 没有登录
     */
    public function nologin(){
        $this->display();
    }
    
    public function reset_passwd(){
        $this->display();
    }

    /**
     * 获取重置密码验证码
     */
    public function reset_passwd_code(){
        $to = $_POST['to'];
        $code = mt_rand(100000,999999);
        $_SESSION['reset']['code'] = $code;
        $_SESSION['reset']['validTime'] = time();
        $content = '您正在修改密码，您的验证码为'.$code.'，有效期为半个小时！';
        $_SESSION['reset']['mphone'] = $to;
        $this->sendMsg($to,$content);
        $this->ajaxReturn($code);
    }
    
    /**
     * 重置密码
     */
    public function do_reset(){
        $data['password'] = I('password');
        $data['password'] = trim(I('password'));
        $data['password'] = md5($data['password']);
        $where['mphone'] = $_SESSION['reset']['mphone'];
        $result = M('member')
            ->where($where)
            ->save($data);

        if($result){
            $this->redirect('user/login/mphone/'.$_SESSION['mphone']);
        }
    }

    /**
     * 是否注册
     */
    public function is_register(){
        $where['mphone'] = I('mphone');
        $count = M('member')->where($where)->count();

        $this->ajaxReturn($count);
    }
}