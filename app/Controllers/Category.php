<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Category extends BaseController
{
    protected $postModel;
    protected $categoryModel;
    protected $db;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->postModel = model('PostModel');
        $this->categoryModel = model('CategoryModel');
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // ดึงหมวดหมู่ทั้งหมดพร้อมจำนวนโพสต์
        // $categories = $this->categoryModel->getActiveCategories();

        // $data = [
        //     'title' => 'หมวดหมู่ทั้งหมด - ShareHub',
        //     'meta_description' => 'รวมหมวดหมู่ทั้งหมดสำหรับการค้นหาเนื้อหาที่คุณสนใจ',
        //     'categories' => $categories
        // ];

        // return view('templates/header', $data)
        //     . view('components/navbar')
        //     . view('category/index')
        //     . view('templates/footer');

        $builder = $this->db->table('categories c');
        $builder->select('c.*, COUNT(p.id) as post_count');
        $builder->join('posts p', 'p.category_id = c.id AND p.status = "published" AND p.deleted_at IS NULL', 'left');
        $builder->groupBy('c.id');
        $builder->orderBy('post_count', 'DESC');
        
        $categories = $builder->get()->getResultArray();

        $data = [
            'title' => 'หมวดหมู่ทั้งหมด - ShareHub',
            'meta_description' => 'รวมหมวดหมู่ทั้งหมดสำหรับการค้นหาเนื้อหาที่คุณสนใจ',
            'categories' => $categories
        ];

        return view('templates/header', $data)
            . view('components/navbar')
            . view('category/index')
            . view('templates/footer');


    }

    public function view($slug)
    {
        // ดึงข้อมูลหมวดหมู่
        $category = $this->categoryModel->where('slug', $slug)->first();
        
        if (!$category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // ดึงโพสต์ในหมวดหมู่นี้
        $posts = $this->postModel
            ->where('status', 'published')
            ->where('deleted_at IS NULL')
            ->where('category_id', $category['id'])
            ->findAll();

        $data = [
            'title' => $category['name'] . ' - ShareHub',
            'meta_description' => $category['description'] ?? 'เนื้อหาทั้งหมดในหมวดหมู่ ' . $category['name'],
            'category' => $category,
            'posts' => $posts
        ];

        return view('templates/header', $data)
            . view('components/navbar')
            . view('category/view')
            . view('templates/footer');
    }
}