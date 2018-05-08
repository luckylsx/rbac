<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class LogRecode{

    //日志记录公共表
    private $recode_log = 'recode_log';
    public function __construct(){
        $this->CI = & get_instance();
    }

    /**
     * 公共日志记录表
     * @param string $url
     * @param string $position
     * @param string $post_url
     * @param array $post
     * @param array $back
     */
    public function recode($url='',$position='',$post_url='',$post='',$back='')
    {
        $data = [
            'url' => $url, //记录日志的方法域名
            'position' => $position, //位置
            'post_url' => $post_url, //请求的域名
            'post' => $post, //提交的参数
            'back' => $back, //返回的参数
            'created_at' => date("Y-m-d H:i:s") //日期
        ];
        $this->CI->db->insert($this->recode_log,$data);
    }

    /**
     * 记录相关日志表
     * @param string $table 表名
     * @param array $data 记录的相关字段
     */
    public function add($table='',$data=[])
    {
        $this->CI->db->insert($table,$data);
    }

    /**
     * 记录文件错误日志
     * @param $content
     * @param string $level
     */
    public function error($content,$level='ERROR')
    {
        $log="log.txt";
        $logSize = 100000;
        if(file_exists($log) && filesize($log)>$logSize){
            unlink($log);
        }
        //日志文件
        $file = FCPATH . 'application/logs/log-'.date("Y-m-d").'.php';
        file_put_contents($file,"$level - ".date("Y-m-d H:i:s",time())." --> ".$content."\n",FILE_APPEND);
    }
}