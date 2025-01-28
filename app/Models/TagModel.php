<?php

namespace App\Models;

use CodeIgniter\Model;

class TagModel extends Model
{
    protected $table = 'tags';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'name',
        'slug',
        'post_count'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * ดึงแท็กยอดนิยม
     */
    // public function getTrendingTags($limit = 10) //จำนวน
    // {
    //     return $this->orderBy('post_count', 'DESC')
    //                 ->limit($limit)
    //                 ->find();
    // }

    
    public function getTrendingTags($limit = 10)
    {
        $builder = $this->db->table('tags');
        $builder->select('tags.*, COUNT(post_tags.post_id) as post_count');
        $builder->join('post_tags', 'post_tags.tag_id = tags.id', 'left');
        $builder->join('posts', 'posts.id = post_tags.post_id AND posts.status = "published"', 'left');
        $builder->groupBy('tags.id');
        $builder->orderBy('post_count', 'DESC');
        $builder->limit($limit);
    
        return $builder->get()->getResultArray();
    }
    

    /**
     * บันทึกแท็กสำหรับโพสต์
     */
    public function saveTags($postId, array $tags)
    {
        $db = \Config\Database::connect();

        // เริ่ม Transaction
        $db->transStart();

        try {
            // ลบแท็กเดิมของโพสต์นี้
            $db->table('post_tags')
               ->where('post_id', $postId)
               ->delete();

            foreach ($tags as $tagName) {
                // สร้างหรือดึงแท็ก
                $tag = $this->firstOrCreate($tagName);
                
                // บันทึกความสัมพันธ์ระหว่างโพสต์และแท็ก
                $db->table('post_tags')->insert([
                    'post_id' => $postId,
                    'tag_id' => $tag['id']
                ]);

                // อัพเดทจำนวนโพสต์ของแท็ก
                $this->update($tag['id'], [
                    'post_count' => $this->getPostCount($tag['id'])
                ]);
            }

            $db->transComplete();
            return $db->transStatus();

        } catch (\Exception $e) {
            log_message('error', '[TagModel::saveTags] Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * สร้างหรือดึงแท็กที่มีอยู่
     */
    private function firstOrCreate($tagName)
    {
        $slug = url_title($tagName, '-', true);
        
        $tag = $this->where('slug', $slug)->first();
        
        if (!$tag) {
            $id = $this->insert([
                'name' => $tagName,
                'slug' => $slug,
                'post_count' => 0
            ]);
            
            $tag = $this->find($id);
        }

        return $tag;
    }

    /**
     * นับจำนวนโพสต์ที่ใช้แท็ก
     */
    private function getPostCount($tagId)
    {
        return $this->db->table('post_tags')
                        ->where('tag_id', $tagId)
                        ->countAllResults();
    }

    /**
     * ดึงแท็กของโพสต์
     */
    public function getPostTags($postId)
    {
        return $this->select('tags.*')
                    ->join('post_tags', 'post_tags.tag_id = tags.id')
                    ->where('post_tags.post_id', $postId)
                    ->findAll();
    }

    /**
     * ค้นหาแท็กตามคำค้น
     */
    public function searchTags($query, $limit = 10)
    {
        return $this->like('name', $query)
                    ->orderBy('post_count', 'DESC')
                    ->limit($limit)
                    ->find();
    }

    //
    //
   

    /**
     * บันทึกแท็กสำหรับโพสต์
     */
   
    /**
     * ดึงหรือสร้างแท็กใหม่
     */
    protected function getOrCreateTag($name)
    {
        $slug = url_title($name, '-', true);
        
        $tag = $this->where('slug', $slug)->first();
        
        if (!$tag) {
            $id = $this->insert([
                'name' => $name,
                'slug' => $slug,
                'post_count' => 0
            ]);
            return $id;
        }
        
        return $tag['id'];
    }

    /**
     * อัพเดทจำนวนโพสต์ของแท็ก
     */
    protected function updateTagCounts()
    {
        $db = \Config\Database::connect();
        
        $query = "
            UPDATE tags t
            LEFT JOIN (
                SELECT tag_id, COUNT(DISTINCT pt.post_id) as post_count
                FROM post_tags pt
                JOIN posts p ON p.id = pt.post_id
                WHERE p.status = 'published' AND p.deleted_at IS NULL
                GROUP BY tag_id
            ) counts ON counts.tag_id = t.id
            SET t.post_count = COALESCE(counts.post_count, 0)
        ";
        
        $db->query($query);
    }


    // public function getAllTagsWithCount()
    // {
    //     $builder = $this->db->table('tags t');
    //     $builder->select('t.*, COUNT(pt.post_id) as post_count');
    //     $builder->join('post_tags pt', 'pt.tag_id = t.id', 'left');
    //     $builder->join('posts p', 'p.id = pt.post_id AND p.status = "published" AND p.deleted_at IS NULL', 'left');
    //     $builder->groupBy('t.id');
    //     $builder->orderBy('post_count', 'DESC');
        
    //     return $builder->get()->getResultArray();
    // }

    // public function getPopularTags($limit = 10)
    // {
    //     $builder = $this->db->table('tags t');
    //     $builder->select('t.*, COUNT(pt.post_id) as post_count');
    //     $builder->join('post_tags pt', 'pt.tag_id = t.id', 'left');
    //     $builder->join('posts p', 'p.id = pt.post_id AND p.status = "published" AND p.deleted_at IS NULL', 'left');
    //     $builder->groupBy('t.id');
    //     $builder->having('post_count >', 0);
    //     $builder->orderBy('post_count', 'DESC');
    //     $builder->limit($limit);
        
    //     return $builder->get()->getResultArray();
    // }




    public function getAllTagsWithCount()
    {
        $builder = $this->db->table('tags');
        $result = $builder->get()->getResultArray();

        // ดึงโพสต์ทั้งหมดที่มีสถานะ published
        $postsBuilder = $this->db->table('posts');
        $postsBuilder->select('tags');
        $postsBuilder->where('status', 'published');
        $postsBuilder->where('deleted_at IS NULL');
        $posts = $postsBuilder->get()->getResultArray();

        // นับจำนวนโพสต์สำหรับแต่ละแท็ก
        $tagCounts = [];
        foreach ($posts as $post) {
            if (!empty($post['tags'])) {
                $postTags = json_decode($post['tags'], true);
                if (is_array($postTags)) {
                    foreach ($postTags as $tag) {
                        if (!isset($tagCounts[$tag])) {
                            $tagCounts[$tag] = 0;
                        }
                        $tagCounts[$tag]++;
                    }
                }
            }
        }

        // เพิ่มจำนวนโพสต์ลงในผลลัพธ์
        foreach ($result as &$tag) {
            $tag['post_count'] = $tagCounts[$tag['name']] ?? 0;
        }

        // เรียงลำดับตามจำนวนโพสต์จากมากไปน้อย
        usort($result, function($a, $b) {
            return $b['post_count'] - $a['post_count'];
        });

        return $result;
    }

    public function getPopularTags($limit = 10)
    {
        $allTags = $this->getAllTagsWithCount();
        
        // กรองเฉพาะแท็กที่มีโพสต์
        $popularTags = array_filter($allTags, function($tag) {
            return $tag['post_count'] > 0;
        });

        // ตัดให้เหลือตามจำนวนที่ต้องการ
        return array_slice($popularTags, 0, $limit);
    }


}