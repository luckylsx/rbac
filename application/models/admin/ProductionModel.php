<?php
/**
 * Created by PhpStorm.
 * User: lucky.li
 * Date: 2018/4/23
 * Time: 16:34
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 产品管理模型
 * Class ProductionModel
 */
class ProductionModel extends CI_Model
{
    //产品表
    private $pro_table="production";
    //产品标签关联表
    private $pro_label_table="production_label";
    //辅助函数存储对象
    private static $helper;
    public function __construct()
    {
        parent::__construct();
        $this->load->library("LogRecode");
        $this->log = new LogRecode();
        //单例模式实例化工具辅助函数
        $this->load->helper(["function_helper",'array']);
        if (!(self::$helper instanceof function_helper)){
            self::$helper = new function_helper();
        }
    }

    /**
     * 查询商品列表字段
     * @param $search array 查询字段
     * @param $page int 页码
     * @param $page_limit int 每页条数
     * @param array $select 查询的字段
     * @return array
     */
    public function getProductionList($search,$page,$page_limit,$select=[])
    {
        //日期处理
        $offset = ($page-1)*$page_limit;
        if ($select){
            $s = implode(',',$select);
        }else{
            $s = '*';
        }
        $this->db->select($s);
        $this->db->order_by('id','desc');
        //根据日期查询
        if (element('start_date',$search)){
            if (element('start_date',$search) && element('end_date',$search)){
                $date = self::$helper->date_formate(element('start_date',$search),element('end_date',$search));
            }else{
                $start_date = element('start_date',$search);
                $date = self::$helper->date_formate($start_date,$start_date);
            }
            $this->db->where("created_at>=",$date[0]);
            $this->db->where("created_at<=",$date[1]);
        }
        //根据商品货号查询
        if ($pro_num = element('pro_num',$search)){
            $this->db->where("Item_No",$pro_num);
        }
        //查询调价存在 根据 分类名称 或 产品名称 或 标签名称 或 摘要查询
        if ($t = element('degist',$search)){
            $this->db->like("cate_name",$t,'after')
                ->or_like('name',$t,'after')
                ->or_like('label',$t,'after')
                ->or_like('abstract',$t,'after');
        }
        $this->db->where("status",1); //未删除
        $db = clone $this->db;
        //统计总记录数
        $total = $this->db->count_all_results($this->pro_table,FALSE);
        $this->db = $db;
        $this->db->limit($page_limit,$offset); //未删除
        $list = $this->db->get($this->pro_table)->result_array();
        return ['list'=>$list,'total'=>$total];
    }

    /**
     * 根据商品id获取商品详情
     * @param $pro_id
     * @param array $select
     * @return mixed
     */
    public function getProductionById($pro_id)
    {
        $this->db->select("p.*,production_id,lp.type_id,type_name,styles_id,styles_list,sum_styles");
        $this->db->where('p.id',$pro_id);
        $this->db->from("$this->pro_table as p");
        $this->db->join("$this->pro_label_table as lp","p.id = lp.production_id",'left');
        $proDetail = $this->db->get()->row_array();
        return $proDetail;
    }
    /**
     * 新增产品 插入数据
     * @param $params
     * @return bool
     */
    public function insertProduction($params)
    {
        if (!$params){
            return false;
        }
        //初始化产品数据
        $production = $this->initProduction($params);
        $production['created_at'] = date("Y-m-d H:i:s");
        //开启事务
        $this->db->trans_begin();
        //插入产品表
        $this->db->insert($this->pro_table,$production);
        //是否插入成功
        $proInsertId = $this->db->insert_id();
        //插入的id不存在 插入失败 事务回滚
        if (!$proInsertId){
            $this->db->trans_rollback();
            return false;
        }
        //初始化产品标签关联数据
        $proLabel = $this->initProLabel($params,$proInsertId);
        $proLabel['production_id'] = $proInsertId; //插入的产品id
        $proLabel['created_at'] = date("Y-m-d H:i:s");
        //插入产品标签关联表
        $this->db->insert($this->pro_label_table,$proLabel);
        //是否插入成功
        $proLabelInsertId = $this->db->insert_id();
        //插入的id不存在 插入失败 事务回滚
        if (!$proLabelInsertId){
            $this->db->trans_rollback();
            return false;
        }
        $this->db->trans_commit();
        return true;
    }

    /**
     * 更新产品
     * @param $params
     * @return bool
     */
    public function updateProduction($params)
    {
        if (!$params){
            return false;
        }
        //初始化产品数据
        $production = $this->initProduction($params);
        //开启事务
        $this->db->trans_start();
        //插入产品表
        $this->db->where('id',$params['id'])->update($this->pro_table,$production);
        //初始化产品标签关联数据
        $proLabel = $this->initProLabel($params);
        //插入产品标签关联表
        $this->db->where('production_id',$params['id'])->update($this->pro_label_table,$proLabel);
        //是否执行成功 执行成功 提交事务 执行失败 回滚事务
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            return false;
        }else{
            $this->db->trans_commit();
            return true;
        }
    }

    /**
     * 下架商品
     * @param $production_id
     * @return bool
     */
    public function upOrDownStair($production_id,$v)
    {
        if (!$production_id || !$v){
            return false;
        }
        //开启事务
        $this->db->trans_start();
        //更新产品表 在架状态 将on_sale 改为2 下架
        $this->db->where('id',$production_id)->update($this->pro_table,['on_sale'=>$v]);
        //更新产品关联表 在架状态 将on_sale 改为2 下架
        $this->db->where('production_id',$production_id)->update($this->pro_label_table,['on_sale'=>$v]);
        //是否执行成功 执行成功 提交事务 执行失败 回滚事务
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            return false;
        }else{
            $this->db->trans_commit();
            return true;
        }
    }

    /**
     * 删除产品
     * @param $production_id
     * @return bool
     */
    public function delProduction($production_id)
    {
        if (!$production_id){
            return false;
        }
        //开启事务
        $this->db->trans_start();
        //产品id是数组 批量更新
        if (is_array($production_id)){
            //更新产品表(软删除) status 改为2 删除
            $this->db->where_in('id',$production_id)->update($this->pro_table,['status'=>2]);
            //更新产品表(软删除) status 改为2 删除
            $this->db->where_in('production_id',$production_id)->update($this->pro_label_table,['status'=>2]);
        }else{
            //更新产品表(软删除) status 改为2 删除
            $this->db->where('id',$production_id)->update($this->pro_table,['status'=>2]);
            //更新产品表(软删除) status 改为2 删除
            $this->db->where('production_id',$production_id)->update($this->pro_label_table,['status'=>2]);
        }
        //是否执行成功 执行成功 提交事务 执行失败 回滚事务
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            return false;
        }else{
            $this->db->trans_commit();
            return true;
        }
    }
    /**
     * 初始化产品表数据
     * @param $params
     * @return array
     */
    protected function initProduction($params)
    {
        if (!$params){
            return [];
        }
        $this->load->model("admin/CategoryModel");
        $cm = new CategoryModel();
        $cate = $cm->getCateById($params['cate_id']);
        if (!$cate){
            return [];
        }
        $data = [
            'cate_id' => $params['cate_id'], //分类id
            'cate_name' => $cate['name'], //分类名称
            'brand_title' => $params['brand_title'], //品牌标题
            'name' => $params['name'], //产品名称
            'label' => $params['label'], //产品标签
            'Item_No' => $params['Item_No'], //产品货号
            'abstract' => $params['abstract'], //产品货号
            'image_cover' => $params['Item_No'], //产品货号
            'price' => $params['Item_No'], //产品货号
            'is_comment' => $params['is_comment'] ? $params['is_comment']:0, //允许评论
            'status' => 1, //启用
            'on_sale' => 1, //在架
            'updated_at' => date("Y-m-d H:i:s"), //更新时间
        ];
        return $data;
    }

    /**
     * 初始化产品标签关联数据
     * @param $params
     * @param $proInsertId
     * @return array
     */
    protected function initProLabel($params)
    {
        $this->load->model("admin/LabelModel");
        $label = new LabelModel();
        //验证类型是否存在
        if (!isset($params['type']) || empty($params['type'])){
            return [];
        }
        //验证风格是否存在
        if (!isset($params['styles']) || empty($params['styles'])){
            return [];
        }
        //根据类型id 获取对应的类型记录
        $type = $label->getLabelById($params['type']);
        //根据类型id 获取对应的风格记录
        $styles = $label->getLabelByIdList($params['styles'],['id','name']);
        //类型或风格不存在
        if (!$type || !$styles){
            return [];
        }
        //初始化数据
        $data = [
            "type_id" => $params['type'], //类型id
            "type_name" => $type['name'], //类型名称
            "styles_id" => json_encode($params['styles']), //风格id列表
            "styles_list" => json_encode(array_column($styles,'name','id'),JSON_UNESCAPED_UNICODE), //风格id值对应列表
            "sum_styles" => array_sum($params['styles']), //风格和
            "on_sale" => 1, //在架
            "status" => 1, //正常
            "updated_at" => date("Y-m-d H:i:s"), //更新时间
        ];
        return $data;
    }
}