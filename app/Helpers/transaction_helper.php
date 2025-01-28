<?php

if (!function_exists('getTransactionStatus')) {
    function getTransactionStatus($status) {
        $statuses = [
            'pending' => 'รอดำเนินการ',
            'processing' => 'กำลังดำเนินการ',
            'completed' => 'สำเร็จ',
            'failed' => 'ไม่สำเร็จ',
            'cancelled' => 'ยกเลิก'
        ];
        return $statuses[$status] ?? $status;
    }
}

if (!function_exists('getTransactionType')) {
    function getTransactionType($type) {
        $types = [
            'sale' => 'รายได้จากการขาย',
            'fee' => 'ค่าธรรมเนียม',
            'withdrawal' => 'ถอนเงิน',
            'refund' => 'คืนเงิน'
        ];
        return $types[$type] ?? $type;
    }
}

if (!function_exists('formatMoney')) {
    function formatMoney($amount) {
        return number_format($amount, 2);
    }
}

if (!function_exists('getStatusBadgeClass')) {
    function getStatusBadgeClass($status) {
        $classes = [
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'failed' => 'danger',
            'cancelled' => 'secondary'
        ];
        return $classes[$status] ?? 'secondary';
    }
}