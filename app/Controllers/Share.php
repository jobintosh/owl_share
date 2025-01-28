<?php

namespace App\Controllers;

class Share extends BaseController
{
    protected $postModel;
    protected $categoryModel;
    protected $tagModel;

    public function __construct()
    {
        $this->postModel = model('PostModel');
        $this->categoryModel = model('CategoryModel');
        $this->tagModel = model('TagModel');
    }

    /**
     * แสดงหน้าแบ่งปันข้อมูล
     */
    public function index()
    {
        // ตรวจสอบการล็อกอิน
        if (!session()->get('user_id')) {
            return redirect()->to('auth/login')->with('error', 'กรุณาเข้าสู่ระบบก่อนแบ่งปันข้อมูล');
        }

        $data = [
            'title' => 'แบ่งปันข้อมูล - ShareHub',
            'categories' => $this->categoryModel->getActiveCategories(),
            'trending_tags' => $this->tagModel->getTrendingTags(),
            'recent_posts' => $this->postModel->getRecentPosts(5),
           
          
            // เพิ่มข้อมูลสำหรับ meta tags
            'meta_description' => 'แบ่งปันความรู้และประสบการณ์กับชุมชน ShareHub',
            'meta_keywords' => 'แบ่งปันความรู้, บทความ, รูปภาพ, ชุมชน',
            'og_image' => base_url('images/share-preview.jpg'),
            
            // เพิ่มข้อมูลสถิติ (ถ้าต้องการ)
            'stats' => [
                'total_posts' => $this->postModel->where('status', 'published')->countAllResults(),
                'total_users' => model('UserModel')->countAllResults(),
                'total_categories' => $this->categoryModel->where('status', 'active')->countAllResults(),
            ]



        ];
       
        return  view('templates/header', $data)
               .view('components/navbar')
                .view('share/index')
               .view('templates/footer');
    }

    /**
     * บันทึกข้อมูลที่แบ่งปัน
     */
    public function create()
    {
        // ตรวจสอบการล็อกอิน
        if (!session()->get('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'กรุณาเข้าสู่ระบบก่อนแบ่งปันข้อมูล'
            ]);
        }

        // ตรวจสอบข้อมูล
        $rules = [
            'title' => 'required|min_length[5]|max_length[255]',
            'category' => 'required|numeric'
          //  'content_type' => 'required|in_list[text,whiteboard,gallery]'
        ];

        // if (!$this->validate($rules)) {
        //     return $this->response->setJSON([
        //         'success' => false,
        //         'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน',
        //         'errors' => $this->validator->getErrors()
        //     ]);
        // }


         // รับค่าและจัดการ tags
         $tagsInput = $this->request->getPost('tags');
         $tags = [];
         
         // ตรวจสอบและแปลง tags เป็น array
         if (!empty($tagsInput)) {
             if (is_string($tagsInput)) {
                 // ถ้าเป็น JSON string ให้แปลงเป็น array
                 $tags = json_decode($tagsInput, true) ?? [];
             } else if (is_array($tagsInput)) {
                 $tags = $tagsInput;
             }
         }
 

        // เตรียมข้อมูล
        $data = [
            'title' => $this->request->getPost('title'),
            'category_id' => $this->request->getPost('category'),
            'type' => $this->request->getPost('content_type'),
            'author_id' => session()->get('user_id'),
            'status' => 'published',
          //  'tags' => json_encode($this->request->getPost('tags') ?? []),
          'tags' => json_encode($tags, JSON_UNESCAPED_UNICODE),
            'content' => $this->request->getPost('content')
        ];


        // $data = [
        //     'title' => 1,
        //     'category_id' => 2,
        //     'type' => 'text',
        //     'author_id' => 4,
        //     'status' => 'published',
        //     'content' => 4
        //  //   'tags' => json_encode($this->request->getPost('tags') ?? [])
        // ];


       

        // จัดการเนื้อหาตามประเภท
        switch ($data['content_type']) {
            case 'text':
             //   $data['content'] = $this->request->getPost('content');
                break;

            case 'whiteboard':
                // บันทึกรูปภาพจาก Base64
                $imageData = $this->request->getPost('whiteboard_data');
                if ($imageData) {
                    $data['content'] = $this->saveWhiteboardImage($imageData);
                }
                break;

            case 'gallery':
                // จัดการรูปภาพหลายรูป
                $images = $this->handleGalleryUpload();
                if ($images) {
                    $data['content'] = json_encode($images);
                }
                break;
        }

        try {
            // บันทึกข้อมูล
            $postId = $this->postModel->insertdb2($data);
          //  print_r($data);

            // บันทึก tags
            // if (!empty($data['tags'])) {
            //     $this->tagModel->saveTags($postId, json_decode($data['tags'], true));
            // }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว',
                'post_id' => $postId
            ]);

        } catch (\Exception $e) {
            log_message('error', '[Share::create] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง d1'
            ]);
        }
    }

    /**
     * บันทึกรูปภาพจาก Whiteboard
     */
    private function saveWhiteboardImage($base64data)
    {
        // แยกข้อมูล Base64
        list($type, $data) = explode(';', $base64data);
        list(, $data) = explode(',', $data);
        $imageData = base64_decode($data);

        // สร้างชื่อไฟล์
        $filename = 'whiteboard_' . time() . '_' . random_string('alnum', 8) . '.png';
        $filepath = FCPATH . 'uploads/whiteboards/' . $filename;

        // บันทึกไฟล์
        if (write_file($filepath, $imageData)) {
            return 'uploads/whiteboards/' . $filename;
        }

        throw new \Exception('ไม่สามารถบันทึกรูปภาพได้');
    }

    /**
     * จัดการการอัพโหลดรูปภาพแกลเลอรี่
     */
    private function handleGalleryUpload()
    {
        $images = [];
        $files = $this->request->getFiles();

        foreach ($files['gallery'] as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(FCPATH . 'uploads/gallery', $newName);
                $images[] = 'uploads/gallery/' . $newName;
            }
        }

        return $images;
    }

    /**
     * อัพโหลดรูปภาพผ่าน TinyMCE
     */
    public function uploadImage()
    {
        $file = $this->request->getFile('file');

        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/content', $newName);

            return $this->response->setJSON([
                'location' => base_url('uploads/content/' . $newName)
            ]);
        }

        return $this->response->setJSON([
            'error' => 'ไม่สามารถอัพโหลดไฟล์ได้'
        ]);
    }
    //
    //

    public function getPosts()
    {
        // ตรวจสอบว่าเป็น AJAX request หรือไม่
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        try {
            $page = $this->request->getGet('page') ?? 1;
            $perPage = 5; // จำนวนโพสต์ต่อหน้า

            // ดึงข้อมูลโพสต์
            $result = $this->postModel->getRecentPostsPaginated($page, $perPage);

            // เพิ่มข้อมูลเพิ่มเติมสำหรับแต่ละโพสต์
            foreach ($result['posts'] as &$post) {
                // เพิ่มข้อมูลการกดไลค์
                if (session()->get('user_id')) {
                    $post['liked'] = model('PostLikeModel')->hasUserLiked(
                        $post['id'], 
                        session()->get('user_id')
                    );
                } else {
                    $post['liked'] = false;
                }

                // แปลง timestamps เป็น readable format
                $post['created_at_formatted'] = date('d M Y H:i', strtotime($post['created_at']));
                
                // ดึงแท็กของโพสต์
                if (!empty($post['tags'])) {
                    $post['tags'] = json_decode($post['tags'], true);
                } else {
                    $post['tags'] = [];
                }

                // // ดึงจำนวนความคิดเห็น
                // $post['comment_count'] = model('CommentModel')
                //     ->where('post_id', $post['id'])
                //     ->where('status', 'approved')
                //     ->countAllResults();
            }

            return $this->response->setJSON([
                'success' => true,
                'posts' => $result['posts'],
                'pagination' => [
                    'current_page' => $result['current_page'],
                    'last_page' => $result['last_page'],
                    'total' => $result['total'],
                    'per_page' => $result['per_page']
                ],
                'has_more' => $result['has_more']
            ]);

        } catch (\Exception $e) {
            log_message('error', '[Share::getPosts] Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง d2'
            ]);
        }
    }

    /**
     * ดึงความคิดเห็นของโพสต์
     */
    // public function getComments($postId)
    // {
    //     if (!$this->request->isAJAX()) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Invalid request'
    //         ]);
    //     }

    //     try {
    //         $comments = model('CommentModel')->getPostComments($postId);
            
    //         // จัดรูปแบบข้อมูลความคิดเห็น
    //         foreach ($comments as &$comment) {
    //             $comment['created_at_formatted'] = date('d M Y H:i', strtotime($comment['created_at']));
    //         }

    //         return $this->response->setJSON([
    //             'success' => true,
    //             'comments' => $comments
    //         ]);

    //     } catch (\Exception $e) {
    //         log_message('error', '[Share::getComments] Error: ' . $e->getMessage());
            
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'ไม่สามารถโหลดความคิดเห็นได้'
    //         ]);
    //     }
    // }

    /**
     * เพิ่มความคิดเห็น
     */
    // public function addComment()
    // {
    //     if (!$this->request->isAJAX()) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Invalid request'
    //         ]);
    //     }

    //     // ตรวจสอบการล็อกอิน
    //     if (!session()->get('user_id')) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'กรุณาเข้าสู่ระบบก่อนแสดงความคิดเห็น'
    //         ]);
    //     }

    //     $rules = [
    //         'post_id' => 'required|numeric',
    //         'content' => 'required|min_length[1]|max_length[1000]'
    //     ];

    //     if (!$this->validate($rules)) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน',
    //             'errors' => $this->validator->getErrors()
    //         ]);
    //     }

    //     try {
    //         $commentModel = model('CommentModel');
            
    //         $commentId = $commentModel->insert([
    //             'post_id' => $this->request->getPost('post_id'),
    //             'author_id' => session()->get('user_id'),
    //             'content' => $this->request->getPost('content'),
    //             'status' => 'approved' // หรืออาจจะเป็น 'pending' ขึ้นอยู่กับการตั้งค่า
    //         ]);

    //         if ($commentId) {
    //             // อัพเดทจำนวนความคิดเห็นในโพสต์
    //             $this->postModel->updateCommentCount($this->request->getPost('post_id'));

    //             // ดึงข้อมูลความคิดเห็นที่เพิ่มเข้าไป
    //             $comment = $commentModel->select('comments.*, users.name as author_name, users.avatar as author_avatar')
    //                 ->join('users', 'users.id = comments.author_id')
    //                 ->find($commentId);

    //             $comment['created_at_formatted'] = date('d M Y H:i', strtotime($comment['created_at']));

    //             return $this->response->setJSON([
    //                 'success' => true,
    //                 'comment' => $comment,
    //                 'message' => 'เพิ่มความคิดเห็นเรียบร้อยแล้ว'
    //             ]);
    //         }

    //         throw new \Exception('ไม่สามารถบันทึกความคิดเห็นได้');

    //     } catch (\Exception $e) {
    //         log_message('error', '[Share::addComment] Error: ' . $e->getMessage());
            
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง d3'
    //         ]);
    //     }
    // }
}