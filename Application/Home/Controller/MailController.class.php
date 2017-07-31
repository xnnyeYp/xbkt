<?php
/**
 * Created by PhpStorm.
 * User: yueping
 * Date: 2016/4/27
 * Time: 20:20
 * email:596169733@qq.com
 */
namespace Home\Controller;

use Think\Controller;

class MailController extends Controller {
    public function index(){
        $to = '1047321734@qq.com';
        $title = '测试';
        $content = '测试内容';
        $result = endMail($to, $title, $content);

        if($result){
            echo '发送成功';
        }else{
            echo '发送失败';
        }
    }
}