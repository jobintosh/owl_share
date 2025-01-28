<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginLogModel extends Model
{
    protected $table = 'login_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'user_id',
        'ip_address',
        'user_agent',
        'status',
        'attempt_count',
        'fail_reason',
        'location',
        'device_type',
        'browser',
        'platform'
    ];

    protected $useTimestamps = false;
    protected $createdField = 'login_time';

    // เพิ่มข้อมูลการเข้าสู่ระบบ
    public function logLogin($userId, $status = 'success', $failReason = null) 
    {
        $agent = $this->request->getUserAgent();
        
        $data = [
            'user_id' => $userId,
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $agent->getAgentString(),
            'status' => $status,
            'device_type' => $this->getDeviceType($agent),
            'browser' => $agent->getBrowser(),
            'platform' => $agent->getPlatform()
        ];

        if ($status === 'failed') {
            $data['fail_reason'] = $failReason;
            $data['attempt_count'] = $this->getAttemptCount($userId);
        }

        // เพิ่ม location จาก IP (ถ้าต้องการ)
        $data['location'] = $this->getLocationFromIP($this->request->getIPAddress());

        return $this->insert($data);
    }

    // นับจำนวนการพยายามเข้าสู่ระบบที่ล้มเหลว
    private function getAttemptCount($userId) 
    {
        return $this->where('user_id', $userId)
                    ->where('status', 'failed')
                    ->where('login_time >', date('Y-m-d H:i:s', strtotime('-30 minutes')))
                    ->countAllResults() + 1;
    }

    // ตรวจสอบประเภทอุปกรณ์
    private function getDeviceType($agent) 
    {
        if ($agent->isMobile()) return 'mobile';
        if ($agent->isTablet()) return 'tablet';
        if ($agent->isRobot()) return 'robot';
        return 'desktop';
    }

    // ดึงข้อมูลตำแหน่งจาก IP (ตัวอย่าง)
    private function getLocationFromIP($ip) 
    {
        // คุณสามารถใช้บริการ IP Geolocation API ต่างๆ ได้
        // เช่น ipapi.co, ipstack.com
        return null;
    }

    // ดึงประวัติการเข้าสู่ระบบของผู้ใช้
    public function getUserLoginHistory($userId, $limit = 10) 
    {
        return $this->where('user_id', $userId)
                    ->orderBy('login_time', 'DESC')
                    ->limit($limit)
                    ->find();
    }

    // ตรวจสอบการเข้าสู่ระบบที่ผิดปกติ
    public function checkSuspiciousActivity($userId) 
    {
        $failedAttempts = $this->where('user_id', $userId)
                               ->where('status', 'failed')
                               ->where('login_time >', date('Y-m-d H:i:s', strtotime('-30 minutes')))
                               ->countAllResults();

        $uniqueIPs = $this->where('user_id', $userId)
                         ->where('login_time >', date('Y-m-d H:i:s', strtotime('-24 hours')))
                         ->groupBy('ip_address')
                         ->countAllResults();

        return [
            'failed_attempts' => $failedAttempts,
            'unique_ips' => $uniqueIPs,
            'is_suspicious' => ($failedAttempts > 5 || $uniqueIPs > 3)
        ];
    }
}