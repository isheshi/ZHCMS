<?php
namespace Admin\Controller;
use Myself\FileSystem;
/**
* 单页管理
*/
class SinglePageController extends CommonController {

	protected $statusArr=array("<font color='#990000'>关闭</font>","<font color='#006600'>开启</font>");	
    protected $statusArr2=array("显示","禁用");
	protected $m = NULL;

	/**
	 * 初始化
	 */
	public function _initialize(){
		parent::_initialize();
        $this->m = M('single_page');    
	}

	/**
	 * 单页管理页面
	 */
    public function index(){
    	$keyword = I('keyword','','trim'); 
            
        if($keyword){
            $map['title'] = array('like',"%$keyword%");//模糊查询
        }
        $map['pid'] = 0;
		$count = $this->m->where($map)->count();
        $p = getpage($count,10);
        $data = D('single_page')->relation(true)->where($map)->order('sort desc, id asc')->limit($p->firstRow, $p->listRows)->select();
        $this->assign('data', $data);// 赋值数据集
        $this->assign('count', $count); 
        $this->assign('page', $p->show()); // 赋值分页输出
		$this->assign('statusArr',$this->statusArr);
		$this->display();
    }

    /**
     * 添加或编辑页面
     */
    public function add(){
        $id = I('id',0,"intval");
        $data = $this->m->find($id);
        $navigation = M('navigation')->order('sort desc, id asc')->select();

        $this->assign('data',$data);
        $this->assign('navigation',$navigation);
        $this->display();
    }
    /**
     * 分页管理
     */
    public function lists(){
        $pid = I('pid',0,'intval');
        $id = I('id',0,'intval');

        $dataInfo = $this->m->find($pid);
        $this->assign('dataInfo',$dataInfo);

        //显示分页管理start
        if($dataInfo['pid'] == 0){
            $map['pid'] = array('eq',$pid);//不显示单页数据
        }else{
            if ($id == 0) {
                $map['pid'] = $dataInfo['pid'];
            }else{
                $map['pid'] = $id;
                $dataInfo2 = $this->m->find($id);
                while ($dataInfo['pid'] > 0) {
                    $dataInfo = $this->m->find($dataInfo['pid']);
                    $this->assign('manage',true);//标识这是分页管理数据
                }
                $this->assign('dataInfo',$dataInfo);
                $this->assign('dataInfo2',$dataInfo2);
            }
        }
        //显示分页管理end
        
        // 获取搜索关键字
        $keyword = I('keyword','','trim'); 
        $time1 = I('time1');  
        $time2 = I('time2');
            
        if($keyword){
            $map['title'] = array('like',"%$keyword%");//模糊查询
        }
        // 获取筛选时间，年月日格式转时间戳
        if($time1 || $tim2) {
            $time1 = strtotime($time1." 00:00:00");
            $time2 = strtotime($time2." 23:59:59");
            $map['add_time'] = array('between',"$time1,$time2");
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

    /**
     * 分页管理添加或编辑页面
     */
    public function listsAdd(){
        $pid = I('pid',0,'intval');
        $id = I('id',0,'intval');
        
        $dataInfo = $this->m->find($pid);
        while ($dataInfo['pid'] > 0) {
            $dataInfo = $this->m->find($dataInfo['pid']);
            $this->assign('manage',true);//标识这是分页管理数据
        }
        $this->assign('dataInfo',$dataInfo);

        if($pid){//优化查询条件，以便找到单页数据
            $map['pid'] = array('gt',0);
            $map['nav_id'] = $dataInfo['nav_id'];
        }

        $data = $this->m->where($map)->find($id);
        $this->assign('data',$data);
        $this->assign('statusArr2',$this->statusArr2);
        //网站基础设置
        $configArr = require('Application/Common/Conf/site.cn.php');
        $this->assign('configArr',$configArr);
        $this->display();
    }

	/**
	 * 保存单页数据
	 */
	public function save(){
        $id = I('id',0,'intval');
        $data = I('post.'); 

        if(isset($data['title']) && empty($data['title'])){
            $this->ajaxReturn('单页名称不能为空');
        }
        if(isset($data['cate_id']) && empty($data['cate_id'])){
            $this->ajaxReturn('请选择所属导航');
        }
        if(isset($data['sort']) && $data['sort']>99999){
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
    
    /**
     * 保存分页数据
     */
    public function listsSave(){
        $id = I('id',0,'intval');
        $data = I('post.'); 

        if(isset($data['title']) && empty($data['title'])){
            $this->ajaxReturn('标题不能为空');
        }
        if(isset($data['sort']) && $data['sort']>99999){
            $this->ajaxReturn("排序最大值为99999哦~");
        }
        if(isset($data['keywords']) && isset($data['description']) && isset($data['page_title'])){
            $map['id'] = $data['pid'];
            $this->m->where($map)->setField('keywords',$data['keywords']);
            $this->m->where($map)->setField('description',$data['description']);
            $this->m->where($map)->setField('page_title',$data['page_title']);
        }
        //数据入库
        if($id == 0){
            $data['add_time'] = time();
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
            $this->m->where('id='.$id)->setField('keywords','');
            $this->m->where('id='.$id)->setField('description','');
            $this->m->where('id='.$id)->setField('page_title','');
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
                $imginfo =  $file['savepath'].$file['savename'];
                cookie('pic_path',$imginfo);
                // echo $imginfo;
            }
        }
    }

    // 标题修改
    public function title(){
        if(IS_POST){
            $p = I("post.");
            if(empty($p['id'])){
                $this->ajaxReturn("操作ID不能为空");
            }
            $data["title"] = $p['title'];
            $res = $this->m->where(array("id"=>$p["id"]))->save($data);
            if($res){
                $this->ajaxReturn("OK");
            }else{
                $this->ajaxReturn("操作失败");
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
        $id = I('id',0,'int');
        $res = $this->m->delete($id);
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