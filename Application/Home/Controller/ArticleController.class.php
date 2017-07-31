<?php
/**
 * Created by PhpStorm.
 * User: yueping
 * Date: 2016/2/28
 * Time: 19:02
 * email:596169733@qq.com
 */
namespace Home\Controller;

use Think\Controller;

class ArticleController extends BaseController {

    public function index(){
        /*文章页面图片*/
        $link = M('linkdeta')->where('linkid=3')->find();
        if($_GET['cate']){
            $where['cate'] = I('cate');
        }
        $where['g.status'] = 1;
        $modela = M('article');
        $article = $modela
            ->alias('a')
            ->where($where)
            ->join('left join grocery g on g.id = a.groid')
            ->field('a.*,g.numbering')
            ->select();

        $cate_where['pid'] = array('eq', 0);
        $cate_where['type'] = 0;
        $category = M('category')
            ->where($cate_where)
            ->select();

        $this->assign('category', $category);
        $this->assign('link',$link);
        $this->assign('article',$article);
        $this->display();
    }

    /**
     * 课程详情
     */
    public function details(){
        $where['article.id'] = I('id');
        $modela = M('article');
        $modela->where($where)->setInc('readnum');

        $article = $modela->
            where($where)->
            join('left join grocery g on g.id=article.groid')->
            join('left join article_share s on s.article_id = article.id')->
            field('article.*,g.numbering,count(s.id) as share_count')->
            find();

        $collect_num = M('article_collect')->where('artid='.I('id'))->count();

        //获取微信JS签名
        $js_sign = R('Home/WeChat/getJsSign',array(I('id')));

        $this->assign('js_sign',$js_sign);
        $this->assign('article',$article);
        $this->assign('collect_num',$collect_num);
        $this->display();
    }

    /**
     * 判断文章发表时间，如果小于一个星期，显示天数，否则，发表日期
     * @param string $time 时间戳
     * @return string $date 天数或者日期
     */
    private function judg_time($time){
        if((time()-$time) > 7*24*60*60){
            return date('Y-m-d',$time);
        }else{
            $day = (time() - $time)/(24*60*60);
            $day = round($day);
            return $day.'天前';
        }
    }

    /**
     * 打赏
     */
    public function praise() {
        if(!isset($_SESSION['userid'])){
            $this->redirect('user/nologin');
        }

        if ($_POST) {
            $point = I('point');
            /*查询用户的库存积分*/
            $modelam =M('member');
            $userid = 'id='.$_SESSION['userid'];
            $user = $modelam->where($userid)->field('point')->find();
            if($user['point'] < $point){
                $error = 1;
                $this->assign('error',$error);
                $this->assign('point',$user['point']);
                $this->display();
                exit();
            }
            $where['id'] = I('id');
            $modela = M('article');
            $modela->where($where)->setInc('praisenum');
            $modela->where($where)->setInc('point',$point);
            $modelam->where($userid)->setDec('point',$point);
            $modelam->where($userid)->setInc('bonusPoint',$point);

            $data['createtime'] =  time();
            $data['articleid'] = I('id');
            $data['userid'] = $_SESSION['userid'];
            $data['point'] = $point;
            $result = M('article_praise')->add($data);
            if($result){
                $this->redirect('article/details/id/'.I('id'));
            }
        }
        $this->assign('id',I('id'));
        $this->display();
    }

    /**
     * 收藏
     */
    public function collect_ajax(){
        $data['artid'] = I('id');
        $data['userid'] = $_SESSION['userid'];
        $find = M('article_collect')->where($data)->find();
        if($find){
            $this->ajaxReturn(0);
        }else{
            $data['createtime'] = time();
            $result = M('article_collect')->add($data);
            $collect_num = M('article_collect')->where('artid='.I('id'))->count();

            $ajax_data['result'] = $result;
            $ajax_data['collect_num'] = $collect_num;
            if($result){
                $this->ajaxReturn($ajax_data);
            }
        }

    }
    
    /**
     * 文章分享
     */
    public function article_share(){
        if(!empty($_SESSION['userid'])){
            $t = time();
            $start = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
            $map['create_time'] = array('GT',$start);
            $map['user_id'] = $_SESSION['userid'];
            $share_count = M('article_share')
                ->field('creat_time')
                ->where($map)
                ->count();

            if($share_count % 3 == 2){
                $point = M('point')
                    ->where('id = 3')
                    ->find();

                M('member')->where('id = '.$_SESSION['userid'])->setInc('point',$point['point']);
                $point_log_data = array(
                    'user_id' => $_SESSION['userid'],
                    'point' => $point['point'],
                    'message' => '每日分享三篇文章',
                    'create_time' => time(),
                    'type' => 1
                );
                M('point_log')->data($point_log_data)->add();
            }
        }

        $data['user_id'] = $_SESSION['userid'];
        $data['article_id'] = I('article_id');
        $data['create_time'] = time();
        $result = M('article_share')->data($data)->add();

        $count = M('article_share')->where('article_id = '.I('article_id'))->count();

        $this->ajaxReturn($count);
    }
}