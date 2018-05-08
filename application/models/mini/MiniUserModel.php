<?php
/**
 * Created by PhpStorm.
 * User: lucky.li
 * Date: 2018/4/20
 * Time: 16:32
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 小程序登录模型
 * Class MiniUserModel
 */
class MiniUserModel extends CI_Model
{
    protected $mini_user='miniapp_user';
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据openid查询用户信息数据
     * @param array $select 查询的字段
     * @param $openid string 用户openid
     * @return mixed
     */
    public function getUserByOpenid($openid,$select=[])
    {
        if (!$select){
            $s = "*";
        }else{
            $s =implode(",",$select);
        }
        $row = $this->db->select($s)->get_where($this->mini_user,['openid'=>$openid])->row_array();
        return $row;
    }

    /**
     * 插入用户数据表
     * @param $data
     * @return array
     */
    public function insertMiniUser($data)
    {
        try{
            $this->db->insert($this->mini_user,$data);
            return ['errcode'=>0];
        }catch(Exception $e){
            $messge = $e->getMessage();
        }
        return ['errcode'=>1,'msg' => $messge];
    }

    /**
     * 更新用户数据
     * @param $openid
     * @param $data
     * @return bool
     */
    public function updateMiniUser($openid,$data)
    {
        if (!$openid || !$data){
            return false;
        }
        //根据openid更新用户数据
        $this->db->where("openid",$openid)->update($this->mini_user,$data);
    }

    /**
     * 格式化用户信息数据
     * @param $userinfo
     * @param $nickname
     * @param $session_key
     * @return bool
     */
    public function initUser($userinfo,$nickname,$session_key)
    {
        if (!$userinfo || !$nickname || !$session_key){
            return false;
        }
        $arr['nickName'] = $nickname;
        $arr['gender'] = $userinfo['gender'];
        $arr['language'] = $userinfo['language'];
        $arr['city'] = $userinfo['city'];
        $arr['province'] = $userinfo['province'];
        $arr['country'] = $userinfo['country'];
        $arr['avatarUrl'] = $userinfo['avatarUrl'];
        $arr['session_key'] = $session_key;
        $arr['created_at'] = date("Y-m-d H:i:s");
        $arr['updated_at'] = date("Y-m-d H:i:s");
        return $arr;
    }
}