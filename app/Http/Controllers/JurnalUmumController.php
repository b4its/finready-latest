<?php

namespace App\Http\Controllers;

use App\Models\JurnalUmum;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JurnalUmumController extends Controller
{
    //
    public function index(Request $request)
    {
        // Set default filter ke bulan dan tahun saat ini
        $bulan = $request->query('bulan', date('m'));
        $tahun = $request->query('tahun', date('Y'));
        $idUsers = auth()->id() ?? 1;

        // Eager load relasi untuk mencegah N+1 query problem
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

        // Flatten data untuk memudahkan iterasi di Blade Table
        foreach ($jurnals as $jurnal) {
            foreach ($jurnal->details as $detail) {
                $isDebit = strtoupper(trim($detail->is_debet)) === 'D';
                $amount = $detail->amount;

                if ($isDebit) {
                    $totalDebit += $amount;
                } else {
                    $totalKredit += $amount;
                }

                $formattedJurnals->push((object)[
                    'tanggal' => $jurnal->periode,
                    'no_faktur' => $detail->no_faktur,
                    'akun_name' => optional($jurnal->akunKeuangan)->name ?? '-',
                    'ref' => optional($jurnal->akunKeuangan)->no_referensi ?? '-',
                    'debit' => $isDebit ? $amount : 0,
                    'kredit' => !$isDebit ? $amount : 0,
                    'keterangan' => $detail->keterangan ?? $jurnal->keterangan,
                    'is_debit' => $isDebit
                ]);
            }
        }

        // Format string periode untuk kop surat (Contoh: 31 Oktober 2024)
        $bulanFormatted = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $lastDayOfMonth = Carbon::createFromDate($tahun, $bulanFormatted, 1)->endOfMonth()->format('d');
        $namaBulan = Carbon::createFromFormat('m', $bulanFormatted)->translatedFormat('F');
        $periodeString = "{$lastDayOfMonth} " . ucfirst($namaBulan) . " {$tahun}";

        return view('dokumen.jurnal_umum', compact(
            'formattedJurnals', 
            'periodeString', 
            'totalDebit', 
            'totalKredit'
        ));
    }
}
