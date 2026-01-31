<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = session('user_id') ?? auth()->id();
        $today = Carbon::today();

        // Ambil Data User
        $userDetails = DB::table('users')->where('id', $userId)->first();

        // Ambil Statistik untuk Kartu Grid
        $stats = [
            'subjects' => DB::table('subjects')->where('user_id', $userId)->count(),
            'active_tasks' => DB::table('tasks')->where('user_id', $userId)->where('is_completed', 0)->count(),
            'streak_days' => $userDetails->streak_days ?? 0,
            'total_study_minutes' => DB::table('study_sessions')->where('user_id', $userId)->sum('duration_minutes') ?? 0,
            'current_gpa' => $userDetails->current_gpa ?? 0.00
        ];

        // Ambil Tugas Hari Ini
        $todayTasks = DB::table('tasks as t')
            ->leftJoin('subjects as s', 't.subject_id', '=', 's.id')
            ->where('t.user_id', $userId)
            ->whereDate('t.deadline', $today)
            ->where('t.is_completed', 0)
            ->select('t.*', 's.name as subject_name')
            ->get();

        return view('dashboard', compact('userDetails', 'stats', 'todayTasks'));
    }
}