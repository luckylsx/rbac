<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('mini_program');
        $this->load->model('wechat/wechat_app_model');
        $this->load->model('wechat/wechat_user_model');
    }

    /**
     * 登录凭证
     */
    public function code()
    {
        $code = $this->input->get('code');
        if (empty($code)) {
            return $this->error(10001);  //code不能为空
        }
        //获取secret
        $secret = $this->mini_program->secret;
        //获取用户openid 及 session_key
        $response = $this->mini_program->jsCode($secret, $code);
        if (!isset($response['openid']) || !isset($response['session_key'])) {
            return $this->error(11002);  //操作失败
        }
        $res = array();
        $res['session_id'] = md5($response['openid'] . $response['session_key']);
        $this->wechat_user_model->replaceUser($response['openid'], $response + $res);

        return $this->success($res);
    }

    /**
     * 获取用户信息
     */
    public function getUserInfo()
    {
        $this->checkLogin();
        $sessionKey = $this->user['session_key'];
        $input = json_decode($this->input->raw_input_stream, true);
        $userInfo = $input['userInfo'] ?? '';
        $rawData = $input['rawData'] ?? '';
        $signature = $input['signature'] ?? '';
        $encryptedData = $input['encryptedData'] ?? '';
        $iv = $input['iv'] ?? '';
        if (empty($userInfo) || empty($rawData) || empty($signature) || empty($encryptedData) || empty($iv)) {
            return $this->error(11001);     //入参错误
        }
        if (sha1($rawData . $sessionKey) != $signature) {
            return $this->error(10003);     //签名错误
        }
        $user = $this->mini_program->decryptData($encryptedData, $iv, $sessionKey);
        if (empty($user)) {
            return $this->error(11002);  //操作失败
        }
        $this->wechat_user_model->replaceUser($this->openid, $user + $userInfo);

        return $this->success();
    }

}
