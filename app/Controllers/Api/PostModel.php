<?php

namespace App\Models;

use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'title', 
        'excerpt',
        'content',
        'image',
        'category',
        'author',
        'views',
        'likes',
        'status'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'excerpt' => 'required|min_length[10]',
        'content' => 'required',
        'category' => 'required|in_list[knowledge,technology,news]',
        'author' => 'required'
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'กรุณาใส่หัวข้อ',
            'min_length' => 'หัวข้อต้องมีความยาวอย่างน้อย 3 ตัวอักษร',
            'max_length' => 'หัวข้อต้องมีความยาวไม่เกิน 255 ตัวอักษร'
        ],
        'excerpt' => [
            'required' => 'กรุณาใส่เนื้อหาย่อ',
            'min_length' => 'เนื้อหาย่อต้องมีความยาวอย่างน้อย 10 ตัวอักษร'
        ],
        'content' => [
            'required' => 'กรุณาใส่เนื้อหา'
        ],
        'category' => [
            'required' => 'กรุณาเลือกหมวดหมู่',
            'in_list' => 'หมวดหมู่ไม่ถูกต้อง'
        ],
        'author' => [
            'required' => 'กรุณาใส่ชื่อผู้เขียน'
        ]
    ];

    // Get trending posts
    public function getTrendingPosts($limit = 6)
    {
        return $this->select('posts.*, COUNT(views.id) as view_count')
                    ->join('post_views as views', 'views.post_id = posts.id', 'left')
                    ->where('posts.status', 'published')
                    ->groupBy('posts.id')
                    ->orderBy('view_count', 'DESC')
                    ->limit($limit)
                    ->find();
    }

    // Get posts by category
    public function getPostsByCategory($category, $limit = 6)
    {
        return $this->where([
                        'category' => $category,
                        'status' => 'published'
                    ])
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->find();
    }

    // Increment view count
    public function incrementViews($postId)
    {
        $db = \Config\Database::connect();
        $db->table('post_views')->insert([
            'post_id' => $postId,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'viewed_at' => date('Y-m-d H:i:s')
        ]);
    }

    // Toggle like
    public function toggleLike($postId, $userId)
    {
        $db = \Config\Database::connect();
        $existing = $db->table('post_likes')
                      ->where([
                          'post_id' => $postId,
                          'user_id' => $userId
                      ])
                      ->get()
                      ->getRow();

        if ($existing) {
            $db->table('post_likes')
               ->where([
                   'post_id' => $postId,
                   'user_id' => $userId
               ])
               ->delete();
            return false; // Unliked
        } else {
            $db->table('post_likes')
               ->insert([
                   'post_id' => $postId,
                   'user_id' => $userId,
                   'liked_at' => date('Y-m-d H:i:s')
               ]);
            return true; // Liked
        }
    }
}