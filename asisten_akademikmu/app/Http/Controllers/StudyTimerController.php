<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudyTimerController extends Controller
{
    public function index()
    {
        // 1. Mengambil ID dari session atau auth (Sama seperti DashboardController)
        $userId = session('user_id') ?? auth()->id();
        $today = Carbon::today();

        // 2. Ambil Data User (PENTING: Agar navbar tidak error 'userDetails' undefined)
        $userDetails = DB::table('users')->where('id', $userId)->first();

        // 3. Ambil Statistik Khusus Belajar
        $studyStats = [
            'today_minutes' => DB::table('study_sessions')
                ->where('user_id', $userId)
                ->whereDate('created_at', $today)
                ->sum('duration_minutes') ?? 0,
            'total_sessions' => DB::table('study_sessions')
                ->where('user_id', $userId)
                ->count(),
        ];

        // 4. Ambil Daftar Mata Kuliah untuk Dropdown Timer
        $subjects = DB::table('subjects')
            ->where('user_id', $userId)
            ->get();

        // 5. Riwayat Belajar Terakhir
        // app/Http/Controllers/StudyTimerController.php

        // 5. Riwayat Belajar Terakhir
        $recentSessions = DB::table('study_sessions as ss')
            ->leftJoin('subjects as s', 'ss.subject_id', '=', 's.id')
            ->where('ss.user_id', $userId)
            ->select('ss.*', 's.name as subject_name')
            ->orderBy('ss.created_at', 'desc') // UBAH INI: gunakan created_at
            ->limit(5)
            ->get();

        // Kirimkan variabel userDetails agar navbar tetap tampil sempurna
        return view('StudyTimer', compact('userDetails', 'subjects', 'studyStats', 'recentSessions'));
    }
}