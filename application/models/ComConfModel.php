<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * redis 获取缓存配置
 * Class RoleModel
 */
class ComConfModel extends CI_Model
{
    //redis key
	private $conf_cache_key = 'common_config';
	//缓存时间
	private $timeLimit = 2*60*60;
	public function __construct() {
		parent::__construct();
		$this->load->library("LogRecode");
		$this->log = new LogRecode();
		$this->load->model("admin/SystemModel");
        $this->systemModel = new SystemModel();
		//使用redis缓存驱动 备用文件缓存
        $this->load->driver('cache', array('adapter' => 'redis','backup' => 'file'));
	}

    /**
     * 获取所有菜单权限列表，并处理成树形结构
     * @return mixed
     */
    public function getConf()
    {
        //获取缓存
        $conf = $this->cache->get($this->conf_cache_key);
        //缓存是否存在
        if ($conf){
            return json_decode($conf,true);
        }else{
            $list = $this->systemModel->getComConfig(['column','value']);
            //加入配置文件
            $data = array_column($list,'value','column');
            $conf = json_encode(array_column($list,'value','column'));
            //存入redis缓存
            $this->add($conf);
            return $data;
        }
    }

    /**
     * 将数据添加到缓存
     * @param $data string 缓存的json字符串
     */
    public function add($data='')
    {
        //存入缓存
        $this->cache->save($this->conf_cache_key,$data,$this->timeLimit);
    }

}