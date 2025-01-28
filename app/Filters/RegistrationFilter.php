<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RegistrationFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // ตรวจสอบว่าเป็น POST request หรือไม่
        if ($request->getMethod() !== 'post') {
            return redirect()->to('/register');
        }

        // ตรวจสอบ CSRF
        $csrf = csrf_hash();
        if (!$csrf || $csrf !== $request->getPost('csrf_token')) {
            return redirect()->to('/register')
                           ->with('error', 'การยืนยันความปลอดภัยล้มเหลว กรุณาลองใหม่อีกครั้ง');
        }

        // ตรวจสอบการกรอกข้อมูล
        $rules = [
            'name' => [
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'กรุณากรอกชื่อ-นามสกุล',
                    'min_length' => 'ชื่อ-นามสกุลต้องมีความยาวอย่างน้อย 3 ตัวอักษร',
                    'max_length' => 'ชื่อ-นามสกุลต้องมีความยาวไม่เกิน 100 ตัวอักษร'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[users.email]',
                'errors' => [
                    'required' => 'กรุณากรอกอีเมล',
                    'valid_email' => 'รูปแบบอีเมลไม่ถูกต้อง',
                    'is_unique' => 'อีเมลนี้ถูกใช้งานแล้ว'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/]',
                'errors' => [
                    'required' => 'กรุณากรอกรหัสผ่าน',
                    'min_length' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร',
                    'regex_match' => 'รหัสผ่านต้องประกอบด้วยตัวอักษรพิมพ์เล็ก พิมพ์ใหญ่ ตัวเลข และอักขระพิเศษ'
                ]
            ],
            'confirm_password' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'กรุณายืนยันรหัสผ่าน',
                    'matches' => 'รหัสผ่านไม่ตรงกัน'
                ]
            ]
        ];

        $validation = \Config\Services::validation();
        $validation->setRules($rules);

        if (!$validation->run($request->getPost())) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $validation->getErrors());
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}