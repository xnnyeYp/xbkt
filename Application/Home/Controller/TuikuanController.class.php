<?php
/**
 * Created by PhpStorm.
 * User: yueping
 * Date: 2016/7/16
 * Time: 22:42
 * email:596169733@qq.com
 */
namespace Home\Controller;

use Think\Controller;

class TuikuanController extends Controller {
    public function _initialize(){
        header("Content-type:text/html;charset=utf-8");
        import('Vendor.Alipay.alipayCore','','.php');
        import('Vendor.Alipay.alipayMd5','','.php');
        import('Vendor.Alipay.alipayNotify','','.php');
        import('Vendor.Alipay.alipaySubmit','','.php');
    }

    public function alipay(){
        $where['o.id'] = $_GET['id'];
        $order = M('course_order')
            ->alias('o')
            ->where($where)
            ->join('left join course c on c.id = o.course_id')
            ->field('o.trade_no,c.price,o.status')
            ->find();

        if($order['status'] == -1){
            redirect('http://www.123bcb.com/admin.php?m=Admin&c=Order&a=index&type=3',1,'订单已退款');
        }
        //批次号，必填，格式：当天日期[8位]+序列号[3至24位]，如：201603081000001
        $batch_no = date('Ymd').rand(1000,9999);
        //退款笔数，必填，参数detail_data的值中，“#”字符出现的数量加1，最大支持1000笔（即“#”字符出现的数量999个）
        $batch_num = 1;
        //退款详细数据，必填，格式（支付宝交易号^退款金额^备注），多笔请用#隔开
        $detail_data = $order['trade_no'].'^'.$order['price'].'^取消订单退款';

        $alipay_config = C('tuikuan');

        $parameter = array(
            "service" => trim($alipay_config['service']),
            "partner" => trim($alipay_config['partner']),
            "notify_url"	=> trim($alipay_config['notify_url']),
            "seller_user_id"	=> trim($alipay_config['seller_user_id']),
            "refund_date"	=> trim($alipay_config['refund_date']),
            "batch_no"	=> $batch_no,
            "batch_num"	=> $batch_num,
            "detail_data"	=> $detail_data,
            "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))

        );

        $alipaySubmit = new \AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        echo $html_text;
    }
    
    public function notify_url(){
        $alipay_config = C('tuikuan');
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();

        if($verify_result) {//验证成功
            //请在这里加上商户的业务逻辑程序代


            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

            //批次号

            $batch_no = $_POST['batch_no'];

            //批量退款数据中转账成功的笔数

            $success_num = $_POST['success_num'];

            //批量退款数据中的详细信息
            $result_details = $_POST['result_details'];

            $where['trade_no'] = substr($result_details, 0, 28);
            M('course_order')->where($where)->setField('status', -1);
            //判断是否在商户网站中已经做过了这次通知返回的处理
            //如果没有做过处理，那么执行商户的业务程序
            //如果有做过处理，那么不执行商户的业务程序

            echo "success";		//请不要修改或删除

            //调试用，写文本函数记录程序运行情况是否正常
            logResult('1112321 ');

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            //验证失败
            echo "fail";

            //调试用，写文本函数记录程序运行情况是否正常
            logResult('32131');
        }

    }
}