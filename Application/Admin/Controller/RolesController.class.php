<?php
namespace Admin\Controller;

/**
* 角色管理
*/
class RolesController extends CommonController
{
	// 角色列表
	public function index(){
		$adminGroups = M('adminGroups');
		// 加载角色前判断是否超级管理员,不是则不显示超级管理员
        if(session('admin_id') != 1){
			$map['gid'] = array('neq',1);
        }
		$data['roles'] = $adminGroups->where($map)->select();
		foreach ($data['roles'] as $key => $value) {
			// dump($data['roles'][$key]['gid']);
			$data['roles'][$key]['admin_username'] = M('admin')->where('gid='.$data['roles'][$key]['gid'])->field('gid,username')->select();
			$data['roles'][$key]['count'] = M('admin')->where('gid='.$data['roles'][$key]['gid'])->count();
		}
		// dump($data['roles']);
        $data['count'] = $adminGroups->count();
		$this->assign('data',$data);
		return $this->display();
	}

	// 角色添加
	public function add(){
		$gid = I('gid',0,'intval');
		$role = M("adminGroups")->find($gid);
		// dump($gid);dump($role);
		$role && $role['rights'] && $role['rights'] = json_decode($role['rights']);
		$this->assign('role',$role);
		// dump($role);
		$menu_list = $this->cates("mid");
		// dump($menu_list);//获取自定义索引列表
		$menus = $this->gettreeitems($menu_list);
		// dump($menus);dump("______________");
		$results = array();
		foreach ($menus as $value) {
			$value['children'] = isset($value['children'])?$this->formatMenus($value['children']):false;
			$results[] = $value;
		}
		// dump($results);
		if($id) {
			$title = "编辑角色";
			$this->assign('gid',$admin['gid']);
		}else{
			$title = "添加角色";
		}
		$this->assign('menus',$results);
		$this->assign('title',$title);
		return $this->display();
	}
	
	// 自定义索引列表
	public function cates($index){
		$lists = M("AdminMenus")->where(array('status'=>0))->select();
		if(!$lists){
			return false;
		}
		$results = [];
		foreach ($lists as $key => $value) {
			$results[$value[$index]] = $value;
		}
		return $results;
	}

	public function gettreeitems($items){
		$tree = array();
		foreach ($items as $item) {
			if(isset($items[$item['pid']])){
				$items[$item['pid']]['children'][] = &$items[$item['mid']];
			}else{
				$tree[] = &$items[$item['mid']];
			}
		}
		return $tree;
	}

	public function formatMenus($items,&$res = array()){
		foreach($items as $item){
			if(!isset($item['children'])){
				$res[] = $item;
			}else{
				$tem = $item['children'];
				unset($item['children']);
				$res[] = $item;
				$this->formatMenus($tem,$res);
			}
		}
		return $res;
	}

	public function save(){
		$gid = I('gid',0,'intval');

		$data['title'] = trim(I('post.title'));
		$data['desc'] = trim(I('post.desc'));
		$menus = I('post.menu/a');
		if(!$data['title']){
			$this->ajaxReturn('角色名称不能为空');
			// exit(json_encode(array('code'=>1,'msg'=>'角色名称不能为空')));
		}
		
		$menus && $data['rights'] = json_encode(array_keys($menus));

		if($gid){
			M("adminGroups")->where(array('gid'=>$gid))->save($data);
		}else{
			$adminGroups = M("adminGroups")->select();
			foreach ($adminGroups as $key => $value) {
				if ($adminGroups[$key]['title'] == $data['title']) {
					$this->ajaxReturn('该角色已存在');
				}
			}
			M("adminGroups")->add($data);
		}
		$this->ajaxReturn('OK');
		// exit(json_encode(array('code'=>0,'msg'=>'保存成功')));
	}

	// 删除
	public function delete(){
		$gid = I('gid','0','intval');
		// 删除角色前判断是否超级管理员
		$admin_id = session('admin_id'); 
		$session_admin = M('admin')->find($admin_id);
		if($session_admin['gid'] == 1){
			$res = M("adminGroups")->where(array('gid'=>$gid))->delete();
        }else{
        	$this->ajaxReturn("您没有权限进行此操作！");
        }
		if(!$res){
			$this->ajaxReturn("删除失败");
		}
		$this->ajaxReturn("OK");
	}
}