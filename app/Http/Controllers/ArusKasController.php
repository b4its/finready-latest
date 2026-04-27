<?php

namespace App\Http\Controllers;

use App\Models\AkunKeuangan;
use App\Models\JurnalUmum;
use App\Models\SaldoAwal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ArusKasController extends Controller
{
    //
    public function index(Request $request)
    {
        $bulan = $request->query('bulan', date('m'));
        $tahun = $request->query('tahun', date('Y'));
        $idUsers = auth()->id() ?? 1;

        // 1. Tarik Data Jurnal & Saldo Awal
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

        $akunKeuangans = AkunKeuangan::where(function($query) use ($idUsers) {
                $query->where('idUsers', $idUsers)->orWhereNull('idUsers');
            })
            ->get();

        // 2. Penampung Komponen Arus Kas
        $arusKas = [
            'operasi_masuk' => [], 'operasi_keluar' => [],
            'investasi_masuk' => [], 'investasi_keluar' => [],
            'pendanaan_masuk' => [], 'pendanaan_keluar' => [],
        ];

        $saldoKasAwal = 0;
        $mutasiKasBersih = 0;

        // 3. Proses Analisis Mutasi per Akun
        foreach ($akunKeuangans as $akun) {
            $isKas = str_contains(strtolower($akun->name), 'kas') || str_contains(strtolower($akun->name), 'bank');

            // Hitung Saldo Awal
            $akunSaldoAwals = $semuaSaldoAwal->filter(function($s) use ($akun) {
                return $s->detailAkunKeuangan && $s->detailAkunKeuangan->idAkunKeuangan == $akun->id;
            });
            $saldoAwalDebet = $akunSaldoAwals->sum('debet');
            $saldoAwalKredit = $akunSaldoAwals->sum('kredit');
            $saldoAwal = $saldoAwalDebet - $saldoAwalKredit;

            // Hitung Mutasi Jurnal Bulan Ini
            $mutasiDebet = 0;
            $mutasiKredit = 0;
            foreach ($semuaJurnal->where('idAkunKeuangan', $akun->id) as $jurnal) {
                foreach($jurnal->details as $d) {
                    if (strtoupper(trim($d->is_debet)) === 'D') $mutasiDebet += $d->amount;
                    else $mutasiKredit += $d->amount;
                }
            }

            $mutasiBersih = $mutasiDebet - $mutasiKredit; // Nilai (+) berarti net Debet, (-) net Kredit

            // Jika akun ini adalah Kas/Bank, tangkap saldo awal dan mutasinya, lalu skip dari loop penamaan
            if ($isKas) {
                $saldoKasAwal += $saldoAwal;
                $mutasiKasBersih += $mutasiBersih;
                continue; 
            }

            if ($mutasiBersih == 0) continue; // Abaikan jika tidak ada mutasi di bulan ini

            // PENGELOMPOKAN ARUS KAS BERDASARKAN KATEGORI AKUN
            // =================================================

            // A. AKTIVITAS OPERASI
            if (in_array($akun->category, ['pendapatan', 'lain-lain'])) {
                if ($mutasiBersih < 0) { // Pendapatan Bertambah (Kredit) -> Uang Masuk
                    $arusKas['operasi_masuk'][] = ['nama' => 'Penerimaan dari ' . $akun->name, 'jumlah' => abs($mutasiBersih)];
                }
            }
            elseif ($akun->category == 'beban_biaya') {
                if ($mutasiBersih > 0) { // Beban Bertambah (Debet) -> Uang Keluar
                    $arusKas['operasi_keluar'][] = ['nama' => 'Pembayaran ' . $akun->name, 'jumlah' => $mutasiBersih];
                }
            }
            elseif ($akun->category == 'aset' && !in_array($akun->detail_category, ['aset_tetap', 'aset_tak_berwujud'])) {
                if (str_contains(strtolower($akun->name), 'piutang')) {
                    if ($mutasiBersih < 0) $arusKas['operasi_masuk'][] = ['nama' => 'Penerimaan Pelunasan ' . $akun->name, 'jumlah' => abs($mutasiBersih)];
                } else { // Persediaan, dll
                    if ($mutasiBersih > 0) $arusKas['operasi_keluar'][] = ['nama' => 'Pembayaran Pembelian ' . $akun->name, 'jumlah' => $mutasiBersih];
                }
            }
            elseif (in_array($akun->category, ['kewajiban']) && !in_array($akun->detail_category, ['kewajiban_jangka_panjang'])) {
                if ($mutasiBersih > 0) $arusKas['operasi_keluar'][] = ['nama' => 'Pembayaran ' . $akun->name, 'jumlah' => $mutasiBersih];
            }

            // B. AKTIVITAS INVESTASI
            elseif (in_array($akun->detail_category, ['aset_tetap', 'aset_tak_berwujud'])) {
                if (!str_contains(strtolower($akun->name), 'akumulasi')) {
                    if ($mutasiBersih > 0) $arusKas['investasi_keluar'][] = ['nama' => 'Pembelian ' . $akun->name, 'jumlah' => $mutasiBersih];
                    elseif ($mutasiBersih < 0) $arusKas['investasi_masuk'][] = ['nama' => 'Penjualan ' . $akun->name, 'jumlah' => abs($mutasiBersih)];
                }
            }

            // C. AKTIVITAS PENDANAAN
            elseif ($akun->category == 'modal' || $akun->detail_category == 'kewajiban_jangka_panjang') {
                if ($mutasiBersih > 0) { // Prive atau Bayar Hutang Panjang (Debet) -> Uang Keluar
                    $namaLabel = str_contains(strtolower($akun->name), 'prive') ? $akun->name : 'Pembayaran ' . $akun->name;
                    $arusKas['pendanaan_keluar'][] = ['nama' => $namaLabel, 'jumlah' => $mutasiBersih];
                } elseif ($mutasiBersih < 0) { // Setoran Modal atau Pinjaman -> Uang Masuk
                    $arusKas['pendanaan_masuk'][] = ['nama' => 'Setoran / Penerimaan ' . $akun->name, 'jumlah' => abs($mutasiBersih)];
                }
            }
        }

        // Kalkulasi Subtotal & Total
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

        // Format Tanggal
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
