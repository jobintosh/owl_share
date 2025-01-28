<?php

namespace App\Models;

use CodeIgniter\Model;

class UserStatsModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    /**
     * ดึงสถิติของผู้เขียน
     *
     * @param int $authorId
     * @return array
     */
    public function getAuthorStats($authorId)
    {
        $db = \Config\Database::connect();

        // ดึงจำนวนโพสต์ทั้งหมด
        $totalPosts = $db->table('posts')
            ->where('author_id', $authorId)
            ->where('status', 'published')
            ->where('deleted_at IS NULL')
            ->countAllResults();

        // ดึงจำนวนผู้ติดตาม
        $followers = $db->table('user_followers')
            ->where('following_id', $authorId)
            ->countAllResults();

        // ดึงจำนวนไลค์ทั้งหมดที่ได้รับ
        $totalLikes = $db->table('post_likes')
            ->join('posts', 'posts.id = post_likes.post_id')
            ->where('posts.author_id', $authorId)
            ->where('posts.status', 'published')
            ->where('posts.deleted_at IS NULL')
            ->countAllResults();

        // ดึงจำนวนความคิดเห็นทั้งหมดที่ได้รับ
        $totalComments = $db->table('comments')
            ->join('posts', 'posts.id = comments.post_id')
            ->where('posts.author_id', $authorId)
            ->where('posts.status', 'published')
            ->where('posts.deleted_at IS NULL')
            ->where('comments.status', 'approved')
            ->countAllResults();

        // ดึงจำนวนการดูทั้งหมด
        $totalViews = $db->table('posts')
            ->where('author_id', $authorId)
            ->where('status', 'published')
            ->where('deleted_at IS NULL')
            ->selectSum('view_count')
            ->get()
            ->getRow()
            ->view_count ?? 0;

        // ดึงโพสต์ยอดนิยม
        $topPosts = $db->table('posts')
            ->select('posts.*, COUNT(DISTINCT pl.id) as like_count, COUNT(DISTINCT c.id) as comment_count')
            ->where('posts.author_id', $authorId)
            ->where('posts.status', 'published')
            ->where('posts.deleted_at IS NULL')
            ->join('post_likes pl', 'pl.post_id = posts.id', 'left')
            ->join('comments c', 'c.post_id = posts.id AND c.status = "approved"', 'left')
            ->groupBy('posts.id')
            ->orderBy('posts.view_count', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // ดึงหมวดหมู่ที่เขียนบ่อย
        $topCategories = $db->table('posts')
            ->select('categories.name, categories.slug, COUNT(*) as post_count')
            ->join('categories', 'categories.id = posts.category_id')
            ->where('posts.author_id', $authorId)
            ->where('posts.status', 'published')
            ->where('posts.deleted_at IS NULL')
            ->groupBy('categories.id')
            ->orderBy('post_count', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // ดึงแท็กที่ใช้บ่อย
        $topTags = $db->table('posts')
            ->select('posts.tags')
            ->where('author_id', $authorId)
            ->where('status', 'published')
            ->where('deleted_at IS NULL')
            ->where('tags IS NOT NULL')
            ->get()
            ->getResultArray();

        // นับความถี่ของแท็ก
        $tagCounts = [];
        foreach ($topTags as $post) {
            if ($tags = json_decode($post['tags'], true)) {
                foreach ($tags as $tag) {
                    if (!isset($tagCounts[$tag])) {
                        $tagCounts[$tag] = 0;
                    }
                    $tagCounts[$tag]++;
                }
            }
        }
        arsort($tagCounts);
        $topTags = array_slice($tagCounts, 0, 5, true);

        // คำนวณอัตราการตอบรับ (Engagement Rate)
        $engagementRate = 0;
        if ($totalPosts > 0) {
            $totalEngagements = $totalLikes + $totalComments;
            $engagementRate = round(($totalEngagements / ($totalPosts * $totalViews)) * 100, 2);
        }

        // ดึงข้อมูลการเติบโต (เปรียบเทียบกับเดือนที่แล้ว)
        $lastMonthStart = date('Y-m-d H:i:s', strtotime('first day of last month'));
        $lastMonthEnd = date('Y-m-d H:i:s', strtotime('last day of last month'));
        $thisMonthStart = date('Y-m-d H:i:s', strtotime('first day of this month'));

        $lastMonthStats = [
            'posts' => $db->table('posts')
                ->where('author_id', $authorId)
                ->where('status', 'published')
                ->where('created_at >=', $lastMonthStart)
                ->where('created_at <=', $lastMonthEnd)
                ->countAllResults(),
            'views' => $db->table('posts')
                ->where('author_id', $authorId)
                ->where('status', 'published')
                ->where('created_at >=', $lastMonthStart)
                ->where('created_at <=', $lastMonthEnd)
                ->selectSum('view_count')
                ->get()
                ->getRow()
                ->view_count ?? 0
        ];

        $thisMonthStats = [
            'posts' => $db->table('posts')
                ->where('author_id', $authorId)
                ->where('status', 'published')
                ->where('created_at >=', $thisMonthStart)
                ->countAllResults(),
            'views' => $db->table('posts')
                ->where('author_id', $authorId)
                ->where('status', 'published')
                ->where('created_at >=', $thisMonthStart)
                ->selectSum('view_count')
                ->get()
                ->getRow()
                ->view_count ?? 0
        ];

        // รวมข้อมูลทั้งหมด
        return [
            'total_stats' => [
                'posts' => $totalPosts,
                'followers' => $followers,
                'likes' => $totalLikes,
                'comments' => $totalComments,
                'views' => $totalViews,
                'engagement_rate' => $engagementRate
            ],
            'growth' => [
                'last_month' => $lastMonthStats,
                'this_month' => $thisMonthStats
            ],
            'top_posts' => $topPosts,
            'top_categories' => $topCategories,
            'top_tags' => $topTags
        ];
    }

    /**
     * ดึงแนวโน้มการเติบโตของผู้ใช้
     */
    public function getUserGrowthTrend($userId, $months = 6)
    {
        $db = \Config\Database::connect();
        $trend = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $startDate = date('Y-m-01', strtotime("-$i months"));
            $endDate = date('Y-m-t', strtotime("-$i months"));

            $monthStats = $db->table('posts')
                ->select('
                    COUNT(*) as post_count,
                    COALESCE(SUM(view_count), 0) as views,
                    COALESCE(SUM(like_count), 0) as likes,
                    COALESCE(SUM(comment_count), 0) as comments
                ')
                ->where('author_id', $userId)
                ->where('status', 'published')
                ->where('created_at >=', $startDate)
                ->where('created_at <=', $endDate)
                ->get()
                ->getRow();

            $trend[] = [
                'month' => date('M Y', strtotime($startDate)),
                'post_count' => (int)$monthStats->post_count,
                'views' => (int)$monthStats->views,
                'likes' => (int)$monthStats->likes,
                'comments' => (int)$monthStats->comments
            ];
        }

        return $trend;
    }
}