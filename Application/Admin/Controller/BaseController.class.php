<?php
namespace Admin\Controller;
use Think\Controller;

class BaseController extends Controller {
    public function _initialize(){
        $sid = session('adminId');
        //判断用户是否登陆
        if(!isset($sid ) ) {
            redirect(U('Login/index'));
        }
        $order_count = M('course_order')
            ->alias('o')
            ->group('o.status')
            ->field('count(*) as count,o.status')
            ->join('course c on c.id = o.course_id')
            ->join('member m on m.id = o.user_id')
            ->join('branch b on b.id = c.branchId')
            ->join('grocery g on g.id = b.groId')
            ->select();
        $total = 0;
        $count = array();
        foreach($order_count as $v){
            $total += $v['count'];
            $count[$v['status']] = $v['count'];
        }
        $count1 = $count[1] + $count[2];
        $count2 = $count[3] + $count[4] + $count[5];

        $_SESSION['order']['total'] = $total;
        $_SESSION['order']['count1'] = $count1;
        $_SESSION['order']['count2'] = $count2;
        $_SESSION['order']['count3'] = $count[0] + $count['-1'];
    }

}