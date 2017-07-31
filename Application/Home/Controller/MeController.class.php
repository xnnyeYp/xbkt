<?php
/**
 * Created by PhpStorm.
 * User: yueping
 * Date: 2016/2/21
 * Time: 12:18
 * email:596169733@qq.com
 */
namespace Home\Controller;

use Think\Controller;

class MeController extends BaseController {

    public function _initialize(){
        $sid = session('userid');
        //判断用户是否登陆
        if(!isset($sid ) ) {
            redirect(U('user/nologin'));
        }
    }

    public function index(){
        if(empty($_SESSION['userid'])){
            $this->redirect('user/nologin');
        }
        $where['id'] = $_SESSION['userid'];
        $user = M('member')->where($where)->find();
        $order_count = M('course_order')
            ->where(array('user_id'=>$_SESSION['userid']))
            ->count();
        
        $this->assign('order_count',$order_count);
        $this->assign('user',$user);
        $this->display();
    }

    /**
     * 编辑资料
     */
    public function edit(){
        $where['id'] = $_SESSION['userid'];
        $user = M('member')->where($where)->find();

        $this->assign('user',$user);
        $this->display();
    }

    /**
     * 邀请二维码
     */
    public function invitation(){
        $where['id'] = $_SESSION['userid'];
        $user = M('member')->where($where)->field('qrcode,inviteCode')->find();

        $this->assign('user',$user);
        $this->display();
    }

    /**
     *我的收藏
     */
    public function  collect(){
        $where['userid'] = $_SESSION['userid'];
        $article = M('article_collect')->
            join('left join article on article.id=article_collect.artid')->
            join('join grocery on grocery.id=article.groid')->
            where($where)->
            field('article.*,grocery.numbering')->
            select();

        $this->assign('article',$article);
        $this->display();
    }

    /**
     * ajax跟新用户性别
     */
    public function update(){
        $where['id'] = I('id');
        $data = $_GET;
        $result = M('member')->where($where)->save($data);
        $this->ajaxReturn($result);
    }


    /**
     * ajax上传图像
     */
    public function upload_image() {
        $image = uploadImage('/Public/uploads/home/head/', $_FILES['image']);
        $this->ajaxReturn($image);
    }

    /**
     * ajax截取图像，保存为用户头像
     */
    public function crop_img(){
        $head_img = '/Public/uploads/home/head/user_head/'.mt_rand(100000,999999).$_SESSION['userid'].'.gif';
        $Image = new \Think\Image();
        $Image->open('.'.$_GET['img_src']);
        $img_info = getimagesize('.'.$_GET['img_src']);
        $ratio = $img_info[0]/$_GET['img_w'];
        $result = $Image->crop($_GET['w']*$ratio, $_GET['w']*$ratio, $_GET['x']*$ratio, $_GET['y']*$ratio, 55, 55)->save('.'.$head_img);
        $user_img =  M('member')->where('id = '.$_SESSION['userid'])->field('headProtrait')->find();
        M('member')->where('id = '.$_SESSION['userid'])->data(array('headProtrait'=>$head_img))->save();
        unlink('.'.$_GET['img_src']);
        unlink('.'.$user_img['headprotrait']);
        $this->ajaxReturn($head_img);
    }


    /**
     * 我的订单列表
     */
    public function order_list(){
        $where['user_id'] = $_SESSION['userid'];
//        $where['o.status'] = array('gt',0);
        $order_list = M('course_order')
            ->alias('o')
            ->where($where)
            ->join('left join course c on c.id = o.course_id')
            ->join('left join branch b on b.id = c.branchId')
            ->field("o.id,c.name,c.detail,c.image,b.braName,b.address,o.status,o.create_time,o.out_trade_no,o.appointment_time,o.experience_time,o.is_pay")
            ->order('o.id DESC')
            ->select();
        $user = M('member')->where('id = '.$_SESSION['userid'])->field('username')->find();

        $this->assign('user',$user);
        $this->assign('order_list',$order_list);
        $this->display();
    }

}