<?php

namespace Home\Controller;

use Think\Controller;

class PostController extends Controller{

    public function index($name){
        $this->display();
    }

    public function view($id){
        $post = M('post')->find($id);
        $this->assign('post', $post);
        $this->display();
    }
}
