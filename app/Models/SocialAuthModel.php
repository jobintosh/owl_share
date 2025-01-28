<?php

namespace App\Models;

use CodeIgniter\Model;

class SocialAuthModel extends Model
{
    protected $table = 'social_auth';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'user_id',
        'provider',
        'social_id',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'profile_data'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'user_id' => 'required|numeric',
        'provider' => 'required|in_list[facebook,google]',
        'social_id' => 'required'
    ];

    // ค้นหาการเชื่อมต่อ Social Login ที่มีอยู่
    public function findExistingAuth($provider, $socialId)
    {
        return $this->where([
            'provider' => $provider,
            'social_id' => $socialId
        ])->first();
    }

    // สร้างหรืออัพเดทการเชื่อมต่อ
    public function createOrUpdateAuth($data)
    {
        $existing = $this->findExistingAuth($data['provider'], $data['social_id']);

        if ($existing) {
            // อัพเดทข้อมูลที่มีอยู่
            $this->update($existing['id'], [
                'access_token' => $data['access_token'] ?? null,
                'refresh_token' => $data['refresh_token'] ?? null,
                'token_expires_at' => $data['token_expires_at'] ?? null,
                'profile_data' => json_encode($data['profile_data'] ?? [])
            ]);
            return $existing['id'];
        } else {
            // สร้างการเชื่อมต่อใหม่
            return $this->insert([
                'user_id' => $data['user_id'],
                'provider' => $data['provider'],
                'social_id' => $data['social_id'],
                'access_token' => $data['access_token'] ?? null,
                'refresh_token' => $data['refresh_token'] ?? null,
                'token_expires_at' => $data['token_expires_at'] ?? null,
                'profile_data' => json_encode($data['profile_data'] ?? [])
            ]);
        }
    }

    // ดึงการเชื่อมต่อทั้งหมดของผู้ใช้
    public function getUserConnections($userId)
    {
        return $this->where('user_id', $userId)->findAll();
    }

    // ลบการเชื่อมต่อ
    public function removeConnection($userId, $provider)
    {
        return $this->where([
            'user_id' => $userId,
            'provider' => $provider
        ])->delete();
    }

    // ตรวจสอบว่ามีการเชื่อมต่อหรือไม่
    public function isConnected($userId, $provider)
    {
        return $this->where([
            'user_id' => $userId,
            'provider' => $provider
        ])->countAllResults() > 0;
    }

    // อัพเดท Access Token
    public function updateAccessToken($id, $accessToken, $expiresAt = null)
    {
        return $this->update($id, [
            'access_token' => $accessToken,
            'token_expires_at' => $expiresAt
        ]);
    }

    // ดึงข้อมูล Profile
    public function getProfileData($userId, $provider)
    {
        $auth = $this->where([
            'user_id' => $userId,
            'provider' => $provider
        ])->first();

        return $auth ? json_decode($auth['profile_data'], true) : null;
    }

    // ตรวจสอบ Token หมดอายุ
    public function isTokenExpired($userId, $provider)
    {
        $auth = $this->where([
            'user_id' => $userId,
            'provider' => $provider
        ])->first();

        if (!$auth || !$auth['token_expires_at']) {
            return true;
        }

        return strtotime($auth['token_expires_at']) < time();
    }

    // รีเฟรช Token
    public function refreshToken($userId, $provider)
    {
        $auth = $this->where([
            'user_id' => $userId,
            'provider' => $provider
        ])->first();

        if (!$auth || !$auth['refresh_token']) {
            return false;
        }

        try {
            // สร้าง client ตามประเภทของ provider
            $client = $this->createClient($provider);
            
            // ขอ token ใหม่
            $newToken = $client->refreshToken($auth['refresh_token']);
            
            // อัพเดทข้อมูล
            $this->update($auth['id'], [
                'access_token' => $newToken->getToken(),
                'refresh_token' => $newToken->getRefreshToken(),
                'token_expires_at' => date('Y-m-d H:i:s', $newToken->getExpires())
            ]);

            return true;
        } catch (\Exception $e) {
            log_message('error', 'Token refresh error: ' . $e->getMessage());
            return false;
        }
    }

    // สร้าง Client สำหรับแต่ละ Provider
    protected function createClient($provider)
    {
        $config = config('OAuth');

        switch ($provider) {
            case 'facebook':
                return new \Facebook\Facebook([
                    'app_id' => $config->facebook['app_id'],
                    'app_secret' => $config->facebook['app_secret'],
                    'default_graph_version' => 'v12.0'
                ]);

            case 'google':
                $client = new \Google_Client();
                $client->setClientId($config->google['client_id']);
                $client->setClientSecret($config->google['client_secret']);
                return $client;

            default:
                throw new \Exception('Unsupported provider');
        }
    }
}