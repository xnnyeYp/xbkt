<?php
/**
 * Created by PhpStorm.
 * User: yueping
 * Date: 2016/2/26
 * Time: 19:04
 * email:596169733@qq.com
 */
namespace Grocery\Controller;

use Grocery\Controller;

class ArticleController extends BaseController {
    public function index(){
        if($_GET['cate']){
            $where['cate'] = I('cate');
        }
        $where['groid'] = $_SESSION['groid'];

        $modela = M('article');
        $count = $modela->where($where)->count();

        $page = new \Extend\Page($count,6);
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $show = $page->show();
        $article = $modela->
            where($where)->
            join('left join article_collect ac on ac.artid=article.id')->
            field('article.*,(select count(*) from article_collect ac where ac.artid=article.id) as cnum')->
            limit($page->firstRow.','.$page->listRows)->
            group('article.id')->
            select();

        /*类别数量*/
        $count0 = $modela->where('groid='.$_SESSION['groid'])->count();

        $cate = M('article')
            ->field('count(*) count,cate')
            ->where('groid='.$_SESSION['groid'])
            ->group('cate')
            ->select();

        $this->assign('cate',$cate);
        $this->assign('count0',$count0);
        $this->assign('page',$show);
        $this->assign('article',$article);
        $this->display();
    }

    /**
     * 添加文章
     */
    public function add(){
        if($_POST){
            $data['title'] = I('title');
            $data['image'] = uploadImage('/Public/uploads/grocery/article/',$_FILES['image']);
            $data['cate'] = I('cate');
            $data['content'] = I('content');
            $data['groid'] = $_SESSION['groid'];
            $data['createtime'] = time();

            $add = M('article')->add($data);
            if($add){
                $this->success('添加文章成功',U('article/index'));
                exit();
            }else{
                $this->error('添加文章失败，请重试！');
                exit();
            }
        }else{
            $where['pid'] = array('neq',0);
            $where['type'] = 0;
            $cate = M('category')->where($where)->select();
            $this->assign('cate',$cate);
        }
        $this->display();
    }

    /**
     * 删除文章
     */
    public function delete(){
        $where['id'] = I('id');
        $delete = M('article')->where($where)->delete();

        if($delete){
            $this->success('删除文章成功！');
        }else{
            $this->error('删除文章失败！请重试！');
        }
    }

    /**
     * 更新查看课程
     */
    public function update(){
        $where['id'] = I('id');
        $modela = M('article');
        $article = $modela->where($where)->find();
        if($_POST){
            $data['id'] = I('id');
            $data['title'] = I('title');
            if($_FILES['image']['name'] != null){
                $data['image'] = uploadImage('/Public/uploads/grocery/article/',$_FILES['image']);
            }
            $data['cate'] = I('cate');
            $data['content'] = I('content');
            $data['groid'] = $_SESSION['groid'];
            $data['createtime'] = time();
            $update = $modela->data($data)->save();
            if($update){
                $this->success('更新成功！',U('article/index'));
                exit();
            }else{
                $this->error('更新失败！请重试！');
                exit();
            }
        }

        $map['pid'] = array('neq',0);
        $map['type'] = 0;
        $cate = M('category')->where($map)->select();

        $this->assign('cate',$cate);
        $this->assign('article',$article);
        $this->display();
    }
}