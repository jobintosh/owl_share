<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

$routes = Services::routes();

// Load the system's routing file first
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// Main routes
$routes->get('/', 'Home::index');
$routes->get('share', 'Share::index');

// API routes
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    // Posts routes
    $routes->get('posts/(:segment)', 'Posts::index/$1');
    $routes->post('posts/like/(:num)', 'Posts::toggleLike/$1');
    $routes->get('posts/trending', 'Posts::trending');

    // Comments routes
    $routes->post('comments', 'Comments::create');
    $routes->get('comments/(:num)', 'Comments::getPostComments/$1');
    $routes->post('post/update-comments', 'PostController::updateCommentsAjax'); //ajax อัพเดทคอมเม้น

    // for add new comment
    // $routes->post('post/addComment/(:num)', 'Post::addComment/$1');
    $routes->post('post/addComment/(:num)', 'Post::addComment/$1');
    $routes->post('post/addCommentReply/(:num)', 'Post::addCommentReply/$1');

    // for update and delete comment
    $routes->post('post/updateComment/(:num)', 'Post::updateComment/$1');
    $routes->post('post/deleteComment/(:num)', 'Post::deleteComment/$1');

    $routes->get('comments/count/(:num)', 'Comments::getCount/$1');
    $routes->get('comments/counts', 'Comments::getCounts');
    $routes->get('post/(:num)/comments/count', 'Comments::getPostCommentCount/$1');
    //ลบโพส
    $routes->get('post/delete/(:num)', 'PostController::deletePost/$1');

    $routes->group('api', function ($routes) {
        $routes->get('comments/count/(:num)', 'Comments::getCountAPI/$1');
        $routes->get('comments/counts', 'Comments::getCountsAPI');
    });


    // Profile routes
    $routes->get('profile', 'Profile::index');
    $routes->put('profile', 'Profile::update');

    // follwer
    // $routes->post('follow/(:num)', 'Follow::follow/$1');
    // $routes->post('unfollow/(:num)', 'Follow::unfollow/$1');

    $routes->post('follow/(:num)', 'Profile::follow/$1');
    $routes->post('unfollow/(:num)', 'Profile::unfollow/$1');

});

// Reaction route
$routes->post('post/reaction', 'Post::reaction');
$routes->get('post/(:num)', 'Post::show/$1'); // Maps post/123 to the show method


$routes->group('auth', function ($routes) {
    $routes->get('login', 'Auth::login', ['as' => 'login']);
    $routes->post('do-login', 'Auth::doLogin', ['as' => 'do-login']);
    $routes->get('logout', 'Auth::logout', ['as' => 'logout']);

    //ลืมรหัสผ่าน
    $routes->get('forgot', 'Auth::forgotPassword'); // สำหรับแสดงหน้า forgot password
    $routes->post('forgot', 'Auth::processForgotPassword'); //ส่งลิ้งค์
    //รีเซ็ตรหัสผ่าน
    $routes->get('resetpassword/(:segment)', 'Auth::resetPasswordEmail/$1');
    $routes->post('resetpassword', 'Auth::processResetPassword');

    $routes->get('auth/resetPassword/(:any)', 'Auth::resetPassword/$1');
});


// Register Routes
$routes->get('register', 'Auth::register', ['as' => 'register']);
$routes->post('register', 'Auth::doRegister');

// Email Verification Routes
// $routes->get('verify/(:any)', 'Auth::verify/$1', ['as' => 'verification.verify']);
// $routes->get('verify', 'Auth::showVerificationNotice', ['as' => 'verification.notice']);
// $routes->post('verify/resend', 'Auth::resendVerification', ['as' => 'verification.resend']);
// Social Login Routes

// Facebook
$routes->get('auth/facebook-callback', 'Auth::facebookCallback');


$routes->get('facebook', 'Auth::facebookLogin', ['as' => 'login.facebook']);
$routes->get('facebook-callback', 'Auth::facebookCallback');
$routes->post(
    'facebook-disconnect',
    'Auth::disconnectFacebook',
    ['filter' => 'auth', 'as' => 'disconnect.facebook']
);

// Google
// $routes->get('google', 'Auth::googleLogin', ['as' => 'login.google']);
// $routes->get('google-callback', 'Auth::googleCallback');
// $routes->post(
//     'google-disconnect',
//     'Auth::disconnectGoogle',
//     ['filter' => 'auth', 'as' => 'disconnect.google']
// );

// // Profile & Settings Routes (Require Authentication)
// //$routes->group('profile', ['filter' => 'auth'], function($routes) {
//     $routes->group('profile', function($routes) {
//     $routes->get('/', 'Profile::index', ['as' => 'profile']);
//     $routes->get('edit', 'Profile::edit', ['as' => 'profile.edit']);
//     $routes->post('update', 'Profile::update', ['as' => 'profile.update']);
//     $routes->get('password', 'Profile::password', ['as' => 'profile.password']);
//     $routes->post('password', 'Profile::updatePassword');

//     // Social Connections
//     $routes->get('connections', 'Profile::connections', ['as' => 'profile.connections']);
// });

// แก้ไขเพื่อให้ view โปร์ไฟล์คนอื่นได้
$routes->group('profile', function ($routes) {
    $routes->get('/', 'Profile::index', ['as' => 'profile']);
    $routes->get('edit', 'Profile::edit', ['as' => 'profile.edit']);
    $routes->post('update', 'Profile::update', ['as' => 'profile.update']);
    $routes->get('password', 'Profile::changePassword', ['as' => 'profile.password']);
    $routes->post('password', 'Profile::updatePassword');
    $routes->get('connections', 'Profile::connections', ['as' => 'profile.connections']);
    // $routes->get('profile/view/(:any)', 'Profile::view/$1', ['as' => 'profile.view']);
    // $routes->get('view/(:any)', 'Profile::view/$1', ['as' => 'profile.view']); //เดิม
        // $routes->get('view/(:any)', 'Profile::view/$1', ['as' => 'profile.view']);
        // $routes->post('view/(:any)/follow/(:num)', 'Profile::follow/$2');
    // $routes->get('follow/(:num)', 'Profile::follow/$1');
    // $routes->post('follow/(:num)', 'Profile::follow/$1');
    // $routes->post('unfollow/(:num)', 'Profile::unfollow/$1');
});



// API Authentication Routes
$routes->group('api', function ($routes) {
    // Basic Auth Routes
    $routes->post('login', 'Api\Auth::login');

    $routes->post('register', 'Api\Auth::register');
    $routes->post('logout', 'Api\Auth::logout', ['filter' => 'auth:api']);
    $routes->post('refresh', 'Api\Auth::refresh', ['filter' => 'auth:api']);



    // $routes->get('forgot', 'Api\Auth::forgotPassword'); // สำหรับแสดงหน้า forgot password
    // $routes->post('forgot', 'Api\Auth::processForgotPassword'); // สำหรับประมวลผลการส่งลิงก์

    // Password Reset Routes
    // $routes->get('/auth/forgot', 'Auth::forgotPassword');
    // $routes->post('/auth/forgot', 'Auth::processForgotPassword');
    // $routes->get('/auth/forgot', 'Auth::forgotPassword');
    // $routes->post('/auth/forgot', 'Auth::processForgotPassword');



    // Social Auth Routes
    $routes->post('auth/facebook', 'Api\Auth::facebookToken');
    $routes->post('auth/google', 'Api\Auth::googleToken');

    // Password Reset
    // $routes->post('forgot-password', 'Api\Auth::forgotPassword');
    // $routes->post('reset-password', 'Api\Auth::resetPassword');


    // $routes->get('auth/forgot-password', 'Auth::forgotPassword');
    // $routes->post('auth/process-forgot-password', 'Auth::processForgotPassword');
    // $routes->get('auth/reset-password/(:any)', 'Auth::resetPassword');

    // Protected Routes
    $routes->group('', ['filter' => 'auth:api'], function ($routes) {
        $routes->get('user', 'Api\Auth::user');
        $routes->post('user/update', 'Api\Auth::updateProfile');
        $routes->post('user/password', 'Api\Auth::updatePassword');
    });
});



// Admin Auth Routes
$routes->group('admin', ['filter' => 'auth.admin'], function ($routes) {
    // User Management
    $routes->get('users', 'Admin\Users::index');
    $routes->get('users/create', 'Admin\Users::create');
    $routes->post('users/store', 'Admin\Users::store');
    $routes->get('users/edit/(:num)', 'Admin\Users::edit/$1');
    $routes->post('users/update/(:num)', 'Admin\Users::update/$1');
    $routes->post('users/delete/(:num)', 'Admin\Users::delete/$1');

    // Social Auth Management
    $routes->get('social-connections', 'Admin\SocialAuth::index');
    $routes->post('social-connections/revoke/(:num)', 'Admin\SocialAuth::revoke/$1');
});

// Utility Routes for Authentication
$routes->group('utils', ['filter' => 'auth'], function ($routes) {
    // Session Management
    $routes->post('invalidate-sessions', 'Auth::invalidateOtherSessions');
    $routes->get('active-sessions', 'Auth::getActiveSessions');

    // Two-Factor Authentication
    $routes->get('2fa/setup', 'TwoFactor::setup');
    $routes->post('2fa/enable', 'TwoFactor::enable');
    $routes->post('2fa/disable', 'TwoFactor::disable');
    $routes->get('2fa/recovery-codes', 'TwoFactor::showRecoveryCodes');
});

$routes->get('post/(:segment)', 'Post::view/$1');
$routes->get('tag/(:segment)', 'Tag::view/$1');
$routes->get('category/(:segment)', 'Category::view/$1');




// Privacy Policy page

$routes->get('policy', 'PrivacyPolicy::index');


// data del
$routes->get('data-deletion', 'DataDeletion::index');

//follwer




/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
