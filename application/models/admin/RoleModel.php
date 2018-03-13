<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 角色处理逻辑
 * Class RoleModel
 */
class RoleModel extends CI_Model {
	
	public function __construct() {
		parent::__construct();
	}

	/**
	 * 获取角色列表
	 */
	public function getRoleList($page=1,$limit=PAGINATION)
    {
        $offset = ($page-1)*$limit;
        $this->db->limit($limit,$offset);
        $this->db->order_by("id");
        $this->db->select("*");
        $this->db->from("admin_role");
        $list = $this->db->get()->result_array();
        $total = $this->db->count_all('admin_role');
        return ['list'=>$list,'total'=>$total];
    }
    /**
     * 角色添加时的参数验证
     * @param $post
     * @return bool
     */
    public function checkParames($post)
    {
        //参数不存在
        if (!$post){
            return false;
        }
        //验证角色名称和角色描述
        if (!element('roleName',$post) || !element('description',$post)){
            return false;
        }
        //菜单权限是否选择
        if (!$post['menu_node']){
            return false;
        }
        return true;
    }

    /**
     * 添加角色插入数据库
     * @param $post
     * @return bool
     */
    public function insertRole($post)
    {
        if (!$post) return false;
        $pid = $this->getPidByid($post['menu_node']);
        $nodeList = array_unique(array_merge($pid,$post['menu_node']));
        //过滤掉id为0的
        $menu_list = array_filter($nodeList,function ($a){
            if ($a>0){
                return $a;
            }
        });
        $data = [
            'role_name' =>  $post['roleName'], //角色名称
            'description' => $post['description'], //角色描述
            'menu_list' => $menu_list?implode(',',$menu_list):"", //角色对应权限菜单列表
            'created_at' => date("Y-m-d H:i:s"), //创建时间
            'updated_at' => date("Y-m-d H:i:s"), //更新时间
        ];
        $this->db->insert("admin_role",$data);
        //返回插入数据id
        $insert_id  = $this->db->insert_id();
        if ($insert_id){
            return $insert_id;
        }else{
            return false;
        }
    }

    /**
     * 根据id查询出对应角色记录
     * @param $id
     * @return bool
     */
    public function getRoleById($id)
    {
        if (!$id) return false;
        $row = $this->db->get_where("admin_role",['id'=>$id])->row_array();
        return $row;
    }

    /**
     * 更新用户角色
     * @param $post
     * @return bool
     */
    public function replaceRole($post)
    {
        if (!$post || !isset($post['id']) || empty($post['id'])){
            return false;
        }
        $pid = $this->getPidByid($post['menu_node']);
        $nodeList = array_unique(array_merge($pid,$post['menu_node']));
        //过滤掉id为0的
        $menu_list = array_filter($nodeList,function ($a){
            if ($a>0){
                return $a;
            }
        });
        $data = [
            'role_name' => $post['roleName'], //角色名称
            'description' => $post['description'], //角色描述
            'menu_list' => $menu_list?implode(',',$menu_list):'', //菜单权限列表
            'updated_at' => date("Y-m-d H:i:s")
        ];
        $updateStatus = $this->db->where('id',$post['id'])->update("admin_role",$data);
        if (!$updateStatus){
            return false;
        }
        return true;
    }

    /**
     * 删除角色
     * @param $id
     * @return bool
     */
    public function delRole($id)
    {
        if (!$id){
            return false;
        }
        $status = $this->db->where("id",$id)->delete("admin_role");
        if (!$status){
            return false;
        }
        return true;
    }

    /**
     * 获取所有角色列表
     * @return mixed
     */
    public function getAllRole()
    {
        $list = $this->db->select("id,role_name")->get("admin_role")->result_array();
        return $list;
    }

    /**
     * 根据菜单获取父菜单列表
     * @param array $idlist
     * @return array|bool
     */
    public function getPidByid($idlist = [])
    {
        if (!$idlist){
            return false;
        }
        $plist = $this->db->where_in("id",$idlist)->get("menu")->result_array();
        $pidList = array_column($plist,'p_id');
        return $pidList;
    }

}