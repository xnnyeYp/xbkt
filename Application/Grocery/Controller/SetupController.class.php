<?php
/**
 * Created by PhpStorm.
 * User: yueping
 * Date: 2016/1/29
 * Time: 15:11
 * email:596169733@qq.com
 */
namespace Grocery\Controller;
use Grocery\Controller;

class SetupController extends BaseController {
    /**
     *显示商户的基本信息
     *date:2016.02.01
     *author:刘北京
     */
    public function  index(){

        $groceryInfo = M('grocery')->where("id = ".$_SESSION['groid'])->find();

        $this->assign('groceryInfo',$groceryInfo);
        $this->display();
    }
	/**
	*检查权限，
	*用户是否已经登录，并且必须是总部
	*
	*/
	private function checkAuth(){
		$branchModel = M('branch');
		$id = session('brachId');
		//本部信息
		$selfInfo = $branchModel->where("id=$id")->find();
		if($selfInfo['type'] != 1){
			return false;
		}
		return true;
	}

	/**
	 * 更新商户信息
	 */
	public function update_gro(){
	    $where['id'] = $_SESSION['groid'];
		var_dump($_FILES['logo']);
		if($_FILES['logo']['size'] != 0 ){
			$data['logo'] = uploadImage('/Public/uploads/grocery/logo/',$_FILES['logo']);
		}

		$data['name'] = I('name');
		M('grocery')->where($where)->data($data)->save();
		$this->redirect('setup/index');
	}

	/**
	 * 分校列表
	 */
	public function branch_list(){
	    $where['groId'] = $_SESSION['groid'];
		$where['braName'] = array('neq','');
		$where['area'] = array('neq','');
		$where['circle'] = array('neq','');
		$branchs = M('branch')->where($where)->group('braName')->select();

		$this->assign('branchs',$branchs);
		$this->display();
	}

	/**
	 * 添加/更新分校
	 */
	public function update_branch(){
		$branch = M('branch')->where('id = '.I('id'))->find();
		$this->assign('branch',$branch);
	    $this->display();
	}

	/**
	 * 帐号列表
	 */
	public function account(){
		$where['a.groId'] = $_SESSION['groid'];
	    $account = M('branch_account')
			->alias('a')
			->join('left join branch b on b.id = a.branchId')
			->where($where)
			->field('a.*,b.braName')
			->select();

		$this->assign('account',$account);
		$this->display();
	}

	/**
	 * 更新帐号
	 */
	public function update_account(){
		if($_POST){
			$data['id'] = $_POST['id'];
			$data['username'] = $_POST['username'];
			$data['branchId'] = $_POST['branchId'];
			$data['wechat'] = $_POST['wechat'];
			$data['mphone'] = $_POST['mphone'];
			if($_POST['password'] != ''){
				$data['password'] = md5($_POST['password']);
			}

			$result = M('branch_account')->data($data)->save();

			$this->redirect(U('setup/account'));
		}else{
			$branch = M('branch')
				->where('groId = '.$_SESSION['groid'])
				->field('id,braName')
				->select();

			$account = M('branch_account')
				->where('id = '.I('id'))
				->find();

			$this->assign('account',$account);
			$this->assign('branch',$branch);
		}

	    $this->display();
	}

	/**
	 * 添加分校管理员
	 */
	public function add_account(){
		$branch = M('branch_account')
			->where('id = '.$_SESSION['account_id'])
			->field('type')
			->find();

		if($branch['type'] != 1){
			$this->error('没有权限！');
		}

		if($_POST){
			$data['username'] = $_POST['username'];
			$data['branchId'] = $_POST['branchId'];
			$data['wechat'] = $_POST['wechat'];
			$data['mphone'] = $_POST['mphone'];
			$data['password'] = md5($_POST['password']);
			$data['type'] = 3;
			$data['groId'] = $_SESSION['groid'];
			M('branch_account')->data($data)->add();

			$this->redirect(U('setup/account'));
		}else{
			$branch = M('branch')
				->where('groId = '.$_SESSION['groid'])
				->field('id,braName')
				->select();

			$this->assign('branch',$branch);
		}
	    $this->display();
	}

	/**
	 * 删除
	 */
	public function delete_branch(){
	    $where['id'] = I('id');
		M('branch')->where($where)->delete();

		$this->redirect('setup/branch_list');
	}

	/**
	 * ajax检查分校是否设置分校管理员
	 */
	public function check_branch(){
		$where['a.branchId'] = $_POST['branchId'];
		$where['a.type'] = 3;
		$result = M('branch_account')
			->alias('b')
			->join('left join branch_account a on a.branchId = b.id')
			->where($where)
			->find();

        if(!empty($result)){
            if($_POST['id'] == $result['id']) {
                $result = 0;
            }else{
                $result = 1;
            }
        }else {
            $result = 0;
        }

		$this->ajaxReturn($result);
	}

    /**
     * 检查手机是否重复
     */
    public function check_mobile(){
        $map['mphone'] = $_POST['mphone'];
        $result = M('branch_account')->where($map)->find();

        if(!empty($result)){
            if($_POST['id'] != $result['id']) {
                $data = 1;
            }else{
                $data = 0;
            }
        }else{
             $data = 0;
        }

        $this->ajaxReturn($data);
    }

    /**
     * 删除帐号
     */
    public function delete_account(){
        $where['id'] = $_GET['id'];
        M('branch_account')->where($where)->delete();
        $this->redirect('setup/account');
    }
}