<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/
//redis
$config['socket_type'] = 'tcp'; //`tcp` or `unix`
//$config['socket'] = '/var/run/redis.sock'; // in case of `unix` socket type
$config['host'] = '127.0.0.1';
$config['password'] = 'redis';
$config['port'] = 6379;
$config['timeout'] = 1000;