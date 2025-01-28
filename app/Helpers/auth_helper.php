<?php

if (!function_exists('is_logged_in')) {
    function is_logged_in() {
        return session()->get('shop_id') !== null;
    }
}

if (!function_exists('current_shop')) {
    function current_shop() {
        return [
            'id' => session()->get('shop_id'),
            'name' => session()->get('shop_name'),
            'email' => session()->get('shop_email')
        ];
    }
}

if (!function_exists('generate_password_reset_token')) {
    function generate_password_reset_token() {
        return bin2hex(random_bytes(32));
    }
}

if (!function_exists('check_password_strength')) {
    function check_password_strength($password) {
        $strength = 0;

        // Length check
        if (strlen($password) >= 8) $strength++;

        // Contains lowercase and uppercase
        if (preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password)) $strength++;

        // Contains numbers
        if (preg_match('/\d/', $password)) $strength++;

        // Contains special characters
        if (preg_match('/[^a-zA-Z\d]/', $password)) $strength++;

        return [
            'score' => $strength,
            'level' => $strength <= 1 ? 'weak' : ($strength <= 3 ? 'medium' : 'strong'),
            'message' => get_password_strength_message($strength)
        ];
    }
}

if (!function_exists('get_password_strength_message')) {
    function get_password_strength_message($strength) {
        switch ($strength) {
            case 0:
            case 1:
                return 'รหัสผ่านควรมีความยาวอย่างน้อย 8 ตัวอักษร และประกอบด้วยตัวอักษรพิมพ์ใหญ่ พิมพ์เล็ก ตัวเลข และอักขระพิเศษ';
            case 2:
                return 'เพิ่มความปลอดภัยโดยใช้ตัวอักษรพิมพ์ใหญ่ พิมพ์เล็ก และตัวเลข';
            case 3:
                return 'เพิ่มความปลอดภัยโดยใช้อักขระพิเศษ';
            case 4:
                return 'รหัสผ่านมีความปลอดภัยสูง';
            default:
                return '';
        }
    }
}

// Add to app/Config/Autoload.php:
// public $helpers = ['auth'];

// Example usage in views:
/*
<?php if (is_logged_in()): ?>
    <div class="user-info">
        ยินดีต้อนรับ, <?= current_shop()['name'] ?>
    </div>
<?php endif; ?>
*/