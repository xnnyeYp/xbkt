<?php
/**
 * Created by PhpStorm.
 * User: yueping
 * Date: 2016/2/27
 * Time: 20:06
 * email:596169733@qq.com
 */
namespace Admin\Controller;

use Admin\Controller;

class ArticleController extends BaseController {
    public function index(){
        if(!empty($_POST['key'])){
            $key = trim($_POST['key']);
            $where['title'] = array('like',"%$key%");
        }

        if($_GET['cate']){
            $where['cate'] = I('cate');
        }

        $cate = M('article')
            ->field('count(*) count,cate')
            ->group('cate')
            ->select();

        $modela = M('article');
        $count = $modela->where($where)->count();
        $page = new \Extend\Page($count,6);
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $show = $page->show();
        $article = $modela->
            where($where)->
            join('left join grocery g on g.id=article.groid')->
            field('article.*,(select count(*) from article_collect ac inner join member m on m.id=ac.userid where ac.artid=article.id) as cnum,g.name')->
            limit($page->firstRow.','.$page->listRows)->
            group('article.id')->
            order('article.id DESC')->
            select();

        /*类别数量*/
        $count0 = $modela->count();


        $this->assign('count0',$count0);
        $this->assign('cate', $cate);
        $this->assign('page',$show);
        $this->assign('article',$article);
        $this->display();
    }

    /**
     * 删除文章
     */
    public function delete(){
        $where['id'] = I('id');
        $delete = M('article')->where($where)->delete();

        if($delete){
            $this->redirect(U('article/index'));
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
        $grolist = M('grocery')->select();
        if($_POST){
            $data['id'] = I('id');
            $data['title'] = I('title');
            if($_FILES['image']['name'] != null){
                $data['image'] = uploadImage('/Public/uploads/grocery/article/',$_FILES['image']);
            }
            $data['cate'] = I('cate');
            $data['content'] = I('content');
            $data['groid'] = I('groid');
            $data['createtime'] = time();
            $update = $modela->data($data)->save();
            if($update){
                $this->redirect(U('article/index'));
                exit();
            }else{
                $this->error('更新失败！请重试！');
                exit();
            }
        }else{
            $cate_where['pid'] = array('eq',0);
            $cate_where['type'] = 0;
            $cate = M('category')->where($cate_where)->select();
            $this->assign('cate',$cate);
        }

        $this->assign('grolist',$grolist);
        $this->assign('article',$article);
        $this->display();
    }

    /**
     * 文章收藏
     */
    public function collect(){
        $where['artid'] = I('id');
        $modela = M('article_collect');
        $count = $modela
            ->where($where)
            ->join('inner join member m on m.id=article_collect.userid')
            ->count();
        $page = new \Extend\Page($count,10);
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $show = $page->show();
        $userlist = $modela->
            where($where)->
            join('inner join member m on m.id=article_collect.userid')->
            field('m.username,m.headProtrait,m.id,m.mphone')->
            limit($page->firstRow.'.'.$page->listRows)->
            select();

        $this->assign('page',$show);
        $this->assign('userlist',$userlist);
        $this->assign('count',$count);
        $this->display();
    }

    /**
     * 文章口碑
     */
    public function praise(){
        $where['articleid'] = I('id');
        $modela = M('article_praise');
        $count = $modela->count();
        $page = new \Think\Pageb($count,10);
        $show = $page->show();
        $art_praise = M('article_praise')->
            where($where)->
            join('left join member m on m.id=article_praise.userid')->
            field('article_praise.*,m.username')->
            limit($page->firstRow.'.'.$page->listRows)->
            select();

        $article = M('article')->where('id = '.I('id'))->field('praisenum,point')->find();

        $this->assign('art_praise',$art_praise);
        $this->assign('article',$article);
        $this->assign('page',$show);
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
            $data['groid'] = I('groid');
            $data['createtime'] = time();

            $add = M('article')->add($data);
            if($add){
                $this->redirect(U('article/index'));
                exit();
            }else{
                $this->error('添加文章失败，请重试！');
                exit();
            }
        }else{
            $where['pid'] = array('eq',0);
            $where['type'] = 0;
            $cate = M('category')->where($where)->select();
            $grolist = M('grocery')->select();

            $this->assign('grolist',$grolist);
            $this->assign('cate',$cate);
        }
        $this->display();
    }
}