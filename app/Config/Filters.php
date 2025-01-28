<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;


use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;



class Filters extends BaseConfig
{
	/**
	 * Configures aliases for Filter classes to
	 * make reading things nicer and simpler.
	 *
	 * @var array
	 */
	public $aliases = [
		'csrf'     => CSRF::class,
		'toolbar'  => DebugToolbar::class,
		'honeypot' => Honeypot::class,
		'login'         => \App\Filters\LoginFilter::class,
        'rateLogin'     => \App\Filters\RateLoginFilter::class,
	];

	/**
	 * List of filter aliases that are always
	 * applied before and after every request.
	 *
	 * @var array
	 */


	// public $globals = [
	// 	'before' => [
	// 		// 'honeypot',
	// 		// 'csrf',
	// 	],
	// 	'after'  => [
	// 		'toolbar',
	// 		'csrf',
	// 		// 'honeypot',
	// 	],
	// ];





	public array $globals = [
        'before' => [
            'csrf' => ['except' => [
                'api/*',           // ยกเว้นทุก API routes
                'auth/*',          // ยกเว้นทุก auth routes
                'share/*',    // ยกเว้นเฉพาะ route
                'upload/*',        // ยกเว้นทุก upload routes
				'post/*',
				'chat/*',
				'com/*',
				// 'profile/*',
            ]],
        ],
        'after' => [
            'toolbar',
         //'secureheaders',
        ],
    ];






	// public array $globals = [
    //     'before' => [
    //         // ลบหรือคอมเมนต์บรรทัดนี้
    //         // 'csrf',
    //         'honeypot',
    //         'invalidchars',
    //     ],
    //     'after' => [
    //         'toolbar',
    //         'secureheaders',
    //     ],
    // ];

	// public array $globals = [
    //     'before' => [
    //         'csrf' => ['except' => [
    //             'api/*',
    //             'auth/social-callback/*'
    //         ]],
    //         'honeypot',
    //         'invalidchars',
    //     ],
    //     'after' => [
    //         'toolbar',
    //         'secureheaders',
    //     ],
    // ];


	/**
	 * List of filter aliases that works on a
	 * particular HTTP method (GET, POST, etc.).
	 *
	 * Example:
	 * 'post' => ['csrf', 'throttle']
	 *
	 * @var array
	 */
	public $methods = [];

	/**
	 * List of filter aliases that should run on any
	 * before or after URI patterns.
	 *
	 * Example:
	 * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
	 *
	 * @var array
	 */
	public $filters = [];

	// public array $filters = [
    //     'auth' => [
    //         'before' => [
    //             'dashboard/*',
    //             'profile/*',
    //             'share/*',
    //             'settings/*'
				
    //         ]
    //     ],
    //     'registration' => [
    //         'before' => [
    //             'auth/doregister'
				
				
    //         ]
	// 		],
	// 		'logout' => [
	// 			'before' => ['auth/logout'],
	// 			'after' => ['auth/logout']
	// 		]
    // ];


	
}
