<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'user_id',
        'type',
        'subject_type',
        'subject_id',
        'data',
        'read_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // สร้างการแจ้งเตือนใหม่
    public function createNotification($data)
    {
        // ตรวจสอบการตั้งค่าการแจ้งเตือนของผู้ใช้
        $userModel = model('UserModel');
        $user = $userModel->find($data['user_id']);
        $settings = json_decode($user['settings'], true);

        if (!isset($settings['notifications']) || $settings['notifications'][$data['type']] === false) {
            return false;
        }

        $notification = [
            'user_id' => $data['user_id'],
            'type' => $data['type'],
            'subject_type' => $data['subject_type'],
            'subject_id' => $data['subject_id'],
            'data' => json_encode($data['data'])
        ];

        $notification_id = $this->insert($notification);

        // ส่งการแจ้งเตือนแบบ Real-time ถ้าเปิดใช้งาน
        if ($settings['notifications']['realtime']) {
            $this->sendRealtimeNotification($notification_id);
        }

        // ส่งอีเมลแจ้งเตือนถ้าเปิดใช้งาน
        if ($settings['notifications']['email']) {
            $this->sendEmailNotification($notification_id);
        }

        return $notification_id;
    }

    // ดึงการแจ้งเตือนที่ยังไม่ได้อ่าน
    public function getUnreadNotifications($user_id, $limit = 10)
    {
        return $this->select('notifications.*, users.name as actor_name, users.avatar as actor_avatar')
                    ->join('users', 'users.id = JSON_EXTRACT(notifications.data, "$.actor_id")', 'left')
                    ->where('notifications.user_id', $user_id)
                    ->where('notifications.read_at IS NULL')
                    ->orderBy('notifications.created_at', 'DESC')
                    ->limit($limit)
                    ->find();
    }

    // มาร์คการแจ้งเตือนว่าอ่านแล้ว
    public function markAsRead($notification_id)
    {
        return $this->update($notification_id, [
            'read_at' => date('Y-m-d H:i:s')
        ]);
    }

    // มาร์คการแจ้งเตือนทั้งหมดว่าอ่านแล้ว
    public function markAllAsRead($user_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('read_at IS NULL')
                    ->set(['read_at' => date('Y-m-d H:i:s')])
                    ->update();
    }

    // ส่งการแจ้งเตือนแบบ Real-time
    protected function sendRealtimeNotification($notification_id)
    {
        $notification = $this->find($notification_id);
        if (!$notification) return false;

        // ส่งข้อมูลผ่าน WebSocket หรือ Server-Sent Events
        $data = [
            'type' => $notification['type'],
            'user_id' => $notification['user_id'],
            'data' => json_decode($notification['data'], true),
            'created_at' => $notification['created_at']
        ];

        // ส่งข้อมูลผ่าน WebSocket (ถ้ามีการติดตั้ง)
        $webSocket = service('WebSocket');
        if ($webSocket) {
            $webSocket->emit('notification', $data, $notification['user_id']);
        }

        return true;
    }

    // ส่งอีเมลแจ้งเตือน
    protected function sendEmailNotification($notification_id)
    {
        $notification = $this->find($notification_id);
        if (!$notification) return false;

        $userModel = model('UserModel');
        $user = $userModel->find($notification['user_id']);
        
        $data = json_decode($notification['data'], true);
        
        // สร้างเนื้อหาอีเมล
        $email = \Config\Services::email();
        $email->setFrom('noreply@sharehub.com', 'ShareHub');
        $email->setTo($user['email']);
        
        switch ($notification['type']) {
            case 'comment':
                $email->setSubject('มีความคิดเห็นใหม่บนโพสต์ของคุณ');
                $email->setMessage($this->getCommentEmailTemplate($data));
                break;
            case 'like':
                $email->setSubject('มีคนถูกใจโพสต์ของคุณ');
                $email->setMessage($this->getLikeEmailTemplate($data));
                break;
            case 'follow':
                $email->setSubject('มีคนเริ่มติดตามคุณ');
                $email->setMessage($this->getFollowEmailTemplate($data));
                break;
        }

        return $email->send();
    }

    // เทมเพลตอีเมลสำหรับความคิดเห็นใหม่
    protected function getCommentEmailTemplate($data)
    {
        return view('emails/notification_comment', $data);
    }

    // เทมเพลตอีเมลสำหรับการถูกใจ
    protected function getLikeEmailTemplate($data)
    {
        return view('emails/notification_like', $data);
    }

    // เทมเพลตอีเมลสำหรับผู้ติดตามใหม่
    protected function getFollowEmailTemplate($data)
    {
        return view('emails/notification_follow', $data);
    }

    // นับจำนวนการแจ้งเตือนที่ยังไม่ได้อ่าน
    public function countUnread($user_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('read_at IS NULL')
                    ->countAllResults();
    }

    // ลบการแจ้งเตือนเก่า
    public function cleanOldNotifications($days = 30)
    {
        return $this->where('created_at <', date('Y-m-d H:i:s', strtotime("-{$days} days")))
                    ->where('read_at IS NOT NULL')
                    ->delete();
    }
}