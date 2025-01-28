<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\I18n\Time;
use App\Models\UserModel;
use App\Models\SocialAuthModel;


class Auth extends BaseController
{
    protected $session;
    protected $userModel;
    protected $socialAuthModel;
    protected $config;
    protected $facebookService;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // $this->facebookService = new \App\Libraries\FacebookService();

        $this->session = \Config\Services::session();
        $this->userModel = model('UserModel');
        // $this->socialAuthModel = model('SocialAuthModel');

        // โหลดค่าคอนฟิก OAuth
        $this->config = config('OAuth');
    }

    public function __construct()
    {
        // โหลด UserModel
        $this->userModel = new UserModel();
        $this->socialAuthModel = new SocialAuthModel();
    }

    // แสดงหน้า Login
    public function login()
    {

        if ($this->session->get('user_id')) {
            return redirect()->to('/');
        }

        $data = [
            'title' => 'เข้าสู่ระบบ - ShareHub',
            // 'validation' => $this->validation,
            'facebook_url' => $this->getFacebookLoginUrl(),
            //   'google_url' => $this->getGoogleLoginUrl()
        ];

        //    return view('auth/login', $data);

        return  view('templates/header', $data)
            . view('components/navbar')
            . view('auth/login')
            . view('templates/footer');
    }

    // ดำเนินการ Login
    public function doLogin()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];

        // ตรวจสอบข้อมูลที่กรอก
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $remember = (bool)$this->request->getPost('remember');

        // ค้นหาผู้ใช้จากอีเมล
        $user = $this->userModel->getUserByEmail($email);

        // ตรวจสอบว่าอีเมลถูกต้องหรือไม่
        if (!$user) {
            session()->setFlashdata('error', 'อีเมลไม่ถูกต้อง');
            return redirect()->to('/auth/login')->withInput();
        }

        // ตรวจสอบรหัสผ่าน
        if (!password_verify($password, $user['password'])) {
            session()->setFlashdata('error', 'รหัสผ่านไม่ถูกต้อง');
            return redirect()->to('/auth/login')->withInput();
        }

        // ตรวจสอบสถานะบัญชีผู้ใช้
        if ($user['status'] !== 'active') {
            session()->setFlashdata('error', 'บัญชีของคุณถูกระงับ กรุณาติดต่อผู้ดูแลระบบ');
            return redirect()->to('/auth/login')->withInput();
        }

        // สร้าง Session
        $this->createUserSession($user);

        // จดจำการเข้าสู่ระบบ
        if ($remember) {
            $this->createRememberToken($user['id']);
        }

        // บันทึกประวัติการเข้าสู่ระบบ
        $this->logLogin($user['id']);

        // แสดงผลสำเร็จและเปลี่ยนเส้นทาง
        session()->setFlashdata('success', 'เข้าสู่ระบบสำเร็จ');
        return redirect()->to('/');
    }


    public function facebookLogin()
    {
        try {
            // สร้าง state token เพื่อป้องกัน CSRF
            $state = bin2hex(random_bytes(16));
            session()->set('fb_state', $state);

            // สร้าง Login URL
            $loginUrl = $this->facebookService->getLoginUrl($state);
            return redirect()->to($loginUrl);
        } catch (\Exception $e) {
            log_message('error', 'Facebook login error: ' . $e->getMessage());
            return redirect()->to('login')
                ->with('error', 'เกิดข้อผิดพลาดในการเชื่อมต่อกับ Facebook');
        }
    }

    /**
     * ยกเลิกการเชื่อมต่อกับ Facebook
     */
    // public function disconnectFacebook()
    // {
    //     try {
    //         $userId = session('user_id');
    //         if (!$userId) {
    //             throw new \Exception('User not logged in');
    //         }

    //         $this->socialAuthModel->removeConnection($userId, 'facebook');
    //         return redirect()->to('profile/connections')
    //             ->with('success', 'ยกเลิกการเชื่อมต่อกับ Facebook เรียบร้อยแล้ว');
    //     } catch (\Exception $e) {
    //         log_message('error', 'Facebook disconnect error: ' . $e->getMessage());
    //         return redirect()->back()
    //             ->with('error', 'เกิดข้อผิดพลาดในการยกเลิกการเชื่อมต่อ');
    //     }
    // }

    // แสดงหน้าสมัครสมาชิก
    public function register()
    {
        if ($this->session->get('user_id')) {
            return redirect()->to('/');
        }

        $data = [
            'title' => 'สมัครสมาชิก - ShareHub',
            'validation' => $this->validation,
            // 'facebook_url' => $this->getFacebookLoginUrl(),
            // 'google_url' => $this->getGoogleLoginUrl()
        ];

        return view('templates/header', $data)
            . view('components/navbar')
            . view('auth/register')
            . view('templates/footer');
    }

    // ดำเนินการสมัครสมาชิก
    public function doregister()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',  // เพิ่ม validation สำหรับ username
            'password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // รับข้อมูลจากฟอร์ม
        $userData = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'username' => $this->request->getPost('username'),
            // 'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),  //มีปัญหาเรื่อง hash ไม่ตรง
            'password' => $this->request->getPost('password'),
            'role' => 'user',
            'status' => 'active'
        ];

        try {
            // บันทึกข้อมูลผู้ใช้ใหม่
            $userId = $this->userModel->insert($userData);

            // ส่งอีเมลยืนยันการสมัครสมาชิก
            // $this->sendVerificationEmail($userData['email']);

            return redirect()->to('login')->with('success', 'สมัครสมาชิกสำเร็จ กรุณาตรวจสอบอีเมลเพื่อยืนยันการสมัครสมาชิก');
        } catch (\Exception $e) {
            log_message('error', 'Registration error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
        }
    }




    // Facebook Login Callback
    public function facebookCallback()
    {
        $fb = new \Facebook\Facebook([
            'app_id' => $this->config->facebook['app_id'],
            'app_secret' => $this->config->facebook['app_secret'],
            'default_graph_version' => 'v21.0'
        ]);

        try {
            $helper = $fb->getRedirectLoginHelper();
            $accessToken = $helper->getAccessToken();

            if (!$accessToken) {
                throw new \Exception('Failed to get access token');
            }

            // ดึงข้อมูลผู้ใช้จาก Facebook
            $response = $fb->get('/me?fields=id,name,email,picture.type(large)', $accessToken);
            $fbUser = $response->getGraphUser();

            // ตรวจสอบและสร้าง/อัพเดทข้อมูลผู้ใช้
            $user = $this->handleSocialLogin('facebook', $fbUser);

            // สร้าง Session
            $this->createUserSession($user);

            return redirect()->to('/')->with('success', 'เข้าสู่ระบบด้วย Facebook สำเร็จ');
        } catch (\Exception $e) {
            log_message('error', 'Facebook login error: ' . $e->getMessage());
            return redirect()->to('login')->with('error', 'เกิดข้อผิดพลาดในการเข้าสู่ระบบด้วย Facebook');
        }
    }

    // Google Login Callback
    // public function googleCallback()
    // {
    //     $client = new \Google_Client();
    //     $client->setClientId($this->config->google['client_id']);
    //     $client->setClientSecret($this->config->google['client_secret']);
    //     $client->setRedirectUri(base_url('auth/google-callback'));

    //     try {
    //         $token = $client->fetchAccessTokenWithAuthCode($this->request->getGet('code'));
    //         $client->setAccessToken($token);

    //         // ดึงข้อมูลผู้ใช้จาก Google
    //         $service = new \Google_Service_Oauth2($client);
    //         $googleUser = $service->userinfo->get();

    //         // ตรวจสอบและสร้าง/อัพเดทข้อมูลผู้ใช้
    //         $user = $this->handleSocialLogin('google', $googleUser);

    //         // สร้าง Session
    //         $this->createUserSession($user);

    //         return redirect()->to('/')->with('success', 'เข้าสู่ระบบด้วย Google สำเร็จ');
    //     } catch (\Exception $e) {
    //         log_message('error', 'Google login error: ' . $e->getMessage());
    //         return redirect()->to('login')->with('error', 'เกิดข้อผิดพลาดในการเข้าสู่ระบบด้วย Google');
    //     }
    // }

    // Logout
    public function logout()
    {
        // ลบ Remember Token ถ้ามี

        //    echo $this->session->get('user_id');
        //    exit;

        if ($this->session->get('user_id')) {
            //   $this->userModel->update($this->session->get('user_id'), ['remember_token' => null]);
        }

        // ลบ Cookie
        //   delete_cookie('remember_token');

        // ลบ Session
        $this->session->destroy();

        return redirect()->to('../auth/login')->with('success', 'ออกจากระบบสำเร็จ');
    }

    // Protected Methods
    public function handleSocialLogin($provider, $socialUser)
    {
        $socialId = $socialUser->getId();
        $email = $socialUser->getEmail();
        $name = $socialUser->getName();
        $picture = $provider === 'facebook' ?
            $socialUser->getPicture()->getUrl() :
            $socialUser->getPicture();
    
        // ตรวจสอบว่า socialAuthModel และ userModel ถูกโหลดหรือไม่
        if ($this->socialAuthModel == null || $this->userModel == null) {
            throw new \Exception('Model ไม่ถูกโหลด');
        }
    
        // ตรวจสอบการเชื่อมต่อ Social Login
        $socialAuth = $this->socialAuthModel->where([
            'provider' => $provider,
            'social_id' => $socialId
        ])->first();
    
        if ($socialAuth) {
            // อัพเดทข้อมูลถ้ามีการเปลี่ยนแปลง
            $this->userModel->update($socialAuth['user_id'], [
                'name' => $name,
                'avatar' => $picture
            ]);
            return $this->userModel->find($socialAuth['user_id']);
        }
    
        // ตรวจสอบผู้ใช้ที่ใช้อีเมลนี้แล้วหรือไม่
        $user = $this->userModel->where('email', $email)->first();
    
        if (!$user) {
            // สร้างผู้ใช้ใหม่
            $userId = $this->userModel->insert([
                'name' => $name,
                'email' => $email,
                'avatar' => $picture,
                'role' => 'user',
                'status' => 'active',
                'email_verified_at' => date('Y-m-d H:i:s')
            ]);
            $user = $this->userModel->find($userId);
        }
    
        // เพิ่มการเชื่อมต่อ Social Login
        $this->socialAuthModel->insert([
            'user_id' => $user['id'],
            'provider' => $provider,
            'social_id' => $socialId
        ]);
    
        return $user;
    }
    
    protected function createUserSession($user)
    {
        $this->session->set([
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'user_email' => $user['email'],
            'user_role' => $user['role'],
            'user_avatar' => $user['avatar']
        ]);
    }

    // protected function createRememberToken($userId)
    // {
    //     $token = bin2hex(random_bytes(32));
    //     $this->userModel->update($userId, ['remember_token' => $token]);

    //     set_cookie([
    //         'name' => 'remember_token',
    //         'value' => $token,
    //         'expire' => 30 * 24 * 60 * 60 // 30 วัน
    //     ]);
    // }

    //fix token not add to db
    protected function createRememberToken($userId)
    {
        $token = bin2hex(random_bytes(32));
        $data = ['remember_token' => $token];

        // Update the user record
        $updateSuccess = $this->userModel->update($userId, $data);

        // Check if the update was successful
        if (!$updateSuccess) {
            log_message('error', "Failed to update remember_token for user: $userId");
            return false; // Or handle the error appropriately
        }

        // Set the cookie if update was successful
        $this->response->setCookie('remember_token', $token, 30 * 24 * 60 * 60); // 30 days

        return true;
    }



    protected function logLogin($userId)
    {
        // $id=$userId;
        // $this->userModel->update($id, [
        //  //   'last_login' => date('m-d-Y H:i:s')
        //     'last_ip' => $this->request->getIPAddress()
        // ]);

        // บันทึกประวัติการเข้าสู่ระบบ
        model('LoginLogModel')->insert([
            'user_id' => $userId,
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString()
        ]);
    }

    protected function getFacebookLoginUrl()
    {
        $fb = new \Facebook\Facebook([
            'app_id' => $this->config->facebook['app_id'],
            'app_secret' => $this->config->facebook['app_secret'],
            'default_graph_version' => 'v21.0'
        ]);

        $helper = $fb->getRedirectLoginHelper();
        return $helper->getLoginUrl(
            base_url('auth/facebook-callback'),
            ['email', 'public_profile']
        );
    }

    // protected function getGoogleLoginUrl()
    // {
    //     $client = new \Google_Client();
    //     $client->setClientId($this->config->google['client_id']);
    //     $client->setClientSecret($this->config->google['client_secret']);
    //     $client->setRedirectUri(base_url('auth/google-callback'));
    //     $client->addScope('email');
    //     $client->addScope('profile');

    //     return $client->createAuthUrl();
    // }

    // protected function sendVerificationEmail($email)
    // {
    //     $token = bin2hex(random_bytes(32));

    //     // บันทึก token ในฐานข้อมูล
    //     $this->userModel->where('email', $email)->update(null, [
    //         'verification_token' => $token
    //     ]);

    //     // ส่งอีเมล
    //     $email = \Config\Services::email();
    //     $email->setTo($email);
    //     $email->setSubject('ยืนยันการสมัครสมาชิก ShareHub');
    //     $email->setMessage(view('emails/verify_email', [
    //         'verifyUrl' => base_url("auth/verify/{$token}")
    //     ]));
    //     $email->send();
    // }


    // zone password reset


    public function forgotPassword()
    {

        $data = [
            'title' => 'ลืมรหัสผ่าน - ShareHub',

        ];

        return  view('templates/header', $data)
            . view('components/navbar')
            . view('/auth/forgot')
            . view('templates/footer');
    }

    //หน้าลืมรหัส่ผ่าน
    public function processForgotPassword()
    {
        $email = $this->request->getPost('email');
        $email = urldecode($email);
        $email = trim($email);

        $userModel = new \App\Models\UserModel();

        // เพิ่ม debug log
        log_message('debug', 'Searching for email: ' . $email);

        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            log_message('debug', 'Email not found in database');
            return redirect()->back()->with('error', 'อีเมลนี้ไม่พบในระบบ');
        }

        try {
            $this->sendResetLink($email);
            return redirect()->to('/auth/forgot')
                ->with('success', 'ลิงก์รีเซ็ตรหัสผ่านถูกส่งไปยังอีเมลของคุณแล้ว');
        } catch (\Exception $e) {
            log_message('error', 'Failed to send reset link: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'เกิดข้อผิดพลาดในการส่งอีเมล กรุณาลองใหม่อีกครั้ง');
        }
    }


    // ส่งลิงก์รีเซ็ตรหัสผ่านโดยใช้อีเมล
    // public function sendResetLink($email)
    // {
    //     // สร้าง token สำหรับการรีเซ็ตรหัสผ่าน
    //     $token = bin2hex(random_bytes(50));  // หรือใช้ JWT ก็ได้
    //     $expiry = Time::now()->addHours(1); // หมดอายุใน 1 ชั่วโมง

    //     // บันทึก token ในฐานข้อมูล (เก็บใน table `password_resets`)
    //     $model = new UserModel();
    //     $model->savePasswordResetToken($email, $token, $expiry);

    //     // สร้าง URL สำหรับการรีเซ็ตรหัสผ่าน
    //     $resetLink = base_url("auth/resetPassword/{$token}");

    //     // ส่งอีเมลให้ผู้ใช้
    //     $this->sendResetEmail($email, $resetLink);
    // }


    // ส่งลิงก์รีเซ็ตรหัสผ่านโดยใช้อีเมล step1
    public function sendResetLink($email)
    {
        // สร้าง token สำหรับการรีเซ็ตรหัสผ่าน
        $token = bin2hex(random_bytes(50));  // หรือใช้ JWT ก็ได้
        $expiration = Time::now()->addHours(1)->toDateTimeString(); // หมดอายุใน 1 ชั่วโมงและแปลงเป็น string

        // บันทึก token ในฐานข้อมูล (เก็บใน table `password_resets`)
        $model = new UserModel();
        $model->savePasswordResetToken($email, $token, $expiration); //ตรงนี้ไม่ยอมอัพเดทค่าเวลาไปที่ expiry

        // สร้าง URL สำหรับการรีเซ็ตรหัสผ่าน
        $resetLink = base_url("auth/resetPassword/{$token}");

        // ส่งอีเมลให้ผู้ใช้
        $this->sendResetEmail($email, $resetLink);
    }


    private function sendResetEmail($email, $resetLink)
    {
        $emailService = \Config\Services::email();
        $emailService->setTo($email);
        $emailService->setSubject('รีเซ็ตรหัสผ่านของคุณ');
        $emailService->setMessage('คลิกที่นี่เพื่อรีเซ็ตรหัสผ่านของคุณ: ' . $resetLink);

        if ($emailService->send()) {
            // การส่งอีเมลสำเร็จ
        } else {
            // การส่งอีเมลล้มเหลว
        }
    }
    //หลังจากที่ผู้ใช้คลิกลิงก์รีเซ็ตรหัสผ่าน
    public function resetPassword($token)
    {
        $model = new UserModel();
        $resetRecord = $model->getResetRecordByToken($token); //เช็คใน db password_resets

        if (!$resetRecord || $resetRecord->expiration < Time::now()) {
            // token ไม่ถูกต้องหรือหมดอายุ
            return redirect()->to('/auth/login')->with('error', 'ลิงก์การรีเซ็ตรหัสผ่านไม่ถูกต้องหรือหมดอายุ');
        }
        return view('templates/header')
            . view('components/navbar')
            . view('/auth/resetpassword', ['token' => $token])
            . view('templates/footer');
    }

    //รับรหัสผ่านพร้อมค่า token ที่ผู้ใช้ส่งมาเพื่อเทียบกับ db ที่มีอยู่
    public function updatePassword()
    {
        $token = $this->request->getVar('token');
        $newPassword = $this->request->getVar('new_password');

        // ตรวจสอบ token
        $model = new UserModel();
        $resetRecord = $model->getResetRecordByToken($token);

        if (!$resetRecord || $resetRecord->expiration < Time::now()) {
            return redirect()->to('/auth/login')->with('error', 'ลิงก์การรีเซ็ตรหัสผ่านไม่ถูกต้องหรือหมดอายุ');
        }

        // อัปเดตรหัสผ่านใน table ผู้ใช้
        $model->updatePassword($resetRecord->email, $newPassword);

        // ลบ token หลังจากรีเซ็ตรหัสผ่านสำเร็จ
        $model->deleteResetRecord($token);

        return redirect()->to('/auth/login')->with('success', 'รหัสผ่านของคุณถูกรีเซ็ตแล้ว');
    }

    // reset password from email link

    public function resetPasswordEmail($token)
    {
        // ค้นหาข้อมูลจาก token ที่ผู้ใช้ส่งมา
        $model = new UserModel();
        $user = $model->findByResetToken($token);

        // ถ้าไม่พบ user หรือ token หมดอายุ
        if (!$user || $this->isTokenExpired($user->expiration)) {
            return redirect()->to('/auth/login')->with('error', 'Token ไม่ถูกต้องหรือหมดอายุแล้ว resetPasswordEmail');
        }

        // ส่งข้อมูลไปยัง view
        return view('reset_password', ['token' => $token]);
    }


    public function processResetPassword()
    {
        $token = $this->request->getPost('token');
        $newPassword = $this->request->getPost('new_password');

        if (empty($token) || empty($newPassword)) {
            log_message('error', 'Token or New Password is empty');
            return redirect()->back()->with('error', 'กรุณากรอกข้อมูลให้ครบถ้วน');
        }

        $model = new UserModel();
        $user = $model->findByResetToken($token); //หา userid จาก token ว่า map จาก userid

        if (!$user || $this->isTokenExpired($user->reset_token_expiration)) {
            log_message('error', 'Token invalid or expired for token: ' . $token);
            return redirect()->to('/auth/login')->with('error', 'Token ไม่ถูกต้องหรือหมดอายุแล้ว --- ติดต่อผู้ดูแลระบบ');
        }

        $updateResult = $model->updatePasswordEmailReset($user->id, ['password' => $newPassword]); //ส่ง userid password token

        if ($updateResult) {
            log_message('info', 'Password updated successfully for userId: ' . $user->id);
            return redirect()->to('/auth/login')->with('success', 'การรีเซ็ตรหัสผ่านสำเร็จ');
        } else {
            log_message('error', 'Password update failed for userId: ' . $user->id);
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการอัพเดตข้อมูล');
        }
    }

    // เงื่อนไขการตรวจสอบว่า token หมดอายุหรือไม่
    private function isTokenExpired($expiration)
    {
        if (!$expiration) {
            return false; //bypass
        }

        $expirationTime = \CodeIgniter\I18n\Time::parse($expiration);
        $currentTime = \CodeIgniter\I18n\Time::now();

        return $currentTime->isAfter($expirationTime);
    }
}
