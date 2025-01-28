<?php

namespace Config;
use Facebook\Facebook;
use CodeIgniter\Config\BaseConfig;

class OAuth extends BaseConfig
{
    public $facebook = [
        'app_id' => '600814942531878',
        'app_secret' => '9335d02f83a933a4d6200df91b202adc',
        // 'app_id' => '1013981228797317',
        // 'app_secret' => 'a7f27e2328dccdad043db9bdcfb8b303',
        'default_graph_version' => 'v21.0',
        'permissions' => ['email', 'public_profile'],
        'redirect_uri' => 'auth/facebook-callback',
        // 'auth_type' => '', // '', 'rerequest', 'reauthenticate'
        'response_type' => 'code',
        'display' => 'page', // page, popup, touch, wap
        'auth_url' => 'https://www.facebook.com/v21.0/dialog/oauth',
        'graph_url' => 'https://graph.facebook.com/v21.0/'
    ];

    public function __construct()
    {
        // แปลง redirect_uri เป็น URL เต็ม
        $this->facebook['redirect_uri'] = base_url($this->facebook['redirect_uri']);
    }
}