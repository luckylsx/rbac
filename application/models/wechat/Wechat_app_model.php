<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wechat_app_model extends CI_Model
{

    protected $tableName = 'wechat_app';

    /**
     * 获取getSecret
     * @param string $appid
     * @return bool
     */
    public function getSecret($appid)
    {
        if (empty($appid)) {
            return false;
        }
        $row = $this->db->select('secret')
            ->get_where($this->tableName, ['appid' => $appid], 1)
            ->row_array();

        return $row['secret'] ?? false;
    }

    /**
     * 保存AccessToken
     * @param $appid
     * @param $accessToken
     * @return bool
     */
    public function setAccessToken($appid, $accessToken)
    {
        if (empty($appid) || empty($accessToken)) {
            return false;
        }

        return $this->db->update($this->tableName, [
            'access_token' => $accessToken,
        ], [
            'appid' => $appid
        ]);
    }

    /**
     * 获取AccessToken
     * @param $appid
     * @return bool
     */
    public function getAccessToken($appid)
    {
        if (empty($appid)) {
            return false;
        }
        $row = $this->db->select('access_token')
            ->get_where($this->tableName, ['appid' => $appid], 1)
            ->row_array();

        return $row['access_token'] ?? false;
    }

}
