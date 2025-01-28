<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Tag extends BaseController
{
    protected $postModel;
    protected $tagModel;
    

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->postModel = model('PostModel');
        $this->tagModel = model('TagModel');
    }

    public function index()
    {
        // ดึงแท็กทั้งหมดพร้อมจำนวนโพสต์
       // $tags = $this->tagModel->findAll();
        
        $tags = $this->tagModel->getAllTagsWithCount();
        
        // ดึงแท็กยอดนิยม
        $popularTags = $this->tagModel->getPopularTags(6);
        
        $data = [
            'title' => 'แท็กทั้งหมด - ShareHub',
            'meta_description' => 'รวมแท็กทั้งหมดสำหรับการค้นหาเนื้อหาที่คุณสนใจ',
            'tags' => $tags,
            'popularTags' => $popularTags
        ];

        return view('templates/header', $data)
            . view('components/navbar')
            . view('tag/index')
            . view('templates/footer');
    
    }

    // public function view($slug)
    // {
    //     // ดึงข้อมูลแท็ก
    //     $tag = $this->tagModel->where('slug', $slug)->first();
        
    //     if (!$tag) {
    //         throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    //     }

    //     // ดึงโพสต์ที่มีแท็กนี้
    //     $posts = $this->postModel->getPostsByTag($tag['name']);

    //     $data = [
    //         'title' => 'แท็ก: ' . $tag['name'] . ' - ShareHub',
    //         'meta_description' => $tag['description'] ?? 'เนื้อหาทั้งหมดที่เกี่ยวข้องกับ ' . $tag['name'],
    //         'tag' => $tag,
    //         'posts' => $posts
    //     ];

    //     return view('templates/header', $data)
    //         . view('components/navbar')
    //         . view('tag/view')
    //         . view('templates/footer');
    // }
    public function view($slug)
    {
        // ดึงข้อมูลแท็ก
        $tag = $this->tagModel->where('slug', $slug)->first();
        
        if (!$tag) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

            
        // ดึงโพสต์ที่มีแท็กนี้โดยใช้ PostModel
        $posts = $this->postModel
        ->where('status', 'published')
        ->where('deleted_at IS NULL')
        ->where("JSON_CONTAINS(tags, '\"" . $tag['name'] . "\"')")
        ->findAll();

    $data = [
        'title' => 'แท็ก: ' . $tag['name'] . ' - ShareHub',
        'meta_description' => $tag['description'] ?? 'เนื้อหาทั้งหมดที่เกี่ยวข้องกับ ' . $tag['name'],
        'tag' => $tag,
        'posts' => $posts
    ];
    
        return view('templates/header', $data)
            . view('components/navbar')
            . view('tag/view')
            . view('templates/footer');
    }
}