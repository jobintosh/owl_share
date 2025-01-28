<?php

namespace App\Controllers;

class PrivacyPolicy extends BaseController
{
    public function index()
    {
        return view('templates/header')
        . view('components/navbar')
        . view('policy/index')
        . view('templates/footer');
    }
}
