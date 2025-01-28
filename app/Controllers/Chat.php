<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Chat extends BaseController
{
    protected $messageModel;
    protected $userModel;
    protected $db;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->messageModel = model('MessageModel');
        $this->userModel = model('UserModel');
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        // ดึงรายการแชทล่าสุด
        $chats = $this->messageModel->getRecentChats(session()->get('user_id'));
        
        $data = [
            'title' => 'ข้อความ - ShareHub',
            'chats' => $chats
        ];

        return view('templates/header', $data)
             . view('components/navbar')
             . view('chat/index')
             . view('templates/footer');
    }

    public function getMessages($recipientId)
    {
        if (!session()->get('user_id') || !$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $messages = $this->messageModel->getConversation(
            session()->get('user_id'),
            $recipientId
        );

        return $this->response->setJSON([
            'success' => true,
            'messages' => $messages
        ]);
    }

    public function sendMessage()
    {
        return $this->response->setJSON([
            'success' => true,
            'users' => 'admin'
        ]);
        exit;
        if (!session()->get('user_id') || !$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $message = [
            'sender_id' => session()->get('user_id'),
            'recipient_id' => $this->request->getPost('recipient_id'),
            'message' => $this->request->getPost('message'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $messageId = $this->messageModel->insert($message);

        return $this->response->setJSON([
            'success' => true,
            'message' => array_merge($message, ['id' => $messageId])
        ]);
    }
    public function searchUsers()
{
    if (!session()->get('user_id') || !$this->request->isAJAX()) {
        return $this->response->setStatusCode(403);
    }

    $searchTerm = $this->request->getGet('q');
    
    $users = $this->userModel
        ->select('id, name, email, avatar')
        ->where('id !=', session()->get('user_id'))
        ->like('name', $searchTerm)
        ->orLike('email', $searchTerm)
        ->where('status', 'active')
        ->findAll(10);

    return $this->response->setJSON([
        'success' => true,
        'users' => $users
    ]);
}








public function startAiChat()
{
    if (!session()->get('user_id') || !$this->request->isAJAX()) {
        return $this->response->setStatusCode(403);
    }

    $topic = $this->request->getJSON()->topic;
    $sessionId = uniqid('ai_');

    // สร้างข้อความต้อนรับตามหัวข้อ
    $welcomeMessages = [
        'general' => 'สวัสดีครับ ผมเป็น AI Assistant ยินดีที่ได้พูดคุยกับคุณ',
        'programming' => 'สวัสดีครับ ผมพร้อมช่วยเหลือเกี่ยวกับการเขียนโปรแกรม',
        'math' => 'สวัสดีครับ ผมพร้อมช่วยเหลือเกี่ยวกับคณิตศาสตร์',
        'science' => 'สวัสดีครับ ผมพร้อมช่วยเหลือเกี่ยวกับวิทยาศาสตร์',
        'language' => 'สวัสดีครับ ผมพร้อมช่วยเหลือเกี่ยวกับภาษา'
    ];

    return $this->sendResponse([
        'success' => true,
        'sessionId' => $sessionId,
        'welcomeMessage' => $welcomeMessages[$topic] ?? $welcomeMessages['general']
    ]);
}

public function sendAiMessage()
{
    if (!session()->get('user_id') || !$this->request->isAJAX()) {
        return $this->response->setStatusCode(403);
    }

    $data = $this->request->getJSON();
    $message = $data->message;
    $sessionId = explode('_', $data->recipient_id)[1];

    // ส่งข้อความไปยัง webhook
    $response = $this->sendToAiWebhook($message, $sessionId);

    return $this->sendResponse([
        'success' => true,
        'message' => $response
    ]);
}

private function sendToAiWebhook($message, $sessionId)
{
    // ตัวอย่างการส่งไปยัง webhook
    $webhookUrl = 'YOUR_WEBHOOK_URL';
    $data = [
        'message' => $message,
        'session_id' => $sessionId
    ];

    $ch = curl_init($webhookUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}
}