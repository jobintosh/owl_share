<?php
// app/Models/PostViewModel.php

namespace App\Models;

use CodeIgniter\Model;

class PostViewModel extends Model
{
    protected $table = 'post_views';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'post_id',
        'user_id',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'platform'
    ];

    protected $useTimestamps = false;
    protected $createdField = 'viewed_at';

    /**
     * บันทึกการดูโพสต์
     */
    public function recordView($postId, $userId = null, $ipAddress = null, $userAgent = null)
    {
        try {
            // ตรวจสอบการดูซ้ำในช่วงเวลาสั้นๆ (เช่น 30 นาที)
            if ($this->isRecentlyViewed($postId, $userId, $ipAddress)) {
                return false;
            }

            $agent = \Config\Services::request()->getUserAgent();

            $data = [
                'post_id' => $postId,
                'user_id' => $userId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'device_type' => $this->getDeviceType($agent),
                'browser' => $agent->getBrowser(),
                'platform' => $agent->getPlatform()
            ];

            $viewId = $this->insert($data);

            // อัพเดทจำนวนการดูในตาราง posts
            $this->updatePostViewCount($postId);

            return $viewId;

        } catch (\Exception $e) {
            log_message('error', '[PostViewModel::recordView] Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ตรวจสอบการดูซ้ำ
     */
    private function isRecentlyViewed($postId, $userId, $ipAddress)
    {
        $where = ['post_id' => $postId];
        
        if ($userId) {
            $where['user_id'] = $userId;
        } else {
            $where['ip_address'] = $ipAddress;
        }

        // ตรวจสอบการดูในช่วง 30 นาทีที่ผ่านมา
        return $this->where($where)
                    ->where('viewed_at >', date('Y-m-d H:i:s', strtotime('-30 minutes')))
                    ->countAllResults() > 0;
    }

    /**
     * ระบุประเภทอุปกรณ์
     */
    private function getDeviceType($agent)
    {
        if ($agent->isMobile()) return 'mobile';
        if ($agent->isTablet()) return 'tablet';
        if ($agent->isRobot()) return 'robot';
        return 'desktop';
    }

    /**
     * อัพเดทจำนวนการดูในตาราง posts
     */
    private function updatePostViewCount($postId)
    {
        $totalViews = $this->where('post_id', $postId)->countAllResults();
        
        return $this->db->table('posts')
                       ->where('id', $postId)
                       ->update(['view_count' => $totalViews]);
    }

    /**
     * ดึงสถิติการดู
     */
    public function getViewStats($postId)
    {
        return [
            'total_views' => $this->where('post_id', $postId)->countAllResults(),
            'unique_viewers' => $this->where('post_id', $postId)
                                   ->groupBy('COALESCE(user_id, ip_address)')
                                   ->countAllResults(),
            'device_stats' => $this->select('device_type, COUNT(*) as count')
                                  ->where('post_id', $postId)
                                  ->groupBy('device_type')
                                  ->findAll(),
            'browser_stats' => $this->select('browser, COUNT(*) as count')
                                   ->where('post_id', $postId)
                                   ->groupBy('browser')
                                   ->findAll()
        ];
    }
}