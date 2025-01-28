<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RateLoginFilter implements FilterInterface
{
    protected $maxAttempts = 5; // จำนวนครั้งที่พยายามล็อกอินสูงสุด
    protected $decayMinutes = 30; // เวลาที่ต้องรอหลังจากพยายามล็อกอินเกินกำหนด (นาที)

    public function before(RequestInterface $request, $arguments = null)
    {
        // ตรวจสอบเฉพาะ POST request
        if ($request->getMethod() !== 'post') {
            return;
        }

        $ip = $request->getIPAddress();
        $email = $request->getPost('email');
        
        if ($this->hasTooManyLoginAttempts($ip, $email)) {
            $minutesLeft = $this->getMinutesLeft($ip, $email);
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', "คุณพยายามเข้าสู่ระบบมากเกินไป กรุณารอ {$minutesLeft} นาที");
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // ถ้าล็อกอินไม่สำเร็จ
        if ($response->getStatusCode() === 302 && session()->has('error')) {
            $ip = $request->getIPAddress();
            $email = $request->getPost('email');
            
            $this->incrementLoginAttempts($ip, $email);
        }
    }

    protected function hasTooManyLoginAttempts($ip, $email)
    {
        $db = \Config\Database::connect();
        $attempts = $db->table('login_attempts')
            ->where('ip_address', $ip)
            ->where('email', $email)
            ->where('created_at >', date('Y-m-d H:i:s', strtotime("-{$this->decayMinutes} minutes")))
            ->countAllResults();

        return $attempts >= $this->maxAttempts;
    }

    protected function incrementLoginAttempts($ip, $email)
    {
        $db = \Config\Database::connect();
        $db->table('login_attempts')->insert([
            'ip_address' => $ip,
            'email' => $email,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    protected function getMinutesLeft($ip, $email)
    {
        $db = \Config\Database::connect();
        $lastAttempt = $db->table('login_attempts')
            ->where('ip_address', $ip)
            ->where('email', $email)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRow();

        if ($lastAttempt) {
            $lastAttemptTime = strtotime($lastAttempt->created_at);
            $timeLeft = ($lastAttemptTime + ($this->decayMinutes * 60)) - time();
            return ceil($timeLeft / 60);
        }

        return 0;
    }

    protected function clearLoginAttempts($ip, $email)
    {
        $db = \Config\Database::connect();
        $db->table('login_attempts')
            ->where('ip_address', $ip)
            ->where('email', $email)
            ->delete();
    }
}