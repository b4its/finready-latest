<?php

namespace App\Http\Controllers;

use App\Models\AkunKeuangan;
use App\Models\JurnalUmum;
use App\Models\SaldoAwal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LabaRugiController extends Controller
{
    //
    public function index(Request $request)
    {
        $bulan = $request->query('bulan', date('m'));
        $tahun = $request->query('tahun', date('Y'));
        $idUsers = (int) ($request->query('idUsers') ?? auth()->id() ?? 1);

        // 1. Tarik semua Jurnal & Saldo Awal sekaligus (Optimasi)
        $semuaJurnal = JurnalUmum::with('details')
            ->where(function($query) use ($idUsers) {
                $query->where('idUsers', $idUsers)->orWhereNull('idUsers');
            })
            ->whereMonth('periode', (int) $bulan)
            ->whereYear('periode', (int) $tahun)
            ->get();

        $semuaSaldoAwal = SaldoAwal::with('detailAkunKeuangan')
            ->whereHas('detailAkunKeuangan', function($q) use ($idUsers) {
                $q->where('idUsers', $idUsers)->orWhereNull('idUsers');
            })
            ->get();

        // 2. Ambil Akun MURNI Berdasarkan Kategori Filament Anda
        $akunLabaRugi = AkunKeuangan::where(function($query) use ($idUsers) {
                $query->where('idUsers', $idUsers)->orWhereNull('idUsers');
            })
            ->whereIn('category', ['pendapatan', 'beban_biaya', 'lain-lain'])
            ->get()
            ->map(function ($akun) use ($semuaJurnal, $semuaSaldoAwal) {
                
                // Hitung Saldo Awal
                $akunSaldoAwals = $semuaSaldoAwal->filter(function($s) use ($akun) {
                    return $s->detailAkunKeuangan && $s->detailAkunKeuangan->idAkunKeuangan == $akun->id;
                });
                $saldoAwalDebet = $akunSaldoAwals->sum('debet');
                $saldoAwalKredit = $akunSaldoAwals->sum('kredit');

                // Hitung Total Mutasi Jurnal
                $mutasiDebet = 0;
                $mutasiKredit = 0;
                $jurnals = $semuaJurnal->where('idAkunKeuangan', $akun->id);
                
                foreach ($jurnals as $jurnal) {
                    foreach ($jurnal->details as $detail) {
                        if (strtoupper(trim($detail->is_debet)) === 'D') {
                            $mutasiDebet += $detail->amount;
                        } else {
                            $mutasiKredit += $detail->amount;
                        }
                    }
                }

                // Total Keseluruhan
                $totalDebet = $saldoAwalDebet + $mutasiDebet;
                $totalKredit = $saldoAwalKredit + $mutasiKredit;

                // Hitung Saldo Normal Berdasarkan `category` riil Anda
                if ($akun->category === 'pendapatan' || $akun->category === 'lain-lain') {
                    // Pendapatan Saldo Normal Kredit
                    $saldoAkhir = $totalKredit - $totalDebet;
                } elseif ($akun->category === 'beban_biaya') {
                    // Beban Saldo Normal Debet
                    $saldoAkhir = $totalDebet - $totalKredit;
                } else {
                    $saldoAkhir = 0;
                }

                return (object)[
                    'id' => $akun->id,
                    'kode' => $akun->no_referensi,
                    'nama' => $akun->name,
                    'category' => $akun->category, // Membaca string 'beban_biaya' asli dari DB
                    'detail_category' => $akun->detail_category,
                    'saldo' => $saldoAkhir > 0 ? $saldoAkhir : 0, 
                ];
            })
            ->reject(function($item) {
                return $item->saldo == 0; // Sembunyikan akun yang nilainya Rp 0
            });

        // ========================================================
        // 3. PEMETAAN LAPORAN LABA RUGI
        // ========================================================

        // A. PENDAPATAN (Menggabungkan 'pendapatan' dan 'lain-lain')
        $pendapatans = $akunLabaRugi->whereIn('category', ['pendapatan', 'lain-lain'])->values();
        $totalPendapatan = $pendapatans->sum('saldo');

        // B. BEBAN POKOK & OPERASIONAL (Kategori 'beban_biaya')
        $semuaBeban = $akunLabaRugi->where('category', 'beban_biaya');
        
        // Memisahkan Beban Pokok (HPP) jika ada kata 'Persediaan' atau 'Pokok'
        $bebanPokoks = $semuaBeban->filter(function($item) {
            $nama = strtolower($item->nama);
            return str_contains($nama, 'persediaan') || str_contains($nama, 'pokok');
        })->values();
        $totalBebanPokok = $bebanPokoks->sum('saldo');

        // Laba Kotor
        $labaKotor = $totalPendapatan - $totalBebanPokok;

        // Sisanya adalah Beban Operasional (seperti Beban Gaji, Beban Sewa)
        $bebanOperasionals = $semuaBeban->reject(function($item) {
            $nama = strtolower($item->nama);
            return str_contains($nama, 'persediaan') || str_contains($nama, 'pokok');
        })->values();
        $totalBebanOperasional = $bebanOperasionals->sum('saldo');

        // C. LABA BERSIH
        $labaBersih = $labaKotor - $totalBebanOperasional;

        // Format Periode (Contoh: 30 April 2026)
        $bulanFormatted = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $lastDayOfMonth = Carbon::createFromDate($tahun, $bulanFormatted, 1)->endOfMonth()->format('d');
        $namaBulan = Carbon::createFromFormat('m', $bulanFormatted)->translatedFormat('F');
        $periodeString = "{$lastDayOfMonth} " . ucfirst($namaBulan) . " {$tahun}";

        return view('dokumen.laba_rugi', compact(
            'periodeString', 
            'pendapatans', 'totalPendapatan',
            'bebanPokoks', 'totalBebanPokok',
            'labaKotor',
            'bebanOperasionals', 'totalBebanOperasional',
            'labaBersih'
        ));
    }
}
