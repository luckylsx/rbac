<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin  extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        //加载用户管理模型类
        $this->load->model("admin/UserModel");
        $this->userModel = new UserModel();
    }

    public function index()
	{
	    $info['menu_list'] = $_SESSION['MyAuth']['user_menu'];
	    $info['user'] = $_SESSION['MyAuth']['user'];
		$this->load->view('admin/index',$info);
	}

    /**
     * 展示后台首页-->我的桌面
     */
	public function welcome()
    {
        $this->load->view('admin/welcome');
    }
}
