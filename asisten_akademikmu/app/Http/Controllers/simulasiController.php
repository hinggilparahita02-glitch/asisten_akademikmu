<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SimulasiController extends Controller
{
    public function index()
    {
        // Di Laravel, ID user diambil dari Session atau Auth
        $userId = session('user_id'); 

        // Ganti dbQueryOne dengan DB::table
        $userDetails = DB::table('users')->where('id', $userId)->first();

        // Ganti dbQuery dengan DB::table
        $subjects = DB::table('subjects')
            ->where('user_id', $userId)
            ->orderBy('name')
            ->get();

        $pageTitle = 'Simulasi IPK - Asisten Akademik Harian';

        return view('simulasi', compact('userDetails', 'subjects', 'pageTitle'));
    }
}