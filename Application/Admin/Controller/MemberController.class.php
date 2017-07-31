<?php
namespace Admin\Controller;
use Admin\Controller;
/**
 * 用户管理
 */
class MemberController extends BaseController
{
    /**
     * 用户列表
     * @return [type] [duescription]
     */
    public function index()
    {
        if(!empty($_POST['key'])){
			$where['_string'] = "username like '%".$_POST['key']."%' or mphone = '".$_POST['key']."'";
		}

		$where['id'] = array('neq',1);
		$where['status'] = 1;
		if((string)I('status') === '0'){
			$where['status'] = 0;
		}
        $model = M('member');
        $count  = $model->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Extend\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$Page->setConfig('prev','上一页');
		$Page->setConfig('next','下一页');
        $show = $Page->show();// 分页显示输出
        $member = $model
			->limit($Page->firstRow.','.$Page->listRows)
			->where($where)
			->order('id DESC')->select();

        $this->assign('member', $member);
		$this->assign('count',$count);
        $this->assign('page',$show);

        $this->display();     
    }
	/**
	商户会员
	*/
	public function groceryMember()
    {
		$key = I('key');
		$model = M('grocery');
        if($key != ""){
			$where['name'] = array('like',"%$key%");
        }
		if(!empty($_GET) && is_numeric($_GET['status'])){
			$where['status'] = I('status');
		}
        $count  = $model->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Extend\Page($count,5);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        $grocery = $model->limit($Page->firstRow.','.$Page->listRows)->where($where)->order('id DESC')->select();
        $this->assign('grocery', $grocery);
		$this->assign('count',$count);
        $this->assign('page',$show);

        $this->display();     
    }

	/**
	 *查看普通会员信息
	 */
	public function update_member(){
		if($_GET['action'] == 'do_update'){
			$data = $_POST;

			if(empty($_POST['password'])){
				unset($data['password']);
			}else{
				$data['password'] = md5($data['password']);
			}

			$where['id'] = $_POST['user_id'];

			M('member')->where($where)->save($data);

			$this->redirect(U('member/index',array('status'=>1)));
		}else{
			$where['id'] = I('id');
			$user = M('member')
				->where($where)
				->find();
		}

		$this->assign('user',$user);
		$this->display();
	}

	/**
	 * 普通会员安全设置
	 */
	public function member_safety(){
		if($_POST){
			$data = $_POST;
			M('member')->data($data)->save();

			$this->redirect(U('member/index'));
		}else{
			$where['id'] = I('id');
			$user = M('member')
				->where($where)
				->find();

			$this->assign('user',$user);
		}

	    $this->display();
	}

	/**
	 * 删除普通会员
	 */
	public function delete_member(){
	    $where['id'] = I('id');
		M('member')->where($where)->delete();

		$this->redirect(U('member/index'));
	}

	/**
	 * 编辑商户会员
	 */
	public function update_grocery(){
	    if($_GET['action'] == 'do_update'){
			$where_g['id'] = $_POST['gid'];
			$data_g['name'] = $_POST['name'];
			$data_g['status'] = $_POST['status'];
			M('grocery')->where($where_g)->save($data_g);

			$where_b['id'] = $_POST['id'];
			$data_b = array(
				'username' => $_POST['username'],
				'braName' => $_POST['braName'],
				'telephone' => $_POST['telephone'],
				'mphone' => $_POST['mphone'],
				'area' => $_POST['area'],
				'circle' => $_POST['circle'],
				'address' => $_POST['address'],
				'status' => $_POST['status']
			);
			if(!empty($_POST['password'])){
				$data_b['password'] = md5($_POST['password']);
			}
			M('branch')->where($where_g)->save($data_b);

			$this->redirect(U('member/groceryMember'));
		}else{
			$where['g.id'] = I('id');
			$wher['b.type'] = 1;
			$grocery = M('grocery')
				->alias('g')
				->join('left join branch b on b.groId = g.id')
				->where($where)
				->field('g.id as gid,g.logo,g.numbering,g.name,g.status as gstatus,b.*')
				->find();

			$this->assign('grocery',$grocery);
		}

		$this->display();
	}

	/**
	 * 删除商户会员
	 */
	public function delete_grocery(){
		$where['id'] = $_GET['id'];
		M('grocery')->where($where)->delete();

		$this->redirect(U('member/groceryMember'));
	}

	/**
	 * 添加商户会员
	 */
	public function add_grocery(){
		if($_GET['action'] == 'do_add'){
			$num_max = M('grocery')->order('id DESC')->getField('numbering');
            $num_max_arr = explode('-', $num_max);
			$data_g['name'] = $_POST['name'];
			$data_g['status'] = $_POST['status'];
			$data_g['numbering'] = 'SH-MC-'.($num_max_arr[2] + 1);
			$data_g['logo'] = uploadImage('/Public/uploads/grocery/logo/',$_FILES['logo']);
			$groid = M('grocery')->add($data_g);

			$data_b = array(
				'username' => $_POST['username'],
				'mphone' => $_POST['mphone'],
				'type' => 1,
				'status' => 1,
				'password' => md5(trim($_POST['password'])),
				'groId' => $groid,
			);
			M('branch_account')->add($data_b);

			$this->redirect(U('member/groceryMember'));
		}
	    $this->display();
	}
	
    /**
     * 删除管理员
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function delete($id)
    {
    	if(C('SUPER_ADMIN_ID') == $id) $this->error("超级管理员不可禁用!");
        $model = M('member');
        //查询status字段值
        $result = $model->find($id);
        //更新字段
        $data['id']=$id;
        if($result['status'] == 1){
        	$data['status']=0;
        }
        if($result['status'] == 0){
        	$data['status']=1;
        }
        if($model->save($data)){
            $this->success("状态更新成功", U('member/index'));
        }else{
            $this->error("状态更新失败");
        }
    }

	/**
	 * 商户安全设置
	 */
	public function grocery_set(){
		$where['id'] = I('id');
		$grocery = M('grocery')
			->where($where)
			->find();

		$branch = M('branch')
			->where('groId = '.I('id'))
			->select();

		$this->assign('branch',$branch);
		$this->assign('grocery',$grocery);
	    $this->display();
	}

	/**
	 * 更新商户信息
	 */
	public function update_grocery_info(){
	    $data['id'] = $_POST['id'];
		if($_FILES['logo']['name'] != null){
			$data['logo'] = uploadImage('/Public/uploads/grocery/logo/',$_FILES['logo']);
		}
		$data['name'] = $_POST['name'];
		$data['status'] = $_POST['status'];
		M('grocery')->data($data)->save();

		$this->redirect(U('member/groceryMember'));
	}

	/**
	 * 添加分校
	 */
	public function add_branch(){
		if($_POST){
			$data['groId'] = $_POST['groId'];
			$data['braName'] = $_POST['braname'];
			$data['area'] = $_POST['area'];
			$data['circle'] = $_POST['circle'];
			$data['address'] = $_POST['address'];
			$data['telephone'] = $_POST['telephone'];
			$data['son_area'] = $_POST['son_area'];

			M('branch')->data($data)->add();

			$this->redirect(U('member/grocery_set',array('id'=>$_POST['groId'])));
		}else{
			$area = M('area')->where('pid = 0')->select();
			$circle = M('circle')->select();
            $gro_name = M('grocery')->where("id = {$_GET['groId']}")->getField('name');

            $this->assign('gro_name', $gro_name);
			$this->assign('area',$area);
			$this->assign('circle',$circle);
		}
		$this->assign('groId',$_GET['groId']);
	    $this->display();
	}

	/**
	 * 删除分校
	 */
	public function delete_branch(){
	    $where['id'] = I('id');
		$branch = M('branch')->where($where)->find();
		M('branch')->where($where)->delete();

		$this->redirect(U('member/grocery_set',array('id'=>$branch['groid'])));
	}

	/**
	 * 编辑分校
	 */
	public function update_branch(){
		if($_POST){
			$data['id'] = $_POST['id'];
			$data['braName'] = $_POST['braname'];
			$data['area'] = $_POST['area'];
			$data['circle'] = $_POST['circle'];
			$data['address'] = $_POST['address'];
			$data['telephone'] = $_POST['telephone'];
            $data['son_area'] = $_POST['son_area'];

			M('branch')->data($data)->save();

			$this->redirect(U('member/grocery_set',array('id'=>$_POST['groId'])));
		}else{
			$area = M('area')->where('pid = 0')->select();
			$circle = M('circle')->select();
			$where['b.id'] = $_GET['id'];
			$branch = M('branch')
                ->alias('b')
                ->where($where)
                ->join('left join grocery g on g.id = b.groId')
                ->field('b.*, g.name')
                ->find();

            $son_area = M('area')
                ->alias('a1')
                ->where("a1.name = '{$branch['area']}'")
                ->join('left join area a2 on a1.id = a2.pid')
                ->field('a2.*')
                ->select();

            $this->assign('son_area', $son_area);
			$this->assign('branch',$branch);
			$this->assign('area',$area);
			$this->assign('circle',$circle);
		}

		$this->assign('groId',$_GET['groId']);
		$this->display();
	}

	/**
	 * 商户安全设置
	 */
	public function safety(){
		$where['a.groId'] = $_GET['id'];
		$account = M('branch_account')
			->alias('a')
			->join('left join branch b on b.id = a.branchId')
			->where($where)
			->field('a.*,b.braname')
			->select();

        $gro_name = M('grocery')->where("id = {$_GET['id']}")->getField('name');

        $this->assign('gro_name', $gro_name);
		$this->assign('groId',$_GET['id']);
		$this->assign('account',$account);
	    $this->display();
	}

	/**
	 * 更新商户会员帐号
	 */
	public function update_account(){
		if($_POST){
			$data['id'] = $_POST['id'];
			if(!empty($_POST['username'])){
				$data['username'] = $_POST['username'];
			}
			$data['wechat'] = $_POST['wechat'];
			if(!empty($_POST['branchId'])){
				$data['branchId'] = $_POST['branchId'];
			}
			$data['mphone'] = $_POST['mphone'];
			if($_POST['password'] != ''){
				$data['password'] = md5($_POST['password']);
			}
            
			M('branch_account')->data($data)->save();

			$this->redirect(U('member/safety',array('id'=>$_POST['groId'])));
		}else{
			$where['a.id'] = $_GET['id'];
			$account = M('branch_account')
				->alias('a')
				->join('left join branch b on b.id = a.branchId')
				->where($where)
				->field('a.*,b.braname')
				->find();

			$branch = M('branch')
				->where('groId = '.$_GET['groId'])
				->field('id,braName')
				->select();

			$this->assign('groId',$_GET['groId']);
			$this->assign('branch',$branch);
			$this->assign('account',$account);
		}

	    $this->display();
	}

	/**
	 * 区域联动
	 */
	public function get_son_area(){
	    $where['a2.name'] = I('pname');
		$son_area = M('area')
			->alias('a1')
			->join('left join area a2 on a2.id = a1.pid')
			->where($where)
			->field('a1.name')
			->select();

		$this->ajaxReturn($son_area);
	}

	/**
	 * 检查手机是否重复
	 */
	public function check_mobile(){
	    $map['mphone'] = empty($_POST['username']) ? 'null' : $_POST['username'];
		$result = M('branch_account')->where($map)->find();
        if(!empty($result)){
            if($result['id'] == $_POST['id']){
                $data = 0;
            }else{
                $data = 1;
            }
        }else{
            $data = 0;
        }

		$this->ajaxReturn($data);
	}

	/**
     * 检查商户名是否重复
     */
	public function check_groname(){
        $map['name'] = $_POST['name'];
        $result = M('grocery')->where($map)->find();
        if(!empty($result)){
            if($result['id'] == $_POST['id']){
                $data = 0;
            }else{
                $data = 1;
            }
        }else{
            $data = 0;
        }

        $this->ajaxReturn($data);
	}
	
	/**
     * 检查帐号用户名是否重复
     */
	public function check_username(){
        $map['username'] = empty($_POST['username']) ? '1' : $_POST['username'];
        $result = M('branch_account')->where($map)->find();
        if(!empty($result)){
            if($result['id'] == $_POST['id']){
                $data = 0;
            }else{
                $data = 1;
            }
        }else{
            $data = 0;
        }

        $this->ajaxReturn($data);
	}

	/**
     * 检查分校名是否重复
     */
    public function check_braname(){
        $map['braName'] = empty($_POST['braname']) ? 'null' : $_POST['braname'];
        $map['groId'] = $_POST['groId'];
        $result = M('branch')->where($map)->find();

        if(!empty($result)){
            if($result['id'] == $_POST['id']){
                $data = 0;
            }else{
                $data = 1;
            }
        }else{
            $data = 0;
        }

        $this->ajaxReturn($data);
    }
}
