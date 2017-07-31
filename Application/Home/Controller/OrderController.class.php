<?php
/**
 * Created by PhpStorm.
 * User: yueping
 * Date: 2016/4/16
 * Time: 14:23
 * email:596169733@qq.com
 * 订单
 */
namespace Home\Controller;

use Think\Controller;

class OrderController extends Controller {

    public function _initialize(){
        header("Content-type:text/html;charset=utf-8");
        import('Vendor.Alipay.alipayCore','','.php');
        import('Vendor.Alipay.alipayMd5','','.php');
        import('Vendor.Alipay.alipayNotify','','.php');
        import('Vendor.Alipay.alipaySubmit','','.php');
        if(empty($_SESSION['userid'])){
            $this->redirect('/User/nologin');
        }
    }

    /**
     * 预约课程
     */
    public function appointment(){
            $where['out_trade_no'] = I('out_trade_no');

            $order = M('course_order')->where($where)->find();
            if ($order) {
                $course_id = $order['course_id'];
                $this->assign('create_time', $order['create_time']);
            } else {
                $data['course_id'] = $_POST['course_id'];
                $data['user_id'] = $_SESSION['userid'];
                $data['out_trade_no'] = $_POST['out_trade_no'];
                $data['is_pay'] = 0;
                $data['create_time'] = time();
                M('course_order')->data($data)->add();
                $course_id = $_POST['course_id'];
                $this->assign('create_time', $data['create_time']);
            }


            $course = M('course')
                ->join('branch b on b.id=course.branchId')
                ->join('grocery g on g.id=b.groId')
                ->where('course.id = ' . $course_id)
                ->field('course.*,g.numbering,b.area,b.circle,b.braName,b.address')->
                find();

            $detalis_thum = preg_replace('/(<img.+?src=")[^"]+(\/.+?>)/','[图片]',htmlspecialchars_decode($course['detail']));
            $detalis_thum = preg_replace('/<\/?[^>]+>/i','',$detalis_thum);
            $detalis_thum = $this->trimall($detalis_thum);
            $detalis_thum = mb_substr($detalis_thum,0,50);

            //生成sign
        $configs = array(
            'return_url'	=>C('alipay.return_url'),
            'notify_url'	=>C('alipay.notify_url'),	//服务器异步通知页面路径(必填)
            'subject'		=>$course['name'],	//订单名称(必填)
            'total_fee'		=>$course['price'],	//付款金额(必填)
            'body'			=>$detalis_thum,	//订单描述
            'show_url'		=>$_POST['show_url'],	//商品展示地址
            'out_trade_no'  =>$_POST['out_trade_no'],
        );

            $this->assign('details_thum',$detalis_thum);
            $this->assign('out_trade_no', I('out_trade_no'));
            $this->assign('course', $course);
            $this->display();
    }

    function trimall($str)//删除空格
    {
        $qian=array(" ","　","\t","\n","\r");$hou=array("","","","","");
        return str_replace($qian,$hou,$str);
    }

    /**
     * 订单详情
     */
    public function details(){
        $where['out_trade_no'] = $_GET['out_trade_no'];
        $order = M('course_order')
            ->alias('o')
            ->join('member m on m.id = o.user_id')
            ->field('o.*,m.mphone')
            ->where($where)
            ->find();

        $course = M('course')
            ->join('branch b on b.id=course.branchId')
            ->join('grocery g on g.id=b.groId')
            ->where('course.id = '.$order['course_id'])
            ->field('course.*,g.numbering,b.area,b.circle,b.braName,b.address,b.id as branch_id')->
            find();

        $course['praise'] = $course['praisepoint']/$course['salepoint']*100;
        $course['praise'] = round($course['praise'],0);
        $praise_count = M('course_praise')->where('courseid='.$order['course_id'])->count();

        $detalis_thum = preg_replace('/(<img.+?src=")[^"]+(\/.+?>)/','[图片]',htmlspecialchars_decode($course['detail']));
        $detalis_thum = preg_replace('/<\/?[^>]+>/i','',$detalis_thum);
        $detalis_thum = $this->trimall($detalis_thum);
        $detalis_thum = mb_substr($detalis_thum,0,50);

        $this->assign('details_thum',$detalis_thum);
        $this->assign('order',$order);
        $this->assign('praise_count',$praise_count);
        $this->assign('course',$course);
        if($order['status'] == 5) {
            $praise = M('course_praise')
                ->where('order_id = ' . $order['id'])
                ->find();
            $this->assign('praise', $praise);
            $this->display('close_order');
        }elseif($order['status'] == 2){
            $where['a.type'] = 3;//分校管理员
            $openid = M('branch')
                ->alias('b')
                ->join('left join course c on c.branchId = b.id')
                ->join('left join course_order o on o.course_id = c.id')
                ->join('left join branch_account a on a.branchId = b.id')
                ->join('left join grocery g on g.id = b.groId')
                ->where($where)
                ->field('a.openid,b.telephone,a.mphone')
                ->find();

            if($course['price'] == 0){
                $price = '免费';
            }else {
                $price = $course['price'].'元';
            }
            //普通会员预约课程，给商户发送微信消息
            $data = array(
                "touser" => "{$openid['openid']}",
                'template_id' => 'U3u0_9_8nEfQcvt72lSlKZ9mJ-oo3R_N9UaKh6Qap8I',
                'url' => 'http://'.$_SERVER['SERVER_NAME'].'/course/id/'.$order['course_id'],
                'topcolor' => '#000',
                'data' => array(
                    'first' => array(
                        'value' => '您在小宝课堂有新的订单，请及时处理。',
                        'color' => '#98d261',
                    ),
                    'keyword1' => array(
                        'value' => $_GET['out_trade_no'],
                        'color' => '#98d261'
                    ),
                    'keyword2' => array(
                        'value' => $course['name'],
                        'color' => '#98d261'
                    ),
                    'keyword3' => array(
                        'value' => $price,
                        'color' => '#98d261'
                    ),
                    'keyword4' => array(
                        'value' => $order['mphone'],
                        'color' => '#98d261'
                    ),
                    'keyword5' => array(
                        'value' => date('Y-m-d H:i:s',$order['create_time']),
                        'color' => '#98d261'
                    ),
                    'remark' => array(
                        'value' => '谢谢您使用小宝课堂',
                        'color' => '#98d261'
                    ),
                ),
            );
            $wechat = A('WeChat');
            $result = $wechat->send_order_msg($data);

            $this->display('appointment_success');
        }elseif($order['status'] == 3){
            $this->display('experiencing');
        }elseif($order['status'] == 4){
            $this->display('experience_end');
        }elseif($order['is_pay'] == 0){
            $this->redirect('/order/appointment/out_trade_no/'.$_GET['out_trade_no']);
        }elseif($order['status'] == 0 || $order['status'] == -1){
            $this->display('order_cancel');
        }
    }

    /**
     * 取消订单
     */
    public function cancel(){
        $where['out_trade_no'] = I('out_trade_no');
        $order = M('course_order')
            ->alias('o')
            ->where($where)
            ->join('left join course c on c.id = o.course_id')
            ->field('o.*,c.name,c.price')
            ->find();

        if(I('action') == 'cancel'){
            $data['status'] = 0;
            $data['cancel_reason'] = htmlspecialchars(I('cancel_reason'));
            $data['id'] = I('id');
            $reasult = M('course_order')->save($data);

            $where_b['a.type'] = 3;//分校管理员
            $where_b['o.id'] = I('id');
            $openid = M('branch')
                ->alias('b')
                ->join('left join course c on c.branchId = b.id')
                ->join('left join course_order o on o.course_id = c.id')
                ->join('left join branch_account a on a.branchId = b.id')
                ->join('left join grocery g on g.id = b.groId')
                ->where($where_b)
                ->field('a.openid,o.out_trade_no,c.name,c.price')
                ->find();

            $data = array(
                "touser" => "{$openid['openid']}",
                'template_id' => 'Aa3C4PagiXWzkvJ0S2WlgvaqnHKlZkaTw5myEcHCdj0',
                'url' => 'http://'.$_SERVER['SERVER_NAME'].'/course/id/'.$order['course_id'],
                'topcolor' => '#000',
                'data' => array(
                    'first' => array(
                        'value' => '您的商户有订单被取消',
                        'color' => '#98d261'
                    ),
                    'keyword1' => array(
                        'value' => $openid['out_trade_no'],
                        'color' => '#98d261'
                    ),
                    'keyword2' => array(
                        'value' => $openid['name'],
                        'color' => '#98d261'
                    ),
                    'keyword3' => array(
                        'value' => $openid['price'].'元',
                        'color' => '#98d261'
                    ),
                    'remark' => array(
                        'value' => '请登录查看',
                        'color' => '#98d261'
                    ),
                ),
            );

            $wechat = A('WeChat');
            $w_result = $wechat->send_order_msg($data);;
            if($reasult){
                $this->redirect('/choice/index');
            }
        }

        $this->assign('order',$order);
        $this->display();
    }

    /**
     * 体验结束
     */
    public function experience_end(){
        //修改订单状态
        $where['id'] = I('order_id');
        $data_o['status'] = 5;
        $result = M('course_order')
            ->where($where)
            ->data($data_o)
            ->save();

        //添加评论和积分记录
        $data_p['createtime'] = time();
        $data_p['comment'] = htmlspecialchars($_POST['comment']);
        $data_p['courseid'] = I('course_id');
        $data_p['point'] = I('point');
        $data_p['userid'] = $_SESSION['userid'];
        $data_p['order_id'] = I('order_id');
        M('course_praise')->add($data_p);

        $point = I('point');
        $praise_point = M('point')->where('id = 5')->find();//课程打赏奖励积分
        $comment_point = M('point')->where('id = 6')->find();//课程评论奖励积分
        //更新用户的库存积分和消费积分
        if(!empty($point)){
            //更新课程的打赏积分和销售积分
            $sql_course = "UPDATE course SET salepoint = salepoint+{$point},praisepoint = praisepoint + 30,praisenum = praisenum + 1 WHERE id = ".I('course_id');
            M('course')->execute($sql_course);
            $total_point = $praise_point['point'] + $comment_point['point'];
            $sql_user = "UPDATE member SET point = point - {$point} + {$total_point},bonusPoint = bonusPoint + {$point} where id = ".$_SESSION['userid'];
        }else{
            $sql_user = "UPDATE member SET bonusPoint = bonusPoint + {$comment_point['point']} where id = ".$_SESSION['userid'];
        }

        M('member')->execute($sql_user);


       $this->redirect('/order/details/out_trade_no/'.I('out_trade_no'));
    }

    /**
     * 获得用户积分
     */
    public function get_point(){
        $where['id'] = $_SESSION['userid'];
        $point = M('member')
            ->where($where)
            ->field('point')
            ->find();

        $this->ajaxReturn($point['point']);
    }

    public function get_sign($configs){
        $alipay_config = C('alipay_config');

        /**************************请求参数配置**************************/
        //支付类型
        $payment_type = C('alipay.payment_type');
        //必填，不能修改
        //服务器异步通知页面路径
        $notify_url = C('alipay.notify_url');
        //需http://格式的完整路径，不能加?id=123这类自定义参数

        //卖家支付宝帐户
        $seller_email = C('alipay.seller_email');

        //必填
        //页面跳转同步通知页面路径
        $return_url = $configs['return_url'];
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
        /****************************************************/
        //>>>>>>>>>>>>第二步
        //接收动态订单数据
        //商户订单号
        $out_trade_no = $configs['out_trade_no'];
        //商户网站订单系统中唯一订单号，必填

        //订单名称
        $subject = $configs['subject'];
        //必填

        //付款金额
        $total_fee = $configs['total_fee'];
        //必填

        //订单描述
        $body = $configs['body'];
        //商品展示地址
        $show_url = $configs['show_url'];
        //需以http://开头的完整路径，例如：http://www.xxx.com/myorder.html

        $alipaySubmit = new \AlipaySubmit($alipay_config);
        $parameter = array(
            "service" => C('alipay_config.service'),
            "partner" => trim($alipay_config['partner']),
            "payment_type"	=> $payment_type,
            "notify_url"	=> $notify_url,
            "return_url"	=> $return_url,
            "seller_id"	=> $seller_email,
            "out_trade_no"	=> $out_trade_no,
            "subject"	=> $subject,
            "total_fee"	=> $total_fee,
            "body"	=> $body,
            "show_url"	=> $show_url,
            'sign_type' => C('alipay_config.sign_type'),
            "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
        );

        return $alipaySubmit->buildRequestPara($parameter);
    }

    /**
     * 生成订单
     */
    public function create_order(){
        $where['out_trade_no'] = $_POST['out_trade_no'];
        $order = M('course_order')
            ->alias('o')
            ->join('member m on m.id = o.user_id')
            ->field('o.*,m.mphone')
            ->where($where)
            ->find();

        $course = M('course')
            ->join('branch b on b.id=course.branchId')
            ->join('grocery g on g.id=b.groId')
            ->where('course.id = '.$order['course_id'])
            ->field('course.*,g.numbering,b.area,b.circle,b.braName,b.address,b.id as branch_id')->
            find();

        $this->assign('post',$_POST);
        $this->assign('order',$order);
        $this->assign('course',$course);
        $this->display();
    }
}