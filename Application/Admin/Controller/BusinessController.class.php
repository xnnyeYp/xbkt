<?php
/**
 * Created by PhpStorm.
 * User: yueping
 * Date: 2016/5/13
 * Time: 19:59
 * email:596169733@qq.com
 * 会员业务
 */
namespace Admin\Controller;


class BusinessController extends BaseController {

    /**
     * 普通会员业务
     */
    public function member(){
        $where['id'] = I('id');
        $order_count = M('course_order')
            ->where('user_id = '.I('id'))
            ->count();

        $collect_count = M('article_collect')
            ->where('userid = '.I('id'))
            ->count();

        $point = M('member')
            ->where('id = '.I('id'))
            ->field('point')
            ->find();

        if(I('type') == 3){//获取积分日志列表
            $Point_log = M('point_log');
            $log_count = $Point_log
                ->where('user_id = '.I('id'))
                ->count();

            $page = new \Extend\Page($log_count,10);
            $page->setConfig('prev','上一页');
            $page->setConfig('next','下一页');
            $point_log = $Point_log
                ->alias('p')
                ->join('left join member m on m.id = p.user_id')
                ->where('p.user_id = '.I('id'))
                ->field('p.*,m.username')
                ->limit($page->firstRow.','.$page->listRows)
                ->select();

            $this->assign('point_log',$point_log);
        }elseif(I('type') == 2){//获取收藏列表
            $page = new \Extend\Page($collect_count,6);
            $page->setConfig('prev','上一页');
            $page->setConfig('next','下一页');
            $collect = M('article_collect')
               ->alias('ac')
               ->join('left join article a on a.id = artid')
               ->join('left join grocery g on g.id = a.groid')
               ->where('ac.userid = '.I('id'))
               ->field('a.title,a.image,ac.createtime,a.cate,g.name')
               ->limit($page->firstRow.','.$page->listRows)
               ->select();

            $this->assign('collect',$collect);
        }else{//获取订单列表
            $page = new \Extend\Page($order_count,6);
            $page->setConfig('prev','上一页');
            $page->setConfig('next','下一页');
            $order = M('course_order')
                ->alias('o')
                ->join('left join course c on c.id = o.course_id')
                ->join('left join branch b on b.id = c.branchId')
                ->join('left join grocery g on g.id = b.groId')
                ->field('o.id,o.out_trade_no,o.status,o.create_time,c.name as cname,c.price,c.image,c.extension,c.extensionImg,b.braName,b.address,g.name as gname')
                ->where('o.user_id = '.I('id'))
                ->limit($page->firstRow.','.$page->listRows)
                ->select();

            $this->assign('order',$order);
        }

        $show = $page->show();
        $this->assign('page',$show);
        $this->assign('type',I('type'));
        $this->assign('order_count',$order_count);
        $this->assign('collect_count',$collect_count);
        $this->assign('point',$point);
        $this->assign('id',I('id'));
        $this->display();
    }
    
    public function grocery(){
        $where['g.id'] = I('id');
        $Order = M('course_order');
        $order_count = $Order
            ->alias('o')
            ->join('left join course c on c.id = o.course_id')
            ->join('left join branch b on b.id = c.branchId')
            ->join('left join grocery g on g.id = b.groId')
            ->order('o.id DESC')
            ->where($where)
            ->count();

        $Article = M('article');
        $article_count = $Article
            ->alias('a')
            ->join('left join grocery g on g.id = a.groId')
            ->where($where)
            ->count();

        //文章分类
        $article_cate = $Article
            ->alias('a')
            ->join('left join grocery g on g.id = a.groId')
            ->where($where)
            ->field('count(*) as count,cate')
            ->group('a.cate')
            ->select();

        $Course = M('course');
        $course_count = $Course
            ->alias('c')
            ->join('left join branch b on b.id = c.branchId')
            ->join('left join grocery g on g.id = b.groId')
            ->where($where)
            ->count();

        if(I('type') == 1){
            $page = new \Extend\Page($order_count,6);
            $page->setConfig('prev','上一页');
            $page->setConfig('next','下一页');
            $order = M('course_order')
                ->alias('o')
                ->join('left join course c on c.id = o.course_id')
                ->join('left join branch b on b.id = c.branchId')
                ->join('left join grocery g on g.id = b.groId')
                ->field('o.id,o.out_trade_no,o.status,c.name as cname,c.price,c.image,c.extension,c.extensionImg,b.braName,b.address,g.name as gname')
                ->where($where)
                ->limit($page->firstRow.','.$page->listRows)
                ->select();

            $this->assign('order',$order);
        }elseif(I('type') == 2){
            $page = new \Extend\Page($course_count,6);
            $page->setConfig('prev','上一页');
            $page->setConfig('next','下一页');
            $course = $Course
                ->alias('c')
                ->join('left join branch b on b.id = c.branchId')
                ->join('left join grocery g on g.id = b.groId')
                ->where($where)
                ->field('c.*')
                ->select();

            $this->assign('course',$course);
        }elseif(I('type') == 3){
            if(!empty($_GET['cate'])){
                $where['a.cate'] = I('cate');
            }
            $page = new \Extend\Page($article_count,6);
            $page->setConfig('prev','上一页');
            $page->setConfig('next','下一页');
            $article =$Article
                ->alias('a')
                ->alias('a')
                ->join('left join grocery g on g.id = a.groId')
                ->join('left join article_collect ac on ac.artid = a.id')
                ->where($where)
                ->limit($page->firstRow.','.$page->listRows)
                ->field('a.*,count(ac.id) as collect_count')
                ->select();

                $this->assign('article',$article);
                $this->assign('article_cate',$article_cate);
        }

        $show = $page->show();

        $gro_name = M('grocery')->where("id = {$_GET['id']}")->getField('name');

        $this->assign('gro_name', $gro_name);
        $this->assign('article_count',$article_count);
        $this->assign('course_count',$course_count);
        $this->assign('order_count',$order_count);
        $this->assign('type',I('type'));
        $this->assign('id',I('id'));
        $this->assign('page',$show);
        $this->display();
    }
}
