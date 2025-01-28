<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class DataDeletion extends Controller
{
    public function index()
    {
        return view('templates/header')
        . view('components/navbar')
        . view('data-deletion/index')
        . view('templates/footer');
    }
}
