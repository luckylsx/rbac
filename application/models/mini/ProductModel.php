<?php
/**
 * Created by PhpStorm.
 * User: lucky.li
 * Date: 2018/4/26
 * Time: 16:34
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 产品管理模型
 * Class ProductionModel
 */
class ProductModel extends CI_Model
{
    //产品表
    private $pro_table="production";
    //产品标签关联表
    private $pro_label_table = "production_label";
    //用户产品收藏表
    private $user_collection_table="user_production_collection";
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
    public function getProductionListByLabel($userLabel,$page,$page_limit,$select=['production_id','type_id','type_name','styles_id','styles_list'])
    {
        //查询字段
        $s = "p.*,".implode(',',$select).",sum_styles,pl.on_sale as pl_on_sale,pl.status as pl_status";
        $offset = ($page-1)*$page_limit;
        //查询的字段
        $this->db->select($s);
        //根据id降序排序
        $this->db->order_by('id','desc');
        //产品类型在用户所选择标签里面的
        $this->db->where("pl.type_id<=",$userLabel['sum_type']);
        //产品类型在用户所选择标签里面的
        $this->db->where("pl.sum_styles<=",$userLabel['sum_style']);
        //已发布
        $this->db->where("pl.on_sale",1);
        //未删除
        $this->db->where("pl.status",1);
        $this->db->from("production_label as pl");
        $this->db->join("production as p","pl.production_id=p.id",'left');
        $db = clone $this->db;
        //统计总记录数
        $total = $this->db->count_all_results();
        $this->db = $db;
        $this->db->limit($page_limit,$offset); //未删除
        $list = $this->db->get()->result_array();
        //获取总页数
        $total_pages = ceil($total/$page_limit);
        //分页数据
        return self::$helper->pageReturn($total,$total_pages,$page,$list);
    }
    /**
     * 用户没选择标签情况下 查询商品列表
     * @param $search array 查询字段
     * @param $page int 页码
     * @param $page_limit int 每页条数
     * @param array $select 查询的字段
     * @return array
     */
    public function getProductionList($page,$page_limit,$select=[])
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
        //查询
        $this->db->where("status",1); //未删除
        $this->db->where("on_sale",1); //已发布
        $db = clone $this->db;
        //统计总记录数
        $total = $this->db->count_all_results($this->pro_table,FALSE);
        $this->db = $db;
        $this->db->limit($page_limit,$offset); //未删除
        //获取记录列表
        $list = $this->db->get($this->pro_table)->result_array();
        //获取总页数
        $total_pages = ceil($total/$page_limit);
        //分页数据
        return self::$helper->pageReturn($total,$total_pages,$page,$list);
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
        //已发布(在架)
        $this->db->where('p.on_sale',1);
        //未删除
        $this->db->where('p.status',1);
        $this->db->from("$this->pro_table as p");
        $this->db->join("$this->pro_label_table as lp","p.id = lp.production_id",'left');
        $proDetail = $this->db->get()->row_array();
        return $proDetail;
    }
    /**
     * 用户产品收藏
     * @param $params
     * @return bool
     */
    public function insertCollection($userInfo,$production_id)
    {
        if (!$userInfo || !$production_id){
            return false;
        }
        //初始化用户产品收藏数据
        $collPro = $this->initUserCollPro($userInfo,$production_id);
        //初始化产品标签关联数据
        //插入产品标签关联表
        $this->db->insert($this->user_collection_table,$collPro);
        //是否插入成功
        $serCollId = $this->db->insert_id();
        //插入的id不存在 插入失败
        if (!$serCollId){
            return false;
        }
        return true;
    }

    /**
     * 删除用户收藏商品
     * @param $production_id
     * @return bool
     */
    public function delUserCollProduction($openid,$production_id)
    {
        if (!$openid || !$production_id){
            return false;
        }
        //更新用户收藏商品表(软删除) status 改为2 删除
        $this->db->where([
                'openid' => $openid, //用户openid
                'production_id' => $production_id, //产品id
            ])->update($this->user_collection_table,['status'=>2]);
        //是否执行成功 执行成功 提交事务 执行失败 回滚事务
        $affected_rows = $this->db->affected_rows();
        //是否更新成功
        if (!$affected_rows){
            return false;
        }
        return true;
    }

    /**
     * 查询该用户是否已收藏该商品
     * @param $openid
     * @param $production_id
     * @return array
     */
    public function getCollectionPro($openid,$production_id)
    {
        if (!$openid || !$production_id){
            return [];
        }
        //更新用户收藏商品表(软删除) status 改为2 删除
        $row = $this->db->where([
            'openid' => $openid, //用户openid
            'production_id' => $production_id, //产品id
            'status' => 1, //产品id
        ])->get($this->user_collection_table)->row_array();
        return $row;
    }
    /**
     * 初始化用户收藏产品数据
     * @param $params
     * @return array
     */
    protected function initUserCollPro($userInfo,$production_id)
    {
        if (!$userInfo || !$production_id){
            return [];
        }
        $data = [
            'user_id' => $userInfo['id'], //小程序用户id
            'openid' => $userInfo['openid'], //用户openid
            'production_id' => $production_id, //产品id
            'status' => 1, //正常
            'created_at' => date("Y-m-d H:i:s"), //创建时间
            'updated_at' => date("Y-m-d H:i:s"), //更新时间
        ];
        return $data;
    }
}