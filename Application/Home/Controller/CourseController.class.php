<?php
/**
 * Created by PhpStorm.
 * User: yueping
 * Date: 2016/2/2
 * Time: 17:48
 * email:596169733@qq.com
 * 课程模块
 */
namespace Home\Controller;

use Think\Controller;

class CourseController extends BaseController {

    public function index(){
        if($_GET['cate']){
            $where['cate1'] = I('cate');
        }
        if($_POST){
            $where['course.name'] = array('like','%'.I('keyword').'%');
        }

        if($_GET['area']){
            $where['b.area'] = I('area');
        }

        if($_GET['price'] == '0'){
            $where['course.price'] = 0;
        }elseif(I('price') == 1){
            $where['course.price'] = array('neq',0);
        }

        if(I('age') == 1){
            $where['age'] = '3 - 12';
        }elseif(I('age') == 2){
            $where['age'] = '12 - 18';
        }

        $where['g.status'] = 1;
        $course = M('course')->
            join('branch b on b.id=course.branchId')->
            join('grocery g on g.id=b.groId')->
            join('course_praise cp on cp.courseid=course.id','LEFT')->
            field('course.*,g.numbering,b.braName,b.area,b.circle,(SELECT count(*) from course_praise cp WHERE cp.courseid=course.id) AS count')->
            where($where)->
            group('course.id')->
            select();

       $area = M('area')
           ->field('name')
           ->select();

        $area = array_unique_2($area);

        $cate = M('category')
            ->where(array('pid'=>'0','status'=>1,'type'=>1))
            ->select();

        foreach($cate as $v){
            if($v['id'] == I('cate')){
                $this->assign('category',$v['title']);
                break;
            }else{
                $this->assign('category','全部分类');
            }
        }

        $this->assign('cate',$cate);
        $this->assign('area',$area);
        $this->assign('course',$course);
        $this->display();
    }

    /**
     * 课程详情
     */
    public function coursedeta(){
        $where['course.id'] = I('id');
        $course = M('course')->
            join('branch b on b.id=course.branchId')->
            join('grocery g on g.id=b.groId')->
            field('course.*,g.numbering,b.area,b.circle,b.braName')->
            where($where)->
            find();
        $course['praise'] = $course['praisepoint']/$course['salepoint']*100;
        $course['praise'] = round($course['praise'],0);
        $praise_count = M('course_praise')->where('courseid='.I('id'))->count();
        $comment = M('course_praise')->
            join('member m on m.id=course_praise.userid')->
            field('course_praise.*,m.username,m.headProtrait')->
            where('courseid='.I('id'))->
            select();
        $out_trade_no = 'SH'.time();

        /*$detalis_thum = preg_replace('/(<img.+?src=")[^"]+(\/.+?>)/','[图片]',htmlspecialchars_decode($course['detail']));
        $detalis_thum = preg_replace('/<\/?[^>]+>/i','',$detalis_thum);
        $detalis_thum = $this->trimall($detalis_thum);

        $detalis_thum = mb_substr($detalis_thum,0,50);*/

//        $this->assign('details_thum',$detalis_thum);
        $this->assign('out_trade_no',$out_trade_no);
        $this->assign('comment',$comment);
        $this->assign('praise_count',$praise_count);
        $this->assign('course',$course);
        $this->display();
    }

    /**
     * 搜索课程
     */
    public function search(){
        $this->display();
    }

    public function search_school(){
        var_dump(C());
    }

    function trimall($str)//删除空格
    {
        $qian=array(" ","　","\t","\n","\r");$hou=array("","","","","");
        return str_replace($qian,$hou,$str);
    }
}