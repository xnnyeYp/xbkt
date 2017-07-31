<?php
namespace Admin\Controller;
use Admin\Controller;

class IndexController extends BaseController{

    public function index(){
        

        $this->display();
    }
    
    public function setup(){
        if($_POST){
            $where['password'] = md5(I('post.old_password'));
            $where['username'] = 'admin';
            $admin = M('member')->where($where)->find();

            if(!$admin){
                $this->error('原密码错误');
            }else{
                $password = md5(I('post.password'));
                $result = M('member')->where('id = 1')->setField('password', $password);

            }
            if($result){
                $this->success('修改密码成功！',U('index/index'));
                exit();
            }
        }

        $this->display();
    }

    public function check_old_password(){
        $where['password'] = md5(I('post.old_password'));
        $where['username'] = 'admin';
        $admin = M('member')->where($where)->find();
        $result = 0;
        if($admin){
            $result = 1;
        }
        $this->ajaxReturn($result);
    }
}
