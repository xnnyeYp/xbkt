<?php
namespace Grocery\Model;
use Think\Model;
class BranchModel extends Model{
	protected $_validate = array(
		array('username','require','分校负责人必填'),
		array('password','require','密码必填'),
		array('repassword','password','确认密码不正确',0,'confirm'),
		array('braName','require','分校名称必填'),
		array('address','require','地址必填')
	);	
}
