<?php

namespace App\Http\Controllers;

use App\Models\AkunKeuangan;
use App\Models\JurnalUmum;
use App\Models\SaldoAwal;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NeracaSaldoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
    {
        $bulan = $request->query('bulan', date('m'));
        $tahun = $request->query('tahun', date('Y'));
        
        $idUsers = auth()->id() ?? 1;

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

                $saldoBerjalan = $saldoAwalDebet - $saldoAwalKredit;

                // Ambil Transaksi
                $jurnals = JurnalUmum::where('idAkunKeuangan', $akun->id)
                    ->whereMonth('periode', (int) $bulan)
                    ->whereYear('periode', (int) $tahun)
                    ->with('details')
                    ->get();

                foreach ($jurnals as $jurnal) {
                    foreach ($jurnal->details as $detail) {
                        $isDebet = strtoupper(trim($detail->is_debet)) === 'D';
                        $debit = $isDebet ? $detail->amount : 0;
                        $kredit = !$isDebet ? $detail->amount : 0;

                        $saldoBerjalan += ($debit - $kredit);
                    }
                }

                // Tentukan letak saldo akhir di Neraca (Debet atau Kredit)
                $debetAkhir = $saldoBerjalan > 0 ? $saldoBerjalan : 0;
                $kreditAkhir = $saldoBerjalan < 0 ? abs($saldoBerjalan) : 0;

                return (object)[
                    'no_referensi' => $akun->no_referensi,
                    'nama_akun' => $akun->name,
                    'debet' => $debetAkhir,
                    'kredit' => $kreditAkhir,
                    'saldo_akhir' => $saldoBerjalan
                ];
            })
            // Filter: Hanya tampilkan akun yang memiliki saldo (tidak nol)
            ->filter(function($akun) {
                return $akun->debet > 0 || $akun->kredit > 0;
            });

        // Hitung Total Seluruh Debet dan Kredit
        $totalDebet = $akunKeuangans->sum('debet');
        $totalKredit = $akunKeuangans->sum('kredit');

        // Setup format tanggal (Cth: PER 31 OKTOBER 2024)
        $bulanFormatted = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $lastDayOfMonth = Carbon::createFromDate($tahun, $bulanFormatted, 1)->endOfMonth()->format('d');
        $namaBulan = Carbon::createFromFormat('m', $bulanFormatted)->translatedFormat('F');
        $periodeString = "PER {$lastDayOfMonth} " . strtoupper($namaBulan) . " {$tahun}";

        return view('dokumen.neraca_saldo', compact('akunKeuangans', 'periodeString', 'totalDebet', 'totalKredit'));
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
