<?php
namespace App\Controllers;

use App\Models\FollowerModel;
use CodeIgniter\Controller;

class FollowController extends Controller
{
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
?>