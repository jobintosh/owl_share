<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
        if (!$session->has('user_id')) {
            // ถ้าเป็น API request ให้ส่ง JSON response
            if (strpos($request->getPath(), 'api/') === 0) {
                return service('response')
                    ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                    ->setJSON([
                        'status' => 401,
                        'error' => true,
                        'messages' => 'Unauthorized access'
                    ]);
            }
            
            // ถ้าเป็น web request ให้ redirect ไปหน้า login
            return redirect()->to('../auth/login')->with('error', 'กรุณาเข้าสู่ระบบก่อนใช้งาน');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something after the request is processed
    }
}