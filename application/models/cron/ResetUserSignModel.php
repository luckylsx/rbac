<?php
/**
 * Created by PhpStorm.
 * User: lucky.li
 * Date: 2018/4/25
 * Time: 10:08
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 计划任务重置用户签到model
 * Class ResetUserSignModel
 */
class ResetUserSignModel extends CI_Model
{
    //用户签到状态表
    private $sign_status_table = "user_sign_status";
    //用户签到积分表
    private $sign_score_table = "user_sign_score";
    public function __construct()
    {
        parent::__construct();
        //载入日志
        $this->load->library("LogRecode");
        $this->log = new LogRecode();
    }

    /**
     * 获取上次签到到今天相差两天(及以上)且签到总天数大于0的记录数
     * @param $current_date
     */
    public function getTotal($current_date)
    {
        $totalRows = $this->db->where("sign_date<=",$current_date)->where("total_day>",0)->count_all_results($this->sign_status_table);
        return $totalRows;
    }

    /**
     * 数量过多时分页获取满足条件的用户openid列表
     * @param $page
     * @param $maxLimit
     * @param $current_date
     * @return mixed
     */
    public function getOpenidListByPage($page,$maxLimit,$current_date,$resetTotal)
    {
        $offset = ($page-1)*$maxLimit;
        $openidList = $this->db->select("openid")
            ->distinct("openid")
            ->where("(sign_date<={$current_date} and total_day>0) or total_day={$resetTotal}")
            ->limit($maxLimit,$offset)
            ->get($this->sign_status_table)
            ->result_array();
        return $openidList;
    }
    /**
     * 数量不多时一次获取满足条件的用户openid列表
     * @param $current_date
     * @return mixed
     */
    public function getOpenidList($current_date,$resetTotal)
    {
        $openidList = $this->db->select("openid")
            ->distinct("openid")
            ->where("(sign_date<={$current_date} and total_day>0) or total_day={$resetTotal}")
            ->get($this->sign_status_table)
            ->result_array();
        return $openidList;
    }

    /**
     * 重置用户签到数据
     */
    public function resetSign($openidList)
    {
        $this->db->trans_start();
        $this->db->where_in('openid',$openidList)->update($this->sign_status_table,['total_day'=>0]);
        $this->db->where_in('openid',$openidList)->update($this->sign_score_table,['total_score'=>0]);
        $this->db->trans_complete();
        //是否更新成功
        if ($this->db->trans_status() === FALSE)
        {
            $openids = "\n".implode("\n",$openidList);
            $this->log->error($openids,'ERROR');
        }
    }
}