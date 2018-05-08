<?php
/**
 * Created by PhpStorm.
 * User: lucky.li
 * Date: 2018/5/3
 * Time: 14:02
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class TestController extends CI_Controller
{
    public function index()
    {
        $query = "SELECT test FROM miniapp_user";
        $this->db->query($query);
        var_dump($this->db->error());
    }
}