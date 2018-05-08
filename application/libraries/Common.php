<?php
/**
 * 公共类
 */
class Common{
    /**
     * 加密
     */
    public function pwd_encode($code=''){
       
        return md5($code);
    }
    /**
     * web端获取或者存储openid
     * @author Toby.tu 2016-09-07
     */
    public function wechatOpenid($openid=''){
        $key = 'djl_wechatopenid_key';
        $CI =& get_instance();
        $CI->load->library('session');
        if(!empty($openid)){
            $CI->session->set_userdata($key,$openid);
        }else{//从session中取openid
            $openid = $CI->session->userdata($key);
        }
        if(!empty($openid)){
            return $openid;
        }
        return '';
    }
    /**
     * 加密openid
     * @author Toby.tu 2016-09-23
     */
    public function encodeOpenId($openid=''){
        if(empty($openid)) return '';
        $CI = & get_instance();
        $CI->load->library('Data');
        return Data::encrypt($openid);
    }
    /**
     * 加密openid
     * @author Toby.tu 2016-09-23
     */
    public function decodeOpenId($openid=''){
        if(empty($openid)) return '';
        $CI = & get_instance();
        $CI->load->library('Data');
        return Data::decrypt($openid);
    }
    /**
     * 获取七牛的图片地址
     * @author Toby.tu 2016-09-25
     */
    /*public function uploadImg($media_id){
        $CI = & get_instance();
        $CI->load->library('wechatclass');
        $CI->load->library('Qiniu');
        $img_url =$CI->wechatclass->getMedia($media_id);
        $img_field = $CI->wechatclass->saveMedia($img_url);  //从微信获取图片
        $img = $CI->qiniu->addImgToQiniu($img_field,"dslr/".substr($img_field,2));
        return 'http://phpbasefiles.woaap.com/'.$img['key'];
    }*/
    /**
     * 数组转换为xml
     * @author Toby.tu 2016-09-30
     */
    // public function arrayToXml($arr=array()){
    //     if(empty($arr)) return '';
    //     $xml = "<xml>";
    //     foreach ($arr as $key=>$val)
    //     {
    //         if (is_numeric($val)){
    //             $xml.="<".$key.">".$val."</".$key.">";
    //         }else{
    //              $xml.="<".$key.">".$val."</".$key.">";
    //         }
    //     }
    //     $xml.="</xml>";
    //     return $xml;
    // }
    /**
     * 数组转换为xml
     * @author Toby.tu 2016-09-30
     */
    public function arrayToXml($array=array()) {
        if(empty($array)) return '';
        if(is_object($array)){
            $array = get_object_vars($array);
        }
        $xml = '<xml>';
        foreach($array as $key => $value){
            $_tag = $key;
            $_id = null;
            if(is_numeric($key)){
                $_tag = 'item';
                $_id = ' id="' . $key . '"';
            }
            $xml .= "<{$_tag}{$_id}>";
            $xml .= (is_array($value) || is_object($value)) ? $this->arrayToXml($value) : htmlentities($value);
            $xml .= "</{$_tag}>";
        }
        $xml.="</xml>";
        return $xml;
    }
    /**
     * xml转换为数组
     * @author Toby.tu 2016-09-30
     */
    public function xmlToArray($xml='') {
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }
    
    public static function safety($value='') {
        if(empty($value)) return '';
        //$value = htmlspecialchars($value);
        $value = strip_tags($value);
        //'select|insert|and|or|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile
        $filter = array('insert'=>'','update'=>'','select'=>'','delete'=>'',
            'drop'=>'','create'=>'','truncate'=>'',"'"=>'','"'=>'',
            '%'=>'\%',"\\"=>'','/'=>'','and'=>'','or'=>'','union'=>'',
            'into'=>'','load_file'=>'','outfile'=>'',';'=>'','<script>'=>'',
            'INSERT'=>'','UPDATE'=>'','SELECT'=>'','DELETE'=>'',
            'DROP'=>'','CREATE'=>'','TRUNCATE'=>'','AND'=>'','OR'=>'','UNION'=>'',
            'INTO'=>'','LOAD_FILE'=>'','OUTFILE'=>'','<SCRIPT>'=>'');
        $value = strtr($value,$filter);
        return trim($value);
    }
    /**
     * 小异步
     * @author Toby.tu 2016-11-02
     */
    public function _curl($url) {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_TIMEOUT,1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
?>
