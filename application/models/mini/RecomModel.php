<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 为你推荐相关逻辑
 * Class MiniUserModel
 */
class RecomModel extends CI_Model
{
    //兴趣标签表
    protected $label_table='interest_label';
    //用户标签关联表
    protected $user_label_table='user_label';
    public function __construct()
    {
        parent::__construct();
        $this->load->library("LogRecode");
        $this->log = new LogRecode();
    }

    /**
     * 根据标签id查询用户信息数据
     * @param $ids array 标签id列表
     * @param $type int 标签type类型
     * * @param array $select 查询的字段
     * @return mixed
     */
    public function getLabelByIds($ids=[],$type=1,$select=[])
    {
        if (!$ids){
            return [];
        }
        if (!$select){
            $s = "*";
        }else{
            $s =implode(",",$select);
        }
        $list = $this->db->select($s)->where_in("id",$ids)->where('type',$type)->get($this->label_table)->result_array();
        return $list;
    }

    /**
     * 插入用户标签关联表
     * @param $data
     * @return bool
     */
    public function insertUserLabel($userInfo,$types,$styles)
    {
        if (!$userInfo || !$types || !$styles){
            return false;
        }
        $data = $this->init($userInfo,$types,$styles);
        $data['created_at'] = date("Y-m-d H:i:s");
        try{
            //插入用户兴趣标签表
            $this->db->insert($this->user_label_table,$data);
            $insertId = $this->db->insert_id();
            if ($insertId){
                return true;
            }else{
                return false;
            }
        }catch (Exception $e){
            $this->log->error("{$userInfo['openid']}选择的标签插入标签表失败",'ERROR');
        }
        return false;
    }

    /**
     * 初始化用户标签数据
     * @param $userInfo array 用户数据
     * @param $types string 选择的类型
     * @param $styles string 选择的风格
     * @return array
     */
    public function init($userInfo,$types,$styles)
    {
        //根据类型id查询出对应的标签列表
        $typeList = $this->getLabelByIds(json_decode($types,true),1,['id','name']);
        if (!$typeList){
            return [];
        }
        //根据风格id查询出对应的标签列表
        $styleList = $this->getLabelByIds(json_decode($styles,true),2,['id','name']);
        if (!$styleList){
            return [];
        }
        $data = [
            'openid' => $userInfo['openid'], //用户openid
            'type_list' => $types, //选择的类型id列表
            'sum_type' => array_sum(json_decode($types,true)), //选择的类型id列表
            'type_value' => json_encode(array_column($typeList,'name'),JSON_UNESCAPED_UNICODE), //选择的类型id列表
            'typeId_value' => json_encode(array_column($typeList,'name','id'),JSON_UNESCAPED_UNICODE), //选择的类型id列表
            'style_list' => $styles, //选择风格列表
            'sum_style' => array_sum(json_decode($styles,true)), //选择风格总和
            'style_value' => json_encode(array_column($styleList,'name'),JSON_UNESCAPED_UNICODE), //选择的选择风格列表
            'styleId_value' => json_encode(array_column($styleList,'name','id'),JSON_UNESCAPED_UNICODE), //选择的选择风格列表
            'updated_at' => date("Y-m-d H:i:s"), //选择的选择风格列表
        ];
        return $data;
    }

    /**
     * 根据用户的openid 查询出 用户选择关注的标签
     * @param $openid
     * @param array $select
     * @return mixed
     */
    public function getUserLabel($openid,$select=[])
    {
        if ($select){
            $s = implode(',',$select);
        }else{
            $s = "*";
        }
        //查询用户关注标签信息
        $userLabel = $this->db->select($s)->where("openid",$openid)
            ->get($this->user_label_table)
            ->row_array();
        return $userLabel;
    }
}