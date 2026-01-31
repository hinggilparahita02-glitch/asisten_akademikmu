<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class kalenderController extends Controller
{
    // Tambahkan fungsi index ini jika belum ada
    public function index()
    {
        // 1. Ambil ID user dari session
        $userId = session('user_id') ?? auth()->id();

        // 2. Ambil data lengkap user dari database (PENTING)
        $userDetails = DB::table('users')->where('id', $userId)->first();

        // 3. Pastikan $userDetails dikirim ke view melalui compact
        return view('kalender', compact('userDetails')); 
    }
}