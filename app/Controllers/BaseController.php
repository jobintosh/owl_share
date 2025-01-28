<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['url', 'form', 'text'];

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->session = \Config\Services::session();
        
        // E.g.: Load auth service if needed
        // $this->auth = new \App\Libraries\Auth();
        
        // Load common data for views
        $this->data = [
            'site_name' => 'ShareHub',
            'current_user' => $this->getCurrentUser(),
            'categories' => [
                'knowledge' => 'ความรู้',
                'technology' => 'เทคโนโลยี',
                'news' => 'ข่าวสาร'
            ]
        ];
    }

    /**
     * Get current logged in user
     */
    protected function getCurrentUser()
    {
        if ($this->session->has('user_id')) {
            // ในโปรเจ็กต์จริงควรดึงข้อมูลจาก Database
            return [
                'id' => $this->session->get('user_id'),
                'name' => $this->session->get('user_name'),
                'email' => $this->session->get('user_email'),
                'avatar' => $this->session->get('user_avatar')
            ];
        }
        return null;
    }

    /**
     * Return JSON response
     */
    protected function jsonResponse($data, $code = 200)
    {
        return $this->response->setStatusCode($code)
                             ->setJSON($data);
    }

    /**
     * Return error response
     */
    protected function errorResponse($message, $code = 400)
    {
        return $this->jsonResponse([
            'status' => $code,
            'error' => true,
            'messages' => $message
        ], $code);
    }

    /**
     * Return success response
     */
    protected function successResponse($data, $message = null)
    {
        $response = [
            'status' => 200,
            'error' => false,
            'data' => $data
        ];

        if ($message) {
            $response['message'] = $message;
        }

        return $this->jsonResponse($response);
    }
}