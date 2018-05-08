<?php
/**
 * Created by PhpStorm.
 * User: lucky.li
 * Date: 2018/4/25
 * Time: 10:05
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 计划任务重置用户签到
 * Class ResetUserSign
 */
class ResetUserSign extends CI_Controller
{
    //一次处理的最大条数
    private $max_rows = 10000;
    //公共配置
    private $comConf;
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['cron/ResetUserSignModel','ComConfModel']);
        //载入公共配置
        $this->ComConfModel = new ComConfModel();
        $this->comConf = $this->ComConfModel->getConf();
        $this->resetUserSign = new ResetUserSignModel();
    }

    /**
     * 重置没有连续签到用户数据(连续签到天数及签到积分)
     */
    public function resetSign()
    {
        $current_date = date("Y-m-d",strtotime("-2 days"));
        $total_rows = $this->resetUserSign->getTotal($current_date);
        //数量所大时 分批次处理
        if ($total_rows>$this->max_rows){
            //要处理的次数
            $total_page = ceil($total_rows/$this->max_rows);
            $i=1;
            while($i<=$total_page){
                //获取满足条件的用户openid列表
                $openidList = $this->resetUserSign->getOpenidListByPage($i,$this->max_rows,$current_date,$this->comConf['sign_reset_total']);
                //重置用户数据(签到天数及积分)
                $this->resetUserSign->resetSign(array_column($openidList,'openid'));
                $i++;
            }
        }else{ //数量不大时 一次处理
            $openidList = $this->resetUserSign->getOpenidList($current_date,$this->comConf['sign_reset_total']);
            //重置用户数据(签到天数及积分)
            $this->resetUserSign->resetSign(array_column($openidList,'openid'));
        }
    }
}