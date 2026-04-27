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
        // Set default filter ke bulan dan tahun saat ini
        $bulan = $request->query('bulan', date('m'));
        $tahun = $request->query('tahun', date('Y'));
        $idUsers = auth()->id() ?? 1; 

        // Menggunakan algoritma query Jurnal Umum (Tanpa ->where('tipe', 3))
        $jurnals = JurnalUmum::with(['akunKeuangan', 'details'])
            ->where(function($query) use ($idUsers) {
                $query->where('idUsers', $idUsers)->orWhereNull('idUsers');
            })
            ->whereMonth('periode', (int) $bulan)
            ->whereYear('periode', (int) $tahun)
            ->orderBy('periode', 'asc')
            ->get();

        $formattedJurnals = collect();
        $totalDebit = 0;
        $totalKredit = 0;
        $rekap = []; 

        foreach ($jurnals as $jurnal) {
            foreach ($jurnal->details as $detail) {
                $isDebit = strtoupper(trim($detail->is_debet)) === 'D';
                $amount = $detail->amount;

                // Hitung Total Bawah
                if ($isDebit) {
                    $totalDebit += $amount;
                } else {
                    $totalKredit += $amount;
                }

                $akunId = $jurnal->akunKeuangan->id ?? 0;
                $akunNo = $jurnal->akunKeuangan->no_referensi ?? '-';
                $akunName = $jurnal->akunKeuangan->name ?? '-';

                // Kalkulasi Rekapitulasi per Akun
                if (!isset($rekap[$akunId])) {
                    $rekap[$akunId] = [
                        'no_referensi' => $akunNo,
                        'debit' => 0,
                        'kredit' => 0
                    ];
                }

                if ($isDebit) {
                    $rekap[$akunId]['debit'] += $amount;
                } else {
                    $rekap[$akunId]['kredit'] += $amount;
                }

                // Masukkan ke koleksi untuk tabel utama
                $formattedJurnals->push((object)[
                    'tanggal' => $jurnal->periode,
                    'no_faktur' => $detail->no_faktur,
                    'akun_name' => $akunName,
                    'ref' => $akunNo,
                    'debit' => $isDebit ? $amount : 0,
                    'kredit' => !$isDebit ? $amount : 0,
                    'keterangan' => $detail->keterangan ?? $jurnal->keterangan,
                    'is_debit' => $isDebit
                ]);
            }
        }

        // Urutkan rekapitulasi berdasarkan Nomor Referensi Akun (Ascending)
        usort($rekap, function($a, $b) {
            return $a['no_referensi'] <=> $b['no_referensi'];
        });

        // Format Tanggal Dinamis (Contoh: 30 April 2026)
        $bulanFormatted = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $lastDayOfMonth = Carbon::createFromDate($tahun, $bulanFormatted, 1)->endOfMonth()->format('d');
        $namaBulan = Carbon::createFromFormat('m', $bulanFormatted)->translatedFormat('F');
        $periodeString = "{$lastDayOfMonth} " . ucfirst($namaBulan) . " {$tahun}"; 

        return view('dokumen.jurnal_penyesuaian', compact(
            'formattedJurnals', 'periodeString', 'namaBulan', 'totalDebit', 'totalKredit', 'rekap'
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
