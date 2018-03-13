<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login  extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct()
    {
        parent::__construct();
        $this->load->model("admin/UserModel");
        $this->UserModel = new UserModel();
    }

    /**
     * 展示登录首页
     */
    public function index()
	{
	    //var_dump($_SESSION['MyAuth']);exit;
	    //判断是否已登录->如果已登录则直接跳转到后台首页
	    if ($_SESSION['MyAuth']){
            redirect('admin/admin/index');
        }
		$this->load->view('admin/login');
	}

    /**
     * 登录
     */
	public function ajaxLogin()
    {
        $info = $this->input->post();
//        var_dump($info);exit;
        $check = $this->checkUserInfo($info);
        //验证参数
        if (!$check){
            $this->load->view('admin/login');
            return;
        }
        //保存用户信息到session
        $this->session->set_userdata(['MyAuth'=>['user'=>$check]]);
        redirect('admin/admin/index');
    }

    /**
     * 登出
     */
    public function logout()
    {
        //清除session
        unset($_SESSION['MyAuth']);
        redirect('admin/login');
    }
    /**
     * 验证用户信息及密码是否正确
     * @param $data
     * @return bool
     */
    protected function checkUserInfo($data)
    {
        if (!$data){
            return false;
        }
        //用户名不存在或者为空
        if (!isset($data['username']) || empty($data['username'])){
            return false;
        }
        //面膜不存在或者为空
        if (!isset($data['password']) || empty($data['password'])){
            return false;
        }
        //验证密码是否正确
        $info = $this->UserModel->getUserInfo($data['username']);
//        var_dump($info);exit;
        if (!$info) return false;
        //验证密码是否正确
        if (!password_verify($data['password'],$info['password'])){
            return false;
        }
        return $info;
    }
}
