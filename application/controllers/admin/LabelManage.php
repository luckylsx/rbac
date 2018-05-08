<?php
/**
 * Created by PhpStorm.
 * User: sf.zhu
 * Date: 2016/3/25
 * Time: 18:18
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 后台标签管理
 * Class UserManage
 */
class LabelManage extends CI_Controller {
    //辅助函数类存储对象
    private static $helper;
    //公共配置文件
    protected $comConf;
    public function __construct(){
        parent::__construct();
        $this->load->model("admin/LabelModel");
        $this->labelModel = new LabelModel();
        $this->load->model("ComConfModel");
        $this->comConf = $this->ComConfModel->getConf();
        //单例模式实例化工具辅助函数
        $this->load->helper(["function_helper",'array']);
        if (!(self::$helper instanceof function_helper)){
            self::$helper = new function_helper();
        }
    }
    /**
     * 咨询管理首页
     */
    public function index()
    {
        $type = $this->input->post("type");
        $type = $type ?? 1;
        $info['labelList'] = $this->labelModel->getTypeList($type);
        $info['conconf'] = $this->comConf;
        $info['type'] = $type;
        $this->load->view("admin/label/label_list",$info);
    }

    /**
     * 展示添加分类页面
     */
    public function labelAdd()
    {
        $this->load->view("admin/label/label_add");
    }

    /**
     * 标签添加
     */
    public function labelAddAction()
    {
        //接收数据
        $params = $this->input->post();
        //载入验证类进行数据验证
        $this->load->library("My_Validate");
        $rules = [['name|分类名称','require'],['cover|封面图','require']];
        $validator = new My_Validate($rules);
        $validator->validate($params);
        if ($error = $validator->getError()){
            self::$helper->ajaxReturn(1,$error);
        }
        //插入分类表
        $status = $this->labelModel->insertLabel($params);
        if ($status){
            self::$helper->ajaxReturn(0,'ok',[]);
        }
        self::$helper->ajaxReturn(1,'add failed');
    }

    /**
     * 展示编辑页面
     */
    public function edit()
    {
        $id = $this->input->get("id");
        if (!$id){
            return false;
        }
        $info['label'] = $this->labelModel->getLabelById($id);
        $this->load->view("admin/label/label_edit",$info);
    }

    /**
     * 编辑提交动作
     */
    public function editAction()
    {
        $data = $this->input->post();
        //载入验证类进行数据验证
        $this->load->library("My_Validate");
        $rules = [['name|分类名称','require'],['cover|封面图','require'],["sort|排序","require"]];
        $validator = new My_Validate($rules);
        $validator->validate($data);
        if ($error = $validator->getError()){
            self::$helper->ajaxReturn(1,$error);
        }
        //插入分类表
        $status = $this->labelModel->updateLabel($data);
        if ($status){
            self::$helper->ajaxReturn(0,'ok',[]);
        }
        self::$helper->ajaxReturn(1,'edit failed');
    }

    /**
     * 删除标签
     */
    public function delLabel()
    {
        //接收要删除的参数id
        $label_id = $this->input->post("id");
        if (!$label_id){
            self::$helper->ajaxReturn(1,'delete failed');
        }
        $status = $this->labelModel->delLabel($label_id);
        if (!$status){
            self::$helper->ajaxReturn(1,'delete failed');
        }
        self::$helper->ajaxReturn(0,'delete success');
    }

    /**
     * 上传图片
     */
    public function upload()
    {
        $this->load->library("UploadImg");
        $up = new UploadImg();
        $res = $up->upload('cover','image','label');
        if (element('errcode',$res)===0){
            $path = strstr($res['url'],"/public");
            self::$helper->ajaxReturn(0,'ok',$path);
        }
        self::$helper->ajaxReturn(1,"上传失败");
    }

}