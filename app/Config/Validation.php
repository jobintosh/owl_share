<?php

namespace Config;

use CodeIgniter\Validation\CreditCardRules;
use CodeIgniter\Validation\FileRules;
use CodeIgniter\Validation\FormatRules;
use CodeIgniter\Validation\Rules;

class Validation
{
	//--------------------------------------------------------------------
	// Setup
	//--------------------------------------------------------------------

	/**
	 * Stores the classes that contain the
	 * rules that are available.
	 *
	 * @var string[]
	 */
	public $ruleSets = [
		Rules::class,
		FormatRules::class,
		FileRules::class,
		CreditCardRules::class,
	];

	/**
	 * Specifies the views that are used to display the
	 * errors.
	 *
	 * @var array<string, string>
	 */
	public $templates = [
		'list'   => 'CodeIgniter\Validation\Views\list',
		'single' => 'CodeIgniter\Validation\Views\single',
	];

	//--------------------------------------------------------------------
	// Rules
	//--------------------------------------------------------------------

	public $registration = [
        'name' => [
            'rules' => 'required|min_length[3]|max_length[100]',
            'errors' => [
                'required' => 'กรุณากรอกชื่อ-นามสกุล',
                'min_length' => 'ชื่อ-นามสกุลต้องมีความยาวอย่างน้อย 3 ตัวอักษร',
                'max_length' => 'ชื่อ-นามสกุลต้องมีความยาวไม่เกิน 100 ตัวอักษร'
            ]
        ],
        'email' => [
            'rules' => 'required|valid_email|is_unique[users.email]',
            'errors' => [
                'required' => 'กรุณากรอกอีเมล',
                'valid_email' => 'รูปแบบอีเมลไม่ถูกต้อง',
                'is_unique' => 'อีเมลนี้ถูกใช้งานแล้ว'
            ]
        ],
        'password' => [
            'rules' => 'required|min_length[8]|strong_password',
            'errors' => [
                'required' => 'กรุณากรอกรหัสผ่าน',
                'min_length' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร',
                'strong_password' => 'รหัสผ่านต้องประกอบด้วยตัวอักษรพิมพ์เล็ก พิมพ์ใหญ่ ตัวเลข และอักขระพิเศษ'
            ]
        ],
        'confirm_password' => [
            'rules' => 'required|matches[password]',
            'errors' => [
                'required' => 'กรุณายืนยันรหัสผ่าน',
                'matches' => 'รหัสผ่านไม่ตรงกัน'
            ]
        ]
    ];

    // เพิ่ม Custom Rule สำหรับตรวจสอบรหัสผ่าน
    public function strong_password(string $str, string &$error = null): bool
    {
        if (! preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $str)) {
            $error = 'รหัสผ่านต้องประกอบด้วยตัวอักษรพิมพ์เล็ก พิมพ์ใหญ่ ตัวเลข และอักขระพิเศษ';
            return false;
        }

        return true;
    }
}
