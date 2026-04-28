<?php

namespace App\Filament\Widgets\Umkm;

use App\Models\LearnProgress;
use App\Models\SaldoAwal;
use App\Models\AkunKeuangan;
use App\Models\DetailJurnalUmum;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UmkmStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id() ?? 1;
        // Secara default mengambil bulan & tahun saat ini (Pastikan data Anda berada di bulan/tahun ini)
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // =========================================================================
        // 1. AMBIL ID KAS & BANK
        // =========================================================================
        $kasIds = AkunKeuangan::where(function($query) use ($userId) {
                $query->where('idUsers', $userId)->orWhereNull('idUsers');
            })
            ->get()
            ->filter(function($akun) {
                $nama = strtolower(trim($akun->name));
                return str_contains($nama, 'kas') || str_contains($nama, 'bank');
            })
            ->pluck('id')
            ->toArray();

        // Cegah error SQL jika kasIds kosong
        if (empty($kasIds)) {
            $kasIds = [0]; 
        }

        // =========================================================================
        // 2. HITUNG SALDO AWAL KHUSUS KAS (TANPA FILTER TIPE)
        // =========================================================================
        $totalSaldoAwal = SaldoAwal::whereHas('detailAkunKeuangan', function($q) use ($kasIds) {
                $q->whereIn('idAkunKeuangan', $kasIds);
            })
            ->where(function($query) use ($userId) {
                $query->where('idUsers', $userId)->orWhereNull('idUsers');
            })
            ->selectRaw('SUM(debet) - SUM(kredit) as net_saldo')
            ->value('net_saldo') ?? 0;

        // =========================================================================
        // 3. HITUNG MUTASI KAS BULAN INI (TANPA FILTER TIPE)
        // =========================================================================
        $mutasiBulanIni = DetailJurnalUmum::whereHas('jurnalUmum', function ($query) use ($userId, $currentMonth, $currentYear, $kasIds) {
                $query->where(function($q) use ($userId) {
                        $q->where('idUsers', $userId)->orWhereNull('idUsers');
                    })
                    ->whereMonth('periode', $currentMonth)
                    ->whereYear('periode', $currentYear)
                    // HAPUS ->where('tipe', 2) DI SINI AGAR DATA MUNCUL
                    ->whereIn('idAkunKeuangan', $kasIds); 
            })
            ->selectRaw("
                SUM(CASE WHEN UPPER(TRIM(is_debet)) = 'D' OR is_debet = '1' THEN amount ELSE 0 END) as total_debet,
                SUM(CASE WHEN UPPER(TRIM(is_debet)) != 'D' AND is_debet != '1' THEN amount ELSE 0 END) as total_kredit
            ") 
            ->first();

        $totalDebet = $mutasiBulanIni->total_debet ?? 0;
        $totalKredit = $mutasiBulanIni->total_kredit ?? 0;
        $netMutasi = $totalDebet - $totalKredit;

        // =========================================================================
        // 4. SALDO AKHIR KAS
        // =========================================================================
        $totalSaldoAkhir = $totalSaldoAwal + $netMutasi;
        
        // =========================================================================
        // 5. PROGRESS BELAJAR
        // =========================================================================
        $total_poin_progress = LearnProgress::where('idUsers', $userId)
            ->whereNotNull('point')
            ->sum("point"); 

        $indikator_level = $total_poin_progress > 1500 
                            ? "Ready" 
                            : ($total_poin_progress > 1000 
                                ? "Structured" 
                                : ($total_poin_progress > 500 ? "Discipline" : "Learning"));

        return [
            Stat::make('Indikator', $indikator_level)
                ->description('Indikator saat ini berdasarkan total poin')
                ->descriptionIcon('heroicon-m-star')
                ->icon('heroicon-m-star')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Total Point', number_format($total_poin_progress, 0, ',', '.'))
                ->description('Jumlah Point Yang Telah Diperoleh')
                ->descriptionIcon('heroicon-m-star')
                ->icon('heroicon-m-star')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
                
            Stat::make('Saldo Kas & Bank', 'Rp ' . number_format($totalSaldoAkhir, 0, ',', '.'))
                ->description('Saldo awal ditambah mutasi kas bulan berjalan')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($totalSaldoAkhir >= 0 ? 'success' : 'danger')
                ->chart([
                    $totalSaldoAwal, 
                    $totalSaldoAwal + ($totalDebet * 0.5), 
                    $totalSaldoAkhir
                ]),
            
            Stat::make('Mutasi Debet (Masuk)', 'Rp ' . number_format($totalDebet, 0, ',', '.'))
                ->description('Total pemasukan kas bulan ini')
                ->icon('heroicon-m-arrow-trending-up')
                ->color('success'), 

            Stat::make('Mutasi Kredit (Keluar)', 'Rp ' . number_format($totalKredit, 0, ',', '.'))
                ->description('Total pengeluaran kas bulan ini')
                ->icon('heroicon-m-arrow-trending-down')
                ->color('warning'),
        ];
    }
}