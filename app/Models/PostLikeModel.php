<?php
// app/Models/PostLikeModel.php

namespace App\Models;

use CodeIgniter\Model;

class PostLikeModel extends Model
{
    protected $table = 'post_likes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = ['post_id', 'user_id'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    /**
     * ตรวจสอบว่าผู้ใช้กดไลค์โพสต์หรือยัง
     */
    public function hasUserLiked($postId, $userId) 
    {
        if (!$postId || !$userId) {
            return false;
        }

        $result = $this->where([
            'post_id' => $postId,
            'user_id' => $userId
        ])->countAllResults();

        return $result > 0;
    }

    /**
     * กดไลค์หรือยกเลิกไลค์
     */
    public function toggleLike($postId, $userId)
    {
        $db = \Config\Database::connect();
        
        try {
            $db->transStart();

            // เช็คว่าเคยไลค์หรือยัง
            $liked = $this->hasUserLiked($postId, $userId);
            
            if ($liked) {
                // ยกเลิกไลค์
                $this->where([
                    'post_id' => $postId,
                    'user_id' => $userId
                ])->delete();
                
                // ลดจำนวนไลค์
                $db->table('posts')
                   ->where('id', $postId)
                   ->set('like_count', 'like_count - 1', false)
                   ->update();
            } else {
                // เพิ่มไลค์
                $this->insert([
                    'post_id' => $postId,
                    'user_id' => $userId
                ]);
                
                // เพิ่มจำนวนไลค์
                $db->table('posts')
                   ->where('id', $postId)
                   ->set('like_count', 'like_count + 1', false)
                   ->update();
            }

            $db->transComplete();

            return !$liked; // คืนค่าสถานะหลังจากทำการ toggle

        } catch (\Exception $e) {
            log_message('error', '[PostLikeModel::toggleLike] Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * นับจำนวนไลค์ของโพสต์
     */
    public function getLikeCount($postId) 
    {
        return $this->where('post_id', $postId)->countAllResults();
    }

    /**
     * ดึงรายชื่อผู้ใช้ที่กดไลค์
     */
    public function getLikedUsers($postId, $limit = 10) 
    {
        return $this->select('users.id, users.name, users.avatar, post_likes.created_at')
                    ->join('users', 'users.id = post_likes.user_id')
                    ->where('post_likes.post_id', $postId)
                    ->orderBy('post_likes.created_at', 'DESC')
                    ->limit($limit)
                    ->find();
    }

    /**
     * อัพเดทจำนวนไลค์ในตาราง posts
     */
    public function updatePostLikeCount($postId) 
    {
        $likeCount = $this->getLikeCount($postId);
        
        return $this->db->table('posts')
                       ->where('id', $postId)
                       ->update(['like_count' => $likeCount]);
    }
}