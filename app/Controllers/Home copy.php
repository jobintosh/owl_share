<?php


namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
// ทดสอบคอมเม้น
$commentModel = model('CommentModel');

class Home extends BaseController
{
    protected $postModel;
    protected $categoryModel;
    protected $sliderModel;



    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // โหลด Model ที่จำเป็น
        $this->postModel = model('PostModel');
        $this->categoryModel = model('CategoryModel');
    }

    public function index()
    {


        // ดึงข้อมูลสำหรับ Hero Slider
        $heroSlides = [
            [
                'title' => 'ก้าวล้ำไปกับ AI',
                'description' => 'เรียนรู้และแบ่งปันเทคโนโลยี AI ที่กำลังเปลี่ยนแปลงโลก',
                'button' => [
                    'text' => 'อ่านเพิ่มเติม',
                    'link' => '#tech-section'
                ],
                'background' => ''
            ]
        ];

        $sliderData = $this->getSliderData();

        // ดึงโพสต์ยอดนิยม
        $trendingPosts = $this->postModel->getTrendingPosts();

        $categoryCounts = $this->categoryModel->getCategoryCounts();

        $categorizedPosts = [
            'knowledge' => $this->postModel->getPostsByCategory('knowledge', 6),
            'technology' => $this->postModel->getPostsByCategory('technology', 6),
            'news' => $this->postModel->getPostsByCategory('news', 6)
        ];




        $tabs = [
            'trending' => [
                'title' => 'กำลังได้รับความนิยม',
                'icon' => 'fas fa-fire',
                'posts' => $this->postModel->getTrendingPosts(12) // ส่วนแรกจำนวน '5' 
            ],
            'knowledge' => [
                'title' => 'การศึกษา',
                'icon' => 'fas fa-book',
                'posts' => $this->postModel->getPostsByCategory('2', 6)
            ],
            'technology' => [
                'title' => 'เทคโนโลยี',
                'icon' => 'fas fa-microchip',
                'posts' => $this->postModel->getPostsByCategory('1', 6)
            ],
            'artificial-intelligence' => [
                'title' => 'ปัญญาประดิษฐ์',
                'icon' => 'fas fa-newspaper',
                'posts' => $this->postModel->getPostsByCategory('5', 6)
            ],
            'online-learning' => [
                'title' => 'การเรียนออนไลน์',
                'icon' => 'fas fa-newspaper',
                'posts' => $this->postModel->getPostsByCategory('7', 6)
            ],
            'study-techniques' => [
                'title' => 'เทคนิคการเรียน',
                'icon' => 'fas fa-newspaper',
                'posts' => $this->postModel->getPostsByCategory('8', 6)
            ],
            'self-development' => [
                'title' => 'การพัฒนาตนเอง',
                'icon' => 'fas fa-newspaper',
                'posts' => $this->postModel->getPostsByCategory('9', 6)
            ]
        ];



        // ดึงหมวดหมู่ทั้งหมดที่ใช้งานอยู่
        $categories = $this->categoryModel->getActiveCategories();

      
        // ดึงสถิติต่างๆ เนื้อหาที่แบ่งปัน สมาชิกทั้งหมด ความคิดเห็นที่อนุมัติแล้ว ที่อยู้่ล่างสุดของ index
        $stats = [
            'total_posts' => $this->postModel->where('status', 'published')->countAllResults(),
            'total_users' => model('UserModel')->where('status', 'active')->countAllResults(),
            'total_comments' => model('CommentModel')->where('status', 'approved')->countAllResults()
        ];


        // ดึงโพสต์ตามหมวดหมู่
        $data = [
            'title' => 'ShareHub - แพลตฟอร์มแบ่งปันความรู้',
            'meta_description' => 'แพลตฟอร์มแบ่งปันความรู้และประสบการณ์ เพื่อการเรียนรู้ที่ไม่มีที่สิ้นสุด',

            'tabs' => $tabs,
            'categories' => $categories,
            'stats' => $stats,
            'popularTags' => $this->getPopularTags(),

            'sliderData' => $sliderData,
            // 'sliderData' => $$heroSlides,
            'trendingPosts' => $trendingPosts,
            'categorizedPosts' => $categorizedPosts,
            'categories' => $this->categoryModel->getActiveCategories(),
            'category_counts' => $categoryCounts
        ];

        return view('templates/header', $data)
            . view('components/navbar')
            . view('components/hero_slider') //herosiider ลบเพิ่มได้ แก้ในไฟล์
            . view('components/content_tabs') //content หน้าหลักและค่าต่างๆ
            . view('templates/footer');

            
        // // ดึงจำนวนโพสต์ในแต่ละหมวดหมู่


        // $data = [
        //     'title' => 'ShareHub - แพลตฟอร์มแบ่งปันความรู้',
        //     'meta_description' => 'แพลตฟอร์มแบ่งปันความรู้และประสบการณ์ เพื่อการเรียนรู้ที่ไม่มีที่สิ้นสุด',
        //     'hero_slides' => $heroSlides,
        //     'trending_posts' => $trendingPosts,
        //     'categorized_posts' => $categorizedPosts,
        //     'category_counts' => $categoryCounts
        // ];


        // // เพิ่มข้อมูล SEO
        // $this->setSEOMetadata($data);

        // return view('templates/header', $data)
        //      . view('components/navbar')
        //      . view('components/hero_slider')
        //     // . view('components/content_tabs')
        //      . view('templates/footer');
    }

    

    //ไม่ได้ใช้
    // protected function getStats()
    // {
    //     $commentModel = new \App\Models\CommentModel();
    //     $userModel = new \App\Models\UserModel();

    //     return [
    //         'total_posts' => $this->postModel->where('status', 'published')
    //             ->where('deleted_at IS NULL')
    //             ->countAllResults(),

    //         'total_users' => $userModel->where('status', 'active')
    //             ->countAllResults(),

    //         'total_comments' => $commentModel->where('status', 'approved')
    //             ->countAllResults(),

    //         'total_categories' => $this->categoryModel->where('status', 'active')
    //             ->countAllResults(),

    //         'total_views' => $this->postModel->selectSum('view_count')
    //             ->where('status', 'published')
    //             ->where('deleted_at IS NULL')
    //             ->get()
    //             ->getRow()
    //             ->view_count ?? 0,

    //         'total_likes' => $this->postModel->selectSum('like_count')
    //             ->where('status', 'published')
    //             ->where('deleted_at IS NULL')
    //             ->get()
    //             ->getRow()
    //             ->like_count ?? 0
    //     ];
    // }


    //ใช้งาน
    protected function getPopularTags($limit = 10)
    {
        // ดึง tags ที่ใช้บ่อยจากโพสต์ทั้งหมด
        $posts = $this->postModel->where('status', 'published')
            ->orderBy('created_at', 'DESC')
            ->findAll(100);

        $tagCounts = [];
        foreach ($posts as $post) {
            if (!empty($post['tags'])) {
                $tags = json_decode($post['tags'], true);
                foreach ($tags as $tag) {
                    if (!isset($tagCounts[$tag])) {
                        $tagCounts[$tag] = 0;
                    }
                    $tagCounts[$tag]++;
                }
            }
        }

        // เรียงลำดับตามความนิยม
        arsort($tagCounts);

        // ตัดเอาเฉพาะ tags ตามจำนวนที่ต้องการ
        return array_slice($tagCounts, 0, $limit, true);
    }

    //ใช้งาน
    protected function getSliderData()
    {
        // ดึงข้อมูล Slider จาก Model หรือกำหนดค่าตายตัว
        return [
            [
                'id' => 1,
                'title' => 'ก้าวล้ำไปกับ AI',
                'description' => 'เรียนรู้และแบ่งปันเทคโนโลยี AI ที่กำลังเปลี่ยนแปลงโลก',
                'image' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995',
                'button' => [
                    'text' => 'อ่านเพิ่มเติม',
                    'link' => '#tech-section'
                ],
                'background_position' => 'center',
                'overlay_opacity' => '0.5'
            ]


        ];
    }

    // สำหรับจัดการ AJAX request เพื่อดึงข้อมูล Slider
    public function getSlides()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $slides = $this->getSliderData();
        return $this->response->setJSON([
            'success' => true,
            'slides' => $slides
        ]);
    }

    // อัพเดทลำดับ Slides
    public function updateSlideOrder()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $slideIds = $this->request->getPost('slide_ids');

        // อัพเดทลำดับใน database
        foreach ($slideIds as $order => $id) {
            $this->sliderModel->update($id, ['order' => $order]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'อัพเดทลำดับเรียบร้อยแล้ว'
        ]);
    }

    // แก้ไข Slide
    public function editSlide($id)
    {
        if (!session()->get('isAdmin')) {
            return redirect()->back();
        }

        if ($this->request->getMethod() === 'post') {
            $data = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'button_text' => $this->request->getPost('button_text'),
                'button_link' => $this->request->getPost('button_link'),
                'status' => $this->request->getPost('status')
            ];

            // จัดการรูปภาพถ้ามีการอัพโหลด slide
            $image = $this->request->getFile('image');
            if ($image && $image->isValid()) {
                $newName = $image->getRandomName();
                $image->move(FCPATH . 'uploads/slides', $newName);
                $data['image'] = 'uploads/slides/' . $newName;
            }

            $this->sliderModel->update($id, $data);
            return redirect()->to('/admin/slides')->with('success', 'อัพเดท Slide เรียบร้อยแล้ว');
        }

        $data = [
            'title' => 'แก้ไข Slide',
            'slide' => $this->sliderModel->find($id)
        ];

        return view('admin/slide_edit', $data);
    }

    // ลบ Slide
    public function deleteSlide($id)
    {
        if (!session()->get('isAdmin')) {
            return $this->response->setStatusCode(403);
        }

        $slide = $this->sliderModel->find($id);
        if ($slide && !empty($slide['image'])) {
            unlink(FCPATH . $slide['image']); // ลบไฟล์รูปภาพ
        }

        $this->sliderModel->delete($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'ลบ Slide เรียบร้อยแล้ว'
            ]);
        }

        return redirect()->to('/admin/slides')->with('success', 'ลบ Slide เรียบร้อยแล้ว');
    }


    protected function setSEOMetadata(&$data)
    {
        $data['meta_tags'] = [
            ['name' => 'description', 'content' => $data['meta_description']],
            ['property' => 'og:title', 'content' => $data['title']],
            ['property' => 'og:description', 'content' => $data['meta_description']],
            ['property' => 'og:type', 'content' => 'website'],
            ['property' => 'og:url', 'content' => current_url()],
            ['name' => 'twitter:card', 'content' => 'summary_large_image']
        ];

        // เพิ่ม Open Graph Image ถ้ามีรูปภาพ
        if (!empty($data['trending_posts'])) {
            $firstPost = $data['trending_posts'][0];
            if (!empty($firstPost['image'])) {
                $data['meta_tags'][] = [
                    'property' => 'og:image',
                    'content' => base_url($firstPost['image'])
                ];
            }
        }
    }

    public function category($slug = null)
    {
        if (!$slug) {
            return redirect()->to('/');
        }

        // ตรวจสอบว่าหมวดหมู่มีอยู่จริง
        $category = $this->categoryModel->where('slug', $slug)->first();
        if (!$category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // ดึงโพสต์ในหมวดหมู่นี้
        $posts = $this->postModel->getPostsByCategory($category['id'], 12);

        $data = [
            'title' => $category['name'] . ' - ShareHub',
            'meta_description' => 'รวมเนื้อหาในหมวดหมู่ ' . $category['name'],
            'category' => $category,
            'posts' => $posts
        ];

        $this->setSEOMetadata($data);

        return view('templates/header', $data)
            . view('components/navbar')
            . view('pages/category')
            . view('templates/footer');
    }

    public function search()
    {
        $keyword = $this->request->getGet('q');
        $category = $this->request->getGet('category');
        $sort = $this->request->getGet('sort') ?? 'latest';

        // ค้นหาโพสต์
        $posts = $this->postModel->searchPosts($keyword, $category, $sort);

        $data = [
            'title' => 'ผลการค้นหา: ' . $keyword . ' - ShareHub',
            'meta_description' => 'ผลการค้นหาสำหรับ ' . $keyword,
            'keyword' => $keyword,
            'category' => $category,
            'sort' => $sort,
            'posts' => $posts,
            'total' => count($posts)
        ];

        return view('templates/header', $data)
            . view('components/navbar')
            . view('pages/search_results')
            . view('templates/footer');
    }

    public function manifest()
    {
        $manifest = [
            'name' => 'ShareHub',
            'short_name' => 'ShareHub',
            'description' => 'แพลตฟอร์มแบ่งปันความรู้และประสบการณ์',
            'start_url' => '/',
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => '#0d6efd',
            'icons' => [
                [
                    'src' => '/images/icon-192x192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png'
                ],
                [
                    'src' => '/images/icon-512x512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png'
                ]
            ]
        ];

        return $this->response->setJSON($manifest);
    }

    

    public function offline()
    {
        return view('pages/offline');
    }

    public function sitemap()
    {
        $categories = $this->categoryModel->findAll();
        $posts = $this->postModel->getAllPublishedPosts();

        $data = [
            'categories' => $categories,
            'posts' => $posts
        ];

        $this->response->setContentType('application/xml');
        return view('sitemap', $data);
    }
}

