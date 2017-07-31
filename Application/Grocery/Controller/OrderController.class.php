<?php
/**
 * Created by PhpStorm.
 * User: yueping
 * Date: 2016/5/10
 * Time: 10:37
 * email:596169733@qq.com
 * 商户订单管理
 */
namespace Grocery\Controller;


class OrderController extends BaseController {
    public function index(){
        if($_GET['status'] == 'no_process'){
            $where['o.status'] = array('in',array(0,1,2));
            $where['is_pay'] = 1;
        }elseif($_GET['status'] == 'process'){
            $where['o.status'] = array('gt',2);
        }

        $branch = M('branch_account')
            ->where('id = '.$_SESSION['account_id'])
            ->field('type')
            ->find();

        if($branch['type'] == 1){
            $where['groId'] = $_SESSION['groid'];
        }else{
            $where['c.branchId'] = $_SESSION['branchId'];
        }

        $count = M('course_order')
            ->alias('o')
            ->join('left join course c on c.id = o.course_id')
            ->join('branch b on b.id = c.branchId')
            ->join('member m on m.id = o.user_id')
            ->where($where)
            ->count();

        $page = new \Extend\Page($count,6);
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $show = $page->show();
        $order = M('course_order')
            ->alias('o')
            ->join('course c on c.id = o.course_id')
            ->join('member m on m.id = o.user_id')
            ->join('branch b on b.id = c.branchId')
            ->field('o.*,m.username,c.name,c.image,c.extension,c.extensionimg,c.price')
            ->where($where)
            ->limit($page->firstRow.','.$page->listRows)
            ->order('o.id DESC')
            ->select();

        $this->assign('page',$show);
        $this->assign('order',$order);
        $this->display();
    }

    public function update(){
        $where['o.id'] = I('id');
        if($_POST){
            $data['status'] = I('status');
            $data['receptionist'] = htmlspecialchars($_POST['receptionist']);
            if($data['status'] == 3 || $data['status'] == 4){
                $user = M('course_order')
                    ->alias('o')
                    ->join('left join course c on c.id = o.course_id')
                    ->join('branch b on b.id = c.branchId')
                    ->where('o.id = '.I('id'))
                    ->field('o.user_id,o.out_trade_no,o.course_id,c.name,o.experience_time,b.address,b.telephone')
                    ->find();

                $openid = M('member')->where('id = '.$user['user_id'])->field('openid')->find();
                $wechat = A('Home/WeChat');
            }
            if($data['status'] == 3){

                $user_order = M('course_order')->where('user_id = '.$user['user_id'])->count();
                //如果第一次体验课程，则奖励积分
                if($user_order == 1){
                    $point = M('point')->where('id = 4')->find();
                    M('member')->where('id = '.$user['user_id'])->setInc('point',$point['point']);
                    $data_p['user_id'] = $user['user_id'];
                    $data_p['point'] = $point;
                    $data_p['create_time'] = time();
                    $data_p['type'] = 1;

                    M('point_log')->data($data_p)->save();
                }

                //发送微信消息，提醒用户订单体验时间
                $msg_data = array(
                    'touser' => $openid['openid'],
                    'template_id' => 'sYxoyGxYDYTmcdo_sDiHZjbP1QPIAqo7LF2cXnGlUwo',
                    'url' => 'http://'.$_SERVER['SERVER_NAME'].'/course/coursedeta/id/'.$user['course_id'],
                    'topcolor' => '#000',
                    'data' => array(
                        'first' => array(
                            'value' => '您好，您在小宝课堂预约'.$user['name'].'：您好，你的你的预约课程已经通过审核，正式邀请你参加线下体验课程。',
                            'color' => '#000',
                        ),
                        'keyword1' => array(
                            'value' => $user['name'],
                            'color' => '#000',
                        ),
                        'keyword2' => array(
                            'value' => I('experience_time'),
                            'color' => '#000',
                        ),
                        'keyword3' => array(
                            'value' => $user['address'],
                            'color' => '#000',
                        ),
                        'remark' => array(
                            'value' => '联系电话：'.$user['telephone'],
                            'color' => '#000',
                        ),
                    )
                );

                $result = $wechat->send_order_msg($msg_data);

            }elseif($data['status'] == 4){
                $msg_data = array(
                    'touser' => $openid['openid'],
                    'template_id' => '1y0e2cqzGKWPlzNZ37Oa4UEOLZIT_psjV5arOUH6w_0',
                    'url' => 'http://'.$_SERVER['SERVER_NAME'],
                    'topcolor' => '#000',
                    'data' => array(
                        'first' => array(
                            'value' => '课程体验结束，请登录小宝课堂进行评价',
                            'color' => '#98d261'
                        ),
                        'keyword1' => array(
                            'value' => date('Y-m-d H:i:s'),
                            'color' => '#98d261'
                        ),
                        'keyword2' => array(
                            'value' => $user['name'],
                            'color' => '#98d261'
                        ),
                        'keyword3' => array(
                            'value' => '体验完成',
                            'color' => '#98d261'
                        ),
                        'remark' => array(
                            'value' => '感谢您使用小宝课堂，欢迎您再次前来学习',
                            'color' => '#98d261'
                        ),
                    )
                );

                $result = $wechat->send_order_msg($msg_data);

            }
            $data['experience_time'] = I('experience_time').' '.I('experience_time_2');
            if(empty($_POST['experience_code'])){
                $data['experience_code'] = 'BCB'.rand('100000000','999999999');
            }

            $result = M('course_order')
                ->alias('o')
                ->where($where)
                ->data($data)
                ->save();

            if($result){
                $this->redirect(U('order/index'));
            }
        }else {
            $order = M('course_order')
                ->alias('o')
                ->join('course c on c.id = o.course_id')
                ->join('member m on m.id = o.user_id')
                ->field('o.*,m.username,c.name,c.image,c.extension,c.extensionimg,c.price')
                ->where($where)
                ->find();
        }

        $this->assign('order',$order);
        $this->display();
    }

    /**
     * 取消订单
     */
    public function delete_order(){
        $where['id'] = $_GET['id'];
        M('course_order')->where($where)->delete();

        $this->redirect(U('order/index'));
    }
}