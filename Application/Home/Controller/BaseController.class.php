<?php
/**
 * Created by PhpStorm.
 * User: yueping
 * Date: 2016/3/15
 * Time: 21:18
 * email:596169733@qq.com
 */
namespace Home\Controller;

use Think\Controller;

class BaseController extends Controller {

    public function _empty(){
        echo '<script>;history.go(-1)</script>';
    }
}