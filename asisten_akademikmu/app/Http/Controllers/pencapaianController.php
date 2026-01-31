<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PencapaianController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $userDetails = DB::table('users')->where('id', $userId)->first();
        
        // Kelompokkan agar bisa di-loop di tampilan grid
        $achievementsByCategory = DB::table('achievements')
            ->where('user_id', $userId)
            ->get()
            ->groupBy('category');

        return view('pencapaian', [
            'streakDays' => $userDetails->streak_days ?? 0,
            'achievementsByCategory' => $achievementsByCategory
        ]);
    }
}