<?php

namespace App\Filament\Widgets\Umkm\Chart;

use App\Models\AkunKeuangan;
use App\Models\DetailJurnalUmum;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UmkmMutasiChart extends ChartWidget
{
    protected ?string $heading = 'Analisis Mutasi Kas Bulanan';
    protected ?string $maxHeight = '300px';
    protected int | string | array $columnSpan = 'full';

    // Property untuk menampung data cache agar tidak query DB 2 kali
    protected ?array $cachedMutasi = null;

    /**
     * Method khusus untuk mengambil data dari DB dan menyimpannya ke cache memory.
     */
    protected function getMutasiData(): array
    {
        // Jika data sudah di-query sebelumnya, langsung gunakan data tersebut
        if ($this->cachedMutasi !== null) {
            return $this->cachedMutasi;
        }

        $userId = Auth::id() ?? 1;
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // 1. AMBIL ID KAS & BANK
        $kasIds = AkunKeuangan::where(function($query) use ($userId) {
                $query->where('idUsers', $userId)->orWhereNull('idUsers');
            })
            ->get()
            ->filter(fn($akun) => str_contains(strtolower($akun->name), 'kas') || str_contains(strtolower($akun->name), 'bank'))
            ->pluck('id')
            ->toArray();

        if (empty($kasIds)) { 
            $kasIds = [0]; 
        }

        // 2. QUERY MUTASI
        $mutasi = DetailJurnalUmum::whereHas('jurnalUmum', function ($query) use ($userId, $currentMonth, $currentYear, $kasIds) {
                $query->where(function($q) use ($userId) {
                        $q->where('idUsers', $userId)->orWhereNull('idUsers');
                    })
                    ->whereMonth('periode', $currentMonth)
                    ->whereYear('periode', $currentYear)
                    ->whereIn('idAkunKeuangan', $kasIds);
            })
            ->selectRaw("
                SUM(CASE WHEN UPPER(TRIM(is_debet)) = 'D' OR is_debet = '1' THEN amount ELSE 0 END) as total_debet,
                SUM(CASE WHEN UPPER(TRIM(is_debet)) != 'D' AND is_debet != '1' THEN amount ELSE 0 END) as total_kredit
            ")
            ->first();

        // Simpan hasil ke cache property
        $this->cachedMutasi = [
            'debet' => (float) ($mutasi->total_debet ?? 0),
            'kredit' => (float) ($mutasi->total_kredit ?? 0),
        ];

        return $this->cachedMutasi;
    }

    protected function getData(): array
    {
        // Panggil method cache kita
        $data = $this->getMutasiData();

        return [
            'datasets' => [
                [
                    'label' => 'Uang Masuk (Debet Kas)',
                    'data' => [$data['debet']],
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#10b981',
                ],
                [
                    'label' => 'Uang Keluar (Kredit Kas)',
                    'data' => [$data['kredit']],
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#f59e0b',
                ],
            ],
            'labels' => [Carbon::now()->translatedFormat('F Y')],
        ];
    }

    /**
     * Menambahkan deskripsi di bawah judul chart untuk menampilkan status jika data kosong.
     * PERBAIKAN: Visibility diubah menjadi public sesuai dengan parent class ChartWidget.
     */
    public function getDescription(): ?string
    {
        $data = $this->getMutasiData();

        // Validasi jika pemasukan dan pengeluaran sama dengan 0
        if ($data['debet'] === 0.0 && $data['kredit'] === 0.0) {
            return '⚠️ Tidak ada data pemasukan dan pengeluaran pada bulan ini.';
        }

        return 'Total akumulasi pemasukan dan pengeluaran kas.';
    }

    protected function getType(): string
    {
        return 'bar';
    }
}