<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\CommentModel;

class PostModel extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'title',
        'slug',
        'content',
        'excerpt',
        'type',
        'image',
        'category_id',
        'author_id',
        'status',
        'tags',
        'view_count',
        'like_count',
        'comment_count'
    ];

    // protected $useTimestamps = true;
    // protected $createdField = 'created_at';
    // protected $updatedField = 'updated_at';
    // protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'content' => 'required',
        'type' => 'required|in_list[text,whiteboard,gallery]',
        'category_id' => 'required|numeric',
        'author_id' => 'required|numeric',
        'status' => 'required|in_list[draft,published,archived]'
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'กรุณาใส่หัวข้อ',
            'min_length' => 'หัวข้อต้องมีความยาวอย่างน้อย 3 ตัวอักษร',
            'max_length' => 'หัวข้อต้องมีความยาวไม่เกิน 255 ตัวอักษร'
        ],
        'content' => [
            'required' => 'กรุณาใส่เนื้อหา'
        ],
        'category_id' => [
            'required' => 'กรุณาเลือกหมวดหมู่',
            'numeric' => 'รหัสหมวดหมู่ไม่ถูกต้อง'
        ]
    ];

    //
    //

    //     public function getPostBySlug($slug)
    // {
    //     return $this->select('
    //         posts.*,
    //         users.name as author_name,
    //         users.avatar as author_avatar,
    //         users.username as author_username,
    //         users.bio as author_bio,
    //         categories.name as category_name,
    //         categories.slug as category_slug,
    //         (SELECT COUNT(*) FROM post_likes WHERE post_id = posts.id) as like_count,
    //         (SELECT COUNT(*) FROM comments WHERE post_id = posts.id AND status = "approved") as comment_count
    //     ')
    //     ->join('users', 'users.id = posts.author_id')
    //     ->join('categories', 'categories.id = posts.category_id')
    //     ->where('posts.slug', $slug)
    //     ->where('posts.status', 'published')
    //     ->where('posts.deleted_at IS NULL')
    //     ->first();

    //     if ($post) {
    //         // แปลง JSON tags เป็น array
    //         $post['tags'] = !empty($post['tags']) ? json_decode($post['tags'], true) : [];

    //         // เพิ่ม flag สำหรับการกดไลค์ถ้ามีการล็อกอิน
    //         if (session()->get('user_id')) {
    //             $post['is_liked'] = $this->db->table('post_likes')
    //                 ->where('post_id', $post['id'])
    //                 ->where('user_id', session()->get('user_id'))
    //                 ->countAllResults() > 0;
    //         } else {
    //             $post['is_liked'] = false;
    //         }
    //     }

    //     return $post;
    // }

    // /**
    //  * อัพเดทจำนวนการดูโพสต์และเก็บประวัติ
    //  *
    //  * @param int $post_id
    //  * @return bool
    //  */
    // public function incrementViewCount($post_id)
    // {
    //     // อัพเดทจำนวนการดู
    //     $success = $this->where('id', $post_id)
    //         ->set('view_count', 'view_count + 1', false)
    //         ->update();

    //     // เก็บประวัติการดู
    //     if ($success && session()->get('user_id')) {
    //         $this->db->table('post_views')->insert([
    //             'post_id' => $post_id,
    //             'user_id' => session()->get('user_id'),
    //             'ip_address' => $this->request->getIPAddress(),
    //             'user_agent' => $this->request->getUserAgent(),
    //             'viewed_at' => date('Y-m-d H:i:s')
    //         ]);
    //     }

    //     return $success;
    // }

    // /**
    //  * ดึงโพสต์ที่เกี่ยวข้องจากหมวดหมู่เดียวกัน
    //  */
    // public function getRelatedPosts($post_id, $category_id, $limit = 6)
    // {
    //     return $this->select('
    //         posts.id,
    //         posts.title,
    //         posts.slug,
    //         posts.excerpt,
    //         posts.image,
    //         posts.created_at,
    //         users.name as author_name,
    //         users.avatar as author_avatar,
    //         (SELECT COUNT(*) FROM post_likes WHERE post_id = posts.id) as like_count,
    //         (SELECT COUNT(*) FROM comments WHERE post_id = posts.id AND status = "approved") as comment_count
    //     ')
    //     ->join('users', 'users.id = posts.author_id')
    //     ->where('posts.id !=', $post_id)
    //     ->where('posts.category_id', $category_id)
    //     ->where('posts.status', 'published')
    //     ->where('posts.deleted_at IS NULL')
    //     ->orderBy('RAND()')
    //     ->limit($limit)
    //     ->find();
    // }


    //
    //

    /**
     * ค้นหาโพสต์ตามเงื่อนไข
     * 
     * @param string|null $search คำค้นหา
     * @param int|null $category หมวดหมู่
     * @param string|null $tag แท็ก
     * @param string $sort การเรียงลำดับ (latest, popular, comments)
     * @param int $page หน้าที่ต้องการ
     * @return array
     */
    public function searchPosts($search = null, $category = null, $tag = null, $sort = 'latest', $page = 1)
    {
        $builder = $this->select('posts.*, users.name as author_name, users.avatar as author_avatar, 
                               categories.name as category_name, categories.slug as category_slug')
            ->join('users', 'users.id = posts.author_id')
            ->join('categories', 'categories.id = posts.category_id')
            ->where('posts.status', 'published')
            ->where('posts.deleted_at IS NULL');

        // ค้นหาตามคำค้น
        if ($search) {
            $builder->groupStart()
                ->like('posts.title', $search)
                ->orLike('posts.content', $search)
                ->orLike('posts.tags', $search)
                ->groupEnd();
        }

        // กรองตามหมวดหมู่
        if ($category) {
            $builder->where('posts.category_id', $category);
        }

        // กรองตามแท็ก
        if ($tag) {
            $builder->where("JSON_CONTAINS(posts.tags, '\"$tag\"')");
        }

        // การเรียงลำดับ
        switch ($sort) {
            case 'popular':
                $builder->orderBy('posts.view_count', 'DESC');
                break;
            case 'comments':
                $builder->orderBy('posts.comment_count', 'DESC');
                break;
            default:
                $builder->orderBy('posts.created_at', 'DESC');
        }

        return $builder->paginate(12, 'default', $page);
    }

    /**
     * ดึงโพสต์จาก slug
     */
    public function getPostBySlug($slug)
    {
        return $this->select('posts.*, users.name as author_name, users.avatar as author_avatar,
                           categories.name as category_name, categories.slug as category_slug')
            ->join('users', 'users.id = posts.author_id')
            ->join('categories', 'categories.id = posts.category_id')
            ->where('posts.slug', $slug)
            ->where('posts.deleted_at IS NULL')
            ->first();
    }

    /**
     * ดึงโพสต์ที่เกี่ยวข้อง
     */
    public function getRelatedPosts($post_id, $category_id, $limit = 6)
    {
        return $this->select('posts.*, users.name as author_name, users.avatar as author_avatar')
            ->join('users', 'users.id = posts.author_id')
            ->where('posts.id !=', $post_id)
            ->where('posts.category_id', $category_id)
            ->where('posts.status', 'published')
            ->where('posts.deleted_at IS NULL')
            ->orderBy('RAND()')
            ->limit($limit)
            ->find();
    }

    /**
     * เพิ่มจำนวนการดู
     */
    public function incrementViewCount($post_id)
    {
        return $this->where('id', $post_id)
            ->set('view_count', 'view_count + 1', false)
            ->update();
    }

    /**
     * อัพเดทจำนวนไลค์
     */
    public function updateLikeCount($post_id, $increment = true)
    {
        return $this->where('id', $post_id)
            ->set('like_count', "like_count " . ($increment ? '+' : '-') . " 1", false)
            ->update();
    }


    /**
     * สร้าง slug จากหัวข้อ
     */
    public function createSlug($title)
    {
        $slug = url_title($title, '-', true);
        $count = 0;
        $original_slug = $slug;

        while (
            $this->where('slug', $slug)
            ->where('deleted_at IS NULL')
            ->countAllResults() > 0
        ) {
            $count++;
            $slug = $original_slug . '-' . $count;
        }

        return $slug;
    }

    /**
     * ดึงโพสต์ยอดนิยม
     */
    public function getTrendingPosts($limit = 6)
    {
        return $this->select('posts.*, users.name as author_name, users.avatar as author_avatar,
                           categories.name as category_name, categories.slug as category_slug')
            ->join('users', 'users.id = posts.author_id')
            ->join('categories', 'categories.id = posts.category_id')
            ->where('posts.status', 'published')
            ->where('posts.deleted_at IS NULL')
            ->orderBy('posts.view_count', 'DESC')
            ->orderBy('posts.created_at', 'DESC')
            ->limit($limit)
            ->find();
    }


    //
    //



    // protected $validationRules = [
    //     'title' => 'required|min_length[3]|max_length[255]',
    //     'content' => 'required',
    //     'type' => 'required|in_list[text,whiteboard,gallery]',
    //     'category_id' => 'required|numeric',
    //     'author_id' => 'required|numeric',
    //     'status' => 'required|in_list[draft,published,archived]'
    // ];

    // protected $validationMessages = [
    //     'title' => [
    //         'required' => 'กรุณาใส่หัวข้อ',
    //         'min_length' => 'หัวข้อต้องมีความยาวอย่างน้อย 3 ตัวอักษร',
    //         'max_length' => 'หัวข้อต้องมีความยาวไม่เกิน 255 ตัวอักษร'
    //     ],
    //     'content' => [
    //         'required' => 'กรุณาใส่เนื้อหา'
    //     ],
    //     'category_id' => [
    //         'required' => 'กรุณาเลือกหมวดหมู่',
    //         'numeric' => 'รหัสหมวดหมู่ไม่ถูกต้อง'
    //     ]
    // ];

    // ดึงโพสต์ยอดนิยม
    // public function getTrendingPosts($limit = 6)
    // {
    //     return $this->select('posts.*, users.name as author_name, users.avatar as author_avatar, 
    //                         categories.name as category_name, categories.slug as category_slug')
    //                 ->join('users', 'users.id = posts.author_id')
    //                 ->join('categories', 'categories.id = posts.category_id')
    //                 ->where('posts.status', 'published')
    //                 ->where('posts.deleted_at IS NULL')
    //                 ->orderBy('posts.view_count', 'DESC')
    //                 ->orderBy('posts.created_at', 'DESC')
    //                 ->limit($limit)
    //                 ->find();
    // }

    // ดึงโพสต์ตามหมวดหมู่
    public function getPostsByCategory($category_id, $limit = 12)
    {
        return $this->select('posts.*, users.name as author_name, users.avatar as author_avatar')
            ->join('users', 'users.id = posts.author_id')
            ->where('posts.category_id', $category_id)
            ->where('posts.status', 'published')
            ->where('posts.deleted_at IS NULL')
            ->orderBy('posts.created_at', 'DESC')
            ->limit($limit)
            ->find();
    }

    // ค้นหาโพสต์
    // public function searchPosts($keyword, $category = null, $sort = 'latest')
    // {
    //     $builder = $this->select('posts.*, users.name as author_name, users.avatar as author_avatar, 
    //                             categories.name as category_name, categories.slug as category_slug')
    //                    ->join('users', 'users.id = posts.author_id')
    //                    ->join('categories', 'categories.id = posts.category_id')
    //                    ->where('posts.status', 'published')
    //                    ->where('posts.deleted_at IS NULL')
    //                    ->groupStart()
    //                        ->like('posts.title', $keyword)
    //                        ->orLike('posts.content', $keyword)
    //                        ->orLike('posts.tags', $keyword)
    //                    ->groupEnd();

    //     if ($category) {
    //         $builder->where('posts.category_id', $category);
    //     }

    //     switch ($sort) {
    //         case 'popular':
    //             $builder->orderBy('posts.view_count', 'DESC');
    //             break;
    //         case 'comments':
    //             $builder->orderBy('posts.comment_count', 'DESC');
    //             break;
    //         default:
    //             $builder->orderBy('posts.created_at', 'DESC');
    //     }

    //     return $builder->paginate(12);
    // }









    //
    // เพิ่มจำนวนการดู
    // public function incrementViewCount($post_id)
    // {
    //     return $this->where('id', $post_id)
    //                 ->set('view_count', 'view_count + 1', false)
    //                 ->update();
    // }

    // เพิ่ม/ลดจำนวนไลค์
    // public function updateLikeCount($post_id, $increment = true)
    // {
    //     return $this->where('id', $post_id)
    //                 ->set('like_count', "like_count " . ($increment ? '+' : '-') . " 1", false)
    //                 ->update();
    // }


    // สร้าง slug จากหัวข้อ
    // public function createSlug($title)
    // {
    //     $slug = url_title($title, '-', true);
    //     $count = 0;
    //     $original_slug = $slug;

    //     // ตรวจสอบว่า slug ซ้ำหรือไม่
    //     while ($this->where('slug', $slug)->where('deleted_at IS NULL')->countAllResults() > 0) {
    //         $count++;
    //         $slug = $original_slug . '-' . $count;
    //     }

    //     return $slug;
    // }

    // ดึงโพสต์ที่เกี่ยวข้อง
    // public function getRelatedPosts($post_id, $category_id, $limit = 6)
    // {
    //     return $this->select('posts.*, users.name as author_name')
    //                 ->join('users', 'users.id = posts.author_id')
    //                 ->where('posts.id !=', $post_id)
    //                 ->where('posts.category_id', $category_id)
    //                 ->where('posts.status', 'published')
    //                 ->where('posts.deleted_at IS NULL')
    //                 ->orderBy('RAND()')
    //                 ->limit($limit)
    //                 ->find();
    // }

    // ดึงโพสต์ทั้งหมดที่เผยแพร่
    public function getAllPublishedPosts()
    {
        return $this->select('id, title, slug, updated_at')
            ->where('status', 'published')
            ->where('deleted_at IS NULL')
            ->orderBy('created_at', 'DESC')
            ->find();
    }

    // ดึงสถิติโพสต์
    public function getStats()
    {
        $result = $this->select('COUNT(*) as total, 
                                SUM(CASE WHEN status = "published" THEN 1 ELSE 0 END) as published,
                                SUM(CASE WHEN status = "draft" THEN 1 ELSE 0 END) as drafts,
                                SUM(view_count) as total_views,
                                SUM(like_count) as total_likes,
                                SUM(comment_count) as total_comments')
            ->where('deleted_at IS NULL')
            ->first();

        $result['categories'] = $this->select('categories.name, COUNT(*) as count')
            ->join('categories', 'categories.id = posts.category_id')
            ->where('posts.status', 'published')
            ->where('posts.deleted_at IS NULL')
            ->groupBy('categories.id')
            ->find();

        return $result;
    }


    //
    //
    public function getRecentPosts($limit = 5, $category_id = null)
    {
        $builder = $this->select('
                    posts.*, 
                    users.name as author_name, 
                    users.avatar as author_avatar,
                    categories.name as category_name,
                    categories.slug as category_slug,
                    (SELECT COUNT(*) FROM post_likes WHERE post_id = posts.id) as like_count,
                    (SELECT COUNT(*) FROM comments WHERE post_id = posts.id AND status = "approved") as comment_count
                ')
            ->join('users', 'users.id = posts.author_id')
            ->join('categories', 'categories.id = posts.category_id')
            ->where('posts.status', 'published')
            ->where('posts.deleted_at IS NULL');

        // กรองตามหมวดหมู่ถ้ามีการระบุ
        if ($category_id !== null) {
            $builder->where('posts.category_id', $category_id);
        }

        // เพิ่ม flag ว่าผู้ใช้ปัจจุบันกดไลค์โพสต์นี้หรือไม่
        if (session()->get('user_id')) {
            $builder->select('(SELECT COUNT(*) FROM post_likes WHERE post_id = posts.id AND user_id = ' . session()->get('user_id') . ') as liked');
        } else {
            $builder->select('0 as liked');
        }

        return $builder->orderBy('posts.created_at', 'DESC')
            ->limit($limit)
            ->find();
    }

    /**
     * ดึงโพสต์ล่าสุดแบบแบ่งหน้า
     * 
     * @param int $page หน้าที่ต้องการ
     * @param int $perPage จำนวนรายการต่อหน้า
     * @return array
     */
    public function getRecentPostsPaginated($page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;

        $builder = $this->select('
                    posts.*, 
                    users.name as author_name, 
                    users.avatar as author_avatar,
                    categories.name as category_name,
                    categories.slug as category_slug,
                    (SELECT COUNT(*) FROM post_likes WHERE post_id = posts.id) as like_count,
                    (SELECT COUNT(*) FROM comments WHERE post_id = posts.id AND status = "approved") as comment_count
                ')
            ->join('users', 'users.id = posts.author_id')
            ->join('categories', 'categories.id = posts.category_id')
            ->where('posts.status', 'published')
            ->where('posts.deleted_at IS NULL');

        // เพิ่ม flag สำหรับการกดไลค์
        if (session()->get('user_id')) {
            $builder->select('(SELECT COUNT(*) FROM post_likes WHERE post_id = posts.id AND user_id = ' . session()->get('user_id') . ') as liked');
        } else {
            $builder->select('0 as liked');
        }

        $posts = $builder->orderBy('posts.created_at', 'DESC')
            ->limit($perPage, $offset)
            ->find();

        // ดึงแท็กของแต่ละโพสต์
        foreach ($posts as &$post) {
            if (!empty($post['tags'])) {
                $post['tags'] = json_decode($post['tags'], true);
            } else {
                $post['tags'] = [];
            }
        }

        // นับจำนวนโพสต์ทั้งหมด
        $total = $this->where('status', 'published')
            ->where('deleted_at IS NULL')
            ->countAllResults();

        return [
            'posts' => $posts,
            'total' => $total,
            'has_more' => ($offset + $perPage) < $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'last_page' => ceil($total / $perPage)
        ];
    }




    /**
     * บันทึกโพสต์พร้อมตรวจสอบข้อมูล
     */
    public function insertdb($data = null)
    {

        // Validation
        if (!isset($data['title']) || empty($data['title'])) {
            throw new \Exception('กรุณาระบุหัวข้อ');
        }

        if (!isset($data['content']) || empty($data['content'])) {
            //  throw new \Exception('กรุณาระบุเนื้อหา');
        }

        if (!isset($data['category_id']) || empty($data['category_id'])) {
            throw new \Exception('กรุณาเลือกหมวดหมู่');
        }
        print_r($data);
        // Set defaults
        $data['view_count'] = 0;
        $data['like_count'] = 0;
        $data['comment_count'] = 0;

        // Insert
        return parent::insert($data);
    }

    // public function createSlug($title) 
    // {
    //     $db = \Config\Database::connect();
    //     $slug = url_title($title, '-', true);
    //     $originalSlug = $slug;

    //     // ค้นหา slug ที่คล้ายกัน
    //     $sql = "SELECT slug FROM posts 
    //             WHERE slug REGEXP ? 
    //             AND deleted_at IS NULL 
    //             ORDER BY slug DESC 
    //             LIMIT 1";

    //     $pattern = '^' . preg_quote($originalSlug) . '(-a[0-9]+)?$';
    //     $result = $db->query($sql, [$pattern])->getRow();

    //     if ($result) {
    //         // หากพบ slug ที่มีอยู่แล้ว
    //         if (preg_match('/-a([0-9]+)$/', $result->slug, $matches)) {
    //             // เพิ่มเลขต่อจากที่มีอยู่
    //             $number = intval($matches[1]) + 1;
    //             $slug = $originalSlug . '-a' . $number;
    //         } else {
    //             // ถ้าไม่มีตัวเลขต่อท้าย ให้เริ่มที่ a1
    //             $slug = $originalSlug . '-a1';
    //         }
    //     }

    //     return $slug;
    // }


    public function insertdb2($data = null)
    {
        // Validation
        if (!isset($data['title']) || empty($data['title'])) {
            throw new \Exception('กรุณาระบุหัวข้อ');
        }

        if (!isset($data['content']) || empty($data['content'])) {
            //  throw new \Exception('กรุณาระบุเนื้อหา');
        }

        if (!isset($data['category_id']) || empty($data['category_id'])) {
            throw new \Exception('กรุณาเลือกหมวดหมู่');
        }

        $db = \Config\Database::connect();

        //ก็ว่าทำไมค่ามันเพิ่มเองหาตั้งนานนะพี่
        $data['view_count'] = 0; 
        $data['like_count'] = 0;
        $data['comment_count'] = 0;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');


        //  log_message('error',$data['content']);

        try {
            // สร้าง SQL Query
            $sql = "INSERT INTO posts (
                    title, 
                    slug, 
                    content, 
                    type,
                    category_id,
                    author_id,
                    status,
                    tags,
                    excerpt,
                    view_count,
                    like_count,
                    comment_count,
                    created_at,
                    updated_at
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                )";

            // Prepare values
            $values = [
                $data['title'],
                // $data['slug'],
                $this->createSlug('data'),
                $data['content'],
                $data['type'],
                $data['category_id'],
                $data['author_id'],
                $data['status'],
                $data['tags'],
                $data['excerpt'] ?? '',
                $data['view_count'],
                $data['like_count'],
                $data['comment_count'],
                $data['created_at'],
                $data['updated_at']
            ];

            // Execute query
            $result = $db->query($sql, $values);


            return $db->insertID();
        } catch (\Exception $e) {
            log_message('error', '[PostModel::insert] SQL Error: ' . $e->getMessage());
            throw new \Exception('ไม่สามารถบันทึกข้อมูลได้: ' . $e->getMessage());
        }
    }

    // อัพเดทคอมเม้น
    // public function updateCommentCount($postId)
    // {
    //     // สร้างตัวแปร commentModel เพื่อใช้ในการนับจำนวนความคิดเห็น
    //     $commentModel = new CommentModel();
    //     // คำนวณจำนวนความคิดเห็นในโพสต์นี้
    //     $commentCount = $commentModel->getCommentCountByPostId($postId);

    //     // ตรวจสอบจำนวนความคิดเห็นที่ได้
    //     log_message('debug', 'Comment Count: ' . $commentCount);

    //     // อัปเดตจำนวนความคิดเห็นในตาราง posts
    //     $updated = $this->update($postId, ['comment_count' => $commentCount]);

    //     // ตรวจสอบการอัปเดต
    //     if ($updated) {
    //         log_message('debug', 'Post ' . $postId . ' updated successfully.');
    //     } else {
    //         log_message('debug', 'Post ' . $postId . ' failed to update.');
    //     }
    // }

    // public function getPostWithCommentCount($postId)
    // {
    //     // เรียกใช้ฟังก์ชัน updateCommentCount เพื่ออัปเดตข้อมูล comment_count
    //     $this->updateCommentCount($postId);
    //     // ดึงข้อมูลโพสต์
    //     return $this->find($postId);
    // }

}
