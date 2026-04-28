<?php

namespace App\Http\Controllers;

use App\Models\AkunKeuangan;
use App\Models\JurnalUmum;
use App\Models\SaldoAwal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ArusKasController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->query('bulan', date('m'));
        $tahun = $request->query('tahun', date('Y'));
        $idUsers = (int) ($request->query('idUsers') ?? auth()->id() ?? 1);

        // 1. Tarik Data Utama (FILTER TIPE SAYA HAPUS TOTAL AGAR DATA MUNCUL)
        $semuaJurnal = JurnalUmum::with(['akunKeuangan', 'details'])
            ->where(function($query) use ($idUsers) {
                $query->where('idUsers', $idUsers)->orWhereNull('idUsers');
            })
            ->whereMonth('periode', (int) $bulan)
            ->whereYear('periode', (int) $tahun)
            ->get();

        $semuaSaldoAwal = SaldoAwal::with('detailAkunKeuangan')
            ->whereHas('detailAkunKeuangan', function($q) use ($idUsers) {
                $q->where('idUsers', $idUsers)->orWhereNull('idUsers');
            })->get();

        $akunKeuangans = AkunKeuangan::where(function($query) use ($idUsers) {
                $query->where('idUsers', $idUsers)->orWhereNull('idUsers');
            })->get();

        // 2. Identifikasi Akun Kas & Bank
        $kasIds = $akunKeuangans->filter(function($a) {
            $nama = strtolower(trim($a->name));
            return str_contains($nama, 'kas') || str_contains($nama, 'bank');
        })->pluck('id')->toArray();

        $saldoKasAwal = 0;
        foreach ($akunKeuangans as $akun) {
            if (in_array($akun->id, $kasIds)) {
                $akunSaldoAwals = $semuaSaldoAwal->filter(function($s) use ($akun) {
                    return $s->detailAkunKeuangan && $s->detailAkunKeuangan->idAkunKeuangan == $akun->id;
                });
                $saldoKasAwal += ($akunSaldoAwals->sum('debet') - $akunSaldoAwals->sum('kredit'));
            }
        }

        // 3. GROUPING JURNAL (Penyebab Zeros diperbaiki di sini)
        $transaksiGroup = [];
        foreach ($semuaJurnal as $jurnal) {
            $idAkun = $jurnal->idAkunKeuangan;
            $namaAkun = optional($jurnal->akunKeuangan)->name ?? '';
            $periode = $jurnal->periode;

            foreach ($jurnal->details as $d) {
                $noFaktur = trim($d->no_faktur ?? '');
                
                // KUNCI PERBAIKAN: Jika no_faktur kosong, tetap kawinkan berdasarkan tanggal & waktu
                if (empty($noFaktur) || $noFaktur === '-') {
                    $faktur = $periode . '_' . trim($jurnal->keterangan ?? $jurnal->created_at);
                } else {
                    $faktur = $periode . '_' . $noFaktur;
                }
                
                if (!isset($transaksiGroup[$faktur])) {
                    $transaksiGroup[$faktur] = [];
                }
                
                $transaksiGroup[$faktur][] = (object)[
                    'idAkun' => $idAkun,
                    'namaAkun' => $namaAkun,
                    'isDebet' => strtoupper(trim($d->is_debet)) === 'D',
                    'amount' => $d->amount
                ];
            }
        }

        // 4. METODE OFFSET KAS PER TRANSAKSI
        $agregat = [
            'op_masuk' => [], 'op_keluar' => [],
            'inv_masuk' => [], 'inv_keluar' => [],
            'pend_masuk' => [], 'pend_keluar' => []
        ];
        $totalPembelianBahanBaku = 0;

        foreach ($transaksiGroup as $faktur => $lines) {
            $kasDebet = 0;
            $kasKredit = 0;

            // Cek apakah ada Kas yang keluar/masuk di faktur ini
            foreach ($lines as $line) {
                if (in_array($line->idAkun, $kasIds)) {
                    if ($line->isDebet) $kasDebet += $line->amount;
                    else $kasKredit += $line->amount;
                }
            }

            // Jika tidak ada uang tunai bergerak, buang (Otomatis filter Jurnal Penyesuaian & Piutang)
            if ($kasDebet == 0 && $kasKredit == 0) continue;

            // Tarik nama akun lawannya
            foreach ($lines as $line) {
                if (in_array($line->idAkun, $kasIds)) continue; 

                $namaLow = strtolower(trim($line->namaAkun));
                $namaRaw = trim($line->namaAkun);
                $amt = $line->amount;

                // A. KAS MASUK
                if ($kasDebet > 0 && !$line->isDebet) {
                    if (str_contains($namaLow, 'penjualan') || str_contains($namaLow, 'pendapatan')) {
                        $agregat['op_masuk']['Penerimaan dari Penjualan Tunai'] = ($agregat['op_masuk']['Penerimaan dari Penjualan Tunai'] ?? 0) + $amt;
                    } elseif (str_contains($namaLow, 'piutang')) {
                        $agregat['op_masuk']['Penerimaan Pelunasan Piutang'] = ($agregat['op_masuk']['Penerimaan Pelunasan Piutang'] ?? 0) + $amt;
                    } elseif (str_contains($namaLow, 'modal')) {
                        $agregat['pend_masuk']['Setoran Modal'] = ($agregat['pend_masuk']['Setoran Modal'] ?? 0) + $amt;
                    } elseif (str_contains($namaLow, 'peralatan') || str_contains($namaLow, 'mesin')) {
                        $agregat['inv_masuk']['Penjualan ' . $namaRaw] = ($agregat['inv_masuk']['Penjualan ' . $namaRaw] ?? 0) + $amt;
                    }
                }
                // B. KAS KELUAR
                elseif ($kasKredit > 0 && $line->isDebet) {
                    if (str_contains($namaLow, 'bahan baku') || str_contains($namaLow, 'persediaan') || str_contains($namaLow, 'gula') || str_contains($namaLow, 'kopi') || str_contains($namaLow, 'susu') || str_contains($namaLow, 'teh')) {
                        $totalPembelianBahanBaku += $amt;
                    } elseif (str_contains($namaLow, 'utang') || str_contains($namaLow, 'hutang')) {
                        $agregat['op_keluar']['Pembayaran Utang Usaha'] = ($agregat['op_keluar']['Pembayaran Utang Usaha'] ?? 0) + $amt;
                    } elseif (str_contains($namaLow, 'gaji')) {
                        $agregat['op_keluar']['Pembayaran Gaji Karyawan'] = ($agregat['op_keluar']['Pembayaran Gaji Karyawan'] ?? 0) + $amt;
                    } elseif (str_contains($namaLow, 'listrik') || str_contains($namaLow, 'air')) {
                        $agregat['op_keluar']['Pembayaran Listrik dan Air'] = ($agregat['op_keluar']['Pembayaran Listrik dan Air'] ?? 0) + $amt;
                    } elseif (str_contains($namaLow, 'promosi')) {
                        $agregat['op_keluar']['Pembayaran Biaya promosi'] = ($agregat['op_keluar']['Pembayaran Biaya promosi'] ?? 0) + $amt;
                    } elseif (str_contains($namaLow, 'sewa')) {
                        $agregat['op_keluar']['Pembayaran Biaya Sewa Tempat'] = ($agregat['op_keluar']['Pembayaran Biaya Sewa Tempat'] ?? 0) + $amt;
                    } elseif (str_contains($namaLow, 'kebersihan')) {
                        $agregat['op_keluar']['Pembayaran Biaya Kebersihan'] = ($agregat['op_keluar']['Pembayaran Biaya Kebersihan'] ?? 0) + $amt;
                    } elseif (str_contains($namaLow, 'prive')) {
                        $agregat['pend_keluar']['Prive Pemilik'] = ($agregat['pend_keluar']['Prive Pemilik'] ?? 0) + $amt;
                    } elseif (str_contains($namaLow, 'peralatan') || str_contains($namaLow, 'mesin')) {
                        $agregat['inv_keluar']['Pembelian ' . $namaRaw] = ($agregat['inv_keluar']['Pembelian ' . $namaRaw] ?? 0) + $amt;
                    } else {
                        $label = str_contains($namaLow, 'pembayaran') ? $namaRaw : 'Pembayaran ' . $namaRaw;
                        $agregat['op_keluar'][$label] = ($agregat['op_keluar'][$label] ?? 0) + $amt;
                    }
                }
            }
        }

        // 5. Susun Array untuk View
        $arusKas = [
            'operasi_masuk' => [], 'operasi_keluar' => [],
            'investasi_masuk' => [], 'investasi_keluar' => [],
            'pendanaan_masuk' => [], 'pendanaan_keluar' => []
        ];

        // Paksa Bahan Baku berada di urutan atas Operasi Keluar layaknya Excel
        if ($totalPembelianBahanBaku > 0) {
            $arusKas['operasi_keluar'][] = ['nama' => 'Pembayaran Pembelian Bahan Baku', 'jumlah' => $totalPembelianBahanBaku];
        }

        foreach($agregat['op_masuk'] as $nama => $jumlah) $arusKas['operasi_masuk'][] = ['nama' => $nama, 'jumlah' => $jumlah];
        foreach($agregat['op_keluar'] as $nama => $jumlah) $arusKas['operasi_keluar'][] = ['nama' => $nama, 'jumlah' => $jumlah];
        foreach($agregat['inv_masuk'] as $nama => $jumlah) $arusKas['investasi_masuk'][] = ['nama' => $nama, 'jumlah' => $jumlah];
        foreach($agregat['inv_keluar'] as $nama => $jumlah) $arusKas['investasi_keluar'][] = ['nama' => $nama, 'jumlah' => $jumlah];
        foreach($agregat['pend_masuk'] as $nama => $jumlah) $arusKas['pendanaan_masuk'][] = ['nama' => $nama, 'jumlah' => $jumlah];
        foreach($agregat['pend_keluar'] as $nama => $jumlah) $arusKas['pendanaan_keluar'][] = ['nama' => $nama, 'jumlah' => $jumlah];

        // 6. Subtotal & Total
        $totalMasukOp = collect($arusKas['operasi_masuk'])->sum('jumlah');
        $totalKeluarOp = collect($arusKas['operasi_keluar'])->sum('jumlah');
        $bersihOperasi = $totalMasukOp - $totalKeluarOp;

        $totalMasukInv = collect($arusKas['investasi_masuk'])->sum('jumlah');
        $totalKeluarInv = collect($arusKas['investasi_keluar'])->sum('jumlah');
        $bersihInvestasi = $totalMasukInv - $totalKeluarInv;

        $totalMasukPend = collect($arusKas['pendanaan_masuk'])->sum('jumlah');
        $totalKeluarPend = collect($arusKas['pendanaan_keluar'])->sum('jumlah');
        $bersihPendanaan = $totalMasukPend - $totalKeluarPend;

        $kenaikanKas = $bersihOperasi + $bersihInvestasi + $bersihPendanaan;
        $saldoKasAkhir = $saldoKasAwal + $kenaikanKas;

        // Tanggal
        $bulanFormatted = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $tglAwal = "1 " . Carbon::createFromFormat('m', $bulanFormatted)->translatedFormat('M Y');
        $tglAkhir = Carbon::createFromDate($tahun, $bulanFormatted, 1)->endOfMonth()->format('d M Y');
        $namaBulan = Carbon::createFromFormat('m', $bulanFormatted)->translatedFormat('F');
        $periodeString = "Per " . Carbon::createFromDate($tahun, $bulanFormatted, 1)->endOfMonth()->format('d') . " " . ucfirst($namaBulan) . " {$tahun}";

        return view('dokumen.arus_kas', compact(
            'periodeString', 'arusKas', 'tglAwal', 'tglAkhir',
            'totalMasukOp', 'totalKeluarOp', 'bersihOperasi',
            'bersihInvestasi',
            'totalMasukPend', 'totalKeluarPend', 'bersihPendanaan',
            'kenaikanKas', 'saldoKasAwal', 'saldoKasAkhir'
        ));
    }
}