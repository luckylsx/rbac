<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 分类逻辑
 * Class RoleModel
 */
class LabelModel extends CI_Model
{
	private $table_name = 'interest_label';
	public function __construct() {
		parent::__construct();
		$this->load->library("LogRecode");
		$this->log = new LogRecode();
	}

    /**
     * 根据type类型获取相应标签
     */
    public function getTypeList($type=1)
    {
        $cate_list = $this->db->where(['type'=>$type,'status'=>1])->order_by("sort",'asc')->get($this->table_name)->result_array();
        return $cate_list;
    }

    /**
     * 根据标签id查询出相应记录
     * @param $menu_id
     * @return mixed
     */
    public function getLabelById($id)
    {
        if (!$id){
            return false;
        }
        $row = $this->db->get_where($this->table_name,[
            'id' => $id,
            'status' => 1
            ])->row_array();
        return $row;
    }
    /**
     * 根据标签id列表批量查询出相应记录
     * @param $menu_id
     * @return mixed
     */
    public function getLabelByIdList($ids,$select=[])
    {
        if (!$ids || !is_array($ids)){
            return false;
        }
        if ($select){
            $s = implode(',',$select);
        }else{
            $s = "*";
        }
        $row = $this->db->select($s)->where_in('id',$ids)->get_where($this->table_name,[
            'status' => 1
        ])->result_array();
        return $row;
    }

    /**
     * 插入菜单数据表
     * @param $params
     * @return bool
     */
    public function insertLabel($params)
    {
        //参数不存在
        if (!$params){
            return false;
        }
        //格式化数据
        $data = $this->init($params);
        $data['created_at'] = date("Y-m-d :H:i:s");
        //插入数据表
        $this->db->insert($this->table_name,$data);
        $id = $this->db->insert_id();
        if (!$id){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 更新菜单数据
     * @param $menu
     * @return bool
     */
    public function updateLabel($data)
    {
        if (!$data || !$data['id']){
            return false;
        }
        //初始化更新数据
        $label = $this->init($data);
        //更新数据
        try{
            $this->db->where('id',$data['id'])->update($this->table_name,$label);
            return true;
        }catch (Exception $e){
            $this->log->error("{$data['id']}更新失败",'error');
        }
        return false;
    }

    /**
     * 删除标签
     * @param $label_id
     * @return bool
     */
    public function delLabel($label_id)
    {
        if (!$label_id){
            return false;
        }
        //软删除， 将状态更新为2
        try{
            $this->db->where("id",$label_id)->update($this->table_name,['status'=>2]);
            $affected_rows = $this->db->affected_rows();
            if (!$affected_rows){
                return false;
            }
            return true;
        }catch (Exception $e){
            $this->log->error("{$label_id}软删除失败",'error');
        }
        return false;
    }

    /**
     * 根据父级分类获取子分类
     * @param $menu_id
     * @return bool
     */
    public function getSubCateByPid($p_id)
    {
        if (!$p_id){
            return false;
        }
        //获取子分类
        $rows = $this->db->get_where($this->table_name,['p_id'=>$p_id])->result_array();
        return $rows;
    }
    /**
     * 初始化菜单数据
     * @param $params
     * @return array|bool
     */
    protected function init($params)
    {
        if (!$params){
            return false;
        }
        $data = [
            'name' => $params['name'], //分类名
            'cover' => $params['cover'], //封面图
            'type' => $params['type'], //父级分类id
            'status' => 1, //是否启用  1正常 2删除',
            'sort' => $params['sort'], //排序
            'updated_at' => date("Y-m-d H:i:s"), //导航父id
        ];
        return $data;
    }

}