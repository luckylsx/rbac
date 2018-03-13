<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 角色处理逻辑
 * Class RoleModel
 */
class MenuModel extends CI_Model {
	
	public function __construct() {
		parent::__construct();
	}

    /**
     * 获取所有菜单权限列表，并处理成树形结构
     * @return mixed
     */
    public function getMenuList()
    {
        $this->db->order_by("sort");
        $this->db->select("*");
        $menuList = $this->db->get_where("menu",['status'=>1,'p_id'=>0])->result_array();
        $this->db->order_by("sort");
        $this->db->where_in("p_id",array_column($menuList,'id'));
        $subMenu = $this->db->get_where("menu",['status'=>1])->result_array();
        foreach ($menuList as &$menu){
            foreach ($subMenu as $sub){
                if ($menu['id']==$sub['p_id']){
                    $menu['subMenu'][] = $sub;
                }
            }
        }
        return $menuList;
    }

}