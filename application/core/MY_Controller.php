<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    protected $uid = 0;

    protected $openid = '';

    protected $user;

    protected $_data = array(
        'version' => '1.0.0',
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('wechat/wechat_user_model');
    }

    /**
     * response json
     * @param $data
     * @return bool
     */
    protected function response($data)
    {
        $this->output->set_content_type('application/json', 'UTF-8')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
        return true;
    }

    /**
     * rest 返回json数据
     * @param $code
     * @param null $data
     * @param array $ext
     */
    protected function respRes($code, $data = null, array $ext = [])
    {
        $codeConfig = config_item('code');
        if (isset($codeConfig[$code])) {
            $msg = $codeConfig[$code];
        } else {
            $code = 500;
            $msg = '系统异常';
        }
        $res = array(
            'errcode' => $code,
            'errmsg' => $msg,
        );
        if (!is_null($data)) {
            $res['data'] = $data;
        }
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($res + $ext, JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * restful 成功返回
     * @param array $data
     * @param array $ext
     * @param int $code
     * @return void
     */
    protected function success($data = null, $ext = [], $code = 0)
    {
        return $this->respRes($code, $data, $ext);
    }

    /**
     * restful 错误返回
     * @param int $code
     * @param null $data
     * @param array $ext
     * @return void
     */
    protected function error($code = 500, $data = null, $ext = [])
    {
        return $this->respRes($code, $data, $ext);
    }

    protected function view($view, $vars = [], $return = false)
    {
        $this->load->view($view, $this->_data + $vars, $return);
    }

    protected function getPageParam()
    {
        $res = array();
        $res['page'] = max(intval($this->input->get('page')), 1);
        $res['per_page'] = isset($_GET['per_page']) ? max(intval($this->input->get('per_page')), 1) : 10;
        $res['offset'] = ($res['page'] - 1) * $res['per_page'];

        return $res;
    }

    /**
     * 登录检测
     */
    protected function checkLogin()
    {
        $sessionId = $this->input->get('session_id');
        $user = $this->wechat_user_model->getUserBySessionId($sessionId);
        if (empty($user['openid'])) {
            return $this->error(10002);  //用户未登录
        }
        $this->uid = $user['id'];
        $this->openid = $user['openid'];
        $this->user = $user;
    }

}

