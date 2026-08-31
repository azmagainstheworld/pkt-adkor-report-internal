<?php

namespace App\Http\Controllers;

use App\Models\StrukturOrganisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StrukturOrganisasiController extends Controller
{
    public function index()
    {
        // Ambil data pertama, jika belum ada, buat data kosong default
        $struktur = StrukturOrganisasi::firstOrCreate(
            ['id' => 1],
            [
                'deskripsi' => 'Berdasarkan SK Direksi Nomor: ... tentang Struktur Organisasi PT Pupuk Kalimantan Timur, lingkup koordinasi Unit Kerja Administrasi Korporat meliputi:',
                'gambar' => null
            ]
        );

        return view('struktur-organisasi', compact('struktur'));
    }

    public function update(Request $request)
    {
        // Validasi input
        $request->validate([
            'deskripsi' => 'required|string',
            'gambar'    => 'nullable|image|mimes:jpeg,png,jpg,svg|max:51200', // Maks 5MB
        ]);

        $struktur = StrukturOrganisasi::first();
        $struktur->deskripsi = $request->deskripsi;

        // Jika ada file gambar yang diunggah
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($struktur->gambar && Storage::disk('public')->exists($struktur->gambar)) {
                Storage::disk('public')->delete($struktur->gambar);
            }
            
            // Simpan gambar baru
            $path = $request->file('gambar')->store('struktur_organisasi', 'public');
            $struktur->gambar = $path;
        }

        $struktur->save();

        return redirect()->back()->with('success', 'Struktur Organisasi berhasil diperbarui!');
    }
}
