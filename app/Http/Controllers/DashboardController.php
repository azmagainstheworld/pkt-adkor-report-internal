<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = Carbon::now();
        $batasWaktu = Carbon::now()->addDays(30); // Pengingat H-30

        // 1. Ambil 5 Perizinan yang akan kedaluwarsa dalam 30 hari ke depan
        $alertPerizinan = DB::table('perizinan_terbit')
            ->where('tanggal_akhir', '>=', $hariIni)
            ->where('tanggal_akhir', '<=', $batasWaktu)
            ->orderBy('tanggal_akhir', 'asc')
            ->take(5)
            ->get();

        // 2. Ambil 5 Rapat/BAR yang statusnya belum "Selesai"
        $alertRapat = DB::table('bar_sk_memo_rapat')
            ->where('status', '!=', 'Selesai') // Pastikan kata 'Selesai' cocok dengan data di DB kamu
            ->orderBy('tanggal_rapat', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact('alertPerizinan', 'alertRapat'));
    }
}