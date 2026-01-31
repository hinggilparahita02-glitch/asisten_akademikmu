<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MataKuliahController extends Controller
{


    // Fungsi pembantu untuk konversi nilai huruf ke bobot angka
    private function gradeToPoint($grade) {
        $points = [
            'A' => 4.0, 'A-' => 3.7, 'B+' => 3.3, 'B' => 3.0, 
            'B-' => 2.7, 'C+' => 2.3, 'C' => 2.0, 'C-' => 1.7, 
            'D' => 1.0, 'E' => 0
        ];
        return $points[$grade] ?? 0;
    }
}