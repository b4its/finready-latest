<?php

namespace App\Http\Controllers;

use App\Models\AkunKeuangan;
use App\Models\JurnalUmum;
use App\Models\SaldoAwal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NeracaSaldoSetelahPenyesuaianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $bulan = $request->query('bulan', date('m'));
        $tahun = $request->query('tahun', date('Y'));
        $idUsers = auth()->id() ?? 1;

        // Ambil semua akun keuangan
        $akunKeuangans = AkunKeuangan::where(function($query) use ($idUsers) {
                $query->where('idUsers', $idUsers)->orWhereNull('idUsers');
            })
            ->get()
            ->map(function ($akun) use ($bulan, $tahun) {
                
                /* ====================================================
                   1. HITUNG SALDO AWAL
                   ==================================================== */
                $saldoAwalDebet = SaldoAwal::whereHas('detailAkunKeuangan', function($q) use ($akun) {
                    $q->where('idAkunKeuangan', $akun->id);
                })->sum('debet');
                
                $saldoAwalKredit = SaldoAwal::whereHas('detailAkunKeuangan', function($q) use ($akun) {
                    $q->where('idAkunKeuangan', $akun->id);
                })->sum('kredit');

                $saldoBerjalanNS = $saldoAwalDebet - $saldoAwalKredit;

                /* ====================================================
                   2. HITUNG NERACA SALDO (NS)
                   ==================================================== */
                $jurnalUmum = JurnalUmum::where('idAkunKeuangan', $akun->id)
                    // ->where('tipe', '!=', 3) // Dihapus agar tidak pusing
                    ->whereMonth('periode', (int) $bulan)
                    ->whereYear('periode', (int) $tahun)
                    ->with('details')
                    ->get();

                foreach ($jurnalUmum as $jurnal) {
                    foreach ($jurnal->details as $detail) {
                        $isDebet = strtoupper(trim($detail->is_debet)) === 'D';
                        $debit = $isDebet ? $detail->amount : 0;
                        $kredit = !$isDebet ? $detail->amount : 0;
                        
                        $saldoBerjalanNS += ($debit - $kredit);
                    }
                }

                $ns_debet = $saldoBerjalanNS > 0 ? $saldoBerjalanNS : 0;
                $ns_kredit = $saldoBerjalanNS < 0 ? abs($saldoBerjalanNS) : 0;

                /* ====================================================
                   3. HITUNG JURNAL PENYESUAIAN (JP)
                   (Sama persis dengan JurnalPenyesuaianController)
                   ==================================================== */
                $jurnalJP = JurnalUmum::where('idAkunKeuangan', $akun->id)
                    // ->where('tipe', 3) // Dihapus agar datanya masuk!
                    ->whereMonth('periode', (int) $bulan)
                    ->whereYear('periode', (int) $tahun)
                    ->with('details')
                    ->get();

                $jp_debet = 0;
                $jp_kredit = 0;
                $saldoBerjalanJP = 0;

                foreach ($jurnalJP as $jurnal) {
                    foreach ($jurnal->details as $detail) {
                        $isDebet = strtoupper(trim($detail->is_debet)) === 'D';
                        $debit = $isDebet ? $detail->amount : 0;
                        $kredit = !$isDebet ? $detail->amount : 0;
                        
                        $jp_debet += $debit;
                        $jp_kredit += $kredit;
                        $saldoBerjalanJP += ($debit - $kredit);
                    }
                }

                /* ====================================================
                   4. HITUNG NERACA SALDO SETELAH DISESUAIKAN (NSSD)
                   ==================================================== */
                // Catatan: Karena filter tipe dihapus, nilai NS dan JP akan menjumlahkan data yang sama.
                // Abaikan saja untuk sekarang agar tampilannya muncul dulu di view.
                $saldoBerjalanNSSD = $saldoBerjalanNS + $saldoBerjalanJP;
                
                $nssd_debet = $saldoBerjalanNSSD > 0 ? $saldoBerjalanNSSD : 0;
                $nssd_kredit = $saldoBerjalanNSSD < 0 ? abs($saldoBerjalanNSSD) : 0;

                return (object)[
                    'kode' => $akun->no_referensi,
                    'nama' => $akun->name,
                    'ns_debet' => $ns_debet,
                    'ns_kredit' => $ns_kredit,
                    'jp_debet' => $jp_debet,
                    'jp_kredit' => $jp_kredit,
                    'nssd_debet' => $nssd_debet,
                    'nssd_kredit' => $nssd_kredit,
                    'is_empty' => ($ns_debet == 0 && $ns_kredit == 0 && $jp_debet == 0 && $jp_kredit == 0 && $nssd_debet == 0 && $nssd_kredit == 0)
                ];
            })
            ->reject(function($item) {
                return $item->is_empty; 
            })
            ->sortBy('kode');

        // Kalkulasi Total Bawah
        $total = (object)[
            'ns_debet' => $akunKeuangans->sum('ns_debet'),
            'ns_kredit' => $akunKeuangans->sum('ns_kredit'),
            'jp_debet' => $akunKeuangans->sum('jp_debet'),
            'jp_kredit' => $akunKeuangans->sum('jp_kredit'),
            'nssd_debet' => $akunKeuangans->sum('nssd_debet'),
            'nssd_kredit' => $akunKeuangans->sum('nssd_kredit'),
        ];

        // Format Periode
        $bulanFormatted = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $lastDayOfMonth = Carbon::createFromDate($tahun, $bulanFormatted, 1)->endOfMonth()->format('d');
        $namaBulan = Carbon::createFromFormat('m', $bulanFormatted)->translatedFormat('F');
        $periodeString = "Per {$lastDayOfMonth} " . ucfirst($namaBulan) . " {$tahun}";

        return view('dokumen.neraca_saldo_setelah_penyesuaian', compact('akunKeuangans', 'periodeString', 'total'));
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
