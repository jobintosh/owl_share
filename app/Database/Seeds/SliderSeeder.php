<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SliderSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'ก้าวล้ำไปกับ AI',
                'description' => 'เรียนรู้และแบ่งปันเทคโนโลยี AI ที่กำลังเปลี่ยนแปลงโลก',
                'image' => 'slides/ai-banner.jpg',
                'button_text' => 'อ่านเพิ่มเติม',
                'button_link' => '#tech-section',
                'background_position' => 'center',
                'overlay_opacity' => 0.50,
                'order' => 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'เรียนรู้ไปด้วยกัน',
                'description' => 'แบ่งปันความรู้และประสบการณ์เพื่อการเติบโตร่วมกัน',
                'image' => 'slides/learning-banner.jpg',
                'button_text' => 'เริ่มเรียนรู้',
                'button_link' => '#knowledge-section',
                'background_position' => 'center',
                'overlay_opacity' => 0.50,
                'order' => 2,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'ชุมชนแห่งการแบ่งปัน',
                'description' => 'ร่วมเป็นส่วนหนึ่งของชุมชนที่พร้อมเติบโตไปด้วยกัน',
                'image' => 'slides/community-banner.jpg',
                'button_text' => 'เริ่มแบ่งปัน',
                'button_link' => 'share',
                'background_position' => 'center',
                'overlay_opacity' => 0.50,
                'order' => 3,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('sliders')->insertBatch($data);
    }
}