<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $table = 'messages';
    protected $primaryKey = 'id';
    protected $allowedFields = ['sender_id', 'recipient_id', 'message', 'is_read'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getRecentChats($userId)
    {
        $subquery = $this->db->table('messages m')
            ->select('m.*, ROW_NUMBER() OVER (PARTITION BY 
                CASE 
                    WHEN m.sender_id = ' . $userId . ' THEN m.recipient_id 
                    ELSE m.sender_id 
                END 
                ORDER BY m.created_at DESC) as rn')
            ->where('m.sender_id', $userId)
            ->orWhere('m.recipient_id', $userId);

        $query = $this->db->table('(' . $subquery->getCompiledSelect() . ') as recent_messages')
            ->where('rn', 1)
            ->orderBy('created_at', 'DESC')
            ->get();

        return $query->getResultArray();
    }

    public function getConversation($userId, $otherId, $limit = 50)
    {
        return $this->db->table('messages')
            ->where('(sender_id = ' . $userId . ' AND recipient_id = ' . $otherId . ')')
            ->orWhere('(sender_id = ' . $otherId . ' AND recipient_id = ' . $userId . ')')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}