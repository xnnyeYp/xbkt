<?php
/**
 * 支付宝支付
 */
namespace Home\Controller;
use Think\Controller;
class PayController extends Controller {
    public function _initialize(){
		header("Content-type:text/html;charset=utf-8");
		import('Vendor.Alipay.alipayCore','','.php');
		import('Vendor.Alipay.alipayMd5','','.php');
		import('Vendor.Alipay.alipayNotify','','.php');
		import('Vendor.Alipay.alipaySubmit','','.php');
    }
	//提交订单入口
    public function userspay(){
		$model_o = M('course_order');
		if(empty($_SESSION['userid'])){
			$this->redirect('/User/nologin');
		}else{
			$where['out_trade_no'] = $_POST['out_trade_no'];
			$data['appointment_time'] = I('appointment_time_1').' '.I('appointment_time_2');
			$data['message'] = I('message');
			$data['status'] = 2;
			$data['is_pay'] = 1;
			$model_o ->where($where)->data($data)->save();
			$this->redirect('/order/details/out_trade_no/'.$_POST['out_trade_no']);
			if(I('total_fee') == 0){
				
			}else{
				$model_o ->where($where)->data($data)->save();
			}

			$configs = array(
				'return_url'	=>C('alipay.return_url'),
				'notify_url'	=>C('alipay.notify_url'),	//服务器异步通知页面路径(必填)
				'subject'		=>$_POST['subject'],	//订单名称(必填)
				'total_fee'		=>$_POST['total_fee'],	//付款金额(必填)
				'body'			=>$_POST['body'],	//订单描述
				'show_url'		=>$_POST['show_url'],	//商品展示地址
				'out_trade_no'  =>$_POST['out_trade_no'],
			);

			//调用支付宝接口
			$this->alipayapi($configs);
		}
    }

	//alipay支付接口  //参数额外配置数组$configs
	public function alipayapi($configs){
		/****************************************************/
		//>>>>>>>>>>>>第一步
		//根据alipay源文件加载顺序依次加载配置
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
        //防钓鱼时间戳
        $anti_phishing_key = '';//$alipaySubmit->query_timestamp();
        //若要使用请调用类文件submit中的query_timestamp函数

        //客户端的IP地址
        $exter_invoke_ip = '';//get_client_ip();   //Thinkphp3.2 系统集成的获取客户端ip方法
        //非局域网的外网IP地址，如：221.0.0.1
		/************************************************************/
		//>>>>>>>>>>>>第三步
		//构造要请求的参数数组，无需改动
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
		//建立请求
		$html_text = $alipaySubmit->getHtml($parameter);
		$html = '<iframe src="'.$html_text.'" frameborder="0" scrolling="no" style="position: absolute;top:0; left: 0;width: 100%;height:950px;border:0px;">
</iframe>';
		$this->assign('html',$html);
		$this->display();
	}

	public function return_url(){
		//计算得出通知验证结果
		$alipay_config = C('alipay_config');   //必须
		$alipayNotify = new \AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyReturn();
		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代码

			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
			//获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

			//商户订单号

			$out_trade_no = $_GET['out_trade_no'];

			//支付宝交易号

			$trade_no = $_GET['trade_no'];

			//交易状态
			$trade_status = $_GET['trade_status'];


			if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
				$where['out_trade_no'] = $_GET['out_trade_no'];
				$data['is_pay'] = 1;
				$data['status'] = 2;
				$data['trade_no'] = $trade_no;
				$result = M('course_order')->where($where)->save($data);
				if(false !== $result || 0 !== $result){
					$this->redirect('/pay/pay_success/out_trade_no/'.$_GET['out_trade_no']);
				}
			} else {
				echo "trade_status=".$_GET['trade_status'];
			}

			echo "验证成功<br />";

			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		else {
			//验证失败
			//如要调试，请看alipay_notify.php页面的verifyReturn函数
			echo "验证失败";
		}
	}

	public function notify_url(){
		//计算得出通知验证结果
		$alipay_config = C('alipay_config');	//必须

		$alipayNotify = new \AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();

		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代


			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

			//获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

			//商户订单号

			$out_trade_no = $_POST['out_trade_no'];

			//支付宝交易号

			$trade_no = $_POST['trade_no'];

			//交易状态
			$trade_status = $_POST['trade_status'];


			if($_POST['trade_status'] == 'TRADE_FINISHED') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
				//如果有做过处理，不执行商户的业务程序

				//注意：
				//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知

				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
			}
			else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
				//如果有做过处理，不执行商户的业务程序

				//注意：
				//付款完成后，支付宝系统发送该交易状态通知

				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
			}

			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

			echo "success";		//请不要修改或删除

			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		else {
			//验证失败
			echo "fail";

			//调试用，写文本函数记录程序运行情况是否正常
			//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}
	}

	public function pay_success(){
		$out_trade_no = I('out_trade_no');

		$this->assign('out_trade_no',$out_trade_no);
		$this->display();
	}
}