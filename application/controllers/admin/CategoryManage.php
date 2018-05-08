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
class CategoryManage extends CI_Controller {
    //辅助函数类存储对象
    private static $helper;
    //公共配置文件
    protected $comConf;
    public function __construct(){
        parent::__construct();
        $this->load->model("admin/CategoryModel");
        $this->categoryModel = new CategoryModel();
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
        $search = $this->input->get("search");
        $page = $this->input->get("page");
        $page = $page ?? 1;
        $pageLimit = $this->comConf['page_limit'];
        $cate = $this->categoryModel->getCateList($page,$pageLimit,$search);
        $info['cateList'] = $cate['list'];
        $base_url = base_url('admin/CategoryManage/index');
        $info['page_show'] = self::$helper->pagination(['cate_name'=>$search],$base_url,$cate['total'],$pageLimit);
        $info['search'] = $search;
        $this->load->view("admin/category/category_list",$info);
    }

    /**
     * 展示添加分类页面
     */
    public function cateAdd()
    {
        $this->load->view("admin/category/category_add");
    }

    /**
     * 分类添加
     */
    public function cateAddAction()
    {
        //接收数据
        $params = $this->input->post();
        //载入验证类进行数据验证
        $this->load->library("My_Validate");
        $rules = [['name|分类名称','require'],['sort|排序','require']];
        $validator = new My_Validate($rules);
        $validator->validate($params);
        if ($error = $validator->getError()){
            self::$helper->ajaxReturn(1,$error);
        }
        //插入分类表
        $status = $this->categoryModel->insertCate($params);
        if ($status){
            self::$helper->ajaxReturn(0,'ok',[]);
        }
        self::$helper->ajaxReturn(1,'add failed');
    }
    /**
     * 展示编辑分类页面
     */
    public function edit()
    {
        $cate_id = $this->input->get("id");
        if (!$cate_id){
            return false;
        }
        //根据id获取详情
        $info['cate'] = $this->categoryModel->getCateById($cate_id);
        $this->load->view("admin/category/category_edit",$info);
    }

    /**
     * 编辑提交页面
     * @return bool
     */
    public function editAction()
    {
        //接收数据
        $params = $this->input->post();
        if (!element('id',$params)){
            self::$helper->ajaxReturn(1,'edit failed');
        }
        //载入验证类进行数据验证
        $this->load->library("My_Validate");
        $rules = [['name|分类名称','require'],['sort|排序','require']];
        $validator = new My_Validate($rules);
        $validator->validate($params);
        if ($error = $validator->getError()){
            self::$helper->ajaxReturn(1,$error);
        }
        //插入分类表
        $status = $this->categoryModel->updateCate($params);
        if ($status){
            self::$helper->ajaxReturn(0,'ok',[]);
        }
        self::$helper->ajaxReturn(1,'edit failed');
    }

    /**
     * 删除分类
     */
    public function delCate()
    {
        $cate_id = $this->input->post("id");
        if (!$cate_id){
            self::$helper->ajaxReturn(1,"delete failed");
        }
        $status = $this->categoryModel->delCate($cate_id);
        if (!$status){
            self::$helper->ajaxReturn(1,'delete failed');
        }
        self::$helper->ajaxReturn(0,'delete success');
    }
}