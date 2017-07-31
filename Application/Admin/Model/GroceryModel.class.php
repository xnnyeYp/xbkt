<?php
namespace Admin\Model;
use Think\Model;
class GroceryModel extends Model{
	protected $_validate = array(
		array('numbering','require','编码必填'), //默认情况下用正则进行验证
		array('name','require','商户名必填'), //默认情况下用正则进行验证
		array('password','require','密码必填'), //默认情况下用正则进行验证
		array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
		array('status',array(1,0),'值的范围不正确！',2,'in'),
	);
}
?>