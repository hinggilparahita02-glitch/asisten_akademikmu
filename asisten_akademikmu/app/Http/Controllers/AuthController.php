<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi input agar data tidak kosong
        $request->validate([
            'name' => 'required',
            'class' => 'required',
        ]);

        $name = $request->name;
        $class = $request->class;

        // 2. Cari user di database
        $user = DB::table('users')
            ->where('name', $name)
            ->where('class', $class)
            ->first();

        // 3. LOGIKA OTOMATIS: Jika user tidak ditemukan, buatkan akun baru langsung
        if (!$user) {
            $userId = DB::table('users')->insertGetId([
                'name' => $name,
                'class' => $class,
                'current_gpa' => 0.00, // Nilai default
                'streak_days' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Ambil data user yang baru saja dibuat
            $user = DB::table('users')->where('id', $userId)->first();
        }

        // 4. Simpan ke Session
        session([
            'user_id' => $user->id, 
            'user_name' => $user->name
        ]);

        return redirect('/dashboard');
    }
}