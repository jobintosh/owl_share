<?php

if (!function_exists('time_ago')) {
    function time_ago($datetime)
    {
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;

        if ($diff < 60) {
            return 'เมื่อสักครู่';
        }

        $intervals = [
            31536000 => 'ปี',
            2592000 => 'เดือน',
            604800 => 'สัปดาห์',
            86400 => 'วัน',
            3600 => 'ชั่วโมง',
            60 => 'นาที'
        ];

        foreach ($intervals as $seconds => $label) {
            $interval = floor($diff / $seconds);
            if ($interval >= 1) {
                return $interval . ' ' . $label . 'ที่แล้ว';
            }
        }

        return 'เมื่อสักครู่';
    }
}

if (!function_exists('format_date')) {
    function format_date($datetime, $format = 'j M Y H:i')
    {
        return date($format, strtotime($datetime));
    }
}

if (!function_exists('thai_month')) {
    function thai_month($month)
    {
        $thai_months = [
            1 => 'มกราคม',
            2 => 'กุมภาพันธ์',
            3 => 'มีนาคม',
            4 => 'เมษายน',
            5 => 'พฤษภาคม',
            6 => 'มิถุนายน',
            7 => 'กรกฎาคม',
            8 => 'สิงหาคม',
            9 => 'กันยายน',
            10 => 'ตุลาคม',
            11 => 'พฤศจิกายน',
            12 => 'ธันวาคม'
        ];
        return $thai_months[(int)$month];
    }
}

if (!function_exists('thai_date')) {
    function thai_date($datetime)
    {
        $timestamp = strtotime($datetime);
        $thai_year = date('Y', $timestamp) + 543;
        $month = thai_month(date('n', $timestamp));
        $date = date('j', $timestamp);
        $time = date('H:i', $timestamp);
        
        return "$date $month $thai_year เวลา $time น.";
    }
}