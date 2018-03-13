<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rbac{

    function auto_verify()
    {
        $ci_obj = &get_instance();
        //$login = $ci_obj->session->has_userdata('admin_user_info');
        $ci_obj->load->helper('url');
		$directory = substr($ci_obj->router->fetch_directory(),0,-1);  //目录
		$controller = $ci_obj->router->fetch_class();  //控制器
		$function = $ci_obj->router->fetch_method();	//方法
        //没有登录则跳转到登录页面
        if( $directory != "" ){    //当非主目录
            if($ci_obj->config->item('rbac_auth_on')){	//开启认证
                if(in_array($directory,$ci_obj->config->item('rbac_auth_dirc'))){ //需要验证的目录
                    if (!in_array($controller,$ci_obj->config->item('rbac_notauth_cont'))){		//需要验证的方法
                        //验证是否登录
                        if(!isset($_SESSION[$ci_obj->config->item('rbac_auth_key')])){
                            redirect('/admin/Login');
                        }
                    }
                }
            }
        }
        //请求的方法不在无需获取菜单列表中
        if (!in_array($controller,$ci_obj->config->item('rbac_notmenu_func'))){
            if (isset($_SESSION['MyAuth']) && $_SESSION['MyAuth'] != null){
                if (!isset($_SESSION['MyAuth']['user_menu']) || !$_SESSION['MyAuth']['user_menu']){
                    $menu_list = $this->get_menu();
                    $_SESSION['MyAuth']['user_menu'] = $menu_list;
                }
            }
        }
    }
    /*
	 * 获取左侧菜单
	*/
    public function get_menu(){
        $ci_obj = &get_instance();

        $ci_obj->load->database();
        //获取登录用户信息
        $user = $_SESSION[$ci_obj->config->item('rbac_auth_key')]['user'];
        //获取该用户对应角色的菜单列表
        //如果是超级用户，则获取所有菜单列表
        $menu_list = '';
        if ($user['role_id']==$ci_obj->config->item('rbac_super_role')){
            $menu = $ci_obj->db->select("id")->get("menu")->result_array();
            $menu_list = implode(',',array_column($menu,'id'));
        }else{
            $menu = $ci_obj->db->select("menu_list")->get_where("admin_role",['id'=>$user['role_id']])->row_array();
            $menu_list = $menu['menu_list'];
        }
        //查询对应菜单
        $query = $ci_obj->db->query("SELECT * FROM menu where status = 1 and p_id = 0 AND id IN(".$menu_list.") order by sort asc ");
        $list = $query->result_array();
        //插入父菜单对应的子菜单
        foreach ($list as $k=>$v){
            $query = $ci_obj->db->query("SELECT * FROM menu where status = 1 and p_id = ".$v['id']." and id in(".$menu_list.") order by sort asc ");
            $sub_menu = $query->result_array();
            $list[$k]['sub_menu'] = $sub_menu;
        }
        return $list;
    }
}