<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiswaController extends Controller
{
    /**
     * Menampilkan Landing Page Utama untuk Siswa
     */
    public function index()
    {
        return view('siswa.landing');
    }
}