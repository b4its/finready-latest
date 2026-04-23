<?php

namespace App\Filament\Widgets\Umkm;

use App\Models\LearnProgress;
use App\Models\SaldoAwal;
use App\Models\DetailJurnalUmum;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UmkmStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // 1. Hitung Total Saldo Awal (Debet - Kredit)
        $totalSaldoAwal = SaldoAwal::where('idUsers', $userId)// Menggunakan WHERE (AND), bukan OR
            ->selectRaw('SUM(debet) - SUM(kredit) as net_saldo')
            ->value('net_saldo') ?? 0;

        // 2. Hitung Mutasi dari Jurnal Umum bulan ini
        // Kita join dari Detail ke Header untuk memfilter idUsers dan Periode
        $mutasiBulanIni = DetailJurnalUmum::whereHas('jurnalUmum', function ($query) use ($userId, $currentMonth, $currentYear) {
                $query->where('idUsers', $userId)
                      ->whereMonth('periode', $currentMonth)
                      ->whereYear('periode', $currentYear);
            })
            ->selectRaw("
                SUM(CASE WHEN is_debet = '1' THEN amount ELSE 0 END) as total_debet,
                SUM(CASE WHEN is_debet = '0' THEN amount ELSE 0 END) as total_kredit
            ")
            ->first();

        $netMutasi = ($mutasiBulanIni->total_debet ?? 0) - ($mutasiBulanIni->total_kredit ?? 0);

        // 3. Saldo Akhir
        $totalSaldoAkhir = $totalSaldoAwal + $netMutasi;
        $total_poin_progress =  LearnProgress::where('point')->count();

        return [
            Stat::make('Total Point', $total_poin_progress)
                ->description('Jumlah Point Yang Telah Diperoleh')
                ->descriptionIcon('heroicon-m-star')
                ->icon('heroicon-m-star')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Total Saldo Bulan Ini', 'Rp ' . number_format($totalSaldoAkhir, 0, ',', '.'))
                ->description('Saldo awal ditambah mutasi jurnal bulan berjalan')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($totalSaldoAkhir >= 0 ? 'success' : 'danger')
                ->chart([
                    $totalSaldoAwal, 
                    $totalSaldoAwal + ($mutasiBulanIni->total_debet * 0.5), 
                    $totalSaldoAkhir
                ]),
            
            Stat::make('Mutasi Debet (Masuk)', 'Rp ' . number_format($mutasiBulanIni->total_debet ?? 0, 0, ',', '.'))
                ->description('Total pemasukan/debet bulan ini')
                ->icon('heroicon-m-arrow-trending-up')
                ->color('info'),

            Stat::make('Mutasi Kredit (Keluar)', 'Rp ' . number_format($mutasiBulanIni->total_kredit ?? 0, 0, ',', '.'))
                ->description('Total pengeluaran/kredit bulan ini')
                ->icon('heroicon-m-arrow-trending-down')
                ->color('warning'),
        ];
    }
}