<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * redis 获取缓存配置
 * Class RoleModel
 */
class ComConfModel extends CI_Model
{
    //redis key
	private $redis_key = 'common_config';
	public function __construct() {
		parent::__construct();
        $this->load->driver('cache', array('adapter' => 'redis', 'backup' => 'file'));
	}

    /**
     * 获取所有菜单权限列表，并处理成树形结构
     * @return mixed
     */
    public function getConf()
    {
        //获取缓存
        $conf = $this->cache->get($this->redis_key);
        //缓存是否存在
        if ($conf){
            return json_decode($conf,true);
        }else{
            $list = $this->systemModel->getComConfig(['column','value']);
            //加入配置文件
            $data = array_column($list,'value','column');
            $conf = json_encode(array_column($list,'value','column'));
            //存入redis缓存
            $this->cache->save("common_config",$conf,$data['redis_time']);
            return $data;
        }
    }

}