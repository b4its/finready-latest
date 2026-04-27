<?php

namespace App\Filament\Widgets\Umkm\Chart;

use App\Models\DetailJurnalUmum;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UmkmMutasiChart extends ChartWidget
{
    // SEMUA PROPERTI DI BAWAH INI TIDAK BOLEH PAKAI 'static'
    protected ?string $heading = 'Analisis Mutasi Bulanan';
    
    // TIPE DATA DIUBAH KE ?string MENGIKUTI CLASS INDUK (ChartWidget)
    protected ?string $maxHeight = '300px'; 

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $userId = Auth::id();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Query mengambil total debet dan kredit
        $mutasi = DetailJurnalUmum::whereHas('jurnalUmum', function ($query) use ($userId, $currentMonth, $currentYear) {
                $query->where('idUsers', $userId)
                      ->whereMonth('periode', $currentMonth)
                      ->whereYear('periode', $currentYear);
            })
            ->selectRaw("
                SUM(CASE WHEN is_debet = '1' THEN amount ELSE 0 END) as total_debet,
                SUM(CASE WHEN is_debet = '0' THEN amount ELSE 0 END) as total_kredit
            ")
            ->first();

        $debet = $mutasi->total_debet ?? 0;
        $kredit = $mutasi->total_kredit ?? 0;

        return [
            'datasets' => [
                [
                    'label' => 'Mutasi Masuk (Debet)',
                    'data' => [$debet],
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#3b82f6',
                ],
                [
                    'label' => 'Mutasi Keluar (Kredit)',
                    'data' => [$kredit],
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#f59e0b',
                ],
            ],
            'labels' => [Carbon::now()->translatedFormat('F Y')], 
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => "function(value) { 
                            return 'Rp ' + value.toLocaleString('id-ID'); 
                        }",
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
        ];
    }
}