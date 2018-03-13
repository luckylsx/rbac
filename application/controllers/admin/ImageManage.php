<?php
/**
 * Created by PhpStorm.
 * User: sf.zhu
 * Date: 2016/3/25
 * Time: 18:18
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 后台用户管理
 * Class UserManage
 */
class ImageManage extends CI_Controller {
    public function __construct(){
        parent::__construct();
    }

    /**
     * 咨询管理首页
     */
    public function index()
    {
        $this->load->view("admin/image/picture_list");
    }

}