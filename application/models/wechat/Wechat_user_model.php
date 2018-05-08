<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wechat_user_model extends CI_Model
{

    protected $tableName = 'miniapp_user';

    /**
     * Replace wechat user
     * @param string $openid
     * @param array $data
     * @return bool
     */
    public function replaceUser($openid = '', $data = array())
    {
        if (empty($openid) || empty($data)) {
            return false;
        }
        $data = array_change_key_case($data, CASE_LOWER);
        $filter = array('openid', 'session_key', 'session_id', 'nickname', 'gender', 'city', 'country', 'province', 'language', 'avatarurl', 'unionid');
        $data = array_intersect_key($data, array_flip($filter));
        if (isset($data['nickname'])) {
            $data['nickname'] = json_encode($data['nickname']);
        }
        $isExists = $this->getUserByOpenid($openid);
        if ($isExists) {
            return $this->db->update($this->tableName, $data, ['openid' => $openid]);
        } else {
            $this->db->insert($this->tableName, $data);
            return $this->db->insert_id();
        }
    }

    /**
     * Get wechat user by openid
     * @param string $openid
     * @return bool
     */
    public function getUserByOpenid($openid = '')
    {
        if (empty($openid)) {
            return false;
        }
        return $this->db->select('id,openid,nickname,avatarurl')->get_where($this->tableName, ['openid' => $openid], 1)->row_array();
    }

    /**
     * 根据session_id获取用户信息
     * @param $session_id
     * @return bool
     */
    public function getUserBySessionId($session_id)
    {
        if (empty($activityCode)) {
            return false;
        }

        return $this->db->select('id,openid,nickname,avatarurl,session_key')->get_where($this->tableName, ['session_id' => $session_id], 1)->row_array();
    }

}
