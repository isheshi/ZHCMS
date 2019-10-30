<?php
namespace Admin\Controller;
use Think\Controller;

class CommonController extends Controller 
{
	 public function _initialize(){
		header('Content-Type:text/html;charset=utf-8');
        if(!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_username'])){
            $this->redirect("Admin/account/login");
        }
    }
}
