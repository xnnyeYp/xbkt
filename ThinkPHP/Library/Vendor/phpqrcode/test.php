<?php 

    include('phpqrcode.php'); 
	$data = 'http://'.$_SERVER['HTTP_HOST'].'/user/registers/inviteCode/'.$inviteCode;            // ������L��M��Q��H            
	$level = 'L';            // ��Ĵ�С��1��10,�����ֻ���4�Ϳ�����            
	$size = 4;            // //����ע���˰Ѷ�ά��ͼƬ���浽���صĴ���,���Ҫ����ͼƬ,��$fileName�滻�ڶ�������false            
	$path = "./image/";		// ���ɵ��ļ���            //
	$datetime = time();
	$fileName = $path.md5($datetime).'.png';          //�ļ���Ҳ���Կ���������һ�����ڱ���  
	QRcode::png($data,$fileName, $level, $size);
	echo $fileName;