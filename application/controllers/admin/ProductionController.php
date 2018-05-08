<?php
/**
 * Created by PhpStorm.
 * User: lucky.li
 * Date: 2018/4/23
 * Time: 16:32
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 产品管理控制器
 * Class ProductionController
 */
class ProductionController extends CI_Controller
{
    //辅助函数类存储对象
    private static $helper;
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['ComConfModel','admin/ProductionModel','admin/CategoryModel']);
        $this->comConfModel = new ComConfModel();
        $this->cate = new CategoryModel();
        //单例模式实例化工具辅助函数
        $this->load->helper("function_helper");
        if (!(self::$helper instanceof function_helper)){
            self::$helper = new function_helper();
        }
        $this->productionModel = new ProductionModel();
        //获取配置信息
        $this->conf = $this->comConfModel->getConf();
        $this->page_limit = $this->conf['page_limit'];
    }

    /**
     * 商品列表展示首页
     */
    public function index()
    {
        //接收查询参数
        $search = $this->input->get();
        //接收页码参数
        $page = $this->input->get("page");
        $page = (bool)$page?intval($page):1;
        $production = $this->productionModel->getProductionList($search,$page,$this->page_limit);
        //产品列表
        $info['proList'] = $production['list'];
        $base_url = base_url("admin/ProductionController/index");
        $info['page_show'] = self::$helper->pagination($search,$base_url,$production['total'],$this->page_limit);
        //查询条件
        $info['search'] = $search;
        $info['total'] = $production['total'];
        $this->load->view("admin/production/production_list",$info);
    }

    /**
     * 展示产品添加页面
     */
    public function productionAdd()
    {
        $this->load->model("admin/LabelModel");
        $lm = new LabelModel();
        //查询出分类列表
        $info['cateList'] = $this->cate->getCate();
        //获取家具类型列表
        $info['types'] = $lm->getTypeList(1);
        //获取家居风格列表
        $info['styles'] = $lm->getTypeList(2);
        $this->load->view("admin/production/production_add",$info);
    }

    /**
     * 新增产品动作
     */
    public function addAction()
    {
        //接收参数
        $params = $this->input->post();
        //定义验证规则
        $error = $this->checkParam($params);
        if ($error){
            self::$helper->ajaxReturn(1,$error);
        }
        //插入产品表
        $status = $this->productionModel->insertProduction($params);
        if (!$status){
            self::$helper->ajaxReturn(1,"新增失败，请重试！");
        }
        self::$helper->ajaxReturn(0,"新增成功");
    }

    /**
     * 展示编辑页面
     * @return bool
     */
    public function edit()
    {
        $pro_id = $this->input->get("id");
        if (!$pro_id){
            return false;
        }
        $this->load->model("admin/LabelModel");
        $lm = new LabelModel();
        //查询出分类列表
        $info['cateList'] = $this->cate->getCate();
        //获取家具类型列表
        $info['types'] = $lm->getTypeList(1);
        //获取家居风格列表
        $info['styles'] = $lm->getTypeList(2);
        //查询茶品详情
        $info['detail'] = $this->productionModel->getProductionById($pro_id);
        //展示编辑页面
        $this->load->view("admin/production/production_edit",$info);
    }

    /**
     * 编辑提交动作
     */
    public function editAction()
    {
        //接收参数
        $params = $this->input->post();
        //定义验证规则
        $error = $this->checkParam($params);
        if ($error){
            self::$helper->ajaxReturn(1,$error);
        }
        //插入产品表
        $status = $this->productionModel->updateProduction($params);
        if (!$status){
            self::$helper->ajaxReturn(1,"编辑失败，请重试！");
        }
        self::$helper->ajaxReturn(0,"编辑成功");
    }

    /**
     * 下架商品
     */
    public function productStop()
    {
        //接收参数
        $production_id = $this->input->post('production_id');
        //产品id不存在
        if (!$production_id){
            self::$helper->ajaxReturn(1,'下架失败，请重试!');
        }
        //下架产品
        $status = $this->productionModel->upOrDownStair($production_id,2);
        if (!$status){
            self::$helper->ajaxReturn(1,'下架失败');
        }
        self::$helper->ajaxReturn(0,'下架成功');
    }

    /**
     * 上架商品
     */
    public function productStart()
    {
        //接收参数
        $production_id = $this->input->post('production_id');
        //产品id不存在
        if (!$production_id){
            self::$helper->ajaxReturn(1,'下架失败，请重试!');
        }
        //下架产品
        $status = $this->productionModel->upOrDownStair($production_id,1);
        if (!$status){
            self::$helper->ajaxReturn(1,'发布失败');
        }
        self::$helper->ajaxReturn(0,'发布成功');
    }
    /**
     * 删除商品
     */
    public function productDel()
    {
        //接收参数
        $production_id = $this->input->post('production_id');
        //产品id不存在
        if (!$production_id){
            self::$helper->ajaxReturn(1,'删除失败，请重试!');
        }
        //删除产品（软删除）
        $status = $this->productionModel->delProduction($production_id);
        if (!$status){
            self::$helper->ajaxReturn(1,'删除失败');
        }
        self::$helper->ajaxReturn(0,'删除成功');
    }

    /**
     * 批量删除产品
     */
    public function productDelBatch()
    {
        //接收参数
        $proIds = $this->input->post('proIds');
        //产品id不存在
        if (!$proIds || !is_array($proIds)){
            self::$helper->ajaxReturn(1,'删除失败，请重试!');
        }
        //删除产品（软删除）
        $status = $this->productionModel->delProduction($proIds);
        if (!$status){
            self::$helper->ajaxReturn(1,'删除失败');
        }
        self::$helper->ajaxReturn(0,'删除成功');
    }
    /**
     * 图片上传
     */
    public function upload()
    {
        $file = $this->input->file();
        var_dump($file);
    }

    /**
     * 验证参数
     * @param $params array 需要验证的参数
     * @return mixed|null|string
     */
    protected function checkParam($params)
    {
        //添加商品 验证规则
        $rules = [
            ['cate_id|分类','require'],
            ['brand_title|品牌标题','require'],
            ['name|产品名称','require'],
            ['label|产品标签','require'],
            ['Item_No|产品货号','require'],
            ['abstract|产品摘要','require'],
            //['description|产品描述','require'],
            ['price|产品价格','require'],
        ];
        //载入验证类
        $this->load->library("My_Validate");
        $validator = new My_Validate($rules);
        //验证参数
        $validator->validate($params);
        //是否有缺少的参数
        $error = $validator->getError();
        //存在缺少参数 返回错误信息
        if ($error){
            return $error;
        }else{ //不存在 返回空
            return '';
        }
    }
}