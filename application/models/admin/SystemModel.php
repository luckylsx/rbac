<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 系統管理
 * Class UserModel
 */
class SystemModel extends CI_Model {
    private static $helper;
    private $cof_table = 'common_conf';
	public function __construct() {
		parent::__construct();
        //单例模式实例化工具辅助函数
        $this->load->helper("function_helper");
        if (!(self::$helper instanceof function_helper)){
            self::$helper = new function_helper();
        }
	}

	/**
	 * 获取公共配置
	 */
	public function getComConfig($select=[]){
	    if (!$select){
	        $s = '*';
        }else{
	        $s = implode(',',$select);
        }
		$list = $this->db->select($s)->where('status',1)->get($this->cof_table)->result_array();
		return $list;
	}

    /**
     * 插入配置表
     * @param $params
     * @return bool
     */
    public function addConf($params)
    {
        if (!$params){
            return false;
        }
        //初始化数据
        $data = $this->initConf($params);
        if (!$data){
            return false;
        }
        //插入数据表
        $this->db->insert($this->cof_table,$data);
        //获取插入的id
        $insert_id = $this->db->insert_id();
        if (!$insert_id){
            return false;
        }
        return true;
	}

    /**
     * 初始化系统数据
     * @param $conf
     * @return array|bool
     */
	protected function initConf($conf)
    {
        if (!$conf){
            return false;
        }
        $data = [
            'column' => element('column',$conf),//字段名
            'value' => element('value',$conf),  //字段值
            'desc' => element('desc',$conf),  //字段描述
            'status' => 1,  //正常
            'created_at' => date("Y-m-d H:i:s"),  //创建时间
            'updated_at' => date("Y-m-d H:i:s"),  //更新时间
        ];
        return $data;
    }

    /**
     * 根据字段名称来查询配置是否已存在
     * @param $column
     * @return mixed
     */
    public function getComConfigByColumn($column)
    {
        $row = $this->db->get_where($this->cof_table,['column'=>$column,'status'=>1])->row_array();
        return $row;
    }

    /**
     * 更新配置文件
     * @param $params
     * @return bool
     */
    public function updateConf($params)
    {
        if (!$params || !$params['id']){
            return false;
        }
        //初始化数据
        $data = [
            'column' => $params['column'], //字段名
            'value' => $params['value'], //字段值
            'desc' => $params['desc'], //字段描述
        ];
        //插入数据表
        $this->db->where('id',$params['id'])->update($this->cof_table,$data);
        //获取插入的id
        $affectes = $this->db->affected_rows();
        //更新受影响的条数
        if (!$affectes){
            return false;
        }
        return true;
    }
    /**
     * 根据id查询出配置记录
     * @param $conf_id
     * @return mixed
     */
    public function getComConfigById($conf_id)
    {
        $row = $this->db->get_where($this->cof_table,['id'=>$conf_id])->row_array();
        return $row;
    }

    /**
     * 根据id删除配置记录
     * @param $conf_id
     * @return bool
     */
    public function deleteConfById($conf_id)
    {
        if (!$conf_id){
            return false;
        }
        //软删除--将状态更新为2
        $this->db->where("id",$conf_id)->update($this->cof_table,["status"=>2]);
        $affected_rows = $this->db->affected_rows();
        if (!$affected_rows){
            return false;
        }
        return true;
    }

}