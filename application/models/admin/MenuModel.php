<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 菜单处理逻辑
 * Class RoleModel
 */
class MenuModel extends CI_Model
{
	private $menu_table = 'menu';
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

    /**
     * 获取父级菜单
     */
    public function getPmenu()
    {
        $menu_list = $this->db->where(['p_id'=>0,'status'=>1])->get($this->menu_table)->result_array();
        return $menu_list;
    }

    /**
     * 根据菜单id查询出相应记录
     * @param $menu_id
     * @return mixed
     */
    public function getMenuById($menu_id)
    {
        $row = $this->db->get_where($this->menu_table,['id'=>$menu_id])->row_array();
        return $row;
    }

    /**
     * 插入菜单数据表
     * @param $params
     * @return bool
     */
    public function insertMenu($params)
    {
        //参数不存在
        if (!$params){
            return false;
        }
        //格式化数据
        $data = $this->initMenu($params);
        $data['created_at'] = date("Y-m-d :H:i:s");
        //插入数据表
        $this->db->insert($this->menu_table,$data);
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
    public function updateMenu($menu)
    {
        if (!$menu || !$menu['id']){
            return false;
        }
        //初始化更新数据
        $data = $this->initMenu($menu);
        //更新数据
        $this->db->where('id',$menu['id'])->update($this->menu_table,$data);
        //是否更新成功
        $rows = $this->db->affected_rows();
        if (!$rows){
            return false;
        }
        return true;
    }

    /**
     * 删除菜单
     * @param $menu_id
     * @return bool
     */
    public function delMenu($menu_id)
    {
        if (!$menu_id){
            return false;
        }
        //软删除， 将状态更新为2
        $this->db->where("id",$menu_id)->update($this->menu_table,['status'=>2]);
        $affected_rows = $this->db->affected_rows();
        if (!$affected_rows){
            return false;
        }
        return true;
    }

    /**
     * 根据父级菜单获取子菜单
     * @param $menu_id
     * @return bool
     */
    public function getSubMenuByPid($menu_id)
    {
        if (!$menu_id){
            return false;
        }
        //获取子菜单
        $rows = $this->db->get_where($this->menu_table,['p_id'=>$menu_id])->result_array();
        return $rows;
    }
    /**
     * 初始化菜单数据
     * @param $params
     * @return array|bool
     */
    protected function initMenu($params)
    {
        if (!$params){
            return false;
        }
        $data = [
            'title' => $params['title'], //菜单名
            'id_name' => $params['id_name'], //菜单对应id值
            'menu_icon' => $params['menu_icon'], //图标
            'menu_url' => $params['menu_url'], //菜单url
            'p_id' => $params['p_id'], //导航父id
            'sort' => $params['sort'], //导航父id
            'updated_at' => date("Y-m-d H:i:s"), //导航父id
        ];
        return $data;
    }

}