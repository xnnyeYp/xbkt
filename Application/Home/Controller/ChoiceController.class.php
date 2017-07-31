<?php
/**
 * Created by PhpStorm.
 * User: yueping
 * Date: 2016/1/31
 * Time: 15:33
 * email:596169733@qq.com
 * 精选页面
 */
namespace Home\Controller;

use Think\Controller;

class ChoiceController extends BaseController {

    public function index(){
        $modelc = M('course');
        $course = $modelc->
            join('branch b on b.id=course.branchId')->
            join('grocery g on g.id=b.groId')->
            join('course_praise cp on cp.courseid=course.id','LEFT')->
            field('course.*,g.numbering,b.braName,b.area,b.circle,(SELECT count(*) from course_praise cp WHERE cp.courseid=course.id) AS count')->
            where('extension = 1 and g.status = 1')->
            group('course.id')->
            order('course.praisepoint/course.salepoint*praisenum DESC')->
            select();
        $link = M('linkdeta')->where('linkid = 1')->order('sortnu')->select();
        $cate = M('category')
            ->where(array('pid'=>'0','status'=>1,'type'=>1))
            ->select();

        $this->assign('cate',$cate);
        $this->assign('link',$link);
        $this->assign('course',$course);
        $this->display();
    }
}