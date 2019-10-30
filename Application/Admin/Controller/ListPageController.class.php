<?php
namespace Admin\Controller;
/**
* 列表页管理
*/
class ListPageController extends CommonController {

	protected $m = NULL;
    protected $statusArr=array("关闭","开启");
    protected $isRecommend=array("未推荐","已推荐");
	
	public function _initialize(){
		parent::_initialize();
        $this->m = M('list_page');  
        $this->m_cate = M('category');  
	}
	


    public function index(){
    	$keyword = I('keyword','','trim'); 
            
        if($keyword){
            $map['title'] = array('like',"%$keyword%");//模糊查询
        }
        $map['pid'] = array('eq',0);
		$count = $this->m->where($map)->count();
        $p = getpage($count,10);
        $data = D('list_page')->relation(true)->where($map)->order('sort desc, id asc')->limit($p->firstRow, $p->listRows)->select();
        $this->assign('data', $data);// 赋值数据集
        $this->assign('count', $count); 
        $this->assign('page', $p->show()); // 赋值分页输出
		$this->assign('showArr',$this->showArr);// 状态
		$this->display();
    }
		
	public function add(){
		$id = I('id',0,'intval');
		$data = $this->m->find($id);
		$this->assign('data',$data);
		//选择导航
        $map['id'] = array('between',"2,5");
		$navigation = M('navigation')->where($map)->order('sort desc, id asc')->select();
		$this->assign('navigation',$navigation);
		// dump($navigation);
		$this->assign('showArr',$this->showArr);
		$this->display();
    }		
	
	public function save(){
        $id = I('id',0,'intval');
        $data = I('post.'); 

        if(isset($data['title']) && empty($data['title'])){
            $this->ajaxReturn('列表页名称不能为空');
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
     * 分页管理
     */
    public function lists(){
        $pid = I('pid',0,'intval');
        $id = I('id',0,'intval');
        $cid = I('cid',0,'intval');

        $dataInfo = $this->m->find($pid);
        $this->assign('dataInfo',$dataInfo);
        $cate= $this->m_cate->where("page_id=".$pid)->find();//判断是否有下级分类,以显示自己需要的分类
        $catePid = $this->m_cate->where("pid=".$cate['id'])->find();
        if($catePid){ 
            $where = 'page_id='.$pid.' and pid > 0'; 
        }else{  
            $where = 'page_id='.$pid; 
        }
        $cateData = $this->m_cate->where($where)->order('sort desc, id asc')->select();
        $this->assign('cateData',$cateData);
        
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
        if($pid){
            $map['pid'] = array('gt',0);
            $map['pid'] = $pid;
        }
        if($cid){
            $map['cid'] = $cid;
            $this->assign('cid',$cid);
        }
        $count = $this->m->where($map)->count();
        $p = getpage($count,10);
        $data = $this->m->where($map)->order('sort desc, id asc')->limit($p->firstRow, $p->listRows)->select();
        // dump($data);
        $this->assign('data', $data);// 赋值数据集
        $this->assign('count', $count); 
        $this->assign('page', $p->show()); // 赋值分页输出
        $this->assign('statusArr',$this->statusArr);//信息状态
        $this->assign('isRecommend',$this->isRecommend);// 首页推荐
        $this->display();
    }

    /**
     * 分页管理添加或编辑页面
     */
    public function listsAdd(){
        $pid = I('pid',0,'intval');
        $id = I('id',0,'intval');
        if ($pid == 4) {
            if ($id > 0) {
                $res['id'] = $this->m->where('id='.$id)->getField('cid');
                $high_id = $this->m_cate->where('id='.$res['id'])->getField('pid');
                $middle_level = $this->m_cate->where('pid='.$high_id)->select();
                $this->assign('high_id',$high_id);
                $this->assign('middle_level',$middle_level);
            }

            $high_level = $this->m_cate->where('pid = 0 and page_id = 4')->select();// 获取高级分类是数据
            $this->assign('high_level',$high_level);

            $dataInfo = $this->m->find($pid);// 获取功能数据    
            $this->assign('dataInfo',$dataInfo);
        }else{
            $dataInfo = $this->m->find($pid);
            $cateData = $this->m_cate->order('sort desc, id asc')->select();
            $this->assign('dataInfo',$dataInfo);
            $this->assign('cateData',$cateData);
        }
      
        $data = $this->m->find($id);// 获取内容数据
        $this->assign('data',$data);
        $this->assign('statusArr',$this->statusArr);//信息状态
        $this->assign('isRecommend',$this->isRecommend);// 首页推荐
        $this->display();
        
    }
        
    /**
     * 获取中级分类信息
     */
    public function get_middle_info(){
        $map['pid'] = I('high_id',0,'intval');
        $middle = $this->m_cate->where($map)->field('id,pid,title')->select();
        $option = '';
        foreach($middle as $vo){
            $option.='<option value='.$vo['id'].'>'.$vo['title'].'</option>';
        }
        echo $option;
    }
    
    /**
     * 保存分页数据
     */
    public function listsSave(){
        $id = I('id',0,'intval');
        $data = I('post.'); 

        if(isset($data['cid']) && empty($data['cid'])){
            $this->ajaxReturn('请选择分类');
        }
        if(isset($data['title']) && empty($data['title'])){
            $this->ajaxReturn('标题不能为空');
        }
        if($data['cid'] == 3 && empty($data['introduce'])){
            $this->ajaxReturn('简介不能为空');
        }
        if(isset($data['content']) && empty($data['content'])){
            $this->ajaxReturn('内容不能为空');
        }
        if(isset($data['pic_path']) && empty($data['pic_path'])){
            $this->ajaxReturn('请上传图片');
        }
        if(isset($data['pid']) && $data['pid'] > 7 && $data['is_recommend'] == 1){
            $this->ajaxReturn('客户见证和资讯中心不能推荐');
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

   
    /**
     * 分类管理
     */
    public function category(){
        $map['page_id'] = I('page_id',0,'intval');
        
        // 获取搜索关键字
        $keyword = I('keyword','','trim'); 
        $cid = I('cid','','intval'); 
        $time1 = I('time1');  
        $time2 = I('time2');
            
        if($keyword){
            $map['title'] = array('like',"%$keyword%");//模糊查询
        }
        if($cid){
            $map['pid'] = $cid;
            $this->assign('cid',$cid);
        }
        //二级分类
        $cateData = $this->m_cate->where('page_id='.$map['page_id'].' and pid=0')->order('sort desc, id asc')->select();
        $this->assign('cateData',$cateData);
        $count = $this->m_cate->where($map)->count();
        $p = getpage($count,10);
        $data = $this->m_cate->where($map)->order('sort desc, id asc')->limit($p->firstRow, $p->listRows)->select();
        // dump($data);
        $this->assign('data', $data);// 赋值数据集
        $this->assign('count', $count); 
        $this->assign('page', $p->show()); // 赋值分页输出
        $this->assign('statusArr',$this->statusArr);//信息状态
        $this->display();
    }

    /**
     * 分类管理添加或编辑页面
     */
    public function categoryAdd(){
        $page_id = I('page_id',0,'intval');
        $id = I('id',0,'intval');
        
        $dataInfo = $this->m_cate->find($page_id);
        $this->assign('dataInfo',$dataInfo);

        $data = $this->m_cate->where($map)->find($id);
        //二级分类
        $cateData = $this->m_cate->where('page_id='.$page_id.' and pid=0')->order('sort desc, id asc')->select();
        $this->assign('cateData',$cateData);
        $this->assign('data',$data);
        $this->assign('statusArr',$this->statusArr);//信息状态
        $this->display();
    }

    /**
     * 分类管理数据
     */
    public function categorySave(){
        $id = I('id',0,'intval');
        $data = I('post.'); 

        if (isset($data['pid']) && $data['pid']==0) {
            $this->ajaxReturn('请选择上级分类');
        }
        if(isset($data['title']) && empty($data['title'])){
            $this->ajaxReturn('分类名称不能为空');
        }
        if(isset($data['status']) && $data['status'] == 0){
            $this->ajaxReturn('请开启状态');
        }
        if(isset($data['sort']) && $data['sort']>99999){
            $this->ajaxReturn("排序最大值为99999哦~");
        }
        //数据入库
        if($id == 0){
            $data['add_time'] = time();
            $res = $this->m_cate->add($data);
            if(!$res){
                $this->ajaxReturn('添加失败');
            }
            $this->ajaxReturn('OK');
        }else{
            $res = $this->m_cate->where('id='.$id)->save($data);
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

    // 标题修改
    public function cateTitle(){
        if(IS_POST){
            $p = I("post.");
            if(empty($p['id'])){
                $this->ajaxReturn("操作ID不能为空");
            }
            $data["title"] = $p['title'];
            $res = $this->m_cate->where(array("id"=>$p["id"]))->save($data);
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
    //修改状态
    public function isRecommend(){
        $map['id'] = I('id',0,'intval');
        $status = I('status',0,'intval');
        $pid = I('pid',0,'intval');
        if($status == 1 && $pid > 7){
            $this->ajaxReturn('客户见证和资讯中心列表页不能推荐');
        }
        $res = $this->m->where($map)->setField('is_recommend',$status);
        if($res){
             $this->ajaxReturn('OK');
        }else{
            $this->ajaxReturn('修改失败');
        }
    }

    public function cateStatus(){
        $map['id'] = I('id',0,'intval');
        $status = I('status',0,'intval');
        $res = $this->m_cate->where($map)->setField('status',$status);
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

    // 内容排序
    public function cateSort(){
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
            $res = $this->m_cate->where(array("id"=>$p["id"]))->save($data);
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
    
    public function cateDelete(){
        $id = I('id',0,'int');
        $res = $this->m_cate->delete($id);
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

    public function cateDelAll(){
        $ids = I('post.ids');
        if(!$ids && !is_array($ids)){
            $this->ajaxReturn('删除失败');
        }
        $map['id']  = array('in',$ids);
        $res = $this->m_cate->where($map)->delete();
        if($res){
            $this->ajaxReturn('OK');
        }else {
            $this->ajaxReturn('删除失败');
        }
    }

                
}