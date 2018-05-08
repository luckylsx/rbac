<?php
/**
 * Created by PhpStorm.
 * User: lucky.li
 * Date: 2018/4/23
 * Time: 13:37
 */
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 为你推荐
 */
class Recommend extends MY_Controller
{
    //前台显示的每页最大数量
    private $show_max;
    public function __construct()
    {
        parent::__construct();
        $this->load->model(["ComConfModel",'mini/RecomModel','mini/ProductModel']);
        //载入公共配置
        $this->comConf = $this->ComConfModel->getConf();
        $this->recomModel = new RecomModel();
        $this->prom = new ProductModel();
        //载入日志
        $this->load->library("LogRecode");
        $this->log = new LogRecode();
        $this->show_max = $this->comConf['page_limit'];
    }

    /**
     * 用户标签
     */
    public function userLabel()
    {
        $this->checkLogin();
        $params = json_decode($this->input->raw_input_stream,true);
            //接收用户选择的类型
        $types = $params["types"]??'';
        //接收用户选择的风格
        $styles = $params["styles"]??'';
        //家居类型是否存在
        if (empty($types)){
            return $this->error(20001); //家居类型未选择
        }
        //家居风格是否存在
        if (empty($styles)){
            return $this->error(20002); //未选择家居风格
        }
        //用户选择的兴趣标签插入数据表
        $status = $this->recomModel->insertUserLabel($this->user,$types,$styles);
        if (!$status){
            return $this->error(20002); //操作失败
        }
        return $this->success();
    }

    /**
     * 根据用户之前选择的标签 展示为你推荐产品
     */
    public function recommendPro()
    {
        //验证用户是否登录
        $this->checkLogin();
        //接收页码
        $page = $this->input->get("page");
        $page = intval($page) ?? 1;
        //获取用户选择的标签
        $userLabel = $this->recomModel->getUserLabel($this->openid);
        //用户标签不存在 推荐最新商品
        if (!$userLabel){
            $list = $this->prom->getProductionList($page,$this->show_max);
        }else{
            $list = $this->prom->getProductionListByLabel($userLabel,$page,$this->show_max);
        }
        return $this->success($list);
    }
}