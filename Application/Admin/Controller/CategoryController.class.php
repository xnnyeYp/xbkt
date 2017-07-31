<?php
namespace Admin\Controller;
use Admin\Controller;
/**
 * 分类管理
 */
class CategoryController extends BaseController
{
    /**
     * 分类列表
     * @return [type] [description]
     */
    public function index() {
        $model = M('category');

        //课程分类
        $count = $model->where('type = 1')->count();
        $Page1 = new \Extend\Page($count,10);
        $Page1->setConfig('prev','上一页');
        $Page1->setConfig('next','下一页');
        $show1 = $Page1->show();
        $cate1 = $model
            ->alias('c1')
            ->join('left join category c2 on c1.pid=c2.id')
            ->limit($Page1->firstRow.','.$Page1->listRows)
            ->order('id DESC')
            ->where('c1.type = 1')
            ->field('c1.*,c2.title as father')
            ->select();

        //文章分类
        $count = $model->where('type = 2')->count();
        $Page2 = new \Extend\Page($count,10);
        $Page2->setConfig('prev','上一页');
        $Page2->setConfig('next','下一页');
        $show2 = $Page2->show();
        $cate2 = $model
            ->alias('c1')
            ->join('left join category c2 on c1.pid=c2.id')
            ->limit($Page2->firstRow.','.$Page2->listRows)
            ->order('id DESC')
            ->where('c1.type = 0')
            ->field('c1.*,c2.title as father')
            ->select();

        //商圈
        $circle = M('circle')->order('id DESC')->select();
        $this->assign('circle',$circle);

        //区域
        $area_count = M('area')->count();
        $Page_area = new \Extend\Page($area_count,10);
        $Page_area->setConfig('prev','上一页');
        $Page_area->setConfig('next','下一页');
        $show_area = $Page_area->show();
        $area = M('area')
            ->alias('a1')
            ->join('left join area a2 on a1.pid = a2.id')
            ->field('a1.*,a2.name as father')
            ->limit($Page_area->firstRow.','.$Page_area->listRows)
            ->order('id DESC')
            ->select();

        $this->assign('area',$area);

        $this->assign('page1',$show1);
        $this->assign('page2',$show2);
        $this->assign('article_cate',$cate2);
        $this->assign('course_cate',$cate1);
        $this->assign('page_area', $show_area);
        $this->display();
    }

    /**
     * 添加分类
     */
    public function add()
    {
        //默认显示添加表单
        if (!IS_POST) {
           $where['type'] = $_GET['cate_type'];
            $model = M('category')->where($where)->select();
            $cate = getSortedCategory($model);

            $this->assign('cate',$cate);
            $this->display();
        }
        if (IS_POST) {
            //如果用户提交数据
            $data['title'] = I('title');
            $data['pid'] = I('pid');
            $data['status'] = I('status');
            $data['type'] = I('type');
            if($_FILES['icon']['size'] > 0){
                $data['icon'] = uploadImage('/Public/uploads/admin/category/',$_FILES['icon']);
            }
            $result = M('category')->add($data);

            $tab = 1;
            if(I('type') == 0){
                $tab = 2;
            }

            if ($result) {
                $this->redirect( U('category/index',array('tab'=>$tab)));
            } else {
                $this->error("分类添加失败");
            }
        }
    }
    /**
     * 更新分类信息
     * @param  [type] $id [分类ID]
     * @return [type]     [description]
     */
    public function update()
    {
        //默认显示添加表单
        if (!IS_POST) {
            $model = M('category')->find(I('id'));

            $this->assign('cate',getSortedCategory(M('category')->select()));
            $this->assign('model',$model);
            $this->display();
        }
        if (IS_POST) {
            $data['title'] = I('title');
            $data['pid'] = I('pid');
            $data['status'] = I('status');
            $data['keywords'] = I('keywords');
            $data['description'] = I('description');
            $data['type'] = I('type');
            if($_FILES['icon']['size'] > 0){
                $data['icon'] = uploadImage('/Public/uploads/admin/category/',$_FILES['icon']);
            }
            $result = M('category')->where('id = '.I('id'))->data($data)->save();

            if(I('type') == 1){
                $tab = 1;
            }else if(I('type') == 0){
                $tab = 2;
            }

            if ($result) {
                $this->success("分类更新成功", U('category/index',array('tab'=>$tab)));
            } else {
                $this->error("分类更新失败");
            }

        }
    }
    /**
     * 删除分类
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function delete($id)
    {
        $model = M('category');
        //查询属于这个分类的文章
        $posts = M('post')->where('cate_id='.$id)->select();
        $cate = $model->where('id = '.$id)->find();
        $tab = 1;
        if($cate['type'] == 0){
            $tab = 2;
        }
        if($posts){
            $this->error("禁止删除含有文章的分类");
        }
        //禁止删除含有子分类的分类
        $hasChild = $model->where('pid='.$id)->select();
        if($hasChild){
            $this->error("禁止删除含有子分类的分类");
        }
        //验证通过
        $result = $model->delete($id);

        if($result){
            $this->redirect( U('category/index',array('tab'=>$tab)));
        }else{
            $this->error("分类删除失败");
        }
    }

    /**
     * 添加商圈或区域
     */
    public function add_circle_area(){
        if($_POST){
            if($_POST['type'] == 1){
                $data['name'] = $_POST['name'];

                M('circle')->data($data)->add();
                $this->redirect(U('category/index',array('tab'=>3)));
            }else{
                $data['name'] = $_POST['name'];
                $data['pid'] = $_POST['pid'];

                M('area')->data($data)->add();
                $this->redirect(U('category/index',array('tab'=>4)));
            }
        }

        if($_GET['type'] != 1){
            $area = M('area')->where('pid = 0')->select();

            $this->assign('area',$area);
        }
        $this->assign('type',$_GET['type']);
        $this->display();
    }

    /**
     * 删除商圈或区域
     */
    public function delete_circle_area(){
        $where['id'] = I('id');
        if($_GET['type'] == 1){
            M('circle')->where($where)->delete();

            $this->redirect(U('category/index',array('tab'=>3)));
        }else{
            M('area')->where($where)->delete();
            $this->redirect(U('category/index',array('tab'=>4)));
        }
    }
    
    /**
     * 更新商圈或区域
     */
    public function update_circle_area(){
        if($_POST){
            if($_POST['type'] == 1){
                $data['id'] = $_POST['id'];
                $data['name'] = $_POST['name'];

                M('circle')->data($data)->save();
                $this->redirect(U('category/index',array('tab'=>3)));
            }else{
                $data['id'] = $_POST['id'];
                $data['name'] = $_POST['name'];
                $data['pid'] = $_POST['pid'];

                M('area')->data($data)->save();
                $this->redirect(U('category/index',array('tab'=>4)));
            }
        }else{
            $where['id'] = $_GET['id'];
            if($_GET['type'] == 1){
                $circle = M('circle')->where($where)->find();

                $this->assign('circle',$circle);
            }else{
                $area = M('area')->where($where)->find();
                $areas = M('area')->where('pid = 0')->select();

                $this->assign('areas',$areas);
                $this->assign('area',$area);
            }
        }
        $this->assign('type',$_GET['type']);
        $this->assign('id',$_GET['id']);
        $this->display();
    }


    public function check_area(){
        $map['name'] = I('post.name');
        $result = M('area')
            ->where($map)
            ->count();

        $this->ajaxReturn($result);
    }
}
