<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Rbac config
|--------------------------------------------------------------------------
*/
$config['rbac_auth_on']	             = TRUE;			      	//是否开启认证
$config['rbac_auth_type']	         = '1';			     		//认证方式1,登录认证;2,实时认证
$config['rbac_auth_key']	         = 'MyAuth';		 		//SESSION标记
$config['rbac_auth_gateway']         = '';    		//默认认证网关
$config['rbac_default_index']        = '';     //成功登录默认跳转模块
$config['rbac_auth_dirc']         = array('admin');	     	    //默认无需认证目录array("public","manage")
$config['rbac_notauth_cont']         = array('Login');
 
/* End of file rbac.php */
/* Location: ./application/config/rbac.php */
