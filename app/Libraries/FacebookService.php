<?php

namespace App\Libraries;


class FacebookService
{
    protected $config;
    protected $fb;

    public function __construct()
    {
        $this->config = config('OAuth')->facebook;
        
        $this->fb = new \Facebook\Facebook([
            'app_id' => $this->config['app_id'],
            'app_secret' => $this->config['app_secret'],
            'default_graph_version' => $this->config['default_graph_version']
        ]);
    }

    /**
     * สร้าง Login URL สำหรับ Facebook
     */
    public function getLoginUrl($state = null)
    {
        $helper = $this->fb->getRedirectLoginHelper();

        // กำหนดค่า state ถ้ามีการส่งมา
        if ($state) {
            $helper->getPersistentDataHandler()->set('state', $state);
        }

        // สร้าง URL พร้อมกำหนด permissions และ options
        $options = [
            'scope' => implode(',', $this->config['permissions']),
            'auth_type' => $this->config['auth_type'],
            'display' => $this->config['display'],
            'response_type' => $this->config['response_type']
        ];

        return $helper->getLoginUrl(
            $this->config['redirect_uri'],
            $this->config['permissions'],
            $options
        );
    }

    /**
     * ดึง Access Token จาก Code ที่ได้จาก Facebook
     */
    public function getAccessTokenFromCode($code)
    {
        $helper = $this->fb->getRedirectLoginHelper();
        
        try {
            return $helper->getAccessToken();
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            throw new \Exception('Graph returned an error: ' . $e->getMessage());
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            throw new \Exception('Facebook SDK returned an error: ' . $e->getMessage());
        }
    }

    /**
     * ดึงข้อมูลผู้ใช้จาก Facebook
     */
    public function getUserProfile($accessToken)
    {
        try {
            $response = $this->fb->get('/me?fields=id,name,email,picture.type(large)', $accessToken);
            return $response->getGraphUser();
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            throw new \Exception('Graph returned an error: ' . $e->getMessage());
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            throw new \Exception('Facebook SDK returned an error: ' . $e->getMessage());
        }
    }

    /**
     * ตรวจสอบความถูกต้องของ Access Token
     */
    public function validateAccessToken($accessToken)
    {
        try {
            $this->fb->get('/me', $accessToken);
            return true;
        } catch(\Exception $e) {
            return false;
        }
    }

    /**
     * ดึงข้อมูล Long-lived Access Token
     */
    public function getLongLivedAccessToken($accessToken)
    {
        try {
            $oAuth2Client = $this->fb->getOAuth2Client();
            return $oAuth2Client->getLongLivedAccessToken($accessToken);
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            throw new \Exception('Error getting long-lived access token: ' . $e->getMessage());
        }
    }

    /**
     * ตรวจสอบ Permissions ที่ได้รับ
     */
    public function checkPermissions($accessToken)
    {
        try {
            $response = $this->fb->get('/me/permissions', $accessToken);
            return $response->getGraphEdge();
        } catch(\Exception $e) {
            return false;
        }
    }

    /**
     * สร้าง Re-request Permissions URL
     */
    public function getReRequestUrl($permissions)
    {
        $helper = $this->fb->getRedirectLoginHelper();
        return $helper->getReRequestUrl(
            $this->config['redirect_uri'],
            $permissions
        );
    }
}