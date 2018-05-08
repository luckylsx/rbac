<?php
/**
 * Created by PhpStorm.
 * User: lucky.li
 * Date: 2018/4/26
 * Time: 13:37
 */
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 产品管理
 */
class ProductController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(["ComConfModel",'mini/ProductModel']);
        //载入公共配置
        $this->comConf = $this->ComConfModel->getConf();
        $this->prom = new ProductModel();
        //载入日志
        $this->load->library("LogRecode");
        $this->log = new LogRecode();
    }

    /**
     * 用户收藏商品
     */
    public function userCollectPro()
    {
        $this->checkLogin();
        //接收用户选择的类型
        $production_id = $this->input->get("production_id");
        //产品id不存在
        if (!$production_id){
            return $this->error(20003); //请选择产品
        }
        //获取该产品详细记录
        $detail = $this->prom->getProductionById($production_id);
        //产品详情不存在
        if (!$detail){
            return $this->error(20004); //产品已删除或已下架
        }
        //查询该商品是否已被该用户收藏
        $coll = $this->prom->getCollectionPro($this->openid,$production_id);
        //存在 说明该用户已收藏该商品
        if ($coll){
            return $this->error(20005); //产品已收藏
        }
        //插入用户收藏表
        $collStatus = $this->prom->insertCollection($this->openid,$production_id);
        //插入成功
        if (!$collStatus){
            return $this->error(11002); //操作失败
        }
        return $this->success(); //操作成功
    }

    /**
     * 删除用户收藏商品
     */
    public function delCollPro()
    {
        $this->checkLogin();
        //接收用户选择的类型
        $production_id = $this->input->get("production_id");
        //产品id不存在
        if (!$production_id){
            return $this->error(20003); //请选择产品
        }
        //删除用户收藏商品
        $delStatus = $this->prom->delUserCollProduction($this->openid,$production_id);
        //删除失败
        if (!$delStatus){
            return $this->error(11002); //操作失败
        }
        return $this->success(); //操作成功
    }

    /**
     * 获取产品详情
     */
    public function getProDetail()
    {
        //接收用户选择的类型
        $production_id = $this->input->get("production_id");
        if (!$production_id){
            return $this->error(20003); //请选择产品
        }
        //获取产品详情
        $detail = $this->prom->getProductionById($production_id);
        //产品详情不存在
        if (!$detail){
            return $this->error(20004); //产品已被下架或删除
        }
        return $this->success($detail);
    }


}