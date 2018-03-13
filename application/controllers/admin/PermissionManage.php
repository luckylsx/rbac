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
class PermissionManage extends CI_Controller {
    public function __construct(){
        parent::__construct();
    }

    /**
     * 后台用户权限列表
     */
    public function index()
    {
        $this->load->view("admin/user/admin_permission");
    }

    /**
     * 角色列表
     */
    public function roleList()
    {
        $this->load->view("admin/user/admin_role");
    }

}