<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'post_id',
        'author_id',
        'parent_id',
        'content',
        'status'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'post_id' => 'required|numeric',
        'author_id' => 'required|numeric',
        'content' => 'required|min_length[1]|max_length[1000]',
        'status' => 'required|in_list[pending,approved,rejected]'
    ];

    protected $validationMessages = [
        'content' => [
            'required' => 'กรุณาใส่เนื้อหาความคิดเห็น',
            'min_length' => 'เนื้อหาความคิดเห็นต้องมีความยาวอย่างน้อย 1 ตัวอักษร',
            'max_length' => 'เนื้อหาความคิดเห็นต้องมีความยาวไม่เกิน 1000 ตัวอักษร'
        ]
    ];

    // ดึงความคิดเห็นทั้งหมดของโพสต์
    public function getPostComments($post_id)
    {
        return $this->select('comments.*, users.name as author_name, users.avatar as author_avatar')
            ->join('users', 'users.id = comments.author_id')
            ->where([
                'comments.post_id' => $post_id,
                'comments.status' => 'approved',
                'comments.parent_id IS NULL'
            ])
            ->orderBy('comments.created_at', 'DESC')
            ->findAll();
    }

    // ดึงการตอบกลับของความคิดเห็น
    public function getCommentReplies($comment_id)
    {
        return $this->select('comments.*, users.name as author_name, users.avatar as author_avatar')
            ->join('users', 'users.id = comments.author_id')
            ->where([
                'comments.parent_id' => $comment_id,
                'comments.status' => 'approved'
            ])
            ->orderBy('comments.created_at', 'ASC')
            ->findAll();
    }
    
    // อัพเดทสถานะความคิดเห็น
    public function updateStatus($comment_id, $status)
    {
        return $this->update($comment_id, ['status' => $status]);
    }

    // ดึงความคิดเห็นที่รออนุมัติ
    public function getPendingComments()
    {
        return $this->select('comments.*, posts.title as post_title, users.name as author_name')
            ->join('posts', 'posts.id = comments.post_id')
            ->join('users', 'users.id = comments.author_id')
            ->where('comments.status', 'pending')
            ->orderBy('comments.created_at', 'DESC')
            ->findAll();
    }

    // ดึงความคิดเห็นล่าสุด
    public function getRecentComments($limit = 5)
    {
        return $this->select('comments.*, posts.title as post_title, users.name as author_name')
            ->join('posts', 'posts.id = comments.post_id')
            ->join('users', 'users.id = comments.author_id')
            ->where('comments.status', 'approved')
            ->orderBy('comments.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    // ดึงสถิติความคิดเห็น
    public function getStats()
    {
        return [
            'total' => $this->countAllResults(),
            'pending' => $this->where('status', 'pending')->countAllResults(),
            'approved' => $this->where('status', 'approved')->countAllResults(),
            'rejected' => $this->where('status', 'rejected')->countAllResults()
        ];
    }

    // ดึงความคิดเห็นของผู้ใช้
    public function getUserComments($user_id, $limit = 10)
    {
        return $this->select('comments.*, posts.title as post_title')
            ->join('posts', 'posts.id = comments.post_id')
            ->where([
                'comments.author_id' => $user_id,
                'comments.status' => 'approved'
            ])
            ->orderBy('comments.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    // เช็คว่าผู้ใช้มีสิทธิ์แก้ไขความคิดเห็นหรือไม่
    public function canEdit($comment_id, $user_id)
    {
        $comment = $this->find($comment_id);
        if (!$comment) return false;

        // ถ้าเป็นเจ้าของความคิดเห็น
        if ($comment['author_id'] === $user_id) return true;

        // ถ้าเป็นแอดมิน
        $userModel = model('UserModel');
        $user = $userModel->find($user_id);
        if ($user && $user['role'] === 'admin') return true;

        return false;
    }
    
    
}

