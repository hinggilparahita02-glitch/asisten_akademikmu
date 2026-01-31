<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ChecklistController extends Controller
{
    public function index()
    {
        $userId = Session::get('user_id') ?? auth()->id();
        $userDetails = DB::table('users')->where('id', $userId)->first();
        
        // Menggunakan tabel 'tasks' karena 'checklists' tidak ada di database
        $tasks = DB::table('tasks')->where('user_id', $userId)->get();

        return view('checklist', compact('userDetails', 'tasks'));
    }

    public function store(Request $request)
    {
        $userId = Session::get('user_id') ?? auth()->id();

        // Validasi input agar data yang masuk benar
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:daily,goal',
        ]);

        // Simpan ke tabel 'tasks'
        DB::table('tasks')->insert([
            'user_id' => $userId,
            'title' => $request->title,
            'type' => $request->type,
            'is_completed' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function toggle(Request $request)
    {
        // Update status di tabel 'tasks'
        DB::table('tasks')
            ->where('id', $request->id)
            ->update(['is_completed' => $request->is_completed]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        // Hapus data dari tabel 'tasks'
        DB::table('tasks')->where('id', $id)->delete();
        
        return response()->json(['success' => true]);
    }
}