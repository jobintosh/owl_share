<?php

namespace App\Models;

use CodeIgniter\Model;

class SliderModel extends Model
{
    protected $table = 'sliders';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'title',
        'description',
        'image',
        'button_text',
        'button_link',
        'background_position',
        'overlay_opacity',
        'order',
        'status'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'description' => 'required|min_length[10]',
        'image' => 'required',
        'status' => 'required|in_list[active,inactive]'
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'กรุณาใส่หัวข้อ',
            'min_length' => 'หัวข้อต้องมีความยาวอย่างน้อย 3 ตัวอักษร'
        ],
        'description' => [
            'required' => 'กรุณาใส่คำอธิบาย',
            'min_length' => 'คำอธิบายต้องมีความยาวอย่างน้อย 10 ตัวอักษร'
        ],
        'image' => [
            'required' => 'กรุณาเลือกรูปภาพ'
        ]
    ];

    // ดึง Slides ที่กำลังใช้งาน
    public function getActiveSlides()
    {
        return $this->where('status', 'active')
                    ->orderBy('order', 'ASC')
                    ->findAll();
    }

    // อัพเดทลำดับ Slides
    public function updateOrder($slides)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($slides as $order => $id) {
            $this->update($id, ['order' => $order]);
        }

        $db->transComplete();
        return $db->transStatus();
    }

    // ตรวจสอบและปรับขนาดรูปภาพ
    public function processImage($image)
    {
        $maxWidth = 1920;
        $maxHeight = 1080;

        // ตรวจสอบขนาดรูปภาพ
        list($width, $height) = getimagesize($image['tmp_name']);
        
        // ถ้าขนาดเล็กกว่าที่กำหนดไม่ต้องปรับ
        if ($width <= $maxWidth && $height <= $maxHeight) {
            return $image;
        }

        // คำนวณขนาดใหม่
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = round($width * $ratio);
        $newHeight = round($height * $ratio);

        // สร้างรูปภาพใหม่
        $sourceImage = imagecreatefromstring(file_get_contents($image['tmp_name']));
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // บันทึกรูปภาพใหม่
        $newFileName = tempnam(sys_get_temp_dir(), 'slider_');
        imagejpeg($newImage, $newFileName, 90);

        // คืนค่าข้อมูลรูปภาพใหม่
        $image['tmp_name'] = $newFileName;
        $image['size'] = filesize($newFileName);

        imagedestroy($sourceImage);
        imagedestroy($newImage);

        return $image;
    }

    // ดึงข้อมูลสถิติ
    public function getStats()
    {
        return [
            'total' => $this->countAll(),
            'active' => $this->where('status', 'active')->countAllResults(),
            'last_updated' => $this->orderBy('updated_at', 'DESC')->first()['updated_at'] ?? null
        ];
    }
}