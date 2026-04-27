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

        // 1. Hitung Total Saldo Awal (Debet - Kredit) khusus tipe 2
        $totalSaldoAwal = SaldoAwal::where('idUsers', $userId)
            ->where('tipe', 2) // <-- TAMBAHAN: Filter tipe uji coba
            ->selectRaw('SUM(debet) - SUM(kredit) as net_saldo')
            ->value('net_saldo') ?? 0;

        // 2. Hitung Mutasi dari Jurnal Umum bulan ini khusus tipe 2
        $mutasiBulanIni = DetailJurnalUmum::whereHas('jurnalUmum', function ($query) use ($userId, $currentMonth, $currentYear) {
                $query->where('idUsers', $userId)
                      ->whereMonth('periode', $currentMonth)
                      ->whereYear('periode', $currentYear)
                      ->where('tipe', 2); // <-- TAMBAHAN: Filter tipe uji coba pada header
            })
            ->selectRaw("
                SUM(CASE WHEN is_debet = 1 THEN amount ELSE 0 END) as total_debet,
                SUM(CASE WHEN is_debet = 0 THEN amount ELSE 0 END) as total_kredit
            ") // <-- PERBAIKAN: Menggunakan integer 1 dan 0, bukan string '1' dan '0'
            ->first();

        $netMutasi = ($mutasiBulanIni->total_debet ?? 0) - ($mutasiBulanIni->total_kredit ?? 0);

        // 3. Saldo Akhir
        $totalSaldoAkhir = $totalSaldoAwal + $netMutasi;
        
        // 4. PERBAIKAN: Query Builder untuk Point (Filter by User & pastikan point valid)
        $total_poin_progress = LearnProgress::where('idUsers', $userId)
            ->whereNotNull('point')
            ->count(); 
            // Catatan: Gunakan ->sum('point') jika Anda ingin menjumlahkan nominal poinnya, bukan menghitung jumlah barisnya.

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
                    $totalSaldoAwal + (($mutasiBulanIni->total_debet ?? 0) * 0.5), // Pastikan tidak null
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