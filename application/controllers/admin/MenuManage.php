<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 后台用户管理
 * Class UserManage
 */
class MenuManage extends CI_Controller {
    private static $helper;
    //配置文件的存放路径
    public function __construct(){
        parent::__construct();
        $this->load->model("admin/MenuModel");
        $this->menuModel = new MenuModel();
        $this->load->library('My_Validate');
        $this->validate = new My_Validate();
        //单例模式实例化工具辅助函数
        $this->load->helper("function_helper");
        if (!(self::$helper instanceof function_helper)){
            self::$helper = new function_helper();
        }
        $this->load->library('My_Validate');
        $this->validate = new My_Validate();
    }

    /**
     * 获取公共配置信息
     */
    public function index()
    {
        //获取后台管理员列表
        $info['menu_list'] = $this->menuModel->getMenuList();
        $this->load->view('admin/menu/menu_list',$info);
    }

    /**
     * 展示添加菜单按钮
     */
    public function menuAdd()
    {
        $info['p_menu'] = $this->menuModel->getPmenu();
        $this->load->view("admin/menu/menu_add",$info);
    }
    /**
     * 添加菜单逻辑
     */
    public function menuAddAction()
    {
        //接收相应参数
        $params = $this->input->post();
        //验证参数
        $rule = [['title|菜单名','require'],['sort|排序','require']];
        $this->validate = new My_Validate($rule);
        $this->validate->validate($params);
        if ($errmsg = $this->validate->getError()){
            self::$helper->ajaxReturn(1,$errmsg);
        }
        //插入菜单数据表
        $status = $this->menuModel->insertMenu($params);
        if (!$status){
            self::$helper->ajaxReturn(1,'add failed');
        }
        self::$helper->ajaxReturn(0,'success');
    }

    /**
     * 展示编辑页面
     * @return bool
     */
    public function menuEdit()
    {
        $menu_id = $this->input->get("id");
        if (!$menu_id){
            return false;
        }
        $info['menu'] = $this->menuModel->getMenuById($menu_id);
        $info['p_menu'] = $this->menuModel->getPmenu();
        $this->load->view("admin/menu/menu_edit",$info);
    }

    /**
     * 更新数据
     */
    public function editAction()
    {
        $menu = $this->input->post();
        if (!$menu['id']){
            self::$helper->ajaxReturn(1,'请选择编辑的记录');
        }
        //更新菜单数据
        $status = $this->menuModel->updateMenu($menu);
        if (!$status){
            self::$helper->ajaxReturn(1,"请重试");
        }
        self::$helper->ajaxReturn(0,'');
    }
    /**
     * 更新数据
     */
    public function delMenu()
    {
        $menu_id = $this->input->post('id');
        if (!$menu_id){
            self::$helper->ajaxReturn(1,'请选择删除的记录');
        }
        //获取该菜单下面的子菜单
        $subMenus = $this->menuModel->getSubMenuByPid($menu_id);
        //存在子菜单
        if ($subMenus){
            self::$helper->ajaxReturn(1,"删除失败，请先删除其下的子菜单");
        }
        //更新菜单数据
        $status = $this->menuModel->delMenu($menu_id);
        if (!$status){
            self::$helper->ajaxReturn(1,"请重试");
        }
        self::$helper->ajaxReturn(0,'');
    }

}