<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatatanController extends Controller
{
    public function index()
    {
        $userId = session('user_id') ?? auth()->id();
        $userDetails = DB::table('users')->where('id', $userId)->first();
        
        // Ambil catatan milik user, urutkan yang di-pin di atas, lalu berdasarkan update terbaru
        $notes = DB::table('notes')
                    ->where('user_id', $userId)
                    ->orderBy('is_pinned', 'desc')
                    ->orderBy('updated_at', 'desc')
                    ->get();

        return view('catatan', compact('userDetails', 'notes'));
    }

    public function store(Request $request)
    {
        $userId = session('user_id') ?? auth()->id();

        DB::table('notes')->updateOrInsert(
            ['id' => $request->id], // Jika ID ada maka update, jika tidak ada maka insert
            [
                'user_id' => $userId,
                'title' => $request->title,
                'content' => $request->content,
                'color' => $request->color ?? 'yellow',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        DB::table('notes')->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }
}