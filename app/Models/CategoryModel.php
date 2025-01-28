<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\CommentModel;
use App\Models\PostModel;


class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    // protected $allowedFields = [
    //     'name',
    //     'slug',
    //     'description',
    //     'icon',
    //     'parent_id',
    //     'order',
    //     'status'
    // ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[100]|is_unique[categories.name,id,{id}]',
        'slug' => 'required|min_length[2]|max_length[100]|is_unique[categories.slug,id,{id}]',
        'status' => 'required|in_list[active,inactive]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'กรุณาใส่ชื่อหมวดหมู่',
            'min_length' => 'ชื่อหมวดหมู่ต้องมีความยาวอย่างน้อย 2 ตัวอักษร',
            'max_length' => 'ชื่อหมวดหมู่ต้องมีความยาวไม่เกิน 100 ตัวอักษร',
            'is_unique' => 'ชื่อหมวดหมู่นี้มีอยู่แล้ว'
        ],
        'slug' => [
            'required' => 'กรุณาใส่ slug',
            'min_length' => 'Slug ต้องมีความยาวอย่างน้อย 2 ตัวอักษร',
            'max_length' => 'Slug ต้องมีความยาวไม่เกิน 100 ตัวอักษร',
            'is_unique' => 'Slug นี้มีอยู่แล้ว'
        ]
    ];

//
//

protected $allowedFields = [
    'name',
    'slug',
    'description',
    'icon',
    'parent_id',
    'order_number',
    'status',
    'post_count'
];



/**
 * ดึงหมวดหมู่ที่ใช้งานอยู่ทั้งหมด
 */
// public function getActiveCategories()
// {
//     return $this->select('categories.*, parent.name as parent_name')
//                 ->join('categories as parent', 'parent.id = categories.parent_id', 'left')
//                 ->where('categories.status', 'active')
//                 ->orderBy('categories.order_number', 'ASC')
//                 ->findAll();
// }

//แสดงหมวดหมู่ที่มีโพสต์และจำนวนโพสต์
public function getActiveCategories()
{
    return $this->select('categories.*, parent.name as parent_name, COUNT(posts.id) as post_count')
                ->join('categories as parent', 'parent.id = categories.parent_id', 'left') // เชื่อมตาราง parent category
                ->join('posts', 'posts.category_id = categories.id AND posts.status = "published"', 'left') // เชื่อมตาราง posts
                ->where('categories.status', 'active') // ดึงเฉพาะหมวดหมู่ที่ active
                ->groupBy('categories.id') // จัดกลุ่มตาม ID หมวดหมู่
                ->orderBy('categories.order_number', 'ASC') // เรียงลำดับตาม order_number
                ->findAll(); // ดึงข้อมูลทั้งหมด
}


/**
 * ดึงหมวดหมู่หลัก (ไม่มี parent)
 */
public function getMainCategories()
{
    return $this->where('parent_id', null)
                ->where('status', 'active')
                ->orderBy('order_number', 'ASC')
                ->findAll();
}

/**
 * ดึงหมวดหมู่ย่อยของหมวดหมู่หลัก
 */
public function getSubCategories($parentId)
{
    return $this->where('parent_id', $parentId)
                ->where('status', 'active')
                ->orderBy('order_number', 'ASC')
                ->findAll();
}

/**
 * ดึงหมวดหมู่พร้อมจำนวนโพสต์
 */
public function getCategoriesWithPostCount()
{
    return $this->select('categories.*, COUNT(posts.id) as post_count')
                ->join('posts', 'posts.category_id = categories.id AND posts.status = "published"', 'left')
                ->where('categories.status', 'active')
                ->groupBy('categories.id')
                ->orderBy('categories.parent_id', 'ASC')
                ->orderBy('categories.order_number', 'ASC')
                ->findAll();
}

/**
 * ดึงหมวดหมู่แบบ nested array
 */
public function getNestedCategories()
{
    $categories = $this->getActiveCategories();
    $nested = [];

    foreach ($categories as $category) {
        if (empty($category['parent_id'])) {
            $nested[$category['id']] = $category;
            $nested[$category['id']]['children'] = [];
        }
    }

    foreach ($categories as $category) {
        if (!empty($category['parent_id']) && isset($nested[$category['parent_id']])) {
            $nested[$category['parent_id']]['children'][] = $category;
        }
    }

    return $nested;
}


//
//




    // ดึงหมวดหมู่ที่ใช้งานอยู่ทั้งหมด
    // public function getActiveCategories()
    // {
    //     return $this->where('status', 'active')
    //                 ->orderBy('parent_id', 'ASC')
    //                 ->orderBy('order', 'ASC')
    //                 ->findAll();
    // }

    // ดึงหมวดหมู่พร้อมจำนวนโพสต์
    // public function getCategoriesWithPostCount()
    // {
    //     return $this->select('categories.*, COUNT(posts.id) as post_count')
    //                 ->join('posts', 'posts.category_id = categories.id AND posts.status = "published"', 'left')
    //                 ->where('categories.status', 'active')
    //                 ->groupBy('categories.id')
    //                 ->orderBy('categories.parent_id', 'ASC')
    //                 ->orderBy('categories.order', 'ASC')
    //                 ->findAll();
    // }

    // ดึงหมวดหมู่ย่อย
    // public function getSubCategories($parent_id)
    // {
    //     return $this->where('parent_id', $parent_id)
    //                 ->where('status', 'active')
    //                 ->orderBy('order', 'ASC')
    //                 ->findAll();
    // }

    // ดึงหมวดหมู่หลัก (ไม่มี parent)
    // public function getMainCategories()
    // {
    //     return $this->where('parent_id', null)
    //                 ->where('status', 'active')
    //                 ->orderBy('order', 'ASC')
    //                 ->findAll();
    // }

    // สร้าง slug จากชื่อหมวดหมู่
    public function createSlug($name)
    {
        $slug = url_title($name, '-', true);
        $count = 0;
        $original_slug = $slug;

        while ($this->where('slug', $slug)->countAllResults() > 0) {
            $count++;
            $slug = $original_slug . '-' . $count;
        }

        return $slug;
    }

    // อัพเดทลำดับการแสดงผล
    public function updateOrder($categories)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($categories as $order => $id) {
            $this->update($id, ['order' => $order]);
        }

        $db->transComplete();
        return $db->transStatus();
    }

    // นับจำนวนโพสต์ในแต่ละหมวดหมู่
    public function getCategoryCounts()
    {
        return $this->select('categories.id, categories.name, COUNT(posts.id) as count')
                    ->join('posts', 'posts.category_id = categories.id')
                    ->where('posts.status', 'published')
                    ->groupBy('categories.id')
                    ->findAll();
    }

    // ดึง Breadcrumb ของหมวดหมู่
    public function getBreadcrumb($category_id)
    {
        $breadcrumb = [];
        $category = $this->find($category_id);

        while ($category) {
            array_unshift($breadcrumb, [
                'id' => $category['id'],
                'name' => $category['name'],
                'slug' => $category['slug']
            ]);

            if ($category['parent_id']) {
                $category = $this->find($category['parent_id']);
            } else {
                break;
            }
        }

        return $breadcrumb;
    }

    // ตรวจสอบว่าหมวดหมู่มีโพสต์หรือไม่
    public function hasActivePosts($category_id)
    {
        $postModel = model('PostModel');
        return $postModel->where('category_id', $category_id)
                        ->where('status', 'published')
                        ->countAllResults() > 0;
    }

    // อัพเดทสถิติหมวดหมู่
    public function updateStats($category_id)
    {
        $postModel = model('PostModel');
        $stats = $postModel->select('COUNT(*) as total_posts, 
                                   SUM(view_count) as total_views,
                                   SUM(like_count) as total_likes,
                                   SUM(comment_count) as total_comments')
                          ->where('category_id', $category_id)
                          ->where('status', 'published')
                          ->first();

        return $this->update($category_id, [
            'post_count' => $stats['total_posts'],
            'view_count' => $stats['total_views'],
            'like_count' => $stats['total_likes'],
            'comment_count' => $stats['total_comments']
        ]);
    }

    

    // public function getActiveCategories()
    // {
    //     $builder = $this->db->table('categories c');
    //     $builder->select('c.*, COUNT(p.id) as post_count');
    //     $builder->join('posts p', 'p.category_id = c.id AND p.status = "published" AND p.deleted_at IS NULL', 'left');
    //     $builder->where('c.status', 'active');
    //     $builder->groupBy('c.id');
    //     $builder->orderBy('post_count', 'DESC');
        
    //     return $builder->get()->getResultArray();
    // }

}