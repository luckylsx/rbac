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
class RoleManage extends CI_Controller {
    private  static $helper = null;
    public function __construct(){
        parent::__construct();
        $this->load->model(["admin/RoleModel","admin/MenuModel"]);
        $this->RoleModel = new RoleModel();
        $this->MenuModel = new MenuModel();
        $this->load->helper("function_helper");
        if (!(self::$helper instanceof function_helper)){
            self::$helper = new function_helper();
        }

    }

    /**
     * 角色列表
     */
    public function index()
    {
        $page = $this->input->get("page");
        $page = $page ? $page:1;
        //获取角色列表
        $roleList = $this->RoleModel->getRoleList($page,PAGINATION);
        //每页记录
        $info['roleList'] = $roleList['list'];
        //总记录数
        $info['total'] = $roleList['total'];
        $base_url = site_url('admin/RoleManage/index');
        $info['page_show'] = self::$helper->pagination('',$base_url,$roleList['total'],PAGINATION);
        $this->load->view("admin/role/admin_role",$info);
    }

    /**
     * 角色添加
     */
    public function roleAdd()
    {
        //处理pos他提交数据  添加角色到数据库
        if (IS_POST){
            $postMenu = $this->input->post();
            //剔除提交按钮的name值
            if ($postMenu){
                unset($postMenu['admin-role-save']);
                //验证相关参数
                $status = $this->RoleModel->checkParames($postMenu);
                //验证失败
                if (!$status){
                    self::$helper->ajaxReturn(1,'role add failed');
                }
                //插入数据库
//                var_dump($postMenu);exit;
                $insertId = $this->RoleModel->insertRole($postMenu);
                if ($insertId){
                    self::$helper->ajaxReturn(0,'ok',[]);
                }
                self::$helper->ajaxReturn(1,'ok',[]);
            }
            self::$helper->ajaxReturn(1,'参数有误');
        }
        //获取树形结构菜单权限列表
        $menuList = $this->MenuModel->getMenuList();
        $info['menuList'] = $menuList;
        $this->load->view("admin/role/admin_role_add",$info);
    }

    /**
     * 角色编辑
     */
    public function roleEdit()
    {
        //接收编辑的id
        $id = $this->input->get('id');
        if (!$id){
            self::$helper->ajaxReturn(1,'failed',[]);
        }
        //获取该id对应的角色记录
        $role = $this->RoleModel->getRoleById($id);
        if (!$role){
            self::$helper->ajaxReturn(1,'failed',[]);
        }
        $role['menu_list'] = $role['menu_list'] ? explode(',',$role['menu_list']):"";
        //获取树形结构菜单权限列表
        $menuList = $this->MenuModel->getMenuList();
        $info['menuList'] = $menuList;
        $info['role'] = $role;
        $this->load->view("admin/role/admin_role_edit",$info);
    }

    /**
     * 更新角色处理逻辑
     */
    public function editAction()
    {
        $post = $this->input->post();
        if (!$post){
            self::$helper->ajaxReturn(1,'edit failed',[]);
        }
        unset($post['admin-role-save']);
        //验证相关参数
        $status = $this->RoleModel->checkParames($post);
        //验证失败
        if (!$status){
            self::$helper->ajaxReturn(1,'role edit failed');
        }
        //验证编辑的改记录是否存在
        $row = $this->RoleModel->getRoleById($post['id']);
        if (!$row){
            self::$helper->ajaxReturn(1,'role edit failed');
        }
        //是否更新成功
        $status = $this->RoleModel->replaceRole($post);
        if ($status){
            self::$helper->ajaxReturn(0,'edit success',[]);
        }
        self::$helper->ajaxReturn(1,'edit failed',[]);
    }

    /**
     * 删除角色
     */
    public function delRole()
    {
        //接收id参数
        $id = $this->input->post("id");
        if (!$id){
            self::$helper->ajaxReturn(1,'delete failed',[]);
        }
        //查询删除的记录是否存在
        $role = $this->RoleModel->getRoleById($id);
        if (!$role){
            self::$helper->ajaxReturn(1,'delete failed',[]);
        }
        //删除记录
        $id = $this->RoleModel->delRole($id);
        //删除记录是否成功
        if (!$id){
            self::$helper->ajaxReturn(1,'delete failed',[]);
        }
        self::$helper->ajaxReturn(0,'delete success',[]);
    }


}