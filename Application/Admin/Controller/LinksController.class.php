<?php
namespace Admin\Controller;
use Admin\Controller;
/**
 * 链接管理
 */
class LinksController extends BaseController
{
    /**
     * 链接列表
     * @return [type] [description]
     */
    public function index($key="")
    {
        if($key == ""){
            $model = M('links');  
        }else{
            $where['title'] = array('like',"%$key%");
            $where['linkcate'] = array('like',"%$key%");
            $where['_logic'] = 'or';
            $model = M('links')->where($where); 
        } 
        
        $count  = $model->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Extend\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        $links = $model->limit($Page->firstRow.','.$Page->listRows)->where($where)->select();
        $this->assign('model', $links);
        $this->assign('page',$show);
        $this->display();     
    }

    /**
     * 添加链接
     */
    public function add()
    {
        //默认显示添加表单
        if (!IS_POST) {
            $this->display();
        }
        if (IS_POST) {
            //如果用户提交数据
            $model = D("links");
            if (!$model->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($model->getError());
                exit();
            } else {
                if ($model->add()) {
                    $this->redirect(U('links/index'));
                } else {
                    $this->redirect(U('links/index'));
                }
            }
        }
    }
    /**
     * 更新链接信息
     * @param  [type] $id [链接ID]
     * @return [type]     [description]
     */
    public function update($id)
    {
        //默认显示添加表单
        if (!IS_POST) {
            $model = M('links')->where('id='.$id)->find();
            $this->assign('model',$model);
            $this->display();
        }
        if (IS_POST) {
            $model = D("links");
            if (!$model->create()) {
                $this->error($model->getError());
            }else{
                if ($model->save()) {
                    $this->success("更新成功", U('links/index'));
                } else {
                    $this->error("更新失败");
                }        
            }
        }
    }
    /**
     * 删除链接
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function delete($id)
    {
        $model = M('links');
        $result = $model->delete($id);
        if($result){
            $this->success("链接删除成功", U('links/index'));
        }else{
            $this->error("链接删除失败");
        }
    }

    /**
     * 链接详情
     * */
    public function details(){
        $model = M('linkdeta');
        $result = $model->where('linkid='.I('id'))->order('sortnu')->select();
        $this->assign('deta',$result);
        $this->assign('linkid',I('id'));
        $this->display();
    }

    /**
     *在链接详情中添加图片
     */
    public function adddeta(){
        if(!IS_POST){
            $this->assign('linkid',I('linkid'));
            $this->display();
        }
        if(IS_POST){
            $data['image'] = uploadImage('/Public/uploads/admin/link/',$_FILES['image']);
            $data['linkid'] = I('linkid');
            $data['linkcontent'] = I('linkcontent');
            $data['remarks'] = I('remarks');
            $data['sortnu'] = I('sortnu');
            $result = M('linkdeta')->add($data);

            if($result) {
                $this->redirect(U('links/details',array('id'=>I('linkid'))),2);
                exit();
            }else{
                $this->redirect(U('links/details',array('id'=>I('linkid'))),2);
                exit();
            }
        }
    }

    /**
     *删除链接详情的图片
     */
    public  function deletedeta($id){
        $linkdeta = M('linkdeta')->where('id='.I('id'))->find();
        unlink('.'.$linkdeta['image']);
        $result = M('linkdeta')->delete($id);
        if($result !== false){
            $this->redirect(U('links/details',array('id'=>I('linkid'))),2);
        }else{
            $this->redirect(U('links/details',array('id'=>I('linkid'))),2);
        }
    }

    /**
     *更新链接详情图片
     */
    public function updatedeta($id){
        if(!IS_POST){
            $result = M('linkdeta')->find($id);
            $this->assign('deta',$result);
            $this->display();
        }
        if(IS_POST){
            $model = M('linkdeta');
            $data['id'] = I('id');
            if($_FILES['image']['name'] != null){
                $data['image'] = uploadImage('/Public/uploads/admin/link/',$_FILES['image']);
            }
            $data['linkcontent'] = I('linkcontent');
            $data['remarks'] = I('remarks');
            $data['sortnu'] = I('sortnu');
            $update = $model->save($data);
            if($update) {
                $this->success('更新成功！',U('links/details',array('id'=>I('linkid'))),2);
                exit();
            }else{
                $this->error('更新失败',U('links/details',array('id'=>I('linkid'))),2);
                exit();
            }
        }
    }
}
