<?php

namespace App\Http\Controllers;

use App\Models\PengajuanDataKeuangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengajuanDataKeuanganController extends Controller
{
    //
    public function store(Request $request)
    {
        // 1. Validasi menggunakan array syntax (lebih aman) dan aturan 'confirmed'
        $validated = $request->validate([
            'alasan' => ['required'],
        ]);

        // 2. Gunakan DB Transaction untuk konsistensi data
        $user = DB::transaction(function () use ($validated) {
            $newUser = PengajuanDataKeuangan::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => $validated['password'], 
                'role'     => $validated['role'], 
            ]);

            return $newUser;
        });

        return redirect()
            ->route('filament.investor.pages.daftar-umkm')
            ->with('success', 'Pengajuan Data Keuangan Telah Berhasil!');
    }
}
