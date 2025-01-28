<?php

if (!function_exists('getBankList')) {
    function getBankList() {
        return [
            'KBANK' => 'ธนาคารกสิกรไทย',
            'BBL' => 'ธนาคารกรุงเทพ',
            'KTB' => 'ธนาคารกรุงไทย',
            'SCB' => 'ธนาคารไทยพาณิชย์',
            'BAY' => 'ธนาคารกรุงศรีอยุธยา',
            'TMB' => 'ธนาคารทหารไทยธนชาต',
            'TBANK' => 'ธนาคารธนชาต',
            'GSB' => 'ธนาคารออมสิน',
            'BAAC' => 'ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร'
        ];
    }
}

if (!function_exists('getBankName')) {
    function getBankName($code) {
        $banks = getBankList();
        return $banks[$code] ?? $code;
    }
}

if (!function_exists('formatAccountNumber')) {
    function formatAccountNumber($accountNumber) {
        $accountNumber = preg_replace('/[^0-9]/', '', $accountNumber);
        $length = strlen($accountNumber);

        if ($length === 10) {
            // Format: XXX-X-XXXXX-X
            return substr($accountNumber, 0, 3) . '-' . 
                   substr($accountNumber, 3, 1) . '-' . 
                   substr($accountNumber, 4, 5) . '-' . 
                   substr($accountNumber, 9, 1);
        }

        if ($length === 12) {
            // Format: XXXX-XXXX-XXXX
            return substr($accountNumber, 0, 4) . '-' . 
                   substr($accountNumber, 4, 4) . '-' . 
                   substr($accountNumber, 8, 4);
        }

        return $accountNumber;
    }
}

// Add bank color/icon information
if (!function_exists('getBankInfo')) {
    function getBankInfo($bankCode) {
        $bankInfo = [
            'KBANK' => [
                'color' => '#138f2d',
                'icon' => 'kasikorn.png',
                'name' => 'ธนาคารกสิกรไทย'
            ],
            'BBL' => [
                'color' => '#1e4598',
                'icon' => 'bangkok.png',
                'name' => 'ธนาคารกรุงเทพ'
            ],
            'KTB' => [
                'color' => '#2182c7',
                'icon' => 'ktb.png',
                'name' => 'ธนาคารกรุงไทย'
            ],
            'SCB' => [
                'color' => '#4e2e7f',
                'icon' => 'scb.png',
                'name' => 'ธนาคารไทยพาณิชย์'
            ],
            'BAY' => [
                'color' => '#fec43b',
                'icon' => 'krungsri.png',
                'name' => 'ธนาคารกรุงศรีอยุธยา'
            ],
            'TMB' => [
                'color' => '#1279be',
                'icon' => 'ttb.png',
                'name' => 'ธนาคารทหารไทยธนชาต'
            ],
            // Add more banks as needed
        ];

        return $bankInfo[$bankCode] ?? [
            'color' => '#6c757d',
            'icon' => 'default-bank.png',
            'name' => getBankName($bankCode)
        ];
    }
}