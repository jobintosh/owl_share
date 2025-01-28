<?php

namespace App\Controllers;
use App\Models\FollowerModel;


class Profile extends BaseController
{
    protected $userModel;
    protected $postModel;
    protected $socialAuthModel;

    public function __construct()
    {
        $this->userModel = model('UserModel');
        $this->postModel = model('PostModel');
        $this->socialAuthModel = model('SocialAuthModel');
    }



    /**
     * แสดงหน้า Profile
     */
    public function index()
    {

        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/auth/login');
        }

        $user = $this->userModel->find($userId);

        // ดึงโพสต์ล่าสุดของผู้ใช้
        $recentPosts = $this->postModel->where('author_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->find();

        // ดึงสถิติต่างๆ
        $stats = [
            'total_posts' => $this->postModel->where('author_id', $userId)->countAllResults(),
            'total_views' => $this->postModel->selectSum('view_count')
                ->where('author_id', $userId)
                ->get()
                ->getRow()
                ->view_count ?? 0,
            'total_likes' => $this->postModel->selectSum('like_count')
                ->where('author_id', $userId)
                ->get()
                ->getRow()
                ->like_count ?? 0
        ];

        // ดึงข้อมูลการเชื่อมต่อ Social
        $socialConnections = $this->socialAuthModel->getUserConnections($userId);

        $data = [
            'title' => 'โปรไฟล์ของฉัน - ShareHub',
            'user' => $user,
            'recent_posts' => $recentPosts,
            'stats' => $stats,
            'social_connections' => $socialConnections,
            'validation' => \Config\Services::validation()
        ];

        return view('templates/header', $data)
            . view('components/navbar')
            . view('profile/index')
            . view('templates/footer');
    }

    /**
     * แสดงหน้าแก้ไขโปรไฟล์
     */
    public function edit()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/auth/login');
        }


        $data = [
            'title' => 'แก้ไขโปรไฟล์ - ShareHub',
            'user' => $this->userModel->find($userId),
            'validation' => \Config\Services::validation()
        ];


        return view('templates/header', $data)
            . view('components/navbar')
            . view('profile/edit')
            . view('templates/footer');
    }

    /**
     * อัพเดทข้อมูลโปรไฟล์
     */
    // public function update()
    // {
    //     $userId = session()->get('user_id');
    //     if (!$userId) {
    //         return redirect()->to('/login');
    //     }

    //     // กำหนดกฎการตรวจสอบข้อมูล
    //     $rules = [
    //         'name' => 'required|min_length[3]|max_length[100]',
    //         'bio' => 'permit_empty|max_length[500]',
    //         'avatar' => [
    //             'uploaded[avatar]',
    //             'is_image[avatar]',
    //             'mime_in[avatar,image/jpg,image/jpeg,image/png]',
    //             'max_size[avatar,1024]',
    //         ]
    //     ];

    //     if (!$this->validate($rules)) {
    //         return redirect()->back()
    //             ->withInput()
    //             ->with('errors', $this->validator->getErrors());
    //     }

    //     // จัดการรูปภาพ
    //     $avatar = $this->request->getFile('avatar');
    //     $avatarPath = null;

    //     if ($avatar->isValid() && !$avatar->hasMoved()) {
    //         $newName = $avatar->getRandomName();
    //         $avatar->move(FCPATH . 'avatars', $newName);
    //         $avatarPath = 'avatars/' . $newName;

    //         // ลบรูปเก่า
    //         $oldAvatar = $this->userModel->find($userId)['avatar'];
    //         if ($oldAvatar && file_exists(FCPATH . $oldAvatar)) {
    //             unlink(FCPATH . $oldAvatar);
    //         }
    //     }

    //     // อัพเดทข้อมูล
    //     $data = [
    //         'name' => $this->request->getPost('name'),
    //         //  'avatar' => $avatarPath,
    //         'bio' => $this->request->getPost('bio')
    //     ];

    //     // echo $avatarPath;
    //     // exit;

    //     if ($avatarPath) {
    //         $data['avatar'] = $avatarPath;
    //     }

    //     $this->userModel->update($userId, $data);

    //     return redirect()->to('/profile')
    //         ->with('success', 'อัพเดทโปรไฟล์เรียบร้อยแล้ว');
    // }



    public function update()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/auth/login');
        }

        // Check if user exists
        if (!$this->userModel->userExists($userId)) {
            return redirect()->to('/profile/update');
        }

        // Validation rules
        $rules = [
            'name' => 'permit_empty|min_length[3]|max_length[100]',
            'bio' => 'permit_empty|max_length[500]',
        ];

        // Add avatar rules only if the avatar is uploaded
        if ($this->request->getFile('avatar')->isValid()) {
            $rules['avatar'] = [
                'uploaded[avatar]',
                'is_image[avatar]',
                'mime_in[avatar,image/jpg,image/jpeg,image/png]',
                'max_size[avatar,1024]',
            ];
        }

        if (!$this->validate($rules)) {
            log_message('debug', 'Validation Errors: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Prepare the data to update
        $data = [];

        // If 'name' is provided, add it to the update data
        $name = $this->request->getPost('name');
        if (!empty($name)) {
            $data['name'] = $name;
        }

        // If 'bio' is provided, add it to the update data
        $bio = $this->request->getPost('bio');
        if (!empty($bio)) {
            $data['bio'] = $bio;
        }

        // Handle avatar upload if provided
        $avatar = $this->request->getFile('avatar');
        if ($avatar && $avatar->isValid() && !$avatar->hasMoved()) {
            $newName = $avatar->getRandomName();
            $avatar->move(FCPATH . 'avatars', $newName);
            $avatarPath = 'avatars/' . $newName;
            $data['avatar'] = $avatarPath;

            // Delete old avatar if it exists
            $oldAvatar = $this->userModel->getUserData($userId)['avatar'];
            if ($oldAvatar && file_exists(FCPATH . $oldAvatar)) {
                unlink(FCPATH . $oldAvatar);
            }
        }

        // If no data to update, return error
        if (empty($data)) {
            log_message('debug', 'No data to update for user ID: ' . $userId);
            return redirect()->back()
                ->with('error', 'No changes detected.');
        }

        // Always update the timestamp when there's any update
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Log the data that will be updated
        log_message('debug', 'Data to update: ' . json_encode($data));

        // Perform the update
        $updated = $this->userModel->updateProfile($userId, $data);

        if ($updated) {
            return redirect()->to('/profile/edit')
                ->with('success', 'อัพเดทโปรไฟล์เรียบร้อยแล้ว');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to update profile');
    }

    // ปัญหามาจากการที่ check data name bio av เลยทำให้ไม่ครบเงื่อนไข ทำให้อัพเดทแยกส่วนไม่ได้ 

    /**
     * แสดงหน้าเปลี่ยนรหัสผ่าน
     */

    public function changePassword()
    {
        $userId = session()->get('user_id');

        // หากไม่มี session ของผู้ใช้ ให้ redirect ไปที่หน้า login
        if (!$userId) {
            return redirect()->to('/auth/login');
        }

        // กำหนดข้อมูลที่จะส่งให้กับ view
        $data = [
            'title' => 'เปลี่ยนรหัสผ่าน - ShareHub',
            'validation' => \Config\Services::validation()
        ];

        // ตรวจสอบว่าเป็นการส่งฟอร์ม
        if ($this->request->getGet()) {
            // if ($this->request->getMethod() === 'post') { 
            // กำหนดกฎการตรวจสอบข้อมูล
            $rules = [
                'currentPassword' => 'required',
                'newPassword' => 'required|min_length[8]',
                'confirmPassword' => 'required|matches[newPassword]'
            ];

            // ตรวจสอบข้อมูลจากฟอร์ม
            if (!$this->validate($rules)) {
                return view('profile/password', $data);
            }

            // รับค่าจากฟอร์ม
            $currentPassword = $this->request->getPost('currentPassword');
            $newPassword = $this->request->getPost('newPassword');

            // ตรวจสอบรหัสผ่านเดิมว่าถูกต้องหรือไม่
            $userModel = new \App\Models\UserModel();
            if (!$userModel->verifyPassword($userId, $currentPassword)) {
                // หากรหัสผ่านเดิมไม่ถูกต้อง ให้แสดงข้อความข้อผิดพลาด
                session()->setFlashdata('error', 'รหัสผ่านเดิมไม่ถูกต้อง');
                return redirect()->back()->withInput();
            }

            // อัปเดตรหัสผ่านใหม่
            if ($userModel->updatePassword($userId, $newPassword)) {
                // หากการอัปเดตสำเร็จ ให้แสดงข้อความสำเร็จ
                session()->setFlashdata('success', 'รหัสผ่านของคุณได้รับการอัปเดตเรียบร้อยแล้ว');
                return redirect()->to('/profile');
            } else {
                // หากไม่สามารถอัปเดตรหัสผ่านได้ ให้แสดงข้อความข้อผิดพลาด
                session()->setFlashdata('error', 'ไม่สามารถอัปเดตรหัสผ่านได้');
                return redirect()->back()->withInput();
            }
        }

        // แสดงหน้าเปลี่ยนรหัสผ่าน
        return view('templates/header', $data)
            . view('components/navbar')
            . view('profile/password')
            . view('templates/footer');
    }



    // /**
    //  * อัพเดทรหัสผ่าน
    //  */
    // public function updatePassword()
    // {
    //     $userId = session()->get('user_id');
    //     if (!$userId) {
    //         return redirect()->to('/auth/login');
    //     }

    //     // กำหนดกฎการตรวจสอบข้อมูล
    //     $rules = [
    //         'current_password' => 'required',
    //         'new_password' => 'required|min_length[8]',
    //         'confirm_password' => 'required|matches[new_password]'
    //     ];

    //     // ตรวจสอบการ validate ข้อมูล
    //     if (!$this->validate($rules)) {
    //         // แสดงข้อผิดพลาดจาก validation
    //         return redirect()->back()
    //             ->withInput()
    //             ->with('errors', $this->validator->getErrors());
    //     }

    //     // ค้นหาผู้ใช้จาก userId
    //     $user = $this->userModel->find($userId);
    //     if (!$user) {
    //         return redirect()->back()->with('error', 'ไม่พบผู้ใช้นี้');
    //     }

    //     // ตรวจสอบรหัสผ่านปัจจุบัน
    //     if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
    //         return redirect()->back()->with('error', 'รหัสผ่านปัจจุบันไม่ถูกต้อง');
    //     }

    //     // แฮชรหัสผ่านใหม่ก่อนบันทึก
    //     $newPassword = $this->request->getPost('new_password');
    //     $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

    //     // อัพเดทรหัสผ่าน
    //     $updateResult = $this->userModel->updatePassword($userId, [
    //         'password' => $newPasswordHash
    //     ]);

    //     // ตรวจสอบผลการอัปเดต
    //     if ($updateResult) {
    //         return redirect()->to('/profile')
    //             ->with('success', 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว');
    //     } else {
    //         return redirect()->back()->with('error', 'ไม่สามารถเปลี่ยนรหัสผ่านได้');
    //     }
    // }


    public function updatePassword()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/auth/login');
        }
    
        // กำหนดกฎการตรวจสอบข้อมูล
        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]'
        ];
    
        // ตรวจสอบการ validate ข้อมูล
        if (!$this->validate($rules)) {
            // แสดงข้อผิดพลาดจาก validation
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
    
        // ค้นหาผู้ใช้จาก userId
        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->back()->with('error', 'ไม่พบผู้ใช้นี้');
        }
    
        // ตรวจสอบรหัสผ่านปัจจุบัน
        if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
            return redirect()->back()->with('error', 'รหัสผ่านปัจจุบันไม่ถูกต้อง');
        }
    
        // ตรวจสอบว่า new_password เป็น string หรือไม่
        $newPassword = $this->request->getPost('new_password');
        if (is_array($newPassword)) {
            return redirect()->back()->with('error', 'รหัสผ่านใหม่ไม่ถูกต้อง');
        }
    
        // แฮชรหัสผ่านใหม่ก่อนบันทึก
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    
        // อัพเดทรหัสผ่าน
        $updateResult = $this->userModel->updatePassword($userId, [
            'password' => $newPasswordHash
        ]);
    
        // ตรวจสอบผลการอัปเดต
        if ($updateResult) {
            return redirect()->to('/profile')
                ->with('success', 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว');
        } else {
            return redirect()->back()->with('error', 'ไม่สามารถเปลี่ยนรหัสผ่านได้');
        }
    }
    


    /**
     * ลบบัญชีผู้ใช้
     */
    public function deleteAccount()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/auth/login');
        }

        // ลบรูปภาพของผู้ใช้
        $user = $this->userModel->find($userId);
        if ($user['avatar'] && file_exists(FCPATH . $user['avatar'])) {
            unlink(FCPATH . $user['avatar']);
        }

        // ลบข้อมูลผู้ใช้
        $this->userModel->delete($userId);

        // ล้าง session
        session()->destroy();

        return redirect()->to('/')
            ->with('success', 'ลบบัญชีผู้ใช้เรียบร้อยแล้ว');
    }
    // สำหรับ view profile คนอื่น profile/view/1
    public function view($identifier)
    {
        // Determine if the identifier is numeric (ID) or a string (username)
        if (is_numeric($identifier)) {
            $user = $this->userModel->find($identifier);
        } else {
            $user = $this->userModel->where('username', $identifier)->first();
        }

        // If user not found, show 404 error
        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("User not found");
        }

        // Fetch user's recent posts
        $recentPosts = $this->postModel->where('author_id', $user['id'])
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->find();

        // Fetch user statistics
        $stats = [
            'total_posts' => $this->postModel->where('author_id', $user['id'])->countAllResults(),
            'total_views' => $this->postModel->selectSum('view_count')
                ->where('author_id', $user['id'])
                ->get()
                ->getRow()
                ->view_count ?? 0,
            'total_likes' => $this->postModel->selectSum('like_count')
                ->where('author_id', $user['id'])
                ->get()
                ->getRow()
                ->like_count ?? 0
        ];

        // Fetch user's social connections
        $socialConnections = $this->socialAuthModel->getUserConnections($user['id']);

        $data = [
            'title' => "{$user['name']}'s Profile - ShareHub",
            'user' => $user,
            'recent_posts' => $recentPosts,
            'stats' => $stats,
            'social_connections' => $socialConnections
        ];

        return view('templates/header', $data)
            . view('components/navbar')
            . view('profile/view')
            . view('templates/footer');
    }
    public function viewByUsername($username)
    {
        $user = $this->userModel->where('username', $username)->first();

        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("User not found.");
        }

        $data = [
            'title' => 'โปรไฟล์ผู้ใช้ - ' . $user['name'],
            'user' => $user,
        ];

        return view('templates/header', $data)
            . view('components/navbar')
            . view('profile/view', $data)
            . view('templates/footer');
    }

    public function follow($userId)
    {
        $followerId = session()->get('user_id'); // ID ของผู้ที่ล็อกอิน
        if ($followerId == $userId) {
            return redirect()->back()->with('error', 'ไม่สามารถติดตามตัวเองได้');
        }

        $followerModel = new FollowerModel();
        $exists = $followerModel->where(['follower_id' => $followerId, 'following_id' => $userId])->first();

        if ($exists) {
            return redirect()->back()->with('message', 'คุณติดตามผู้ใช้นี้อยู่แล้ว');
        }

        $followerModel->insert([
            'follower_id' => $followerId,
            'following_id' => $userId
        ]);

        return redirect()->back()->with('message', 'ติดตามสำเร็จ');
    }

    public function unfollow($userId)
    {
        $followerId = session()->get('user_id'); // ID ของผู้ที่ล็อกอิน

        $followerModel = new FollowerModel();
        $followerModel->where(['follower_id' => $followerId, 'following_id' => $userId])->delete();

        return redirect()->back()->with('message', 'ยกเลิกการติดตามสำเร็จ');
    }
}
