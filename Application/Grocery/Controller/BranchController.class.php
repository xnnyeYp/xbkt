<?php
namespace Grocery\Controller;
use Grocery\Controller;
//分校控制器
class BranchController extends BaseController{
	/**
     * [updateBranch 更新分校信息]
     * @param  [type] $groId [商户id]
     * @param  [type] $type  [分校类型]
     * @param  [type] $name  [字段名称]
     * @param  [type] $value [字段值]
     * @return [type]        [1,成功,0,失败]
     */
    public function ajaxUpdate($id,$type,$name,$value){
        if(isset($id,$type,$name,$value)){
            $branchModel = M('branch');
            $data[$name] = $value;
            //print_r($_POST);
            if($branchModel->where("id='$id'")->save($data)){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(0);
            }
        }else{
            $this->ajaxReturn('非法操作!');
        }
    }
	/**
     * [addOrUpdateAccount 添加或者修改账号]
     */
    public function updateAccount(){
        $branchModel = M('branch');
		if(!empty($_POST)){
			$data[$_POST['name']] = $_POST['value'];
			if($branchModel->where("id='{$_POST['id']}'")->save($data)){
				$this->ajaxReturn('1');
			}else{
				$this->ajaxReturn('0');
			}
		}else{
			$this->ajaxReturn('2');//非法操作
		}
    }
	public function test(){
		echo 'hello';
	}
	/*
	*
	*创建账号
	*
	*/
	public function createAccount(){
		if(!empty($_POST) && $this->checkAuth()){
			$branchModel = D('branch');
			if($branchModel->create()){
				$id = session('groceryId');
				//$branchModel = D('branch');
				$branchInfo = $branchModel->where("id=$id")->field('type,groId')->find();
				if($branchInfo['type'] == 1){//权限检查，只有是总部才能创建分校
					//$data['username'] = $_POST['username'];
					$_POST['password'] = md5($_POST['password']);
					$_POST['type'] = 2;
					$_POST['status'] = 1;
					$_POST['groId'] = $branchInfo['groid'];
					//print_r($branchInfo);die;
					if($branchModel->data($_POST)->add()){
						$this->success('分校创建成功!',U('Setup/index'));
					}else{
						$this->error('分校创建失败!');
					}
				}else{
					$this->error('非法操作!');
				}
				//var_dump($_POST);
			}else{
				exit($branchModel->getError());
			}
			
		}else{
			$this->display();
		}
		
	}
	/**
	*检查权限，
	*
	*
	*/
	public function checkAuth(){
		$branchModel = M('branch');
		$id = session('branchId');
		//本部信息
		$selfInfo = $branchModel->where("id=$id")->find();
		if($selfInfo['type'] != 1){
			return false;
		}
		return true;
	}
}
?>