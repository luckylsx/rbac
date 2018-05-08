<?php
/**
 * Created by PhpStorm.
 * User: lucky.li
 * Date: 2018/4/23
 * Time: 13:37
 */
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 签到
 */
class SignController extends MY_Controller
{
    //公共配置
    private $comConf;
    public function __construct()
    {
        parent::__construct();
        $this->load->model(["ComConfModel",'mini/SignModel']);
        //载入公共配置
        $this->comConf = $this->ComConfModel->getConf();
        $this->signModel = new SignModel();
        //载入日志
        $this->load->library("LogRecode");
        $this->log = new LogRecode();
    }

    /**
     * 用户签到
     */
    public function userSign()
    {
        //检查用户是否登录 获取用户基本信息
        $this->checkLogin();
        //获取用户签到记录
        $signRow = $this->signModel->getSignStatusByOpenid($this->openid);
        //签到记录存在 之前签到过
        if ($signRow){
            //今天签到日期
            $today_sign_time = date("Y-m-d");
            //上次签到日期
            $last_sign_date = $signRow['sign_date'];
            //分别处理签到的不同情况(当天已签到 连续签到 签到中断后首次签到)
            $this->diffDeal($today_sign_time,$last_sign_date,$this->user);
        }else{ //签到记录不存在 首次签到 插入数据
            //记录签到日志
            $this->signModel->log($this->user,$this->comConf['sign_score']);
            //首次签到 插入签到数据
            $signStatus = $this->signModel->insertUserSignFirst($this->user,$this->comConf['sign_score']);
            if (!$signStatus){
                return $this->error(410002); //签到失败 请重试
            }
            return $this->success(); //签到成功
        }
    }

    /**
     * 签到对不同的情况做处理
     * @param $today_sign_time string 上次签到时间
     * @param $last_sign_time string 本次签到时间
     * @param $userInfo array 用户信息
     */
    protected function diffDeal($today_sign_time,$last_sign_time,$userInfo)
    {
        //两次日期相差天数
        $diff_day = date_diff(date_create($today_sign_time),date_create($last_sign_time));
        //两次签到小于1天 则说明今天已签到
        if ($diff_day->d <1){
            return $this->error(410001); //今天已签到
        }else if($diff_day->d == 1){ //等于1天 今天未签到 可以签到 连续签到
            //记录签到日志
            $this->signModel->log($userInfo,$this->comConf['sign_score']);
            //更新签到状态及积分表
            $signStatus = $this->signModel->updateUserSign($userInfo,$this->comConf['sign_score']);
            //更新失败 -- 签到失败
            if (!$signStatus){
                return $this->error(410002); //签到失败 请重试
            }
            return $this->success(); //签到成功
        }else{ //本次签到与上次签到时间相差两天 说明 中间 签到中断（所有 积分 签到状态 重置）
            //记录签到日志
            $this->signModel->log($userInfo,$this->comConf['sign_score']);
            //所有 积分 签到状态 重置  作为首次签到
            $signStatus = $this->signModel->updateUserSignForCenterFirst($userInfo,$this->comConf['sign_score']);
            //更新失败 -- 签到失败
            if (!$signStatus){
                $this->error(410002); //签到失败 请重试
            }
            return $this->success(); //签到成功
        }
    }
}