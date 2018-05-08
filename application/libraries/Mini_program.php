<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(__DIR__ . '/Http.php');

/**
 * Mini_program
 * Class Mini_program
 */
class Mini_program extends \App\Libraries\Http
{
    public $appid = 'wx33fe999e717b9615';
    public $secret;

    const API_TOKEN = 'https://api.weixin.qq.com/cgi-bin/token';

    const API_JS_CODE_TO_SESSION = 'https://api.weixin.qq.com/sns/jscode2session';
    //公共配置信息
    protected $comConf;

    public function __construct()
    {
        parent::__construct();
        $this->CI = & get_instance();
        //获取公共配置
        $this->CI->load->model("ComConfModel");
        $this->comConf = $this->CI->ComConfModel->getConf();
        $this->appid = $this->comConf['appid'];
        $this->secret = $this->comConf['secret'];
    }

    public function token($secret)
    {
        $params = [
            'appid' => $this->appid,
            'grant_type' => 'client_credential',
            'secret' => $secret,
        ];

        return $this->parseJSON('get', [self::API_TOKEN, $params]);
    }

    public function jsCode($secret, $jsCode)
    {
        $params = [
            'appid' => $this->appid,
            'secret' => $secret,
            'js_code' => $jsCode,
            'grant_type' => 'authorization_code',
        ];

        return $this->parseJSON('get', [self::API_JS_CODE_TO_SESSION, $params]);
    }

    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @param $sessionKey
     * @return mixed
     */
    public function decryptData($encryptedData, $iv, $sessionKey)
    {
        if (strlen($sessionKey) != 24) {
            return false;
        }
        if (strlen($iv) != 24) {
            return false;
        }
        $aesCipher = base64_decode($encryptedData);
        $aesKey = base64_decode($sessionKey);
        $aesIV = base64_decode($iv);

        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $res = json_decode($result, true);
        $appid = $res['watermark']['appid'] ?? '';
        if ($appid != $this->appid) {
            return false;
        }
        return $res;
    }

}
