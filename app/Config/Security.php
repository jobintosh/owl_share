<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Security extends BaseConfig
{
    public $csrfProtection = 'session';
    
    public $tokenName = 'csrf_token_name';
    
    public $headerName = 'X-CSRF-TOKEN';
    
    public $cookieName = 'csrf_cookie_name';
    
    public $expires = 7200;
    
    public $regenerate = true;
    
    public $redirect = true;
    
    public $samesite = 'Lax';

    // Content Security Policy
    public $CSPEnabled = true;

    public array $CSPDirectives = [
        'default-src' => ['none'],
        'script-src' => [
            'self',
            'cdnjs.cloudflare.com',
            'cdn.tiny.cloud',
            'cdn.jsdelivr.net',
            "'unsafe-inline'",
            "'unsafe-eval'"
        ],
        'style-src' => [
            'self',
            'cdnjs.cloudflare.com',
            'cdn.tiny.cloud',
            'fonts.googleapis.com',
            "'unsafe-inline'"
        ],
        'img-src' => [
            'self',
            'data:',
            'blob:',
            'cdn.tiny.cloud',
            '*'
        ],
        'font-src' => [
            'self',
            'fonts.gstatic.com',
            'cdnjs.cloudflare.com'
        ],
        'connect-src' => [
            'self',
            'blob:'
        ],
        'media-src' => ['self'],
        'form-action' => ['self']
    ];
}