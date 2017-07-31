<?php
/**
 * @param $data
 */
function dd($data)
{
	echo "<pre>";
	print_r($data);
	echo "</pre>";
}

/**
 * 获取排序后的分类
 * @param  [type]  $data  [description]
 * @param  integer $pid   [description]
 * @param  string  $html  [description]
 * @param  integer $level [description]
 * @return [type]         [description]
 */
function getSortedCategory($data,$pid=0,$html="|---",$level=0)
{
	$temp = array();
	foreach ($data as $k => $v) {
		if($v['pid'] == $pid){
	
			$str = str_repeat($html, $level);
			$v['html'] = $str;
			$temp[] = $v;

			$temp = array_merge($temp,getSortedCategory($data,$v['id'],'|---',$level+1));
		}
		
	}
	return $temp;
}

/**
 * 根据key，返回当前行的所有数据
 * @param  string  $key  字段key
 * @return array         当前行的所有数据
 */
function getSettingValueDataByKey($key)
{
	return M('setting')->getByKey($key);
}

/**
 * 根据key返回field字段
 * @param  string $key   [description]
 * @param  string $field [description]
 * @return string        [description]
 */
function getSettingValueFieldByKey($key,$field)
{
	return M('setting')->getFieldByKey($key,$field);
}
/**
 * 上传图片函数
 * @param array $file 文件信息数组
 * @param string $path 图片上传路径
 * @return string 图片保存的路径或错误提示
 * */
function uploadImage($path,$file){
	$upload = new \Think\Upload();// 实例化上传类
	$upload->maxSize   =     3145728 ;// 设置附件上传大小
	$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	$upload->rootPath = '.'.$path;
	$upload->savepath = '';
	$upload->subName =  array('date','Ymd');//设置子目录创建方式
	// 上传文件
	$info   =   $upload->uploadOne($file);
	if($info){
		return $path.$info['savepath'].$info['savename'];
	}else{
		return $upload->getError();
	}
}

/**
 * 在字符串的指定字符前插入字符串
 * @param string $string 原字符串
 * @param string $s 指定字符
 * @param string $substr 要插入的字符串
 * @return string 返回的字符串
 */
function insert_str($string,$s,$substr){
	for($i = 0;$i<strlen($string);$i++){
		if($string[$i] == $s){
			$num = $i;
		}
	}
	$startstr = '';
	for($i = 0;$i<$num;$i++){
		$startstr .=$string[$i];
	}
	$laststr = '';
	for($i = $num;$i<strlen($string);$i++){
		$laststr .= $string[$i];
	}

	return $startstr.$substr.$laststr;
}

/**
 * 去掉二维数组中的重复数组
 * @param array $array 二维数组
 * @return array $temp2 返回无重复的二维数组
 */
function array_unique_2($array){
	$key_arr = array_keys($array[0]);
	foreach($array as $arr){
		$temp[] = implode(',',$arr);
	}
	$temp = array_unique($temp);
	foreach($temp as $key=>$v){
		$temp1 = explode(',',$v);
		for($i=0;$i<count($key_arr);$i++){
			$temp2[$key][$key_arr[$i]] = $temp1[$i];
		}
	}

	return $temp2;
}

/*
 * 发送邮件
 * @param $to string
 * @param $title string
 * @param $content string
 * @return bool
 * */
function SendMail($to, $title, $content) {
	Vendor('PHPMailer.PHPMailerAutoload');
	$mail = new PHPMailer(); //实例化
	$mail->IsSMTP(); // 启用SMTP
	$mail->Host=C('MAIL_HOST'); //smtp服务器的名称（这里以QQ邮箱为例）
	$mail->SMTPAuth = C('MAIL_SMTPAUTH'); //启用smtp认证
	$mail->Username = C('MAIL_USERNAME'); //你的邮箱名
	$mail->Password = C('MAIL_PASSWORD') ; //邮箱密码
	$mail->From = C('MAIL_FROM'); //发件人地址（也就是你的邮箱地址）
	$mail->FromName = C('MAIL_FROMNAME'); //发件人姓名
	$mail->AddAddress($to,"尊敬的客户");
	$mail->WordWrap = 50; //设置每行字符长度
	$mail->IsHTML(C('MAIL_ISHTML')); // 是否HTML格式邮件
	$mail->CharSet=C('MAIL_CHARSET'); //设置邮件编码
	$mail->Subject =$title; //邮件主题
	$mail->Body = $content; //邮件内容
	$mail->AltBody = "这是一个纯文本的身体在非营利的HTML电子邮件客户端"; //邮件正文不支持HTML的备用显示
	return($mail->Send());
}

/**
 * 获取用户IP
 * @return string
 */
function GetIP(){
	if(!empty($_SERVER["HTTP_CLIENT_IP"])){
		$cip = $_SERVER["HTTP_CLIENT_IP"];
	}
	elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
		$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	elseif(!empty($_SERVER["REMOTE_ADDR"])){
		$cip = $_SERVER["REMOTE_ADDR"];
	}
	else{
		$cip = "无法获取！";
	}
	return $cip;
}

/**
 * POST 请求
 * @param string $url
 * @param array $param
 * @param boolean $post_file 是否文件上传
 * @return string content
 */
function http_post($url,$param,$post_file=false){
	$oCurl = curl_init();
	if(stripos($url,"https://")!==FALSE){
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
	}
	if (is_string($param) || $post_file) {
		$strPOST = $param;
	} else {
		$aPOST = array();
		foreach($param as $key=>$val){
			$aPOST[] = $key."=".urlencode($val);
		}
		$strPOST =  join("&", $aPOST);
	}
	curl_setopt($oCurl, CURLOPT_URL, $url);
	curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt($oCurl, CURLOPT_POST,true);
	curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
	$sContent = curl_exec($oCurl);
	$aStatus = curl_getinfo($oCurl);
	curl_close($oCurl);
	if(intval($aStatus["http_code"])==200){
		return $sContent;
	}else{
		return false;
	}
}
