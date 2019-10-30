<?php
namespace Admin\Controller;

/**
* 管理员管理
*/
class AdminController extends CommonController
{
	// 管理员列表
	public function index(){
		$admin = D('Admin');

		// 加载角色前判断是否超级管理员,不是则不显示超级管理员
        if(session('admin_id') != 1){
			$map['gid'] = array('neq',1);
        }
		$data['list'] = $admin->where($map)->relation(true)->order('login_time desc')->select();
        $data['count'] = $admin->count();

		$this->assign('data',$data);
		$this->assign('title','管理员列表');
		$this->display();
	}

	// 添加管理员
	public function add(){
		$id = I('id','0','intval');
		// 加载管理员
		$admin = M('admin')->find($id);
		// 加载角色
		// 加载角色前判断是否超级管理员,不是则不显示超级管理员权限
        $admin_id = session('admin_id'); 
		$session_admin = M('admin')->find($admin_id);
        if($session_admin['gid'] != 1 && $admin['gid'] != 1){
			$map['gid'] = array('neq',1);
        }
		$data['groups'] = M('adminGroups')->where($map)->select();
		if($id) {
			$title = "编辑管理员";
			$this->assign('gid',$admin['gid']);
		}else{
			$title = "添加管理员";
		}
		$this->assign('data',$data);
		$this->assign('admin',$admin);
		$this->assign('title',$title);
		$this->display();
	}

	// 保存管理员
	public function save(){
		$id = I('id','0','intval');
		$data['username'] = trim(I('post.username'));
		$data['gid'] = (int)I('post.gid');
		$password = trim(I('post.password'));
		$password2 = trim(I('post.password2'));
		$data['status'] = (int)(I('post.status'));

		if($id==0 && !$data['username']){
			$this->ajaxReturn("用户名不能为空");
		}
		if(!$data['gid']){
			$this->ajaxReturn("权限不能为空");
		}
		if($id==0 && !$password){
			$this->ajaxReturn("密码不能为空");
		}
		if($password != $password2){
			$this->ajaxReturn("两次密码输入不一致");
		}
		
		$res = true;
		if($id==0){
			$salt=rand(100000,999999);
			//生成双重MD5之后的密码
			$data['salt'] = $salt;
			if($password){
				// 密码处理，加密则加md5 
				$data['password'] = md5(md5($password).$salt);
			}
			// 检查用户是否已存在
			$item = M('admin')->where(array('username'=>$data['username']))->find();
			if($item){
				$this->ajaxReturn("该用户已存在");
			}
			$data['add_time'] = date("Y-m-d H:i:s");
			$data['add_admin_id'] = session('admin_id');
			$res = M('admin')->add($data);
		}else{
			if($password != '' && $password2 !=''){
				$salt=rand(100000,999999);
				//生成双重MD5之后的密码
				$data['salt'] = $salt;
				// 密码处理，加密则加md5 
				$data['password'] = md5(md5($password).$salt);
			}
			// 保存用户前判断是否为本人，或者超级管理员
	        $admin_id = session('admin_id'); 
			$session_admin = M('admin')->find($admin_id);
			$add_admin = M('admin')->find($id);
	        if($id == $admin_id || $session_admin['gid'] == 1 || $add_admin['add_admin_id'] == $session_admin['id']){
				M('admin')->where(array('id'=>$id))->save($data);
	        }else{
	        	$this->ajaxReturn("您没有权限进行此操作！");
	        }
		}
		if($res){
			$this->ajaxReturn("OK");
		}	
	}

	// 删除管理员
	public function delete(){
		$id = (int)I('post.id');
		// 删除用户前判断是否为本人，或者超级管理员,或者执行添加管理员操作的管理员
        $admin_id = session('admin_id'); 
		$session_admin = M('admin')->find($admin_id);
		$add_admin = M('admin')->find($id);
        if($id == $admin_id || $session_admin['gid'] == 1 || $add_admin['add_admin_id'] == $session_admin['id']){
			$res = M('admin')->where(array('id'=>$id))->delete();
        }else{
        	$this->ajaxReturn("您没有权限进行此操作！");
        }
		if(!$res){
			$this->ajaxReturn("删除失败");
		}
		$this->ajaxReturn("OK");
	}

}