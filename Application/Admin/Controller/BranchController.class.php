<?php
namespace Admin\Controller;
use Admin\Controller;
class BranchController extends BaseController{
	public function update(){
		if(!empty($_POST)){
			$branchModel = M('branch');
			//print_r($_POST);die;
			if($_POST['password']!=$_POST['password1']){
				$_POST['password'] = md5($_POST['password']);
			};
			if($branchModel->where("id='{$_POST['id']}'")->save($_POST)){
				$this->success('修改成功!',U('member/groceryMember'));
			}else{
				$this->error('修改失败!');
			}
		}else{
			$this->error('非法操作！');
		}
	}
	public function detail($id){
		$branchModel = M('branch');
		if(!empty($id)){
			$branch_info = $branchModel->where("id='$id'")->find();
			$this->assign('branch_info',$branch_info);
		}else{
			$this->error('非法操作！');
		}
		$this->display();
	}
	public function delete($id){
		if(!empty($id)){ 
			$branchModel = M('branch');
			if($branchModel->where("id='$id'")->delete()){
				$this->success('删除成功！',U('member/groceryMember'));
			}else{
				$this->error('删除失败！');
			}
		}else{
			$this->error('非法操作！');
		}
	}
	public function add(){
		if(!empty($_POST)){
			//print_r($_POST);
			$branchModel = M('branch');
			$_POST['password'] = md5($_POST['password']);
			if($branchModel->add($_POST)){
				$this->success('创建成功!',U('member/groceryMember'));
			}else{
				$this->error('创建失败!');
			}
		}else{
			$this->assign('groId',$_GET['groId']);
			$this->display();
		}
	}
}
?>