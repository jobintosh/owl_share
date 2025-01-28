<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class Posts extends ResourceController
{
    protected $modelName = 'App\Models\PostModel';
    protected $format    = 'json';

    public function index($category = null)
    {
        // ในโปรเจ็กต์จริงควรดึงข้อมูลจาก Database
        $sampleData = [
            'knowledge' => [
                [
                    'title' => 'การเรียนรู้ตลอดชีวิต',
                    'excerpt' => 'เทคนิคการพัฒนาตนเองอย่างต่อเนื่อง',
                    'image' => 'https://images.unsplash.com/photo-1516321497487-e288fb19713f',
                    'author' => 'วิทยากร สอนดี',
                    'date' => '2024-10-12',
                    'views' => 1200,
                    'likes' => 180
                ],
                [
                    'title' => 'เทคนิคการจดบันทึก',
                    'excerpt' => 'วิธีจดบันทึกให้มีประสิทธิภาพ',
                    'image' => 'https://images.unsplash.com/photo-1517842645767-c639042777db',
                    'author' => 'นักเขียน ใจดี',
                    'date' => '2024-10-11',
                    'views' => 980,
                    'likes' => 150
                ]
            ],
            'technology' => [
                [
                    'title' => 'Blockchain เบื้องต้น',
                    'excerpt' => 'ทำความรู้จักกับเทคโนโลยี Blockchain',
                    'image' => 'https://images.unsplash.com/photo-1639762681057-408e52192e55',
                    'author' => 'Tech Guide',
                    'date' => '2024-10-13',
                    'views' => 2500,
                    'likes' => 320
                ],
                [
                    'title' => 'IoT สำหรับบ้าน',
                    'excerpt' => 'แนะนำอุปกรณ์ IoT สำหรับบ้านอัจฉริยะ',
                    'image' => 'https://images.unsplash.com/photo-1558346490-a72e53ae2d4f',
                    'author' => 'Smart Home',
                    'date' => '2024-10-12',
                    'views' => 1800,
                    'likes' => 260
                ]
            ],
            'news' => [
                [
                    'title' => 'เทรนด์เทคโนโลยี 2024',
                    'excerpt' => 'อัพเดทเทรนด์เทคโนโลยีล่าสุด',
                    'image' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c',
                    'author' => 'Tech Report',
                    'date' => '2024-10-14',
                    'views' => 3200,
                    'likes' => 420
                ],
                [
                    'title' => 'การศึกษาไทยในยุคดิจิทัล',
                    'excerpt' => 'การเปลี่ยนแปลงของการศึกษาไทย',
                    'image' => 'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45',
                    'author' => 'Education News',
                    'date' => '2024-10-13',
                    'views' => 2100,
                    'likes' => 280
                ]
            ]
        ];

        if (!isset($sampleData[$category])) {
            return $this->failNotFound('Category not found');
        }

        return $this->respond([
            'status' => 200,
            'error' => false,
            'posts' => $sampleData[$category]
        ]);
    }
}