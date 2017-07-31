<?php
/**
 * Created by PhpStorm.
 * User: yueping
 * Date: 2016/2/13
 * Time: 22:29
 * email:596169733@qq.com
 * 课程模块
 */
namespace Grocery\Controller;

class CourseController extends BaseController {
    /**
     * 显示商户课程
     */
    public function index(){
        $c = M('course');

        $branch = M('branch_account')
            ->where('id = '.$_SESSION['account_id'])
            ->field('type')
            ->find();

        if($branch['type'] == 1){
            $where['groId'] = $_SESSION['groid'];
        }else{
            $where['branchId'] = $_SESSION['branchId'];
        }

        $count = M('course')
            ->join('left join branch b on b.id = course.branchId')
            ->where($where)
            ->count();
        $page = new \Extend\Page($count,6);
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $show = $page->show();

//        if(!empty($_POST['key'])){
//            $where['course.name'] = array('like',"%{$_POST['key']}%");
//        }
        $course = $c->
            order('course.id')->
            limit($page->firstRow.','.$page->listRows)->
            join('branch on course.branchId=branch.id')->
            field('course.*,branch.braName,branch.area,branch.circle')->
            where($where)->
            order('course.id DESC')->
            select();

        if(!empty($_GET['id'])){

            $where['id'] = I('id');
            $find = $c->where($where)->find();
            if($find['extension'] == 1){
                $stampImg = insert_str($find['image'],'.','_stamp');
                $img = new \Think\Image();
                $img->open('.'.$find['image'])->water('./Public/uploads/admin/course/tuiguang.png',\Think\Image::IMAGE_WATER_NORTHWEST)->save('.'.$stampImg);
                $data['extensionImg'] = $stampImg;
                $c->where($where)->setField($data);
                if(!$c){
                    $this->error($c->getLastSql(),'',10);
                }
            }
        }
        $this->assign('page',$show);
        $this->assign('course',$course);
        $this->display();
    }

    /**
     * 添加课程
     */
    public function addCourse(){
        $cate = M('category')->where('pid=0')->select();
        $branchs = M('branch')
            ->where('groId = '.$_SESSION['groid'])
            ->field('id,braName')
            ->select();

        if($_POST){
            $data['name'] = I('name');
            $data['image'] = uploadImage('/Public/uploads/admin/course/',$_FILES['image']);
            $data['price'] = round(I('price'),2);
            $data['cate1'] = I('cate1');
            $data['cate2'] = I('cate2');
            $data['branchId'] = I('branchId');
            $data['age'] = I('age');
            $data['detail'] = I('detail');

            if(M('course')->data($data)->add()){
                $this->success('添加课程成功！',U('course/index'),3);
                exit();
            }else{
                $this->error('添加失败！请重试！');
                exit();
            }
        }

        $this->assign('branchs',$branchs);
        $this->assign('cate1',$cate);
        $this->display();
    }

    /**
     * 课程类别二级联动
      */
    public function get_next_cate(){
        $pid = $_POST['pid'];
        $where['pid'] = $pid;
        $cate = M('category')->where($where)->select();
        $this->ajaxReturn($cate);
    }

    /**
     * 编辑课程
     */
    public function update(){
        $where['course.id'] = I('id');
        $c = M('course');
        $branchs = M('branch')
            ->where('groId = '.$_SESSION['groid'])
            ->field('id,braName')
            ->select();

        $course = $c->
            where($where)->
            join('left join category on category.id = course.cate2')->
            join('left join branch b on b.id = course.branchId')->
            field('course.*,category.*,b.area,b.circle')->
            find();

       
        $cate = M('category')->where('pid=0')->select();

        $this->assign('branchs', $branchs);
        $this->assign('cate1',$cate);
        $this->assign('course',$course);
        $this->assign('id',I('id'));
        $this->display();
    }

    /**
     * 删除课程
     */
    public function delete(){
        $where['id'] = I('id');
        $delete = M('course')->where($where)->delete();
        if($delete){
            $this->success('删除成功！');
        }else{
            $this->error('删除失败！请重试！');
        }
    }

}