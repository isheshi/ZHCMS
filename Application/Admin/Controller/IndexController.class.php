<?php
namespace Admin\Controller;

class IndexController extends CommonController {
    public function index(){
		header("content-type:text/html;charset=utf-8");         //设置编码

	  	session_start(); 
        $username = session('admin_username');
        $admin_id = session('admin_id');
       
        $menus = false;
        $groups = D('Admin')->relation(true)->where(array('username'=>$username))->order('login_time desc')->select();

        $this->assign('admin_id',$admin_id);
        $this->assign('username',$username);
        $this->assign('groups',$groups[0]['gid']);
        // dump($groups);

		$role = M('adminGroups')->where(array('gid'=>$groups[0]['gid']['gid']))->find();
		// dump($role);
		if($role){
			$role['rights'] = (isset($role['rights']) && $role['rights']) ? json_decode($role['rights'],true) : [];
		}
		if($role['rights']){
			$where = 'mid in('.implode(',',$role['rights']).') and ishidden=0 and status=0';
			// dump($where);dump("------------------------------------------------------------------------------");
			$menus = $this->cates("mid",$where);
		// dump($menus);dump("======================================================================");
			$menus && $menus = $this->gettreeitems($menus);
			// dump($menus);dump("======================================================================");
		}
		// $site =M('sites')->where(array('names'=>'site'))->find();
		// $site && $site['values'] = json_decode($site['values']);

		// $this->assign('site',$site);
		$this->assign('role',$role);
		$this->assign('menus',$menus);
		// dump($role);
		// dump($menus);
		$this->assign('title','后台首页');
        $this->display();
    }

	// 自定义索引列表
	public function cates($index,$where){
		$lists = M("AdminMenus")->where($where)->select();
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

	//后台欢迎页
	public function welcome(){
		$list = M("admin")->where("id = ".session('admin_id'))->find();
		$this->assign('list',$list);
		$this->assign('title','后台首页');
        $this->display();
    }
}