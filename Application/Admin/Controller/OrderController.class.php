<?php
/**
 * Created by PhpStorm.
 * User: yueping
 * Date: 2016/5/28
 * Time: 20:07
 * email:596169733@qq.com
 */
namespace Admin\Controller;

use Think\Controller;

class OrderController extends BaseController {

    public function index(){
        if($_GET['type'] == 1){
            $where['o.status'] = array('in','2,0');
        }elseif($_GET['type'] == 2){
            $where['o.status'] = array('in','3,4,5');
        }elseif($_GET['type'] == 3){
            $where['o.status'] = array('in','-1,0');
        }

       if($_POST['key'] != ''){
           $map['c.name'] =  array('like','%'.$_POST['key'].'%');
           $map['g.name'] =  array('like','%'.$_POST['key'].'%');
           $map['_logic'] = 'or';
           $where['_complex'] = $map;
       }elseif($_POST['start_time'] != ''){
           $where['o.create_time'] = array('egt',trim(strtotime($_POST['start_time'])));
           $where['_logic'] = 'or';
       }
        if($_POST['end_time'] != ''){
           $where['p.createtime'] = array('elt',trim(strtotime($_POST['end_time'])));
       }

        $count = M('course_order')
            ->alias('o')
            ->join('course c on c.id = o.course_id')
            ->join('member m on m.id = o.user_id')
            ->join('branch b on b.id = c.branchId')
            ->join('grocery g on g.id = b.groId')
            ->join('left join course_praise p on p.order_id = o.id')
            ->where($where)
            ->order('o.id DESC')
            ->count();

        $page = new \Extend\Page($count,6);
        $page->setConfig('prev', '上一页');
        $page->setConfig('next', '下一页');
        $show = $page->show();
        $order = M('course_order')
            ->alias('o')
            ->join('course c on c.id = o.course_id')
            ->join('member m on m.id = o.user_id')
            ->join('branch b on b.id = c.branchId')
            ->join('grocery g on g.id = b.groId')
            ->join('left join course_praise p on p.order_id = o.id')
            ->field('o.*,m.username,c.name,c.image,c.extension,c.extensionimg,c.price')
            ->limit($page->firstRow.','.$page->listRows)
            ->where($where)
            ->order('o.id DESC')
            ->select();

        $this->assign('page',$show);
        $this->assign('order',$order);
        $this->display();
    }

    public function update(){
        $where['o.id'] = I('id');

        $order = M('course_order')
            ->alias('o')
            ->join('course c on c.id = o.course_id')
            ->join('member m on m.id = o.user_id')
            ->join('left join course_praise p on p.order_id = o.id')
            ->join('left join branch_account b on b.id = o.receptionist')
            ->field('o.*,m.username as m_username,c.name,c.image,c.extension,c.extensionimg,c.price,p.point,p.comment,b.username,p.createtime')
            ->where($where)
            ->find();
        $this->assign('order',$order);

        $this->display();
    }

    /**
     * 删除
     */
    public function delete(){
        $where['id'] = I('id');
        M('course_order')->where($where)->delete();
        $this->redirect(U('order/index'));
    }
}