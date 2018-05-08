<?php
/**
 * Created by PhpStorm.
 * User: lucky.li
 * Date: 2018/3/16
 * Time: 13:54
 */

/**
 * 上传素材类
 * Class UploadWx
 */
class UploadImg{
    //access_token
    protected $ac;
    public function __construct()
    {
        $this->CI = & get_instance();
    }

    /**
     * 上传文件
     * @param string $filename 文件名称
     * @param string $type 文件类型 默认为image
     * @return array
     */
    public function upload($filename='',$type='image',$save_path='')
    {
        if ($type=='image'){
            if (!$save_path){
                $path = FCPATH.'public/upload/ikea_menu/image/menu/'.date("Ymd").'/';
            }else{
                $path = FCPATH.'public/upload/ikea_menu/image/'.$save_path.'/'.date("Ymd").'/';
            }
            //创建目录
            $this->createDir($path);
            $config['upload_path'] = $path;
            $config['allowed_types']    = 'gif|jpg|png';
            //图片大小限制 2M
            $config['max_size']  = '2048';
        }else if ($type=='video'){
            if (!$save_path){
                $path = FCPATH.'public/upload/ikea_menu/video/menu/'.date("Ymd").'/';
            }else{
                $path = FCPATH.'public/upload/ikea_menu/video/'.$save_path.'/'.date("Ymd").'/';
            }

            //创建目录
            $this->createDir($path);
            $config['upload_path'] = $path;
            $config['allowed_types']    = 'mp4|mpeg|flv';
            //视频大小限制 10M
            $config['max_size']     = '10240';
        }

        //上传后的文件名
        $config['file_name']   = date("YmdHis"). substr(uniqid(),0,4);
        //载入上传类
        $this->CI->load->library('upload', $config);
        //上传图片
        if ( ! $this->CI->upload->do_upload($filename)){
            //上传失败，返回错误信息
            $error = array('error' => $this->CI->upload->display_errors());
            return ['errcode'=>1,'errmsg'=>$error];
        }else{
            //上传图片到服务器
            $data = array('upload_data' => $this->CI->upload->data());
            //服务器图片的url
            $image_url = $data['upload_data']['full_path'];
            //上传成功，返回图片url
            return ['errcode'=>0,'url'=>$image_url];
        }
    }
    /**
     * 递归创建目录
     * @param string $path
     */
    function createDir($path = '')
    {
        if (!file_exists($path)){
            $this->createDir(dirname($path));
            mkdir($path,0777);
        }
    }
}