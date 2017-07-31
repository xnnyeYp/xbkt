<?php
namespace Admin\Model;
use Think\Model;
class LinksModel extends Model{
    protected $_validate = array(
        array('title','require','请填写链接备注！'), //默认情况下用正则进行验证
        array('linkcate','require','请填写链接类别！'), //默认情况下用正则进行验证
    );
}