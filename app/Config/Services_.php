<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Email\Email;

class Auth extends BaseController
{
    public function sendResetEmail($email)
    {
        // สร้างตัวแปร emailService จากการใช้ Config\Services::email()
        $emailService = \Config\Services::email();

        // ตั้งค่า SMTP ในโค้ดโดยตรง
        $config = [
            'protocol'  => 'smtp',
            'SMTPHost'  => 'smtp.office365.com', // SMTP server
            'SMTPUser'  => 'datacenter@dusit.ac.th',
            'SMTPPass'  => 'everyThing@arit',
            'SMTPPort'  => 587, // พอร์ตที่ใช้งาน
            'mailType'  => 'html', // ใช้ HTML ในการส่ง
            'charset'   => 'utf-8',
            'wordWrap'  => true,
        ];

        // โหลดการตั้งค่าของ emailService
        $emailService->initialize($config);

        // กำหนดค่าของอีเมล
        $emailService->setFrom('datacenter@dusit.ac.th', 'Reset Password - OWL Share');
        $emailService->setTo($email);
        $emailService->setSubject('Password Reset Link');

        // เนื้อหาของอีเมล
        $message = "กรุณาคลิกที่ลิงก์ด้านล่างเพื่อรีเซ็ตรหัสผ่านของคุณ:\n";
        $message .= base_url('/auth/forgot' . $resetToken); // ใส่ลิงก์สำหรับการรีเซ็ตรหัสผ่าน
        $emailService->setMessage($message);

        // ส่งอีเมล
        if ($emailService->send()) {
            return redirect()->to('/auth/forgot')->with('success', 'ลิงก์รีเซ็ตรหัสผ่านถูกส่งไปยังอีเมลของคุณแล้ว');
        } else {
            return redirect()->to('/auth/forgot')->with('error', 'ไม่สามารถส่งอีเมลได้ กรุณาลองใหม่');
        }
    }
}
