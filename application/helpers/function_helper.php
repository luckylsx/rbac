<?php

/**
 * Description of function_help
 *
 * @author tangxj   2016-01-22
 */
class function_helper {
    public function __construct()
    {
        $this->ci = &get_instance();
	}
	/**
	 * 根据用户id得到对应的hash值
	 * @param int $userid
	 * @return string
	 */
	public function getUserHash($userid){
		$str = bin2hex($userid);
		
		$hash = substr($str, 0, 4);
		
		if (strlen($hash)<4){
			$hash = str_pad($hash, 4, "0");
		}
		
		return $hash;
	}

    /**
     * api接口返回json数据
     * @param $data
     * @return string
     */
    public function json_response($data){
        $this->ci->output->set_header('Content-Type: application/json; charset=utf-8');
        return json_encode($data,JSON_UNESCAPED_UNICODE);
    }

    /**
     * 返回失败
     * @param $errorcode
     * @param $errormsg
     * @return string
     */
    public function error($errcode,$errormsg)
    {
        echo $this->json_response(['errcode'=>$errcode,'errmsg'=>$errormsg]);
        exit;
    }

    /**
     * 请求成功
     * @param $data
     * @return string
     */
    public function success($data)
    {
        if ($data && is_array($data)){
            echo  $this->json_response(['errcode'=>1000,"data"=>$data]);
            exit;
        }
        echo $this->json_response(['errcode'=>1000,"data"=>$data]);
        exit;
    }

    /**
     * curl post请求
     * @param $url
     * @param $param
     * @param bool $post_file
     * @return bool|mixed
     */
    public function http_post($url,$param,$post_file=false){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        if (is_string($param) || $post_file) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach($param as $key=>$val){
                $aPOST[] = $key."=".urlencode($val);
            }
            $strPOST =  join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_TIMEOUT,5);   //只需要设置一个秒的数量就可以
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($oCurl, CURLOPT_POST,true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }

    /**
     * curl get请求
     * @param $url
     * @return mixed
     */
    public function httpGet($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt ($curl, CURLOPT_TIMEOUT, 10 );
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

    /**
     * 验证数组参数
     * @param $data
     * @param $rules
     * @return bool|mixed
     */
    public function check_param($data,$rules=[])
    {
        if (!is_array($data)){
            return "格式有误";
        }
        //描述字段存在
        if ($rules && is_array($rules) && is_array($data)){
            foreach ($rules as $key=>$v){
                if (!element($key,$data)){
                    return element($key,$rules)."必填！";
                }
            }
        }else{ //描述字段不存在，验证数组值是否存在
            foreach ($data as $key=>$v){
                if (!$v){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 参数加密
     * @param array $encode
     * @return bool|string
     */
    public function bcryptCode($encode)
    {
        if (!$encode || !is_array($encode)){
            return false;
        }
        //加密
        $code = base64_encode(urlencode(http_build_query($encode)));
        return str_replace("=","",$code);
    }

    /**
     * 解密
     * @param string $s
     * @return array
     */
    public function encryptCode($s)
    {
        if (!$s || !is_string($s)){
            return [];
        }
        //解密
        $param = urldecode(base64_decode($s));
        //将字符串拆分成数组
        parse_str($param, $output);
        return $output;
    }
    /**
     * 检查用户名是否符合规定
     *由字母,数字,或者下划线、中划线组成
     * @param STRING $username 要检查的用户名
     * @return TRUE or FALSE
     */
    function is_username($username,$min=4,$max=15)
    {
        $strlen = strlen(trim($username)); //去除首尾空格
        if (!preg_match("/^[a-zA-Z0-9_-][a-zA-Z0-9_-]+$/",$username)){
            return false;
        }elseif ($max < $strlen || $strlen < $min){
            return false;
        }
        return true;
    }

    /**
     * 验证密码规则 由字母、数字组成
     * @param $value
     * @param int $minLen 默认5位
     * @param int $maxLen 默认16位
     * @return bool|int
     */
    function isPWD($value,$minLen=5,$maxLen=16){
        $match='/^[\\~!@#$%^&*()-_=+|{}\[\],.?\/:;\'\"\d\w]{'.$minLen.','.$maxLen.'}$/';
        $v = trim($value);
        if(empty($v)){
            return false;
        }
        return preg_match($match,$v);
    }

    /**
     * ajax请求返回
     * @param int $status 0 请求成功   1 请求失败
     * @param string $message 请求失败时的错误信息
     */
    public function ajaxReturn($status=0,$message='ok',$data=[])
    {
        if ($status != 0 ){ //返回失败结果
            echo $this->json_response(['errcode'=>$status,"message"=>$message]);
        }else{
            echo $this->json_response([
                'errcode' => $status,
                'message' => $message,
                "data"=>$data
            ]);
        }
        exit;
    }

    /**
     * 生成随机字符串
     * @param int $length
     * @return string
     */
    public function createNonceStr($length = 8)
    {
        $arr1 = range('a','z'); //小写字母
        $arr2 = range('A','Z'); //大写字母
        $arr3 = range('0','9'); //数字
        //合并字符串
        $chars = implode("",array_merge($arr1,$arr2,$arr3));
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 获取当前时间戳，精确到毫秒
     * @return float
     */
    public function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * ci 分页
     * @param $param
     * @param $base_url
     * @param $total
     * @param int $per_page
     */
    public function pagination($param,$base_url,$total,$per_page=10)
    {
        if (isset($param['page'])){
            unset($param['page']);
        }
        //载入分页类
        $this->ci->load->library('pagination');
        //载入分页配置
        $config  = $this->ci->config->config['pagination'];
        //配置搜索参数
        if (!empty($param)){
            $base_url = $base_url."?".http_build_query($param);
        }
        $config['base_url']   = $base_url;
        $config['total_rows'] = $total;
        $config['per_page'] = $per_page;
        $config['page_query_string'] = TRUE;
        $config['use_page_numbers'] = TRUE;
        $config['query_string_segment'] = 'page';
        $this->ci->pagination->initialize($config);
        //分页链接
        return $this->ci->pagination->create_links();
    }

    /**
     * 正则验证手机号
     * @param $mobile
     * @return bool
     */
    public function isMobile($mobile)
    {
        $reg = '/^1[345789]{1}\d{9}$/';
        //验证正则是否正确
        if (preg_match($reg,$mobile)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 数组合并
     * @param $arr array 操作的数组
     * @param $key string key
     * @param $value string value
     * @return array
     */
    public function array_com($arr,$key,$value)
    {
        //判断参数是否存在
        if (!$arr || !is_array($arr) || !$key || !$value){
            return [];
        }
        //获取作数新数组key的一列数
        $keys = array_column($arr,$key);
        //获取作数新数组value的一列数
        $values = array_column($arr,$value);
        //拼凑数组 key:serviceid value:对应的会话数量
        $newArr = array_combine($keys,$values);
        return $newArr;
    }

    /**
     * 格式化时间戳，精确到毫秒，x代表毫秒
     * @param $tag
     * @param $time
     * @return mixed
     */
    function microtime_format($tag, $time='')
    {
        if (!$time){
            $time = $this->microtime_float();
        }
        list($usec, $sec) = explode(".", $time);
        $date = date($tag,$usec);
        return str_replace('x', $sec, $date);
    }

    /**
     * 密码加密
     * @param $password
     * @return bool|mixed|string
     */
    function pass_hash($password){
        return password_hash($password,PASSWORD_BCRYPT);
    }
}
