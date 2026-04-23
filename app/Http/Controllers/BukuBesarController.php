<?php

namespace App\Http\Controllers;

use App\Models\AkunKeuangan;
use App\Models\JurnalUmum;
use App\Models\SaldoAwal;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BukuBesarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
    {
        // 1. Ambil Periode dari parameter URL (?bulan=04&tahun=2026)
        $bulan = $request->query('bulan', date('m'));
        $tahun = $request->query('tahun', date('Y'));
        
        // Menggunakan ID User yang sedang login (fallback ke 1 jika null)
        $idUsers = auth()->id() ?? 1; 

        // 2. Ambil Akun Keuangan (Tangkap yang spesifik milik user ATAU akun global yang NULL)
        $akunKeuangans = AkunKeuangan::where(function($query) use ($idUsers) {
                $query->where('idUsers', $idUsers)->orWhereNull('idUsers');
            })
            ->get()
            ->map(function ($akun) use ($bulan, $tahun) {
                
                // Ambil Saldo Awal
                $saldoAwalDebet = SaldoAwal::whereHas('detailAkunKeuangan', function($q) use ($akun) {
                    $q->where('idAkunKeuangan', $akun->id);
                })->sum('debet');
                
                $saldoAwalKredit = SaldoAwal::whereHas('detailAkunKeuangan', function($q) use ($akun) {
                    $q->where('idAkunKeuangan', $akun->id);
                })->sum('kredit');

                $saldoAwal = $saldoAwalDebet - $saldoAwalKredit;
                $saldoBerjalan = $saldoAwal;

                // Ambil transaksi Jurnal Umum (Perbaikan: casting tipe data bulan dan tahun)
                $jurnals = JurnalUmum::where('idAkunKeuangan', $akun->id)
                    ->whereMonth('periode', (int) $bulan) 
                    ->whereYear('periode', (int) $tahun)
                    ->with('details') 
                    ->orderBy('periode', 'asc')
                    ->get();

                $transaksi = [];
                
                // Ekstraksi Detail Jurnal
                foreach ($jurnals as $jurnal) {
                    foreach ($jurnal->details as $detail) {
                        
                        // PERBAIKAN: Baca indikator D (Debet) dan K (Kredit) persis dari database
                        $isDebet = strtoupper(trim($detail->is_debet)) === 'D';
                        
                        $debit = $isDebet ? $detail->amount : 0;
                        $kredit = !$isDebet ? $detail->amount : 0;

                        // Perhitungan Saldo 
                        $saldoBerjalan += ($debit - $kredit);

                        $transaksi[] = [
                            'tanggal' => Carbon::parse($jurnal->periode)->format('d'),
                            'keterangan' => $detail->keterangan ?? $jurnal->keterangan,
                            'ref' => $detail->no_faktur ?? '-',
                            'debit' => $debit,
                            'kredit' => $kredit,
                            'saldo' => $saldoBerjalan,
                        ];
                    }
                }

                return (object)[
                    'no_referensi' => $akun->no_referensi,
                    'nama_akun' => $akun->name,
                    'saldo_awal' => $saldoAwal,
                    'transaksi' => $transaksi,
                    'total_debit' => collect($transaksi)->sum('debit'),
                    'total_kredit' => collect($transaksi)->sum('kredit'),
                    'saldo_akhir' => $saldoBerjalan
                ];
            })
            // Tampilkan hanya jika ada data transaksi / ada mutasi saldo
            ->filter(function($akun) {
                return count($akun->transaksi) > 0 || $akun->saldo_awal != 0;
            });

        // 3. Format String Periode (Pastikan bulan terbaca dua digit menggunakan str_pad)
        $bulanFormatted = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $namaBulan = Carbon::createFromFormat('m', $bulanFormatted)->translatedFormat('F');
        $periodeString = $namaBulan . ' ' . $tahun;

        return view('dokumen.buku_besar', compact('akunKeuangans', 'periodeString', 'tahun', 'namaBulan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
