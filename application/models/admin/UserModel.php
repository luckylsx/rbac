<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 用户处理逻辑
 * Class UserModel
 */
class UserModel extends CI_Model {
    private static $helper;
	public function __construct() {
		parent::__construct();
        //单例模式实例化工具辅助函数
        $this->load->helper("function_helper");
        if (!(self::$helper instanceof function_helper)){
            self::$helper = new function_helper();
        }
	}

	/**
	 * 判断用户是否存在
	 */
	public function userExist($username){
		$this->db->where('username' , $username);
		$row = $this->db->get('admin_user')->row_array();
		if (empty($row)){
			return false;
		}else{
			return true;
		}
	}

    /**
     * 根据登录账号获取用户信息
     * @param $username
     */
    public function getUserInfo($username)
    {
        $info = $this->db->get_where("admin_user",['username'=>$username])->row_array();
        return $info;
    }

	/**
	 * 插入User表
	 * @param array $data
	 * @return boolean
	 */
	public function addUser($data)
    {
		if(empty($data)){
			return FALSE;
		}
		//密码加密
        $data['password'] = self::$helper->pass_hash($data['password']);
		//添加时间
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		//验证该账户是否存在 存在则返回
		if ($this->userExist($data['username'])){
		    return false;
        }
        //不存在插入数据库
        $this->db->insert('admin_user',$data);
        $user_id = $this->db->insert_id();
        return $user_id;
	}

    /**
     * 验证用户名和密码
     * @param $data
     * @return bool
     */
	public function checkUserInfo($data)
    {
        if (!$data){
            return false;
        }
        //用户名不存在或者为空
        if (!isset($data['username']) || empty($data['username'])){
            return false;
        }
        //面膜不存在或者为空
        if (!isset($data['password']) || empty($data['password'])){
            return false;
        }
        return true;
    }

    /**
     * 获取后台用户列表
     * @return mixed
     */
    public function getAdminList($username='',$page = 1,$limit=PAGINATION)
    {
        $offset = ($page-1) * $limit;
        $this->db->limit($limit,$offset);
        $this->db->order_by('admin_user.id', 'DESC');
        $this->db->select('admin_user.*,admin_role.role_name,admin_role.description');
        $this->db->from('admin_user');
        $this->db->join('admin_role', 'admin_user.role_id = admin_role.id','left');
        if ($username!=''){
            $this->db->where("admin_user.username",$username);
        }
        $list = $this->db->get()->result_array();
        if ($username){
            $total = $this->db->where('username',$username)->count_all_results('admin_user');
        }else{
            $total = $this->db->count_all_results('admin_user');
        }
        return ['list'=>$list,'total'=>$total];
    }

    /**
     * 根据id查询后台用户详情用户
     * @param $user_id
     * @return mixed
     */
    public function getUserInfoById($user_id)
    {
        $user = $this->db->get_where("admin_user",['id'=>$user_id])->row_array();
        return $user;
    }

    /**
     * 更新后台用户
     * @param $post
     * @param string $password
     * @return bool
     */
    public function replaceAdmin($post,$password='')
    {
        if (!$post){
            return false;
        }
        $data = [
            'username' => element('username',$post),
            'role_id' => element('role_id',$post),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        if ($password){
            $data['password'] = $password;
        }
        //更新数据
        $status = $this->db->where("id",$post['user_id'])->update("admin_user",$data);
        if ($status){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 删除后台用户
     * @param $id
     * @return bool
     */
    public function delAdminUser($id)
    {
        if (!$id){
            return false;
        }
        //删除记录
        $status = $this->db->where('id',$id)->delete("admin_user");
        if (!$status){
            return false;
        }
        return true;
    }

}