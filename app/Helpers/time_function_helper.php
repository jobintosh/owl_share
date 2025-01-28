<?php
if (!function_exists('time_ago')) {
    function time_ago($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;
        $string = array(
            'y' => 'ปี',
            'm' => 'เดือน',
            'w' => 'สัปดาห์',
            'd' => 'วัน',
            'h' => 'ชั่วโมง',
            'i' => 'นาที',
            's' => 'วินาที',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v;
            } else {
                unset($string[$k]);
            }
        }
        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . 'ที่แล้ว' : 'เมื่อสักครู่';
    }
}
?>
