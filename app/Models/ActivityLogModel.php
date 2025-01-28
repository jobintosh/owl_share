<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'properties',
        'ip_address',
        'user_agent'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // บันทึกกิจกรรม
    public function logActivity($data)
    {
        $log = [
            'user_id' => $data['user_id'] ?? session()->get('user_id'),
            'action' => $data['action'],
            'subject_type' => $data['subject_type'] ?? null,
            'subject_id' => $data['subject_id'] ?? null,
            'description' => $data['description'],
            'properties' => json_encode($data['properties'] ?? []),
            'ip_address' => service('request')->getIPAddress(),
            'user_agent' => service('request')->getUserAgent()->getAgentString()
        ];

        return $this->insert($log);
    }

    // ดึงกิจกรรมล่าสุดของผู้ใช้
    public function getUserActivities($user_id, $limit = 10)
    {
        return $this->where('user_id', $user_id)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->find();
    }

    // ดึงกิจกรรมทั้งหมดที่เกี่ยวกับเนื้อหา
    public function getSubjectActivities($subject_type, $subject_id)
    {
        return $this->select('activity_logs.*, users.name as user_name, users.avatar as user_avatar')
                    ->join('users', 'users.id = activity_logs.user_id')
                    ->where('subject_type', $subject_type)
                    ->where('subject_id', $subject_id)
                    ->orderBy('created_at', 'DESC')
                    ->find();
    }

    // ดึงกิจกรรมทั้งหมดตามประเภท
    public function getActivitiesByAction($action, $limit = 50)
    {
        return $this->select('activity_logs.*, users.name as user_name')
                    ->join('users', 'users.id = activity_logs.user_id')
                    ->where('action', $action)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->find();
    }

    // ดึงสถิติกิจกรรม
    public function getActivityStats($days = 30)
    {
        $db = \Config\Database::connect();
        
        return $db->query("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as total,
                COUNT(DISTINCT user_id) as unique_users,
                action,
                COUNT(*) as action_count
            FROM activity_logs
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY DATE(created_at), action
            ORDER BY date DESC
        ", [$days])->getResultArray();
    }

    // ดึงผู้ใช้ที่มีกิจกรรมมากที่สุด
    public function getMostActiveUsers($limit = 10)
    {
        return $this->select('users.*, COUNT(*) as activity_count')
                    ->join('users', 'users.id = activity_logs.user_id')
                    ->groupBy('users.id')
                    ->orderBy('activity_count', 'DESC')
                    ->limit($limit)
                    ->find();
    }

    // ลบกิจกรรมเก่า
    public function cleanOldActivities($days = 90)
    {
        return $this->where('created_at <', date('Y-m-d H:i:s', strtotime("-{$days} days")))
                    ->delete();
    }

    // ดึงรายงานกิจกรรมรายวัน
    public function getDailyReport($date)
    {
        return $this->select('
                COUNT(*) as total_activities,
                COUNT(DISTINCT user_id) as unique_users,
                COUNT(DISTINCT ip_address) as unique_ips,
                action,
                COUNT(*) as action_count,
                HOUR(created_at) as hour
            ')
            ->where('DATE(created_at)', $date)
            ->groupBy('action, HOUR(created_at)')
            ->orderBy('hour', 'ASC')
            ->find();
    }

    // ตรวจสอบกิจกรรมที่น่าสงสัย
    public function detectSuspiciousActivities()
    {
        $threshold = 100; // จำนวนกิจกรรมต่อชั่วโมงที่ถือว่าผิดปกติ
        
        return $this->select('
                user_id,
                ip_address,
                COUNT(*) as activity_count,
                MIN(created_at) as first_activity,
                MAX(created_at) as last_activity
            ')
            ->where('created_at >', date('Y-m-d H:i:s', strtotime('-1 hour')))
            ->groupBy('user_id, ip_address')
            ->having('activity_count >', $threshold)
            ->find();
    }
}