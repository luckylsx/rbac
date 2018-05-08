<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 签到
 * Class SignModel
 */
class SignModel extends CI_Model
{
    //用户签到状态表
    protected $sign_status_table='user_sign_status';
    //用户签到积分表
    protected $sign_score_table='user_sign_score';
    //签到日志记录表
    protected $sign_log_table='user_sign_log';
    public function __construct()
    {
        parent::__construct();
        $this->load->library("LogRecode");
        $this->log = new LogRecode();
    }

    /**
     * 根据用户openid查询用户签到记录
     * @param $openid
     * @param array $select
     * @return array
     */
    public function getSignStatusByOpenid($openid,$select=[])
    {
        if (!$openid){
            return [];
        }
        if ($select){
            $s = implode(',',$select);
        }else{
            $s = "*";
        }
        $row = $this->db->select($s)->get_where($this->sign_status_table,['openid'=>$openid])->row_array();
        return $row;
    }
    /**
     * 根据用户openid查询用户签到积分记录
     * @param $openid
     * @param array $select
     * @return array
     */
    public function getSignScoreByOpenid($openid,$select=[])
    {
        if (!$openid){
            return [];
        }
        if ($select){
            $s = implode(',',$select);
        }else{
            $s = "*";
        }
        $row = $this->db->select($s)->get_where($this->sign_score_table,['openid'=>$openid])->row_array();
        return $row;
    }

    /**
     * 用户连续签到
     * @param $userInfo array 签到用户信息
     * @param $score int 签到得分
     * @return bool
     */
    public function updateUserSign($userInfo,$score)
    {
        //开启事务
        $this->db->trans_start();
        $lastSign = $this->getSignStatusByOpenid($userInfo['openid']);
        //更新签到状态表
        $signStatus = [
            'last_sign_time' => $lastSign['sign_time'], //上次签到时间
            'sign_time' => time(), //本次签到时间
            'last_sign_date' => $lastSign['sign_date'], //上次签到日期
            'sign_date' => date("Y-m-d"), //本次签到日期
            'total_day' => $lastSign['total_day']+1, //签到总天数
            'updated_at' => date("Y-m-d H:i:s"),
        ];
        $this->db->where("openid",$userInfo['openid'])->update($this->sign_status_table,$signStatus);
        $upStatus = $this->db->affected_rows();
        //更新失败回滚事务
        if (!$upStatus){
            $this->db->trans_rollback();
            return false;
        }
        //更新签到积分表
        $scoreRow = $this->getSignScoreByOpenid($userInfo['openid']);
        $scoreData = [
            'total_score' => $scoreRow['total_score']+$score,
            'updated_at' => date("Y-m-d H:i:s")
        ];
        $this->db->where("openid",$userInfo['openid'])->update($this->sign_score_table,$scoreData);
        $upScore = $this->db->affected_rows();
        //更新失败 事务回滚
        if (!$upScore){
            $this->db->trans_rollback();
            return false;
        }
        //更新成功 提交事务
        $this->db->trans_commit();
        return true;
    }

    /**
     * 用户签到中断后 首次签到
     */
    public function updateUserSignForCenterFirst($userInfo,$score)
    {
        //开启事务
        $this->db->trans_start();
        //初始化签到状态数据
        $signStatus = [
            "last_sign_time" => time(), //上次签到时间
            "sign_time" => time(), //本次签到时间
            "sign_date" => date("Y-m-d"), //本次签到日期
            "last_sign_date" => date("Y-m-d"), //上次签到日期
            "total_day" => 1, //签到总天数
            'created_at' => date("Y-m-d H:i:s"), //创建时间
            "updated_at" => date("Y-m-d H:i:s")  //更新时间
        ];
        //更新签到状态表
        $this->db->where("openid",$userInfo['openid'])->update($this->sign_status_table,$signStatus);
        //获取更新影响记录数
        $upStatus = $this->db->affected_rows();
        //更新失败回滚事务
        if (!$upStatus){
            $this->db->trans_rollback();
            return false;
        }
        //更新签到积分表
        $scoreData = [
            'total_score' => $score,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ];
        $this->db->where("openid",$userInfo['openid'])->update($this->sign_score_table,$scoreData);
        //获取更新影响记录数
        $upScore = $this->db->affected_rows();
        //更新失败 事务回滚
        if (!$upScore){
            $this->db->trans_rollback();
            return false;
        }
        //更新成功 提交事务
        $this->db->trans_commit();
        return true;
    }

    /**
     * 用户首次签到插入数据库
     */
    public function insertUserSignFirst($userInfo,$score)
    {
        //初始化用户签到状态数据
        $signStatus = $this->initSignStatus($userInfo);
        //初始化用户签到积分数据
        $signScore = $this->initSignScore($userInfo,$score);
        //格式化数据是否存在
        if (!$signStatus || !$signScore){
            return false;
        }
        //开启事务
        $this->db->trans_start();
        $this->db->insert($this->sign_status_table,$signStatus);
        //是否插入成功
        $signStatusId = $this->db->insert_id();
        if (!$signStatusId){ //不存在 插入失败
            $this->db->trans_rollback();
            return false;
        }
        $signScoreId = $this->db->insert($this->sign_score_table,$signScore);
        if (!$signScoreId){ //不存在 插入失败
            $this->db->trans_rollback();
            return false;
        }
        //插入成功
        $this->db->trans_commit();
        return true;
    }

    /**
     * 初始化用户签到状态数据
     * @param $userInfo
     * @return array
     */
    protected function initSignStatus($userInfo)
    {
        if (!$userInfo){
            return [];
        }
        //根据用户openid查询出用户上次签到记录
        $data = [
            'openid' => $userInfo['openid'], //用户openid
            'nickname' => $userInfo['nickname'], //用户昵称
            'gender' => $userInfo['gender'], //用户性别 1男 2女
            'last_sign_time' => time(), //上次的签到时间(首次签到时当次的签到日期)
            'sign_time' => time(), //签到时间
            'sign_date' => date("Y-m-d"), //签到日期
            'last_sign_date' => date("Y-m-d"), //上次签到日期（首次签到时当次的签到日期）
            'total_day' => 1, //签到总天数
            'created_at' => date("Y-m-d H:i:s"), //签到创建时间
            'updated_at' => date("Y-m-d H:i:s"), //更新时间
        ];
        return $data;
    }
    /**
     * 初始化用户签到积分数据
     * @param $userInfo
     * @return array
     */
    protected function initSignScore($userInfo,$score)
    {
        if (!$userInfo){
            return [];
        }
        //根据用户openid查询出用户上次签到记录
        $data = [
            'openid' => $userInfo['openid'], //用户openid
            'nickname' => $userInfo['nickname'], //用户昵称
            'gender' => $userInfo['gender'], //用户性别 1男 2女
            'total_score' => $score, //用户签到积分
            'created_at' => date("Y-m-d H:i:s"), //签到创建时间
            'updated_at' => date("Y-m-d H:i:s"), //更新时间
        ];
        return $data;
    }

    /**
     * 记录签到日志表
     * @param $userInfo
     * @param $score
     */
    public function log($userInfo,$score)
    {
        $data = [
            'openid' => $userInfo['openid'], //用户openid
            'sign_score' => $score, //用户签到得分
            'created_at' => date("Y-m-d H:i:s"), //创建时间
        ];
        $this->db->insert($this->sign_log_table,$data);
    }
}