<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 小程序登录
 * Class Login
 */
class Login extends MY_Controller
{
    //辅助函数类存储对象
    private static $helper;
    //小程序appSecret;
    protected $appSecret='';
    //公众号appid
    protected $appid='';
    //公共配置文件
    protected $comConf;
    //日志记录表
    protected $ikea_push_log = 'ikea_push_log';
    //小程序用户表
    protected $ikea_miniapp_user = 'miniapp_user';
    public function __construct()
    {
        parent::__construct();
        $this->load->model(["ComConfModel",'mini/MiniUserModel']);
        $this->comConf = $this->ComConfModel->getConf();
        $this->load->library("Common");
        $this->common = new Common();
        $this->miniUserModel = new MiniUserModel();
        //公众号appSecret
        $this->appSecret = element('secret',$this->comConf);
        //微信公众号appid
        $this->appid = element('appid',$this->comConf);
        $this->load->library("LogRecode");
        $this->log = new LogRecode();
        //单例模式实例化工具辅助函数
        $this->load->helper(["function_helper",'array']);
        if (!(self::$helper instanceof function_helper)){
            self::$helper = new function_helper();
        }
    }
    /**
     * 小程序登录获取用户信息
     */
    public function getUserInfo()
    {
        $code = $this->input->get('code');
        $encryptedData = $this->input->get('encryptedData');
        $iv = $this->input->get('iv');
        if (!$code || !$encryptedData || !$iv){
            self::$helper->json(1001,'code missing');
        }
        //记录请求日志
        $logarray = array(
            'openid'=>$iv, //接收的解密iv
            'content'=>$encryptedData, //需要解密的数据
            'type' => 1, //标识类型：1:接收解密iv 2:接收获取session_key的code 3:为解密失败的记录值 4:openid
            'created_at'=>date('Y-m-d H:i:s'), //创建时间
        );
        //记录接日志表
        $this->log->add($this->ikea_push_log,$logarray);
        //code是否存在
        if (empty($code)){ //不存在返回，失败数据
            self::$helper->json(1001,'code missing');
        }else{
            //调用微信接口获取openid,session_key,unionid
            $url = "https://api.weixin.qq.com/sns/jscode2session?appid='.$this->appid.'&secret='.$this->appSecret.'&js_code={$code}&grant_type=authorization_code";
            $res = self::$helper->httpGet($url);
            $result = json_decode($res,true);
            $logarray = array(
                'openid'=>$code, //接收的解密iv
                'content'=>json_encode($result,JSON_UNESCAPED_UNICODE), //需要解密的数据
                'type' => 2, //标识类型：1:接收解密iv 2:接收获取session_key的code 3:为openid'
                'created_at'=>date('Y-m-d H:i:s'), //创建时间
            );
            //记录接日志表
            $this->log->add($this->ikea_push_log,$logarray);
            //获取失败
            if (isset($result['errcode']) || empty($result)){
                self::$helper->json(1001,$result['errmsg']);
            }
            //将用户的基本字段openid与unionid插入数据库
            $openid = $result['openid'];
            $session_key = $result['session_key'];
            $unionid = $result['unionid'];
            $row=$this->miniUserModel->getUserByOpenid($openid);
            if (empty($row) && $openid){
                $u = array('openid'=>$openid,'unionId'=>$unionid,'created_at'=>date('Y-m-d  H:i:s'));
                $uStatus = $this->miniUserModel->insertMiniUser($this->ikea_miniapp_user,$u);
                if ($uStatus['errcode']){
                    self::$helper->json(1001,'登录失败，请重试！');
                }
            }
            //解密用户信息数据
            if (!empty($encryptedData) && !empty($iv)){
                $this->load->library("WXBizDataCrypt");
                $wXBizDataCrypt = new WXBizDataCrypt($this->appid,$session_key);
                $resData = $wXBizDataCrypt->decryptData($encryptedData,$iv,$data);
                //解密成功
                if ($resData==0 && $data){
                    $userinfo = json_decode($resData,true);
                    $nickname  = addslashes($userinfo['nickname']);
                    //对昵称进行处理
                    if (strpos($nickname,"'")!==false){
                        $nickname = '"'.$nickname.'"';
                    }else{
                        $nickname = '"'.$nickname.'"';
                    }
                    $row = $this->miniUserModel->getUserByOpenid($openid,['openid','nickname','unionId']);
                    //数据记录存在 并且nickname或者unionId为空
                    if ($row){
                        if (!$row['unionId']){
                            $arr['unionId'] = $userinfo['unionId'];
                        }
                        if (!$row['nickName']){
                            $arr['nickName'] = $nickname;
                            $arr['gender'] = $userinfo['gender'];
                            $arr['language'] = $userinfo['language'];
                            $arr['city'] = $userinfo['city'];
                            $arr['province'] = $userinfo['province'];
                            $arr['country'] = $userinfo['country'];
                            $arr['avatarUrl'] = $userinfo['avatarUrl'];
                            $arr['updated_at'] = date("Y-m-d H:i:s");
                        }
                        $arr['session_key'] = $session_key;
                        $this->miniUserModel->updateMiniUser($openid,$arr);
                    }else{
                        $initData = $this->miniUserModel->initUser($userinfo,$nickname,$session_key);
                        $this->miniUserModel->insertMiniUser($initData);
                    }
                }else{
                    $logarray=array(
                        'openid'=>$openid,
                        'created_at'=>date('Y-m-d H:i:s'),
                        'content'=>json_encode(array('result'=>$resData,'key'=>$session_key,'encrydata'=>$encryptedData,'iv'=>$iv),JSON_UNESCAPED_SLASHES),
                        'type'=>3,
                    );
                    $this->log->add($this->ikea_push_log,$logarray);
                }
            }
            $rd3_session = $this->common->encodeOpenId($openid);
            self::$helper->json(1000,'ok',$rd3_session);
        }
    }
}