<?php
namespace Admin\Controller;
use Think\Controller;
use Myself\FileSystem;
class NavigationController extends CommonController {
	
	public function _initialize(){
		parent::_initialize();
		$this->m = M('navigation');
	}
		
	
	public function index(){
		$keyword = I('keyword','','trim'); 
            
        if($keyword){
            $map['title'] = array('like',"%$keyword%");//模糊查询
        }
        $count = $this->m->where($map)->count();
        $p = getpage($count,10);
        $data = $this->m->where($map)->order('sort desc, id asc')->limit($p->firstRow, $p->listRows)->select();
        // dump($data);
        $this->assign('data', $data);// 赋值数据集
        $this->assign('count', $count); 
        $this->assign('page', $p->show()); // 赋值分页输出
		$this->display();
    }

	/*
	 * 添加或编辑页面
	 */
	public function add(){
		$id = I('id',0,'intval');
		$data = $this->m->find($id);
        // $navigation = M('navigation')->order('sort desc, id asc')->limit(9)->select();

        $this->assign('data',$data);
        // $this->assign('navigation',$navigation);
		$this->display();
    }

	/**
	 * 数据
	 */
	public function save(){
        $id = I('id',0,'intval');
        $data['title'] =  I('title','','trim');
        $data['url'] =  I('url','','trim');
        $data['is_recommend'] = I('is_recommend',0,'intval');
        $data['pid'] = I('pid',0,'intval');
        $data['sort'] = I('sort',0,'intval');

        if(!$data['title']){
            $this->ajaxReturn('导航名称不能为空');
        }
        if(!$data['url']){
            $this->ajaxReturn('链接地址不能为空');
        }
        if($data['sort']>99999){
            $this->ajaxReturn("排序最大值为99999哦~");
        }
        //数据入库
        if($id == 0){
            $res = $this->m->add($data);
            if(!$res){
                $this->ajaxReturn('添加失败');
            }
            $this->ajaxReturn('OK');
        }else{
            $res = $this->m->where('id='.$id)->save($data);
            if(!$res){
                $this->ajaxReturn('修改失败');
            }
            $this->ajaxReturn('OK');
        }
	
	}
	
    //修改状态
    public function status(){
        $map['id'] = I('id',0,'intval');
        $status = I('status',0,'intval');
        $res = $this->m->where($map)->setField('is_recommend',$status);
        if($res){
             $this->ajaxReturn('OK');
        }else{
            $this->ajaxReturn('修改失败');
        }
    }

    // 内容排序
    public function sort(){
        if(IS_POST){
            $p = I("post.");
            if(empty($p['id'])){
                $this->ajaxReturn("操作ID不能为空");
            }else if(!preg_match("/^[0-9]*$/",$p['sort'])){
                $this->ajaxReturn("排序必须是个正整数哦~");
            }else if($p['sort']>99999){
                $this->ajaxReturn("排序最大值为99999哦~");
            }
            $data["sort"] = $p['sort'];
            $res = $this->m->where(array("id"=>$p["id"]))->save($data);
            if($res){
                $this->ajaxReturn("OK");
            }else{
                $this->ajaxReturn("操作失败");
            }
        }
    }

    /**
     * 单选删除
     */
    public function delete(){
        $high_id = I('id',0,'int');
        $res = $this->m->delete($high_id);
        if($res){
            $this->ajaxReturn('OK');
        }else{
            $this->ajaxReturn('删除失败');
        }
    }

    /**
     * 全选删除
     */
    public function delAll(){
        $ids = I('post.ids');
        if(!$ids && !is_array($ids)){
            $this->ajaxReturn('删除失败');
        }
        $map['id']  = array('in',$ids);
        $res = $this->m->where($map)->delete();
        if($res){
            $this->ajaxReturn('OK');
        }else {
            $this->ajaxReturn('删除失败');
        }
    }


}