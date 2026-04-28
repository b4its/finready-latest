<?php

namespace App\Filament\Widgets\Investor;

use App\Models\LearnProgress;
use App\Models\PengajuanDataKeuangan;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class InvestorStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();

        // 1. Total Pengajuan Data Keuangan oleh Investor ini
        $totalPengajuan = PengajuanDataKeuangan::where('idUsers', $userId)->count();

        // 2. Total Pengajuan yang Diterima atau Sudah Didanai (Status 1 dan 2)
        // Perbaikan: Menggunakan whereIn agar query tetap terisolasi hanya untuk user ini
        $totalPengajuanDiterima = PengajuanDataKeuangan::where('idUsers', $userId)
            ->where('status_pengajuan', 1)
            ->count();

        $totalPengajuanDdanai = PengajuanDataKeuangan::where('idUsers', $userId)
            ->where('status_pengajuan', 2)
            ->count();

            



        // 4. Hitung UMKM Level Ready (Total Poin > 1500)
        // Langkah A: Ambil semua ID User yang akumulasi poinnya lebih dari 1500
        $readyUmkmIds = LearnProgress::select('idUsers')
            ->groupBy('idUsers')
            ->havingRaw('SUM(point) > 1500')
            ->pluck('idUsers');

        // Langkah B: Hitung jumlah User dengan role 'umkm' yang ID-nya ada di Langkah A
        $umkmReadyCount = User::where('role', 'umkm')
            ->whereIn('id', $readyUmkmIds)
            ->count();

                // 3. Total Keseluruhan UMKM di sistem
        // Menghitung user yang rolenya umkm DAN memiliki data di tabel umkm_profiles
        $totalUMKM = User::where('role', 'umkm')
            ->whereHas('umkmProfile') // Pastikan nama relasi di model User adalah umkmProfile
            ->count();

        // Untuk perhitungan UMKM Level Ready juga harus difilter hal yang sama
        $umkmReadyCount = User::where('role', 'umkm')
            ->whereHas('umkmProfile')
            ->whereIn('id', $readyUmkmIds)
            ->count();

        return [
            Stat::make('Total UMKM', $totalUMKM)
                ->description('Keseluruhan UMKM terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('UMKM Level Ready', $umkmReadyCount)
                ->description('Total UMKM telah siap')
                ->descriptionIcon('heroicon-m-star')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Grafik dummy opsional agar UI lebih menarik

            Stat::make('Pengajuan Data Keuangan', $totalPengajuan)
                ->description('Permintaan akses data')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),

            Stat::make('Pengajuan Data Keuangan yang diterima', $totalPengajuanDiterima)
                ->description('Total Pengajuan Data Keuangan yang diterima')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            
            Stat::make('UMKM yang telah didanai', $totalPengajuanDdanai)
                ->description('Total UMKM yang telah didanai')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}