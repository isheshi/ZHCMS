<?php
namespace Admin\Controller;

class ImgController extends CommonController {

	 protected $m = NULL;
	 protected $statusArr=array("显示","禁用");

	 public function _initialize(){
	 		parent::_initialize();
			$this->m = M('img');		
	 }

    public function index(){
		$data = $this->m->where('pid=0')->order('sort desc, id asc')->select();
		$this->assign('data',$data);// 赋值
		$this->assign('title','广告位管理');
		$this->display();
    }

	 /**
     * 分页管理
     */
    public function lists(){
        $pid = I('pid',0,'intval');
        $dataInfo = $this->m->find($pid);
        $data = $this->m->where('pid='.$pid)->order('sort desc, id asc')->select();
        $this->assign('data',$data);// 赋值
        $this->assign('count',count($data));
        $this->assign('title',$dataInfo['index_title']);
        $this->assign('pid',$pid);
        $this->display();
    }

	/**
	 * 分页新增或编辑页面
	 */
	public function listsAdd(){
        $id = I('id',0,'intval');
        $pid = I('pid',0,'intval');
		$data = $this->m->find($id);
        $this->assign('data',$data);
        $this->assign('statusArr',$this->statusArr);// 赋值
        $this->assign('pid',$pid);
		$this->display();
    }

	/**
	 * 添加或修改数据
	 */
	public function listsSave(){
        $id = I('id',0,'intval');
        $pid = I('pid',0,'intval');
        $data = I('post.'); 

        if(isset($data['alt']) && empty($data['alt'])){
            $this->ajaxReturn('标题不能为空');
        }
        if(isset($data['img']) && empty($data['img'])){
            $this->ajaxReturn('请上传图片');
        }
        if(isset($data['sort']) && $data['sort']>99999){
            $this->ajaxReturn("排序最大值为99999哦~");
        }
        //数据入库
        if($id == 0){
        	$data['add_time'] = time();
            $res = $this->m->add($data);
            if(!$res){
                $this->ajaxReturn('添加失败');
            }
            $this->m->where('id='.$pid)->setInc('index_num');
            $this->ajaxReturn('OK');
        }else{
            $res = $this->m->where('id='.$id)->save($data);
            if(!$res){
                $this->ajaxReturn('修改失败');
            }
            $this->ajaxReturn('OK');
        }
	}

	//tp上传图片到本地
    public function fileUpload(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =      './Uploads/'; // 设置附件上传根目录
        $upload->savePath  =      ''; // 设置附件上传（子）目录
        // 上传文件 
        $info   =   $upload->upload();
        if(!$info) {
            // 上传错误提示错误信息
            $this->error($upload->getError());
        }else{
            // 上传成功 获取上传文件信息
            foreach($info as $file){
                $info =  $file['savepath'].$file['savename'];
                cookie('imgurl',$info);
                // echo $imginfo;
            }
        }
    }

    //修改状态
    public function status(){
        $map['id'] = I('id',0,'intval');
        $status = I('status',0,'intval');
        $res = $this->m->where($map)->setField('status',$status);
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
        $id = I('id',0,'intval');
        $data = $this->m->find($id);
        $res = $this->m->delete($id);
        if($res){
            $this->m->where('id='.$data['pid'])->setDec('index_num');
            $this->ajaxReturn('OK');
        }else{
            $this->ajaxReturn('删除失败');
        }
    }

    /**
     * 全选删除
     */
    public function delAll(){
        $ids = I('ids');
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