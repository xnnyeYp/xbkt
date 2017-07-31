<?php
/**
 * Created by PhpStorm.
 * User: yueping
 * Date: 2016/5/18
 * Time: 13:36
 * email:596169733@qq.com
 * 积分管理
 */
namespace Admin\Controller;

use Think\Controller;

class PointController extends BaseController {
    public function index(){
        $point = M('point')->select();

        $this->assign('point',$point);
        $this->display();
    }

    /**
     * 更新
     */
    public function update(){
        if($_GET['action'] == 'do'){
            $data['id'] = I('id');
            $data['point'] = I('point');

            M('point')->data($data)->save();
            $this->redirect('point/index');
        }else{
            $where['id'] = I('id');
            $point = M('point')->where($where)->find();
        }
        $this->assign('point',$point);
        $this->display();
    }
}