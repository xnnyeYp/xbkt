<?php
namespace Grocery\Controller;
use Think\Controller;

class BaseController extends Controller {

    public function _initialize(){
       if(!empty($_SESSION['account_id'])){
            $sid = session('account_id');
            $branch = M('branch_account')
                ->where('id = '.$_SESSION['account_id'])
                ->field('type')
                ->find();
        }


        if($branch['type'] == 1){
            $where['groId'] = $_SESSION['groid'];
        }else{
            $where['c.branchId'] = $_SESSION['branchId'];
        }
        $where['o.status'] = array('in',array('0','1','2'));
        $where['o.is_pay'] = 1;
        $no_process = M('course_order')
            ->alias('o')
            ->join('left join course c on c.id = o.course_id')
            ->join('member m on m.id = o.user_id')
            ->join('branch b on b.id = c.branchId')
            ->where($where)
            ->order('o.id')
            ->count();
        $_SESSION['no_process'] = $no_process;

        //判断用户是否登陆
        if(!isset($sid ) ) {
            redirect(U('Login/index'));
        }

    }

}