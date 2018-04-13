<?php
/**
 * Created by PhpStorm.
 * User: sf.zhu
 * Date: 2016/3/25
 * Time: 18:18
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 后台用户管理
 * Class UserManage
 */
class UserManage extends CI_Controller {
    private static $helper;
    //每页条数
    private $page_limit;
    //配置信息
    private $conf;
    public function __construct(){
        parent::__construct();
        $this->load->model(["admin/UserModel",'ComConfModel']);
        $this->userModel = new UserModel();
        $this->comConfModel = new ComConfModel();
        //单例模式实例化工具辅助函数
        $this->load->helper("function_helper");
        if (!(self::$helper instanceof function_helper)){
            self::$helper = new function_helper();
        }
        //获取配置信息
        $this->conf = $this->comConfModel->getConf();
        $this->page_limit = $this->conf['page_limit'];
    }

    /**
     * 后台用户列表
     */
    public function index()
    {
        //获取后台管理员列表
        $username = $this->input->get("name");
        $page = $this->input->get("page");
        $page = $page ? $page:1;
        $adminList = $this->userModel->getAdminList($username,$page,$this->page_limit);
        $base_url = site_url('admin/UserManage/index');
        $info['adminList'] = $adminList['list'];
        $info['page_show'] = self::$helper->pagination(['username'=>$username],$base_url,$adminList['total'],$this->page_limit);
        $info['name'] = '';
        if ($username){
            $info['name'] = $username;
        }
        $info['total'] = $adminList['total'];
        $this->load->view("admin/user/user_list",$info);
    }

    /**
     * 后台用户添加
     */
    public function addUser()
    {
        if (IS_POST){
            $data = $this->input->post();
            //验证相关参数
            $checkStatus = $this->userModel->checkUserInfo($data);
            //验证失败
            if (!$checkStatus){
                self::$helper->ajaxReturn(1,'admin user add failed',[]);
            }
            $status = $this->userModel->addUser($data);
            //添加失败
            if (!$status){
                self::$helper->ajaxReturn(1,'admin user add failed',[]);
            }
            self::$helper->ajaxReturn(0,'admin user success',[]);
        }
        $this->load->model("admin/RoleModel");
        $roleModel = new RoleModel();
        $info['roleList'] = $roleModel->getAllRole();
        $this->load->view("admin/user/user_add",$info);
    }

    /**
     * 用户列表
     */
    public function userEdit()
    {
        //接收修改的user_id参数
        $user_id = $this->input->get("user_id");
        if (!$user_id){
            self::$helper->ajaxReturn(1,'edit failed',[]);
        }
        //载入模型
        $this->load->model("admin/RoleModel");
        $this->load->model("admin/UserModel");
        $userModel = new UserModel();
        $roleModel = new RoleModel();
        //获取要编辑的用户记录
        $user = $userModel->getUserInfoById($user_id);
        if (!$user){
            self::$helper->ajaxReturn(1,'edit_failed',[]);
        }
        //获取所有角色列表
        $roleList = $roleModel->getAllRole();
        $info['roleList'] = $roleList;
        $info['user'] = $user;
        $this->load->view("admin/user/user_edit",$info);
    }

    /**
     * 更新提交的数据
     */
    public function editAction()
    {
        $post = $this->input->post();
        //验证参数
        if (!$post || !element('username',$post) || !element('role_id',$post)){
            self::$helper->ajaxReturn(1,'edit failed',[]);
        }
        //验证修改的记录是否存在
        $row = $this->userModel->getUserInfoById($post['user_id']);
        if (!$row){
            self::$helper->ajaxReturn(1,'edit failed',[]);
        }
        //如果修改密码，则加密密码
        $password = '';
        if ($post['password']){
            $password = self::$helper->pass_hash($post['password']);
        }
        //更新记录
        $status = $this->userModel->replaceAdmin($post,$password);
        if (!$status){
            self::$helper->ajaxReturn(1,'edit failed',[]);
        }
        self::$helper->ajaxReturn(0,'edit success',[]);
    }

    /**
     * 删除用户逻辑
     */
    public function delUser()
    {
        //接收删除的参数id
        $id = $this->input->post('id');
        //参数不存在
        if (!$id){
            self::$helper->ajaxReturn(1,'delete admin user failed!',[]);
        }
        //验证该纪录是否存在
        $user = $this->userModel->getUserInfoById($id);
        //不存在
        if (!$user){
            self::$helper->ajaxReturn(1,'delete admin user failed!',[]);
        }
        //删除记录
        $status = $this->userModel->delAdminUser($id);
        //删除是否成功
        if (!$status){
            self::$helper->ajaxReturn(1,'delete admin user failed!',[]);
        }
        self::$helper->ajaxReturn(0,'delete admin user success!',[]);
    }

}