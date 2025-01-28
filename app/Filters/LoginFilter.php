<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LoginFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // ถ้าผู้ใช้ล็อกอินอยู่แล้ว ให้ redirect ไปหน้าหลัก
        if (session()->get('user_id')) {
            return redirect()->to('/');
        }

        // ตรวจสอบ Remember Token
        $rememberToken = get_cookie('remember_token');
        if ($rememberToken) {
            $userModel = model('UserModel');
            $user = $userModel->where('remember_token', $rememberToken)->first();
            
            if ($user) {
                // Auto Login
                session()->set([
                    'user_id' => $user['id'],
                    'user_name' => $user['name'],
                    'user_email' => $user['email'],
                    'user_role' => $user['role'],
                    'user_avatar' => $user['avatar']
                ]);

                // อัพเดทเวลาล็อกอินล่าสุด
                $userModel->update($user['id'], [
                    'last_login' => date('Y-m-d H:i:s'),
                    'last_ip' => $request->getIPAddress()
                ]);

                // บันทึก Activity Log
                model('ActivityLogModel')->logActivity([
                    'user_id' => $user['id'],
                    'action' => 'auto_login',
                    'description' => 'เข้าสู่ระบบอัตโนมัติด้วย Remember Token',
                    'ip_address' => $request->getIPAddress(),
                    'user_agent' => $request->getUserAgent()->getAgentString()
                ]);

                return redirect()->to('/');
            }

            // ถ้า token ไม่ถูกต้องให้ลบ cookie
            delete_cookie('remember_token');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // ไม่ต้องทำอะไรหลังจากการตรวจสอบ
    }
}