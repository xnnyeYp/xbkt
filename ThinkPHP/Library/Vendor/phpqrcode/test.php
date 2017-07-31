<?php 

    include('phpqrcode.php'); 
	$data = 'http://'.$_SERVER['HTTP_HOST'].'/user/registers/inviteCode/'.$inviteCode;            // 纠错级别：L、M、Q、H            
	$level = 'L';            // 点的大小：1到10,用于手机端4就可以了            
	$size = 4;            // //下面注释了把二维码图片保存到本地的代码,如果要保存图片,用$fileName替换第二个参数false            
	$path = "./image/";		// 生成的文件名            //
	$datetime = time();
	$fileName = $path.md5($datetime).'.png';          //文件名也可以考虑用生成一个日期变量  
	QRcode::png($data,$fileName, $level, $size);
	echo $fileName;