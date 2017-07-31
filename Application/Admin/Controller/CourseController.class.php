<?php
/**
 * Created by PhpStorm.
 * User: yueping
 * Date: 2016/2/17
 * Time: 18:50
 * email:596169733@qq.com
 * 课程管理模块
 */
namespace Admin\Controller;

class CourseController extends BaseController {
    public function index(){
        if(I('key')){
            $key = trim(I('key'));
            $where['course.name'] = array('like','%'.$key.'%');
            $where['g.name'] = array('like','%'.$key.'%');
            $where['_logic'] = 'or';
        }
        if(I('extension')){
            $where['extension'] = I('extension');
        }
        $modelc = M('course');
        $count = $modelc
            ->where($where)
            ->join('branch b on b.id=course.branchId')
            ->join('grocery g on g.id=b.groId')
            ->count();
        $page = new \Extend\Page($count,6);
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $show = $page->show();

        $course = $modelc->
            join('branch b on b.id=course.branchId')->
            join('grocery g on g.id=b.groId')->
            join('left join course_order o on o.course_id = course.id')->
            join('left join category ca on ca.id = course.cate1')->
            field('course.*,g.name as gname,b.braName,b.id bid,(select count(*) from course_order o left join course c on c.id = o.course_id where o.course_id = course.id) as order_count,ca.title')->
            where($where)->
            limit($page->firstRow.','.$page->listRows)->
            group('course.id')->
            order('course.id DESC')->
            select();

        $this->assign('course',$course);
        $this->assign('show',$show);
        $this->display();
    }

    /**
     * 学校分校二级联动
     */
    public function get_branch(){
        $where['groId'] = $_POST['groId'];
        $branch = M('branch')->where($where)->select();
        $this->ajaxReturn($branch);
    }

    /**
     * 课程详情/更新
     */
    public  function update(){
        $where['course.id'] = I('id');
        $c = M('course');
        $course = $c->
            where($where)->
            join('branch b on b.id=course.branchId')->
            join('grocery g on g.id=b.groId')->
            join('left join category on category.id = course.cate2')->
            field('course.*,category.*,g.name as gname,b.braName,b.id bid')->
            find();
        $cate = M('category')->where('pid=0 and type=1')->select();
        if($_POST) {
            $whereu['id'] = I('id');
            $data['name'] = I('name');
            if (!empty($_FILES['image']['name'])) {
                $data['image'] = uploadImage('/Public/uploads/admin/course/', $_FILES['image']);
                 if($course['extension'] == 1){
                     $stampImg = insert_str($data['image'],'.','_stamp');
                     $img = new \Think\Image();
                     $img->open('.'.$data['image'])->water('./Public/uploads/admin/course/tuiguang.png',\Think\Image::IMAGE_WATER_NORTHWEST)->save('.'.$stampImg);
                     $data['extensionImg'] = $stampImg;
                 }
            }
            $data['extension'] = I('extension');
            if(I('extension') == 1 && empty($_FILES['image']['name'])){
                $stampImg = insert_str($course['image'],'.','_stamp');
                $img = new \Think\Image();
                $img->open('.'.$course['image'])->water('./Public/uploads/admin/course/tuiguang.png',\Think\Image::IMAGE_WATER_NORTHWEST)->save('.'.$stampImg);
                $data['extensionImg'] = $stampImg;
            }
            $data['price'] = round(I('price'),2);
            $data['cate1'] = I('cate1');
            $data['cate2'] = I('cate2');
            $data['age'] = I('age');
            $data['detail'] = I('detail');
            $data['branchId'] = I('branchId');
            $result = $c->where($whereu)->save($data);
            if($result){
                $this->success('保存成功！',U('course/index',array('id'=>I('id'))));
                exit();
            }else{
                $this->error('保存失败！请重试！');
                exit();
            }
        }
        $grocery = M('grocery')->select();
        $this->assign('grocery',$grocery);
        $this->assign('cate1',$cate);
        $this->assign('course',$course);
        $this->assign('id',I('id'));
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
     * 删除课程
     */
    public function delete(){
        $where['id'] = $_GET['id'];
        M('course')->where($where)->delete();

        $this->redirect(U('course/index'));
    }

    /**
     * 创建课程
     */
    public function add(){
        if($_POST){
            $data['name'] = I('name');
            $data['image'] = uploadImage('/Public/uploads/admin/course/',$_FILES['image']);
            $data['price'] = round(I('price'),2);
            $data['cate1'] = I('cate1');
            $data['cate2'] = I('cate2');
            $data['branchId'] = I('branchId');
            $data['age'] = I('age');
            $data['detail'] = I('detail');
            $data['extension'] = I('extension');
            if(I('extension') == 1){
                $stampImg = insert_str($data['image'],'.','_stamp');
                $img = new \Think\Image();
                $img->open('.'.$data['image'])->water('./Public/uploads/admin/course/tuiguang.png',\Think\Image::IMAGE_WATER_NORTHWEST)->save('.'.$stampImg);
                $data['extensionImg'] = $stampImg;
            }

            M('course')->data($data)->add();

            $this->redirect(U('course/index'));
        }else{
            $cate = M('category')->where('pid=0 and type=1')->select();
            $grocery = M('grocery')->select();
            $this->assign('grocery',$grocery);
            $this->assign('cate1',$cate);
        }

        $this->display();
    }

    /**
     * 口碑
     */
    public function praise(){
        $where['courseid'] = I('id');
        $count = M('course_praise')
            ->where($where)
            ->count();

        $page = new \Extend\Page($count,10);
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $show = $page->show();

        $praise = M('course_praise')
            ->alias('p')
            ->where($where)
            ->join('left join member m on m.id = p.userid')
            ->field('p.*,m.username')
            ->limit($page->firstRow.','.$page->listRows)
            ->order('p.id DESC')
            ->select();

        $this->assign('page',$show);
        $this->assign('praise',$praise);
        $this->display();
    }

    /**
     * 设置口碑
     */
    public function update_praise(){
        if($_POST){
            $data['id'] = $_POST['id'];
            $data['point'] = $_POST['point'];
            $data['comment'] = $_POST['comment'];

            M('course_praise')->data($data)->save();

            $this->redirect(U('course/praise',array('id'=>$_POST['courseid'])));
        }else{
            $where['p.id'] = I('id');
            $praise = M('course_praise')
                ->alias('p')
                ->where($where)
                ->join('left join member m on m.id = p.userid')
                ->field('p.*,m.username')
                ->find();

            $this->assign('praise',$praise);
        }

        $this->display();
    }
}