<?php

namespace App\Models;

use CodeIgniter\Model;

class PostReactionModel extends Model
{
    protected $table = 'post_reactions'; // ชื่อตารางในฐานข้อมูล
    protected $primaryKey = 'id'; // คีย์หลักของตาราง

    protected $allowedFields = ['post_id', 'user_id', 'reaction_type', 'created_at']; // ฟิลด์ที่อนุญาตให้กรอกข้อมูล
    protected $useTimestamps = true; // เปิดการใช้งาน timestamps
    protected $createdField = 'created_at'; // คอลัมน์สำหรับวันที่สร้าง

    /**
     * Get reactions count for a specific post
     */
    public function getReactionsByPost($postId)
    {
        return $this->select('reaction_type, COUNT(*) as count')
                    ->where('post_id', $postId)
                    ->groupBy('reaction_type')
                    ->findAll();
    }
    

    /**
     * Add, update, or delete reaction for a user
     */
    public function saveReaction($postId, $userId, $reactionType)
    {
        // Check if the user has already reacted to this post
        $existingReaction = $this->where('post_id', $postId)
            ->where('user_id', $userId)
            ->first();

        if ($existingReaction) {
            // If the reaction type is the same, remove the reaction (user un-reacts)
            if ($existingReaction['reaction_type'] === $reactionType) {
                return $this->delete($existingReaction['id']);
            } else {
                // If the reaction type is different, update the reaction (user changes reaction)
                return $this->update($existingReaction['id'], ['reaction_type' => $reactionType]);
            }
        }

        // If no existing reaction, add a new one
        return $this->insert([
            'post_id' => $postId,
            'user_id' => $userId,
            'reaction_type' => $reactionType,
            'created_at' => date('Y-m-d H:i:s') // Ensure the creation time is set correctly if needed
        ]);
    }

    /**
     * Get a user's current reaction for a specific post
     */
    public function getUserReaction($postId, $userId)
    {
        return $this->where('post_id', $postId)
                    ->where('user_id', $userId)
                    ->first(); // Returns the reaction or null if no reaction
    }
}
