<?php

namespace App\Http\Controllers;

use App\Models\JurnalUmum;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class JurnalPenyesuaianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
    {
        $bulan = $request->query('bulan', date('m'));
        $tahun = $request->query('tahun', date('Y'));
        
        // PERBAIKAN UTAMA: Tambahkan ->where('tipe', 3) agar hanya menarik data Penyesuaian
        $jurnals = JurnalUmum::with(['akunKeuangan', 'details'])
            ->where('tipe', 3) 
            ->whereMonth('periode', (int) $bulan)
            ->whereYear('periode', (int) $tahun)
            ->orderBy('periode', 'asc')
            ->get();

        $totalDebit = 0;
        $totalKredit = 0;
        $rekap = []; // Array untuk menyimpan data tabel Rekapitulasi

        // Proses data untuk jurnal utama dan merangkum rekapitulasi
        foreach ($jurnals as $jurnal) {
            foreach ($jurnal->details as $detail) {
                // Membaca karakter 'D' atau 'K' langsung dari database
                $isDebet = strtoupper(trim($detail->is_debet)) === 'D';
                $amount = $detail->amount;

                // Tambah Total Bawah untuk memvalidasi Balance
                if ($isDebet) {
                    $totalDebit += $amount;
                } else {
                    $totalKredit += $amount;
                }

                // Kalkulasi Rekapitulasi per Akun
                $akunId = $jurnal->akunKeuangan->id ?? 0;
                $akunNo = $jurnal->akunKeuangan->no_referensi ?? '-';

                if (!isset($rekap[$akunId])) {
                    $rekap[$akunId] = [
                        'no_referensi' => $akunNo,
                        'debit' => 0,
                        'kredit' => 0
                    ];
                }

                if ($isDebet) {
                    $rekap[$akunId]['debit'] += $amount;
                } else {
                    $rekap[$akunId]['kredit'] += $amount;
                }
            }
        }

        // Urutkan rekapitulasi berdasarkan Nomor Referensi Akun agar rapi (Ascending)
        usort($rekap, function($a, $b) {
            return $a['no_referensi'] <=> $b['no_referensi'];
        });

        // Format Tanggal untuk Header
        $bulanFormatted = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $lastDayOfMonth = Carbon::createFromDate($tahun, $bulanFormatted, 1)->endOfMonth()->format('d');
        $namaBulan = Carbon::createFromFormat('m', $bulanFormatted)->translatedFormat('F');
        $periodeString = "{$lastDayOfMonth} " . $namaBulan . " {$tahun}";

        return view('dokumen.jurnal_penyesuaian', compact(
            'jurnals', 'periodeString', 'namaBulan', 'totalDebit', 'totalKredit', 'rekap'
        ));
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
