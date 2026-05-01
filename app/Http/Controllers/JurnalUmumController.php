<?php

namespace App\Http\Controllers;

use App\Models\JurnalUmum;
use App\Models\UmkmProfile;
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
        $idUsers = (int) ($request->query('idUsers') ?? auth()->id() ?? 1);
        $detailProfilUMKM = UmkmProfile::where('idUsers', $idUsers)->first();

        // Eager load relasi berdasarkan model yang Anda berikan
        $jurnals = JurnalUmum::with(['akunKeuangan', 'details'])
            ->where(function($query) use ($idUsers) {
                $query->where('idUsers', $idUsers)->orWhereNull('idUsers');
            })
            ->whereMonth('periode', (int) $bulan)
            ->whereYear('periode', (int) $tahun)
            // FILTER UTAMA: Menggunakan kolom 'tipe' (tinyint) sesuai skema Anda. 
            // Asumsi 0 = Jurnal Umum. Silakan sesuaikan jika Jurnal Umum bernilai lain (misal 1).
            ->where('tipe', 1) 
            ->orderBy('periode', 'asc')
            ->get();

        $formattedJurnals = collect();
        $totalDebit = 0;
        $totalKredit = 0;

        // Kelompokkan data berdasarkan Tanggal dan No. Faktur agar Keterangan dicetak 1x di bawah
        $groupedByFaktur = [];

        foreach ($jurnals as $jurnal) {
            foreach ($jurnal->details as $detail) {
                $noFaktur = $detail->no_faktur ?? '-';
                $tanggal = $jurnal->periode;
                
                // Unique key untuk grouping tiap 1 transaksi (1 faktur di hari yang sama)
                $groupKey = $tanggal . '_' . $noFaktur;

                // Inisiasi struktur grup jika belum ada
                if (!isset($groupedByFaktur[$groupKey])) {
                    $groupedByFaktur[$groupKey] = [
                        'tanggal' => $tanggal,
                        'no_faktur' => $noFaktur,
                        'items' => [],
                        // Prioritaskan keterangan dari detail, jika kosong ambil dari header jurnal_umum
                        'keterangan_transaksi' => $detail->keterangan ?? $jurnal->keterangan 
                    ];
                }

                $isDebit = strtoupper(trim($detail->is_debet)) === 'D';
                $amount = $detail->amount;

                if ($isDebit) {
                    $totalDebit += $amount;
                } else {
                    $totalKredit += $amount;
                }

                // Kumpulkan baris akun debit/kredit ke dalam grup ini
                $groupedByFaktur[$groupKey]['items'][] = (object)[
                    'akun_name' => optional($jurnal->akunKeuangan)->name ?? '-',
                    'ref' => optional($jurnal->akunKeuangan)->no_referensi ?? '-',
                    'debit' => $isDebit ? $amount : 0,
                    'kredit' => !$isDebit ? $amount : 0,
                    'is_debit' => $isDebit
                ];
            }
        }

        // Flatten data untuk dikirim ke Blade View
        foreach ($groupedByFaktur as $fakturData) {
            
            // Opsional (Best Practice Akuntansi): Urutkan agar posisi Debit selalu di atas Kredit
            usort($fakturData['items'], function($a, $b) {
                return $b->is_debit <=> $a->is_debit; 
            });

            // 1. Masukkan baris akun (Debit & Kredit)
            foreach ($fakturData['items'] as $item) {
                $formattedJurnals->push((object)[
                    'is_keterangan_row' => false,
                    'tanggal' => $fakturData['tanggal'],
                    'no_faktur' => $fakturData['no_faktur'],
                    'akun_name' => $item->akun_name,
                    'ref' => $item->ref,
                    'debit' => $item->debit,
                    'kredit' => $item->kredit,
                    'is_debit' => $item->is_debit
                ]);
            }
            
            // 2. Masukkan Keterangan Transaksi di baris paling bawah khusus untuk transaksi ini
            if (!empty($fakturData['keterangan_transaksi'])) {
                $formattedJurnals->push((object)[
                    'is_keterangan_row' => true,
                    'tanggal' => $fakturData['tanggal'],
                    'no_faktur' => $fakturData['no_faktur'],
                    'keterangan' => $fakturData['keterangan_transaksi']
                ]);
            }
        }

        // Format string periode untuk kop surat (Contoh: 31 Oktober 2026)
        $bulanFormatted = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $lastDayOfMonth = Carbon::createFromDate($tahun, $bulanFormatted, 1)->endOfMonth()->format('d');
        $namaBulan = Carbon::createFromFormat('m', $bulanFormatted)->translatedFormat('F');
        $periodeString = "{$lastDayOfMonth} " . ucfirst($namaBulan) . " {$tahun}";

        return view('dokumen.jurnal_umum', compact(
            'formattedJurnals', 
            'periodeString', 
            'totalDebit', 
            'totalKredit', 'detailProfilUMKM'
        ));
    }
}
