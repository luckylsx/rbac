<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 分类逻辑
 * Class RoleModel
 */
class CategoryModel extends CI_Model
{
	private $cate_table = 'category';
	public function __construct() {
		parent::__construct();
        $this->load->library("LogRecode");
        $this->log = new LogRecode();
	}

    /**
     * 获取所有分类
     * @return mixed
     */
    public function getCateList($page,$limit=10,$cate_name='')
    {
        $offset = ($page-1)*$limit;
        $this->db->order_by("sort");
        $this->db->select("*");
        if ($cate_name){
            $this->db->like("name",$cate_name,'after');
        }
        $this->db->where("status",1);
        $db = clone $this->db;
        $this->db->limit($limit,$offset);
        $cateList = $this->db->get($this->cate_table)->result_array();
        $this->db = $db;
        $total = $this->db->count_all_results($this->cate_table);
        return ['list'=>$cateList,'total'=>$total];
    }
    /**
     * 获取所有分类列表
     * @return mixed
     */
    public function getCate($select=[])
    {
        if ($select){
            $s = implode(",",$select);
        }else{
            $s = "*";
        }
        $this->db->order_by("sort");
        $this->db->select($s);
        $this->db->where("status",1);
        $cateList = $this->db->get($this->cate_table)->result_array();
        return $cateList;
    }

    /**
     * 根据菜单id查询出相应记录
     * @param $menu_id
     * @return mixed
     */
    public function getCateById($cate_id)
    {
        $row = $this->db->get_where($this->cate_table,['id'=>$cate_id])->row_array();
        return $row;
    }

    /**
     * 插入分类数据表
     * @param $params
     * @return bool
     */
    public function insertCate($params)
    {
        //参数不存在
        if (!$params){
            return false;
        }
        //格式化数据
        $data = $this->init($params);
        $data['created_at'] = date("Y-m-d :H:i:s");
        //插入数据表
        try{
            $this->db->insert($this->cate_table,$data);
            $id = $this->db->insert_id();
            if (!$id){
                return false;
            }else{
                return true;
            }
        }catch (Exception $e){
            $this->log->error("分类插入失败",'error');
        }
        return false;
    }

    /**
     * 更新分类数据
     * @param $menu
     * @return bool
     */
    public function updateCate($params)
    {
        if (!$params || !$params['id']){
            return false;
        }
        //初始化更新数据
        $data = $this->init($params);
        //更新数据
        $this->db->where('id',$params['id'])->update($this->cate_table,$data);
        //是否更新成功
        $rows = $this->db->affected_rows();
        if (!$rows){
            return false;
        }
        return true;
    }

    /**
     * 删除分类（软删除）
     * @param $menu_id
     * @return bool
     */
    public function delCate($cate_id)
    {
        if (!$cate_id){
            return false;
        }
        //软删除， 将状态更新为2
        $this->db->where("id",$cate_id)->update($this->cate_table,['status'=>2]);
        $affected_rows = $this->db->affected_rows();
        if (!$affected_rows){
            return false;
        }
        return true;
    }

    /**
     * 初始化分类数据
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
            'status' => 1, //是否启用  1正常 2删除',
            'sort' => $params['sort'], //排序
            'updated_at' => date("Y-m-d H:i:s"), //导航父id
        ];
        return $data;
    }

}