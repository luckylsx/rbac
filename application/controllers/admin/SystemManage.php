<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 后台用户管理
 * Class UserManage
 */
class SystemManage extends CI_Controller {
    private static $helper;
    //配置文件的存放路径
    private $common = 'common';
    public function __construct(){
        parent::__construct();
        $this->load->model("admin/SystemModel");
        $this->systemModel = new SystemModel();
        $this->load->library('My_Validate');
        $this->validate = new My_Validate();
        //单例模式实例化工具辅助函数
        $this->load->helper("function_helper");
        if (!(self::$helper instanceof function_helper)){
            self::$helper = new function_helper();
        }
        $this->load->driver('cache', array('adapter' => 'redis', 'backup' =>
            'file'));
    }

    /**
     * 获取公共配置信息
     */
    public function index()
    {
        //获取后台管理员列表
        $info['conf_list'] = $this->systemModel->getComConfig();
        $this->load->view('admin/system/system_list',$info);
    }
    /**
     * 展示系统配置添加
     */
    public function showSystemAdd()
    {
        $this->load->view('admin/system/system_add');
    }

    /**
     * 添加配置值动作
     */
    public function systemAddAction()
    {
        $conf = $this->input->post();
        //验证字段是否缺少必填字段
        $rule = [['column|字段名称','require'],['value|字段值','require'],['desc|字段描述','require']];
        $this->validate = new My_Validate($rule);
        $this->validate->validate($conf);
        if ($errmsg = $this->validate->getError()){
            self::$helper->ajaxReturn(1,$errmsg);
        }
        $row = $this->systemModel->getComConfigByColumn($conf['column']);
        //查询该配置是否已存在
        if ($row){
            self::$helper->ajaxReturn(1,"该配置名已存在");
        }
        //插入数据表
        $status = $this->systemModel->addConf($conf);
        if (!$status){
            self::$helper->ajaxReturn(1,"请重试");
        }
        //加入配置文件
        $this->addConfFile(true);
        self::$helper->ajaxReturn(0,"添加成功！");
    }
    /**
     * 展示系统配置添加
     */
    public function showUpdate()
    {
        $this->load->view('admin/system/system_add');
    }

    /**
     * 展示编辑页面
     */
    public function systemEdit()
    {
        //接收编辑记录id
        $conf_id = $this->input->get("id");
        //验证参数是否存在
        if (!$conf_id){
            self::$helper->ajaxReturn(1,"编辑失败，缺少参数");
        }
        //查询出相应配置详情
        $info['conf']=$this->systemModel->getComConfigById($conf_id);
        //载入编辑页面
        $this->load->view("admin/system/system_edit",$info);
    }

    /**
     * 编辑配置信息
     */
    public function editAction()
    {
        $params = $this->input->post();
        //验证字段是否缺少必填字段
        $rule = [['column|字段名称','require'],['value|字段值','require'],['desc|字段描述','require']];
        $this->validate = new My_Validate($rule);
        $this->validate->validate($params);
        if ($errmsg = $this->validate->getError()){
            self::$helper->ajaxReturn(1,$errmsg);
        }
        //插入数据表
        $status = $this->systemModel->updateConf($params);
        if (!$status){
            self::$helper->ajaxReturn(1,"请重试");
        }
        //加入配置文件
        $this->addConfFile(true);
        self::$helper->ajaxReturn(0,"编辑成功！");
    }

    /**
     * 删除配置记录
     */
    public function delSystemConf()
    {
        //接收参数
        $conf_id = $this->input->post('id');
        //参数id是否存在
        if (!$conf_id){
            self::$helper->ajaxReturn(1,"");
        }
        //软删除
        $status = $this->systemModel->deleteConfById($conf_id);
        if (!$status){
            self::$helper->ajaxReturn(1,'请重试');
        }
        //加入配置文件
        $this->addConfFile(true);
        self::$helper->ajaxReturn(0,'');
    }

    /**
     * 将信息添加到配置文件
     */
    protected function addConfFile($is_redis=true)
    {
        $list = $this->systemModel->getComConfig(['column','value']);
        //加入配置文件
        $data = array_column($list,'value','column');
        if ($is_redis){
            //存入redis缓存
            $this->cache->save("common_config",json_encode($data),2*60*60);
        }else{
            file_put_contents(APPPATH.$this->common.'/common_conf.json',json_encode($data));
        }
    }

}