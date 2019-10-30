<?php
namespace Admin\Controller;
use Think\Controller;
/**
* 登录退出管理
*/
class AccountController extends Controller
{ 
	// 用户登录
	public function login(){
		if(IS_GET){	          
			$this->display();
		}else{
			//接受用户密码调用模型方法进行比对
			$username = I('username','','trim');
			$password = I('password');
			$res = $this->checkPassword($username,$password);
			if(!$res){
				$this->ajaxReturn('用户不存在或密码错误');
			}
			else{
		        $data['login_ip'] = get_client_ip();
				$data['login_time'] = date("Y-m-d H:i:s");
	            M("Admin")->where(array('id'=>$res['id']))->save($data);                  //增加数据
	            session_start(); 
	            session('admin_id', $res["id"]);     //将admin_id存入session
	            session('admin_username', $res["username"]); //将admin_username存入session
	            $this->ajaxReturn('OK');
	       }
		}
	}
	
	public function checkPassword($username,$password){
        $map['username'] = $username;
        $admin = M('Admin')->where($map)->find();
		// 验证密码，输入密码双重md5加密加盐与数据库密码对比验证 
		$pwd = md5(md5($password).$admin['salt']);
        if($admin['password'] === $pwd){
            return $admin;
        }else{
            return false;
        }
    }

	public function logout(){
  		$data = M("Admin")->where(array('id'=>session('admin_id')))->find();
		session('admin_id',null);
		session('admin_username',null);
		session('login_ip',$data['login_ip']);
		session('login_time',$data['login_time']);
	}
}