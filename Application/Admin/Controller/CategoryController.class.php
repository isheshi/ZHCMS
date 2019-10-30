<?php
namespace Admin\Controller;

class CategoryController extends CommonController {


	 protected $m = NULL;
	 protected $m_list = NULL;
	 protected $statusArr=array("关闭","开启");
	 
	 public function _initialize(){
	 		parent::_initialize();
			$this->m = M('news_class');	
			$this->m_list = M('list_page');	//var_dump($_COOKIE['AUTH_USER_NAME']);
	 }
	


    public function index(){
		header('Content-Type:text/html;charset=utf-8');
		
		// $where['language'] = cookie('AUTH_USER_LANG');
		// //-----配置信息-------
		$rid = I('rid',0,'intval');
		if ($rid) {
			$map['rid'] = $rid;
		}
		$l_data = $this->m_list->find($rid);
		$this->assign('rid',$rid);// 赋值数据集
		$this->assign('l_data',$l_data);	
		// dump($l_data);
		//------------------------
		$parentid = I('parentid',0,'intval');
		
		$where['rid'] = $rid;
		$where['parent_id'] = $parentid;
		
		$count = $this->m->where($map)->count();
        $p = getpage($count,10);
        $data = $this->m->where($map)->order('sort desc')->limit($p->firstRow, $p->listRows)->select();
        $this->assign('data', $data);// 赋值数据集
        $this->assign('count', $count); 
        $this->assign('page', $p->show()); // 赋值分页输出
       	$this->assign('statusArr',$this->statusArr);// 状态
		$this->display();
    }
	
	public function add(){
		$id = I('id',0,"intval");
		$rid = I('rid',0,"intval");
		$data = $this->m->find($id);

		$this->assign('data',$data);
		$this->assign('rid',$rid);
		$this->display();
	}
	
	// public function saveAll(){ // 批量添加类别名称
	// 	header('Content-Type:text/html;charset=utf-8');

	// 	$data=$this->m->create();
	// 	$class_name=$data["class_name"];
	// 	$class_name_arr=explode("\r\n",$data["class_name"]);
	// 	$class_name_arr=array_filter($class_name_arr);// 过滤空数组
	// 	//$class_name_arr=array_unique($class_name_arr);// 过滤重复
	// 	foreach($class_name_arr as $key=>$item){
	// 		$dateList[$key]["rid"]=intvalval($data["rid"]);
	// 		$dateList[$key]["class_name"]=$item;
	// 		$dateList[$key]["parent_id"]=intvalval($data["parent_id"]);
	// 		$dateList[$key]["depth"]=intvalval($data["depth"]);
	// 		$dateList[$key]["list_order"]=10;
	// 		$dateList[$key]['language'] = cookie('AUTH_USER_LANG');
	// 	}
	// 	$result=$this->m->addAll($dateList);
	// 	//echo $this->m->data($class_name_arr)->addAll();
	// 	if ($result){
	// 		$this->success($GLOBALS['notice']['success'][0]);
	// 	}else{
	// 		$this->error($GLOBALS['notice']['error'][0]);
	// 	}
	// }
	
	public function save(){ 
        $id = I('id',0,'intval');
        $data = I('post.'); 

        if(isset($data['class_name']) && empty($data['class_name'])){
            $this->ajaxReturn('分类名称不能为空');
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

	 // 标题修改
    public function title(){
        if(IS_POST){
            $p = I("post.");
            if(empty($p['id'])){
                $this->ajaxReturn("操作ID不能为空");
            }
            $data["class_name"] = $p['title'];
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