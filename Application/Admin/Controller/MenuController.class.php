<?php
namespace Admin\Controller;

/**
* 菜单管理
*/
class MenuController extends CommonController
{
	// 菜单列表
	public function index(){
		$pid = (int)I('get.pid');
		$data['lists'] = M('adminMenus')->where(array('pid'=>$pid))->select();

		// 返回上级菜单
		$backid = 0;
		if($pid > 0){
			$parent = M('adminMenus')->where(array('mid'=>$pid))->find();
			$backid = $parent['pid'];
		}

		$this->assign('pid',$pid);
		$this->assign('backid',$backid);
		$this->assign('data',$data);
		return $this->display();
	}

	// 保存菜单
	public function save(){
		$pid = (int)I('post.pid');
		$ords = I('post.ords/a');
		$titles = I('post.titles/a');
		$controllers = I('post.controllers/a');
		$methods = I('post.methods/a');
		$ishiddens = I('post.ishiddens/a');
		$status = I('post.status/a');
		$icons = I('post.icons/a');

		foreach ($ords as $key => $value) {
			// 新增
			$data['pid'] = $pid;
			$data['ord'] = $value;
			$data['title'] = $titles[$key];
			$data['icon'] = $icons[$key];
			$data['controller'] = $controllers[$key];
			$data['method'] = $methods[$key];
			$data['ishidden'] = isset($ishiddens[$key]) ? 1 : 0;
			$data['status'] = isset($status[$key]) ? 1 : 0;

			if($key==0 && $data['title']){
				M('adminMenus')->add($data);
			}
			if($key > 0){
				if($data['title']=='' && $data['icon']=='' && $data['controller']=='' && $data['method'] == ''){
					// 删除
					M('adminMenus')->where(array('mid'=>$key))->delete();
				}else{
					// 修改
					M('adminMenus')->where(array('mid'=>$key))->save($data);
				}
			}
			
		}

		exit(json_encode(array('code'=>0,'msg'=>'保存成功')));
	}
}