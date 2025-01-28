<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;


class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';



    protected $allowedFields = [
        'name',
        'email',
        'username',
        'password',
        'avatar',
        'bio',
        'role',
        'status',
        'settings',
        'last_login',
        'remember_token',
        'facebook_id'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]',
        // 'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        //  'username' => 'required|alpha_numeric|min_length[3]|max_length[30]|is_unique[users.username,id,{id}]',
        'password' => 'required|min_length[8]',
        'role' => 'required|in_list[user,editor,admin]',
        'status' => 'required|in_list[active,inactive,banned]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'กรุณาใส่ชื่อ-นามสกุล',
            'min_length' => 'ชื่อต้องมีความยาวอย่างน้อย 3 ตัวอักษร'
        ],
        // 'email' => [
        //     'required' => 'กรุณาใส่อีเมล',
        //     'valid_email' => 'รูปแบบอีเมลไม่ถูกต้อง',
        //     'is_unique' => 'อีเมลนี้มีผู้ใช้งานแล้ว'
        // ],
        // 'username' => [
        //     'required' => 'กรุณาใส่ชื่อผู้ใช้',
        //     'alpha_numeric' => 'ชื่อผู้ใช้ต้องประกอบด้วยตัวอักษรและตัวเลขเท่านั้น',
        //     'is_unique' => 'ชื่อผู้ใช้นี้มีคนใช้แล้ว'
        // ],
        'password' => [
            'required' => 'กรุณาใส่รหัสผ่าน',
            'min_length' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร'
        ]
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    // แปลงรหัสผ่านเป็น hash ก่อนบันทึก
    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password'])) return $data;

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        return $data;
    }

    // สร้าง Remember Token
    public function createRememberToken($user_id)
    {
        $token = bin2hex(random_bytes(32));
        $this->update($user_id, [
            'remember_token' => $token
        ]);
        return $token;
    }

    // ดึงข้อมูลผู้ใช้จาก Remember Token
    public function getUserByRememberToken($token)
    {
        return $this->where('remember_token', $token)
            ->where('status', 'active')
            ->first();
    }

    // อัพเดทครั้งสุดท้ายที่เข้าสู่ระบบ
    public function updateLastLogin($user_id)
    {
        return $this->update($user_id, [
            'last_login' => date('Y-m-d H:i:s')
        ]);
    }

    // ดึงสถิติของผู้ใช้
    public function getUserStats($user_id)
    {
        $postModel = model('PostModel');
        $commentModel = model('CommentModel');

        return [
            'posts' => $postModel->where('author_id', $user_id)
                ->where('status', 'published')
                ->countAllResults(),
            'comments' => $commentModel->where('author_id', $user_id)
                ->where('status', 'approved')
                ->countAllResults(),
            'likes_received' => $postModel->selectSum('like_count')
                ->where('author_id', $user_id)
                ->where('status', 'published')
                ->first()['like_count'] ?? 0,
            'views_received' => $postModel->selectSum('view_count')
                ->where('author_id', $user_id)
                ->where('status', 'published')
                ->first()['view_count'] ?? 0
        ];
    }

    // ดึงผู้ใช้ที่มีส่วนร่วมมากที่สุด
    public function getTopContributors($limit = 10)
    {
        return $this->select('users.*, 
                            COUNT(DISTINCT posts.id) as post_count,
                            COUNT(DISTINCT comments.id) as comment_count,
                            SUM(posts.view_count) as total_views')
            ->join('posts', 'posts.author_id = users.id AND posts.status = "published"', 'left')
            ->join('comments', 'comments.author_id = users.id AND comments.status = "approved"', 'left')
            ->where('users.status', 'active')
            ->groupBy('users.id')
            ->orderBy('post_count', 'DESC')
            ->orderBy('comment_count', 'DESC')
            ->limit($limit)
            ->find();
    }

    // ติดตามผู้ใช้
    public function follow($follower_id, $following_id)
    {
        $db = \Config\Database::connect();
        return $db->table('user_followers')
            ->insert([
                'follower_id' => $follower_id,
                'following_id' => $following_id,
                'created_at' => date('Y-m-d H:i:s')
            ]);
    }

    // เลิกติดตามผู้ใช้
    public function unfollow($follower_id, $following_id)
    {
        $db = \Config\Database::connect();
        return $db->table('user_followers')
            ->where('follower_id', $follower_id)
            ->where('following_id', $following_id)
            ->delete();
    }

    // ตรวจสอบว่ากำลังติดตามหรือไม่
    public function isFollowing($follower_id, $following_id)
    {
        $db = \Config\Database::connect();
        return $db->table('user_followers')
            ->where('follower_id', $follower_id)
            ->where('following_id', $following_id)
            ->countAllResults() > 0;
    }

    // ดึงรายการผู้ติดตาม
    public function getFollowers($user_id, $limit = 10)
    {
        return $this->select('users.*')
            ->join('user_followers', 'user_followers.follower_id = users.id')
            ->where('user_followers.following_id', $user_id)
            ->where('users.status', 'active')
            ->limit($limit)
            ->find();
    }

    // ดึงรายการที่กำลังติดตาม
    public function getFollowing($user_id, $limit = 10)
    {
        return $this->select('users.*')
            ->join('user_followers', 'user_followers.following_id = users.id')
            ->where('user_followers.follower_id', $user_id)
            ->where('users.status', 'active')
            ->limit($limit)
            ->find();
    }


    ///
    ///







    /**
     * ดึงข้อมูลผู้ใช้จากอีเมล
     *
     * @param string $email
     * @return array|null
     */


    /**
     * ดึงข้อมูลผู้ใช้จากอีเมลพร้อม Social Auth
     *
     * @param string $email
     * @return array|null
     */
    public function getUserByEmailWithSocial($email)
    {
        return $this->select('users.*, roles.permissions, GROUP_CONCAT(social_auth.provider) as social_providers')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->join('social_auth', 'social_auth.user_id = users.id', 'left')
            ->where('users.email', $email)
            ->groupBy('users.id')
            ->first();
    }



    /**
     * บันทึกประวัติการล็อกอินที่ล้มเหลว
     *
     * @param int $userId
     * @return void
     */


    /**
     * อัพเดทการเข้าสู่ระบบล่าสุด
     *
     * @param int $userId
     * @return bool
     */


    // public function getUserByEmail($email)
    // {
    //     $user = $this->where('email', $email)->first();

    //     if (!$user) {
    //         return false;
    //     }

    //     return $user;
    // }

    public function getUserByEmail($email)
    {
        // ลบช่องว่างก่อนและหลังอีเมล
        $email = trim($email);

        $user = $this->where('email', $email)->first();

        if (!$user) {
            log_message('debug', 'No user found for email: ' . $email);
            return false;
        }

        return $user;
    }


    /**
     * ตรวจสอบการล็อกอิน
     */
    public function validateLogin($email, $password)
    {
        $user = $this->getUserByEmail($email);

        if (!$user) {
            return false;
        }

        // ตรวจสอบสถานะผู้ใช้
        if ($user['status'] !== 'active') {
            return false;
        }

        // ตรวจสอบรหัสผ่านโดยตรง
        if ($password !== $user['password']) {
            // บันทึกการล็อกอินที่ล้มเหลว
            $this->logFailedLogin($user['id']);
            return false;
        }

        // อัพเดทข้อมูลการล็อกอินล่าสุด
        $this->update($user['id'], [
            'last_login' => date('Y-m-d H:i:s'),
            'last_ip' => service('request')->getIPAddress()
        ]);

        return $user;
    }

    /**
     * บันทึกการล็อกอินที่ล้มเหลว
     */
    protected function logFailedLogin($userId)
    {
        $failedAttempts = $this->where('id', $userId)->first()['failed_attempts'] ?? 0;
        $failedAttempts++;

        $this->update($userId, [
            'failed_attempts' => $failedAttempts,
            'last_failed_login' => date('Y-m-d H:i:s')
        ]);

        // ล็อคบัญชีถ้าล็อกอินล้มเหลวเกิน 5 ครั้ง
        if ($failedAttempts >= 5) {
            $this->update($userId, [
                'status' => 'locked',
                'locked_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * รีเซ็ตการล็อกอินที่ล้มเหลว
     */
    public function resetFailedAttempts($userId)
    {
        return $this->update($userId, [
            'failed_attempts' => 0,
            'last_failed_login' => null
        ]);
    }

    /**
     * เช็คว่าบัญชีถูกล็อคหรือไม่
     */
    public function isLocked($userId)
    {
        $user = $this->find($userId);
        if (!$user) {
            return false;
        }

        return $user['status'] === 'locked';
    }

    /**
     * ปลดล็อคบัญชี
     */
    public function unlockAccount($userId)
    {
        return $this->update($userId, [
            'status' => 'active',
            'failed_attempts' => 0,
            'last_failed_login' => null,
            'locked_at' => null
        ]);
    }

    // หา user
    public function userExists($userId)
    {
        return $this->db->table($this->table)
            ->where($this->primaryKey, $userId)
            ->countAllResults() > 0;
    }

    // Helper method to get user data
    public function getUserData($userId)
    {
        return $this->find($userId);
    }

    // อัปเดตข้อมูลโปรไฟล์
    public function updateProfile($userId, $data)
    {
        try {
            $builder = $this->db->table($this->table);

            // Prepare update data dynamically
            $updateData = [];

            if (isset($data['name'])) {
                $updateData['name'] = $data['name'];
            }

            if (isset($data['bio'])) {
                $updateData['bio'] = $data['bio'];
            }

            if (isset($data['avatar'])) {
                $updateData['avatar'] = $data['avatar'];
            }

            // Always update the timestamp
            if (!empty($updateData)) {
                $updateData['updated_at'] = date('Y-m-d H:i:s');
            }

            // Check if there is data to update
            if (empty($updateData)) {
                log_message('debug', 'No data to update for user ID: ' . $userId);
                return false; // No data to update
            }

            // Execute the update query
            $result = $builder->where($this->primaryKey, $userId)
                ->update($updateData);

            // Log the query for debugging
            log_message('debug', 'Update Query: ' . $this->db->getLastQuery());

            if ($result) {
                return true;
            }

            // If update fails, log the error
            log_message('error', 'Update failed for user ID: ' . $userId);
            return false;
        } catch (\Exception $e) {
            log_message('error', 'Database error: ' . $e->getMessage());
            return false;
        }
    }


    // password function check 
    public function verifyPassword($userId, $password)
    {
        $user = $this->where('id', $userId)->first();
        if ($user && password_verify($password, $user['password'])) {
            return true;
        }
        return false;
    }

    // อย่าลบ
    // public function updatePassword($userId, $data)
    // {
    //     // ทำการอัปเดตข้อมูลรหัสผ่านในฐานข้อมูล
    //     return $this->db->table('users')
    //         ->where('id', $userId)
    //         ->update($data); // ใช้ข้อมูลที่ได้รับมา
    // }


    //บันทึก email token expiry expiration ลง db password_resets
    // public function savePasswordResetToken($email, $token, $expirationTime)
    // {
    //     $builder = $this->db->table('password_resets');

    //     // ลบข้อมูลเก่า (ถ้ามี)
    //     $builder->where('email', $email)->delete();

    //     // เพิ่มข้อมูลใหม่
    //     $data = [
    //         'email' => $email,
    //         'token' => $token,
    //         'expiration' => $expirationTime, // ตรงกับชื่อคอลัมน์ที่เพิ่ม
    //     ];

    //     $builder->insert($data);
    // }


    //สำหรับการบันทึก token และเวลาหมดอายุ เข้า db step1
    // public function savePasswordResetToken($email, $token, $expiration = null)
    // {
    //     $builder = $this->db->table('password_resets');

    //     // Delete existing entries for the email
    //     $builder->where('email', $email)->delete();

    //     // Set default expiration if not provided
    //     if ($expirationTime === null) {
    //         $expirationTime = \CodeIgniter\I18n\Time::now()->modify('+1 day')->toDateTimeString();
    //     }

    //     // Prepare data for insertion
    //     $data = [
    //         'email' => $email,
    //         'token' => $token,
    //         'expiration' => $expiration, // Use as-is
    //     ];

    //     // Insert new token and expiration
    //     $builder->insert($data);
    // }



    // ฟังก์ชันสำหรับดึงข้อมูลการรีเซ็ตรหัสผ่าน ก่อนจะเข้าหน้านี้ได้ระบบจะเทียบ token ที่ post มากับ db ว่าตรงกันหรือไม่
    public function getResetRecordByToken($token)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('password_resets');
        return $builder->where('token', $token)->get()->getRow();
    }


    // อัพเดทรหัสผ่านแบบปกติผ่าน Profile
    public function updatePassword($userId, $data)
    {
        // ตรวจสอบว่า 'password' เป็น string
        if (isset($data['password']) && is_array($data['password'])) {
            throw new \InvalidArgumentException('Password ต้องเป็น string');
        }

        // อัปเดตข้อมูลในฐานข้อมูล
        return $this->update($userId, ['password' => $data['password']]);
    }
    // เปลี่ยนรหัสผ่านผ่าน email
    public function updatePasswordEmail($userId, $data)
    {
        // ตรวจสอบว่า 'password' เป็น string
        if (isset($data['password']) && is_array($data['password'])) {
            throw new \InvalidArgumentException('Password ต้องเป็น string');
        }

        // อัปเดตข้อมูลในฐานข้อมูล
        return $this->update($userId, ['password' => $data['password']]);
    }


    //ลบ record หลังจากรีเซ็ตรหัสผ่าน
    public function deleteResetRecord($token)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('password_resets');
        $builder->delete(['token' => $token]);
    }

    // get new password reset
    // public function findByResetToken($token)
    // {
    //     return $this->where('reset_token', $token)
    //         ->where('reset_token_expiration >=', date('Y-m-d H:i:s')) // ตรวจสอบว่า token ยังไม่หมดอายุ ติดตรงนี้
    //         ->first();
    // }

    // public function findByResetToken($token) //db users
    // {
    //     $currentTime = \CodeIgniter\I18n\Time::now();
    //     return $this->where('reset_token', $token) // ชื่อคอลัมน์จริง
    //         ->where('reset_token_expiration >=', $currentTime) // ชื่อคอลัมน์จริง
    //         ->first();
    // }

//     public function findByResetToken($token)
// {
//     // ตรวจสอบ SQL Query และ log ค่า token
//     log_message('debug', 'Searching for token: ' . $token);
    
//     $builder = $this->db->table($this->table);
//     $builder->where('reset_token', $token);
//     $query = $builder->get();
    
//     // Log ข้อมูลที่ได้จากการ query
//     log_message('debug', 'Token query result: ' . json_encode($query->getRow()));
    
//     return $query->getRow();
// }

public function findByResetToken($token)
{
    return $this->db->table('password_resets')
        ->where('token', $token)
        ->join('users', 'users.email = password_resets.email')
        ->get()
        ->getRow();
}

    // บันทึก token และเวลาหมดอายุลงในฐานข้อมูล ตอนที่ user กดลืมรหัสผ่าน (password_resets)
    public function savePasswordResetToken($email, $token, $expirationTime = null)
    {
        $builder = $this->db->table('password_resets');
        $builder->where('email', $email)->delete();

        $data = [
            'email' => $email,
            'token' => $token, // ชื่อคอลัมน์จริง
            'expiration' => $expirationTime, // ชื่อคอลัมน์จริง
        ];

        $builder->insert($data);
    }

    // ใน UserModel.php
    // public function updatePasswordEmailReset($userId, $newPassword)
    // {
    //     // 1. เข้ารหัสรหัสผ่านก่อนบันทึก
    //     $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    //     // 2. อัปเดตฐานข้อมูล
    //     $builder = $this->db->table('users');
    //     $builder->where('id', $userId); // ตรวจสอบชื่อคอลัมน์ ID ให้ตรงกับฐานข้อมูล
    //     $result = $builder->update(['password' => $hashedPassword]); // ตรวจสอบชื่อคอลัมน์ password

    //     // 3. คืนค่า true/false ตามผลลัพธ์
    //     return $result;
    // }
    //step สุดท้ายหลังจากที่ที่ผู้ใช้กรอกรหัสผ่านใหม่แล้วกด sumit
    
//     public function updatePasswordEmailReset($userId, $data)
// {
//     // ตรวจสอบโครงสร้างข้อมูล
//     if (!is_array($data) || !isset($data['password'])) {
//         throw new \InvalidArgumentException('ข้อมูล password ไม่ถูกต้อง');
//     }

//     // เข้ารหัสรหัสผ่าน
//     $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

//     // อัปเดตฐานข้อมูล
//     return $this->update($userId, ['password' => $hashedPassword]);
//}

// public function updatePasswordEmailReset($userId, $data) //มีปัญหาคือเปลี่ยนรหัสมันจะเปลี่ยนทุกแอคเค้าใน db และ hash มีปัญหาทำให้รหัสผ่านผิดเสมอ
// {
//     if (!is_array($data) || !isset($data['password'])) {
//         log_message('error', 'Invalid data passed to updatePasswordEmailReset');
//         throw new \InvalidArgumentException('ข้อมูล password ไม่ถูกต้อง');
//     }

//     $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

//     if (!$this->update($userId, ['password' => $hashedPassword])) {
//         log_message('error', 'Failed to update user. Query: ' . $this->db->getLastQuery());
//         return false;
//     }

//     log_message('info', 'Password updated in database for userId: ' . $userId);
//     return true;
// }
// public function updatePasswordEmailReset($userId, $data)
// {
//     // ตรวจสอบข้อมูล password
//     if (!is_array($data) || !isset($data['password'])) {
//         log_message('error', 'Invalid data passed to updatePasswordEmailReset');
//         throw new \InvalidArgumentException('ข้อมูล password ไม่ถูกต้อง');
//     }

//     // แฮชรหัสผ่าน
//     $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

//     // อัปเดตข้อมูล
//     $builder = $this->db->table($this->table);
//     $builder->where('id', $userId);
//     $result = $builder->update(['password' => $hashedPassword]);

//     // ตรวจสอบจำนวนแถวที่อัปเดต
//     $affectedRows = $this->db->affectedRows(); // ใช้ affectedRows() แทน

//     if ($affectedRows === 0) {
//         log_message('error', 'No rows affected for userId: ' . $userId);
//         return false;
//     }

//     log_message('info', 'Password updated successfully for userId: ' . $userId);
//     return true;
// }
public function updatePasswordEmailReset($userId, $data)
{
    // ตรวจสอบค่า userId แบบละเอียด
    if (empty($userId) || !is_numeric($userId)) {
        log_message('error', 'Invalid user ID detected: ' . var_export($userId, true));
        throw new \InvalidArgumentException('User ID ไม่ถูกต้อง');
    }

    // ตรวจสอบข้อมูล password
    if (empty($data['password'])) {
        log_message('error', 'Empty password for user ID: ' . $userId);
        throw new \InvalidArgumentException('รหัสผ่านไม่สามารถว่างได้');
    }

    // สร้าง password hash
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    if (!$hashedPassword) {
        log_message('error', 'Hashing failed for user ID: ' . $userId);
        throw new \RuntimeException('ระบบเข้ารหัสรหัสผ่านขัดข้อง');
    }

    // อัปเดตข้อมูลด้วย transaction
    $db = \Config\Database::connect();
    try {
        $db->transStart();
        
        $updateData = [
            'password' => $hashedPassword,
            'reset_token' => null,
            'reset_token_expiration' => null
        ];
        
        $builder = $db->table('users');
        $builder->where('id', $userId); // ใช้ชื่อคอลัมน์ให้ตรงกับฐานข้อมูล
        $builder->update($updateData);
        
        $affectedRows = $db->affectedRows();
        $db->transComplete();

        if ($affectedRows === 0) {
            log_message('error', 'Update failed - No user found with ID: ' . $userId);
            return false;
        }
        
        // ตรวจสอบผลลัพธ์
        $updatedUser = $builder->where('id', $userId)->get()->getRow();
        if (!password_verify($data['password'], $updatedUser->password)) {
            log_message('critical', 'Password verification failed for ID: ' . $userId);
            throw new \RuntimeException('การตรวจสอบรหัสผ่านล้มเหลว');
        }
        
        return true;

    } catch (\Exception $e) {
        $db->transRollback();
        log_message('error', 'Database error: ' . $e->getMessage());
        return false;
    }
}

}
