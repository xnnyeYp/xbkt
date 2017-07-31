<?php
/**
 * Created by PhpStorm.
 * User: yueping
 * Date: 2016/3/16
 * Time: 13:08
 * email:596169733@qq.com
 */
namespace Home\Controller;

use Think\Controller;

class EmptyController extends Controller {
    public function index(){
        echo '<script>;history.go(-1)</script>';
    }
}