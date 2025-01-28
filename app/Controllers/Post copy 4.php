<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\PostReactionModel;
use App\Models\PostModel;
use App\Models\UserModel;

helper('time_function_helper'); //โหลด helper เพื่อแสดงเวลาโพส

class Post extends BaseController
{
    protected $postModel;
    protected $categoryModel;
    protected $commentModel;
    protected $tagModel;
    protected $db;


    public function __construct()
    {
        $this->postModel = model('PostModel');
        $this->categoryModel = model('CategoryModel');
        $this->commentModel = model('CommentModel');
        $this->tagModel = model('TagModel');
    }

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // โหลด Model ที่จำเป็น
        $this->postModel = model('PostModel');
        $this->categoryModel = model('CategoryModel');
        $this->db = \Config\Database::connect();
    }




    /**
     * แสดงหน้ารายการโพสต์ทั้งหมด
     */
    public function index()
    {
        //แสดงโพสทั้งหมด
        $posts = $this->postModel->searchPosts(
            $search,
            $category,
            $tag,
            $sort,
            $page
        );

        $data = [
            'title' => 'โพสต์ทั้งหมด - ShareHub',
            'posts' => $posts,
            'categories' => $this->categoryModel->getActiveCategories(), //ดึงหมวดหมู่ที่เปิดใช้งานพร้อมจำนวนโพสต์
            'trending_tags' => $this->tagModel->getPopularTags(), //ดึง tag ยอดนิยม
            'pager' => $this->postModel->pager,
            'current_category' => $category,
            'current_tag' => $tag,
            'current_search' => $search,
            'current_sort' => $sort,
        ];

        return view('templates/header', $data)
            . view('components/navbar')
            . view('post/index')
            . view('templates/footer');
    }

    public function view($slug)
    {

        try {
            // 1. ดึงข้อมูลโพสต์และตรวจสอบ
            $post = $this->postModel->getPostBySlug($slug);

            if (!$post) {
                return redirect()->to('/')->with('error', 'ไม่พบบทความที่คุณต้องการ');
            }

            // 2. ตรวจสอบสิทธิ์การเข้าถึง
            if ($post['status'] !== 'published') {
                $currentUser = session()->get('user_id');
                $isAuthor = $currentUser === $post['author_id'];
                $isAdmin = session()->get('role') === 'admin';

                if (!$currentUser || (!$isAuthor && !$isAdmin)) {
                    return redirect()->to('/')->with('error', 'บทความนี้ยังไม่ได้เผยแพร่');
                }
            }

            // 3. เตรียมข้อมูลเพิ่มเติม
            // 3.1 จัดการแท็ก
            $post['tags'] = $this->formatTags($post['tags']);

            // 3.2 ตรวจสอบการกดไลค์
            // $post['is_liked'] = false;
            // if (session()->get('user_id')) {
            //     $post['is_liked'] = model('PostLikeModel')->hasUserLiked(
            //         $post['id'], 
            //         session()->get('user_id')
            //     );
            // }

            // 3.3 คำนวณเวลาอ่าน
            // $post['reading_time'] = $this->calculateReadingTime($post['content']);

            // 4. ดึงข้อมูลที่เกี่ยวข้อง
            // 4.1 โพสต์ที่เกี่ยวข้อง
            $relatedPosts = $this->postModel->getRelatedPosts(
                $post['id'],
                $post['category_id'],
                6
            );

            // 4.2 ความคิดเห็นพร้อมการตอบกลับ
            $comments = $this->commentModel->getPostComments($post['id']);

            // 4.3 ข้อมูลเพิ่มเติมของผู้เขียน
            $authorStats = model('UserStatsModel')->getAuthorStats($post['author_id']);

            // 5. สร้างข้อมูล SEO
            $seoData = $this->generateSeoData($post);

            // 6. สร้าง Breadcrumb
            $breadcrumb = $this->generateBreadcrumb($post);

            // 7. บันทึกการดู
            $this->recordPostView($post);

            // 8. เตรียมข้อมูลสำหรับ View
            $data = [
                'title' => $post['title'] . ' - ' . config('App')->siteName,
                'post' => $post,
                'author_stats' => $authorStats,
                'comments' => $comments,
                'related_posts' => $relatedPosts,
                'categories' => $this->categoryModel->getActiveCategories(),
                'trending_tags' => $this->tagModel->getTrendingTags(10),
                'breadcrumb' => $breadcrumb,
                'seo' => $seoData
            ];




            // 9. แสดงผล
            return view('templates/header', $data)
                . view('components/navbar')
                . view('components/breadcrumb', ['items' => $breadcrumb])
                //  . view('post/view_header', ['post' => $post, 'author_stats' => $authorStats])
                . view('post/view_content', [
                    'post' => $post,
                    'related_posts' => $relatedPosts,
                    'categories' => $data['categories'],
                    'trending_tags' => $data['trending_tags']
                ])
                //  . view('post/view_comments', [
                //      'post' => $post,
                //      'comments' => $comments
                //  ])
                . view('post/view_scripts')
                . view('templates/footer');
        } catch (\Exception $e) {
            log_message('error', '[Post::view] Error: ' . $e->getMessage());
            return redirect()->to('/')->with('error', 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
        }
    }




    // ใน PostModel
    // public function createSlug($title) 
    // {
    //     $slug = url_title($title, '-', true);
    //     $count = 0;
    //     $originalSlug = $slug;

    //     while ($this->where('slug', $slug)
    //                 ->where('deleted_at IS NULL')
    //                 ->countAllResults() > 0) 
    //     {
    //         $count++;
    //         $suffix = 'a' . $count; // เพิ่ม a ตามด้วยตัวเลข
    //         $slug = $originalSlug . '-' . $suffix;
    //     }

    //     return $slug;
    // }

    // หรือใช้ Raw SQL
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



    /**
     * Private helper methods
     */
    private function formatTags($tags)
    {
        if (empty($tags)) {
            return [];
        }

        try {
            if (is_string($tags)) {
                $tags = json_decode($tags, true);
            }
            return is_array($tags) ? array_filter($tags) : [];
        } catch (\Exception $e) {
            log_message('error', 'Error processing tags: ' . $e->getMessage());
            return [];
        }
    }

    private function calculateReadingTime($content)
    {
        $wordCount = str_word_count(strip_tags($content));
        $minutesToRead = ceil($wordCount / 200); // ประมาณ 200 คำต่อนาที

        return [
            'minutes' => $minutesToRead,
            'word_count' => $wordCount
        ];
    }

    private function generateSeoData($post)
    {
        return [
            'title' => $post['title'] . ' - ' . config('App')->siteName,
            'description' => $post['excerpt'] ?: mb_substr(strip_tags($post['content']), 0, 160),
            'keywords' => implode(',', array_merge(
                $post['tags'],
                [$post['category_name'], config('App')->siteName]
            )),
            'author' => $post['author_name'],
            // 'username' => $post['author_username'],
            'published_time' => $post['created_at'],
            'modified_time' => $post['updated_at'],
            'image' => $post['image'] ?: config('App')->defaultPostImage,
            'type' => 'article',
            'site_name' => config('App')->siteName
        ];
    }

    private function generateBreadcrumb($post)
    {
        return [
            [
                'title' => 'หน้าแรก',
                'url' => site_url()
            ],
            [
                'title' => $post['category_name'],
                'url' => site_url('category/' . $post['category_slug'])
            ],
            [
                'title' => $post['title'],
                'url' => null
            ]
        ];
    }

    private function recordPostView($post)
    {
        // เพิ่มจำนวนการดู
        $this->postModel->incrementViewCount($post['id']);

        // บันทึกประวัติการดู
        if (session()->get('user_id')) {
            model('PostViewModel')->insert([
                'post_id' => $post['id'],
                'user_id' => session()->get('user_id'),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString()
            ]);

            // บันทึก activity log
            // model('ActivityLogModel')->log(
            //     'view_post',
            //     'post',
            //     $post['id'],
            //     'ดูบทความ: ' . $post['title']
            // );
        }
    }

    /**
     * สร้าง Private Methods สำหรับช่วยจัดการข้อมูล
     */
    private function processPostTags($tags)
    {
        if (empty($tags)) {
            return [];
        }

        try {
            if (is_string($tags)) {
                $tags = json_decode($tags, true);
            }
            return is_array($tags) ? array_filter($tags) : [];
        } catch (\Exception $e) {
            log_message('error', 'Error processing post tags: ' . $e->getMessage());
            return [];
        }
    }

    private function formatPostContent($content)
    {
        // แปลง Markdown เป็น HTML (ถ้าใช้ Markdown)
        if (class_exists('\Parsedown')) {
            $parsedown = new \Parsedown();
            $content = $parsedown->text($content);
        }

        // ทำความสะอาด HTML
        $purifier = new \HTMLPurifier();
        $content = $purifier->purify($content);

        return $content;
    }

    private function getPostExcerpt($content, $length = 160)
    {
        $excerpt = strip_tags($content);
        if (mb_strlen($excerpt) > $length) {
            $excerpt = mb_substr($excerpt, 0, $length) . '...';
        }
        return $excerpt;
    }



    //
    //



    /**
     * แสดงหน้าแก้ไขโพสต์
     */
    public function edit($id)
    {
        $post = $this->postModel->find($id);

        // ตรวจสอบสิทธิ์
        if (!$this->canEditPost($post)) {
            return redirect()->back()->with('error', 'คุณไม่มีสิทธิ์แก้ไขโพสต์นี้');
        }

        $data = [
            'title' => 'แก้ไขโพสต์ - ' . $post['title'],
            'post' => $post,
            'categories' => $this->categoryModel->getActiveCategories(),
            'tags' => $this->tagModel->getPostTags($post['id'])
        ];

        return view('templates/header', $data)
            . view('post/edit')
            . view('templates/footer');
    }

    /**
     * บันทึกการแก้ไขโพสต์
     */
    public function update($id)
    {
        $post = $this->postModel->find($id);

        if (!$this->canEditPost($post)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'คุณไม่มีสิทธิ์แก้ไขโพสต์นี้'
            ]);
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
            'category_id' => $this->request->getPost('category'),
            'tags' => json_encode($this->request->getPost('tags') ?? [])
        ];

        if ($this->postModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'บันทึกการแก้ไขเรียบร้อยแล้ว'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง'
        ]);
    }

    /**
     * ลบโพสต์ work
     */
    public function delete($id)
    {
        $post = $this->postModel->find($id);

        if (!$this->canEditPost($post)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'คุณไม่มีสิทธิ์ลบโพสต์นี้'
            ]);
        }

        if ($this->postModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'ลบโพสต์เรียบร้อยแล้ว'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง'
        ]);
    }

    /**
     * กดถูกใจโพสต์
     */
    // public function like($id)
    // {
    //     if (!session()->get('user_id')) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'กรุณาเข้าสู่ระบบก่อนกดถูกใจ'
    //         ]);
    //     }

    //     $liked = model('PostLikeModel')->toggleLike($id, session()->get('user_id'));
    //     $likeCount = model('PostLikeModel')->getLikeCount($id);

    //     return $this->response->setJSON([
    //         'success' => true,
    //         'liked' => $liked,
    //         'like_count' => $likeCount
    //     ]);
    // }

    /**
     * เพิ่มความคิดเห็น
     */
    public function comment($id)
    {
        if (!session()->get('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'กรุณาเข้าสู่ระบบก่อนแสดงความคิดเห็น'
            ]);
        }

        $data = [
            'post_id' => $id,
            'author_id' => session()->get('user_id'),
            'content' => $this->request->getPost('content'),
            'parent_id' => $this->request->getPost('parent_id'),
            'status' => 'approved'
        ];

        if ($commentId = $this->commentModel->insert($data)) {
            $comment = $this->commentModel->getComment($commentId);
            return $this->response->setJSON([
                'success' => true,
                'comment' => $comment
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง'
        ]);
    }

    /**
     * ตรวจสอบสิทธิ์การแก้ไขโพสต์
     */
    protected function canEditPost($post)
    {
        if (!$post) return false;

        $userId = session()->get('user_id');
        $userRole = session()->get('user_role');

        return $post['author_id'] === $userId || $userRole === 'admin';
    }






    private function isRecentlyViewed($postId, $userId, $ipAddress)
    {
        $where = ['post_id' => $postId];

        if ($userId) {
            $where['user_id'] = $userId;
        } else {
            $where['ip_address'] = $ipAddress;
        }

        // ตรวจสอบการดูในช่วง 30 นาทีที่ผ่านมา
        return $this->where($where)
            ->where('viewed_at >', date('Y-m-d H:i:s', strtotime('-30 minutes')))
            ->countAllResults() > 0;
    }

    /**
     * ระบุประเภทอุปกรณ์
     */
    private function getDeviceType($agent)
    {
        if ($agent->isMobile()) return 'mobile';
        if ($agent->isTablet()) return 'tablet';
        if ($agent->isRobot()) return 'robot';
        return 'desktop';
    }

    /**
     * อัพเดทจำนวนการดูในตาราง posts ไม่ได้ใข้งาน
     */
    // private function updatePostViewCount($postId)
    // {
    //     $totalViews = $this->where('post_id', $postId)->countAllResults();

    //     return $this->db->table('posts')
    //         ->where('id', $postId)
    //         ->update(['view_count' => $totalViews]); //เจอปัญหาเวลาโพสโดยที่ยังไม่มีใครดู จะเป็น 1 เอง ไม่เกี่ยวกับ function +1 ใน comment
    // }

    /**
     * ดึงสถิติการดู
     */

    //ไม่ได้ใช้
    // public function getViewStats($postId)
    // {
    //     return [
    //         'total_views' => $this->where('post_id', $postId)->countAllResults(),
    //         'unique_viewers' => $this->where('post_id', $postId)
    //             ->groupBy('COALESCE(user_id, ip_address)')
    //             ->countAllResults(),
    //         'device_stats' => $this->select('device_type, COUNT(*) as count')
    //             ->where('post_id', $postId)
    //             ->groupBy('device_type')
    //             ->findAll(),
    //         'browser_stats' => $this->select('browser, COUNT(*) as count')
    //             ->where('post_id', $postId)
    //             ->groupBy('browser')
    //             ->findAll()
    //     ];
    // }

    // แสดงความคิดเห็น ใข้งาน
    // public function addComment($postId)
    // {
    //     if (!session()->get('user_id')) {
    //         return redirect()->to('/auth/login');
    //     }

    //     $content = $this->request->getPost('content');
    //     if (empty($content)) {
    //         return redirect()->back()->with('error', 'โปรดกรอกความคิดเห็น');
    //     }

    //     $db = \Config\Database::connect();
    //     $db->transStart();

    //     $this->commentModel->save([
    //         'post_id' => $postId,
    //         'author_id' => session()->get('user_id'),
    //         'content' => $content,
    //         'status' => 'approved', // Default to approved
    //     ]);

    //     //สำหรับ view comment count ใน index มีคอมเม้นใหม่เพิ่มเข้ามา จะ +1 ใน col comment_count
    //     $postModel = new \App\Models\PostModel();
    //     $postModel->where('id', $postId)->set('comment_count', 'comment_count + 1', false)->update();
    //     $db->transComplete();

    //     if ($db->transStatus() === false) {
    //         // Rollback and handle error
    //         return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการเพิ่มความคิดเห็น');
    //     }

    //     return redirect()->back()->with('success', 'ความคิดเห็นของคุณถูกโพสต์แล้ว');
    // }


    // // ส่วน comment reply
    // public function addCommentReply($commentId)
    // {
    //     // Check if the user is logged in
    //     if (!session()->get('user_id')) {
    //         return redirect()->to('/auth/login');
    //     }

    //     // Get the content for the reply
    //     $content = $this->request->getPost('content');
    //     if (empty($content)) {
    //         return redirect()->back()->with('error', 'โปรดกรอกความคิดเห็น');
    //     }

    //     // Get the original comment details
    //     $originalComment = $this->commentModel->find($commentId);

    //     if (!$originalComment) {
    //         return redirect()->back()->with('error', 'ความคิดเห็นที่ตอบกลับไม่พบ');
    //     }

    //     // Insert the reply into the database
    //     $this->commentModel->save([
    //         'post_id' => $originalComment['post_id'],
    //         'author_id' => session()->get('user_id'),
    //         'content' => $content,
    //         'parent_id' => $commentId,
    //         'status' => 'approved', //can set in db
    //     ]);

    //     return redirect()->back()->with('success', 'ตอบกลับความคิดเห็นของคุณถูกโพสต์แล้ว');
    // }

//     public function addComment($postId)
// {
//     // ตรวจสอบ CSRF token
//     $csrfToken = $this->request->getPost(csrf_token());
//     if (!$csrfToken || $csrfToken !== csrf_hash()) {
//         return $this->response->setJSON([
//             'success' => false,
//             'message' => 'Invalid CSRF token',
//         ]);
//     }

//     // ตรวจสอบข้อมูลที่ส่งมา
//     $content = $this->request->getPost('content');
//     if (empty($content)) {
//         return $this->response->setJSON([
//             'success' => false,
//             'message' => 'โปรดกรอกความคิดเห็น',
//         ]);
//     }

//     // บันทึกความคิดเห็นลงฐานข้อมูล
//     $db = \Config\Database::connect();
//     $db->transStart();

//     $this->commentModel->save([
//         'post_id' => $postId,
//         'author_id' => session()->get('user_id'),
//         'content' => $content,
//         'status' => 'approved', // Default to approved
//     ]);

//     // อัปเดตจำนวนความคิดเห็น
//     $postModel = new \App\Models\PostModel();
//     $postModel->where('id', $postId)->set('comment_count', 'comment_count + 1', false)->update();
//     $db->transComplete();

//     if ($db->transStatus() === false) {
//         // Rollback and handle error
//         return $this->response->setJSON([
//             'success' => false,
//             'message' => 'เกิดข้อผิดพลาดในการเพิ่มความคิดเห็น',
//         ]);
//     }

//     // ส่งกลับ JSON response
//     return $this->response->setJSON([
//         'success' => true,
//         'message' => 'ความคิดเห็นของคุณถูกโพสต์แล้ว',
//         'comment' => [
//             'content' => $content,
//             'author_name' => session()->get('username'), // เปลี่ยนเป็นข้อมูลผู้ใช้จริง
//             'created_at' => date('Y-m-d H:i:s'),
//         ],
//     ]);
// }

// public function addComment($postId)
// {
//     try {
//         // ตรวจสอบ CSRF token
//         $csrfToken = $this->request->getPost(csrf_token());
//         if (!$csrfToken || $csrfToken !== csrf_hash()) {
//             throw new \RuntimeException('Invalid CSRF token');
//         }

//         // ตรวจสอบข้อมูลที่ส่งมา
//         $content = $this->request->getPost('content');
//         if (empty($content)) {
//             throw new \RuntimeException('โปรดกรอกความคิดเห็น');
//         }

//         // บันทึกข้อมูล
//         $db = \Config\Database::connect();
//         $db->transStart();

//         $this->commentModel->save([
//             'post_id' => $postId,
//             'author_id' => session()->get('user_id'),
//             'content' => $content,
//             'status' => 'approved',
//         ]);

//         // อัปเดตจำนวนความคิดเห็น
//         $postModel = new \App\Models\PostModel();
//         $postModel->where('id', $postId)->set('comment_count', 'comment_count + 1', false)->update();
//         $db->transComplete();

//         if ($db->transStatus() === false) {
//             throw new \RuntimeException('เกิดข้อผิดพลาดในการเพิ่มความคิดเห็น');
//         }

//         // เตรียมข้อมูล response
//         $responseData = [
//             'success' => true,
//             'message' => 'ความคิดเห็นของคุณถูกโพสต์แล้ว',
//             'comment' => [
//                 'content' => $content,
//                 'author_name' => session()->get('username') ?? 'ผู้ใช้ไม่ระบุชื่อ',
//                 'created_at' => date('Y-m-d H:i:s'),
//             ]
//         ];

//         return $this->response
//             ->setStatusCode(200)
//             ->setJSON($responseData);

//     } catch (\Exception $e) {
//         return $this->response
//             ->setStatusCode(500)
//             ->setJSON([
//                 'success' => false,
//                 'message' => $e->getMessage()
//             ]);
//     }
// }

// public function addCommentReply($commentId)
// {
//     // ตรวจสอบ CSRF token
//     $csrfToken = $this->request->getPost(csrf_token());
//     if (!$csrfToken || $csrfToken !== csrf_hash()) {
//         return $this->response->setJSON([
//             'success' => false,
//             'message' => 'Invalid CSRF token',
//         ]);
//     }

//     // ตรวจสอบข้อมูลที่ส่งมา
//     $content = $this->request->getPost('content');
//     if (empty($content)) {
//         return $this->response->setJSON([
//             'success' => false,
//             'message' => 'โปรดกรอกความคิดเห็น',
//         ]);
//     }

//     // ดึงข้อมูลความคิดเห็นต้นทาง
//     $originalComment = $this->commentModel->find($commentId);
//     if (!$originalComment) {
//         return $this->response->setJSON([
//             'success' => false,
//             'message' => 'ไม่พบความคิดเห็นต้นทาง',
//         ]);
//     }

//     // บันทึกความคิดเห็นตอบกลับลงฐานข้อมูล
//     $this->commentModel->save([
//         'post_id' => $originalComment['post_id'],
//         'author_id' => session()->get('user_id'),
//         'content' => $content,
//         'parent_id' => $commentId,
//         'status' => 'approved', // Default to approved
//     ]);

//     // ส่งกลับ JSON responseเกิดข้อผิดพลาด: Invalid CSRF token
//     return $this->response->setJSON([
//         'success' => true,
//         'message' => 'ตอบกลับความคิดเห็นของคุณถูกโพสต์แล้ว',
//         'reply' => [
//             'content' => $content,
//             'author_name' => session()->get('username'), // เปลี่ยนเป็นข้อมูลผู้ใช้จริง
//             'created_at' => date('Y-m-d H:i:s'),
//         ],
//     ]);
// }


public function addComment($postId)
{
    try {
        // ตรวจสอบ CSRF token
        $csrfToken = $this->request->getPost(csrf_token());
        if (!$csrfToken || $csrfToken !== csrf_hash()) {
            throw new \RuntimeException('Invalid CSRF token');
        }

        // ตรวจสอบข้อมูลที่ส่งมา
        $content = $this->request->getPost('content');
        if (empty($content)) {
            throw new \RuntimeException('โปรดกรอกความคิดเห็น');
        }

        // บันทึกข้อมูล
        $db = \Config\Database::connect();
        $db->transStart();

        // บันทึกความคิดเห็น
        $this->commentModel->save([
            'post_id' => $postId,
            'author_id' => session()->get('user_id'),
            'content' => $content,
            'status' => 'approved',
        ]);

        // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
        $userModel = new UserModel(); // ใช้ UserModel ที่ถูกต้อง
        $user = $userModel->find(session()->get('user_id'));

        // อัปเดตจำนวนความคิดเห็น
        $postModel = new PostModel();
        $postModel->where('id', $postId)->set('comment_count', 'comment_count + 1', false)->update();
        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \RuntimeException('เกิดข้อผิดพลาดในการเพิ่มความคิดเห็น');
        }

        // ส่งกลับ JSON response
        return $this->response->setJSON([
            'success' => true,
            'message' => 'ความคิดเห็นของคุณถูกโพสต์แล้ว',
            'comment' => [
                'content' => $content,
                'author_name' => $user['username'],
                'created_at' => date('Y-m-d H:i:s'),
            ]
        ]);

    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

public function addCommentReply($commentId)
{
    try {
        // ตรวจสอบ CSRF token
        $csrfToken = $this->request->getPost(csrf_token());
        if (!$csrfToken || $csrfToken !== csrf_hash()) {
            throw new \RuntimeException('Invalid CSRF token');
        }

        // ตรวจสอบข้อมูลที่ส่งมา
        $content = $this->request->getPost('content');
        if (empty($content)) {
            throw new \RuntimeException('โปรดกรอกความคิดเห็น');
        }

        // ดึงข้อมูลความคิดเห็นต้นทาง
        $originalComment = $this->commentModel->find($commentId);
        if (!$originalComment) {
            throw new \RuntimeException('ไม่พบความคิดเห็นต้นทาง');
        }

        // บันทึกข้อมูล
        $db = \Config\Database::connect();
        $db->transStart();

        // บันทึกความคิดเห็นตอบกลับ
        $this->commentModel->save([
            'post_id' => $originalComment['post_id'],
            'author_id' => session()->get('user_id'),
            'content' => $content,
            'parent_id' => $commentId,
            'status' => 'approved',
        ]);

        // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
        $userModel = new UserModel();
        $user = $userModel->find(session()->get('user_id'));

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \RuntimeException('เกิดข้อผิดพลาดในการตอบกลับ');
        }

        // ส่งกลับ JSON response
        return $this->response->setJSON([
            'success' => true,
            'message' => 'ตอบกลับความคิดเห็นของคุณถูกโพสต์แล้ว',
            'reply' => [
                'content' => $content,
                'author_name' => $user['username'], // ใช้ข้อมูลจากฐานข้อมูล
                'created_at' => date('Y-m-d H:i:s'),
            ]
        ]);

    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

    // reaction
    public function reaction()
    {
        $json = $this->request->getJSON(true);
        $postReactionModel = new PostReactionModel();

        $postId = $json['post_id'] ?? null;
        $reactionType = $json['reaction_type'] ?? null;
        $userId = session()->get('user_id');

        // Debugging: Check if we have valid data
        if (!$postId || !$reactionType || !$userId) {
            log_message('error', 'Invalid input: ' . json_encode([
                'post_id' => $postId,
                'reaction_type' => $reactionType,
                'user_id' => $userId
            ]));
            return $this->response->setJSON([
                'success' => false,
                'message' => 'กรุณาเข้าสู่ระบบก่อนทำการแสดงความคิดเห็น',
            ]);
        }

        try {
            // Save or update the reaction
            $result = $postReactionModel->saveReaction($postId, $userId, $reactionType);

            // Get the updated reactions count
            $reactions = $postReactionModel->getReactionsByPost($postId);
            $reactionCounts = [];
            foreach ($reactions as $reaction) {
                $reactionCounts[$reaction['reaction_type']] = $reaction['count'];
            }

            return $this->response->setJSON([
                'success' => true,
                'reactionCount' => $reactionCounts[$reactionType] ?? 0,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in reaction processing: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while processing your reaction.',
            ]);
        }
    }

    public function getReactionCounts($postId)
    {
        $postReactionModel = new PostReactionModel();

        // Fetch the reaction counts for the specified post
        $reactions = $postReactionModel->getReactionsByPost($postId);
        $reactionCounts = [];
        foreach ($reactions as $reaction) {
            $reactionCounts[$reaction['reaction_type']] = $reaction['count'];
        }

        // Return the counts as a JSON response
        return $this->response->setJSON($reactionCounts);
    }

    // อัปเดตจำนวนความคิดเห็น กำลังใช้งาน ห้ามลบ
    public function updateCommentCount($postId)
    {
        $postModel = new PostModel();

        // เรียกฟังก์ชันใน Model เพื่ออัปเดต comment_count
        $commentCount = $postModel->updateCommentCount($postId);

        // ส่งข้อมูลกลับในรูป JSON (ถ้าต้องการ)
        return $this->response->setJSON([
            'status' => 'success',
            'postId' => $postId,
            'commentCount' => $commentCount,
        ]);
    }

    public function show($postId)
    {
        $postModel = new PostModel();
        $post = $postModel->getPostWithCommentCount($postId);

        if ($post) {
            return view('post_view', ['post' => $post]);
        } else {
            // จัดการกรณีที่ไม่พบโพสต์
        }
    }
}
