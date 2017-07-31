<?php
namespace Grocery\Controller;
use Grocery\Controller;
//分校控制器
class GroceryController extends BaseController{
	/**
     * 修改名称
     * 
     * return [1,成功,0,失败]
     */
    public function ajaxUpdateName(){
    	$grocery = M('Grocery');
		$branchCtrl = A('branch');
    	if(isset($_POST) && $branchCtrl->checkAuth()){
    		$data['name'] = $_POST['grocery_name'];//商户名
    		$data['groId'] = $_POST['grocery_id'];//商户编码
    		//$this->ajaxReturn($grocery);
    		if($grocery->where("id='{$data['groId']}'")->save($data)){
    			$this->ajaxReturn(1);
			}else{
				$this->ajaxReturn(0);
			}
    	}else{
			$this->ajaxReturn(0);
		}
    	
    }
}
?>