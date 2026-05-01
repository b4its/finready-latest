<?php

namespace App\Http\Controllers;

use App\Models\AkunKeuangan;
use App\Models\JurnalUmum;
use App\Models\SaldoAwal;
use App\Models\UmkmProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NeracaController extends Controller
{
    //
    public function index(Request $request)
    {
        $bulan = $request->query('bulan', date('m'));
        $tahun = $request->query('tahun', date('Y'));
        $idUsers = (int) ($request->query('idUsers') ?? auth()->id() ?? 1);

        // 1. Tarik Data Jurnal & Saldo Awal (Optimasi Single Query)
        $semuaJurnal = JurnalUmum::with('details')
            ->where(function($query) use ($idUsers) {
                $query->where('idUsers', $idUsers)->orWhereNull('idUsers');
            })
            ->whereMonth('periode', (int) $bulan)
            ->whereYear('periode', (int) $tahun)
            ->get();

        $detailProfilUMKM = UmkmProfile::where('idUsers', $idUsers)->first();

        $semuaSaldoAwal = SaldoAwal::with('detailAkunKeuangan')
            ->whereHas('detailAkunKeuangan', function($q) use ($idUsers) {
                $q->where('idUsers', $idUsers)->orWhereNull('idUsers');
            })
            ->get();

        // 2. Variabel Penampung Laba/Rugi & Modal Akhir
        $totalPendapatan = 0;
        $totalBeban = 0;
        $modalAwal = 0;
        $prive = 0;

        // 3. Proses Semua Akun
        $akunDiproses = AkunKeuangan::where(function($query) use ($idUsers) {
                $query->where('idUsers', $idUsers)->orWhereNull('idUsers');
            })
            ->get()
            ->map(function ($akun) use ($semuaJurnal, $semuaSaldoAwal, &$totalPendapatan, &$totalBeban, &$modalAwal, &$prive) {
                
                // Hitung Saldo Awal
                $akunSaldoAwals = $semuaSaldoAwal->filter(function($s) use ($akun) {
                    return $s->detailAkunKeuangan && $s->detailAkunKeuangan->idAkunKeuangan == $akun->id;
                });
                $saldoAwalDebet = $akunSaldoAwals->sum('debet');
                $saldoAwalKredit = $akunSaldoAwals->sum('kredit');

                // Hitung Mutasi
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

                $totalDebet = $saldoAwalDebet + $mutasiDebet;
                $totalKredit = $saldoAwalKredit + $mutasiKredit;

                // Hitung Saldo Berdasarkan Kategori
                if ($akun->category === 'aset') {
                    $saldo = $totalDebet - $totalKredit;
                } elseif (in_array($akun->category, ['kewajiban', 'modal', 'pendapatan', 'lain-lain'])) {
                    $saldo = $totalKredit - $totalDebet;
                } elseif ($akun->category === 'beban_biaya') {
                    $saldo = $totalDebet - $totalKredit;
                } else {
                    $saldo = 0;
                }

                // Ekstraksi untuk Laba/Rugi dan Perubahan Modal
                if (in_array($akun->category, ['pendapatan', 'lain-lain'])) {
                    $totalPendapatan += $saldo;
                } elseif ($akun->category === 'beban_biaya') {
                    $totalBeban += $saldo;
                } elseif ($akun->category === 'modal') {
                    // Prive sifatnya mengurangi modal (bersaldo normal debet, jadi jika dihitung Kredit-Debet hasilnya minus, yang mana benar)
                    if (str_contains(strtolower($akun->name), 'prive')) {
                        $prive += abs($totalDebet - $totalKredit);
                    } else {
                        $modalAwal += $saldo;
                    }
                }

                return (object)[
                    'nama' => $akun->name,
                    'category' => $akun->category,
                    'detail_category' => $akun->detail_category,
                    'saldo' => $saldo,
                ];
            })
            ->reject(function($item) {
                return $item->saldo == 0; // Buang yang saldonya 0
            });

        // 4. Kalkulasi Modal Akhir
        $labaBersih = $totalPendapatan - $totalBeban;
        $modalAkhir = $modalAwal + $labaBersih - $prive;

        // 5. Kelompokkan Data Untuk Tampilan Neraca
        // AKTIVA (KIRI)
        $aktivaLancar = $akunDiproses->where('detail_category', 'aset')->values();
        $totalAktivaLancar = $aktivaLancar->sum('saldo');

        $aktivaTetap = $akunDiproses->whereIn('detail_category', ['aset_tetap', 'aset_tak_berwujud'])->values();
        $totalAktivaTetap = $aktivaTetap->sum('saldo');

        $totalAktiva = $totalAktivaLancar + $totalAktivaTetap;

        // PASIVA (KANAN)
        $hutangPendek = $akunDiproses->whereIn('detail_category', ['kewajiban_jangka_pendek', 'kewajiban'])->values();
        $totalHutangPendek = $hutangPendek->sum('saldo');

        $hutangPanjang = $akunDiproses->where('detail_category', 'kewajiban_jangka_panjang')->values();
        $totalHutangPanjang = $hutangPanjang->sum('saldo');

        $totalHutang = $totalHutangPendek + $totalHutangPanjang;
        
        $totalPasiva = $totalHutang + $modalAkhir;

        // Format Periode
        $bulanFormatted = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $lastDayOfMonth = Carbon::createFromDate($tahun, $bulanFormatted, 1)->endOfMonth()->format('d');
        $namaBulan = Carbon::createFromFormat('m', $bulanFormatted)->translatedFormat('F');
        $periodeString = "Per {$lastDayOfMonth} " . ucfirst($namaBulan) . " {$tahun}";

        return view('dokumen.neraca', compact(
            'periodeString', 
            'aktivaLancar', 'totalAktivaLancar',
            'aktivaTetap', 'totalAktivaTetap', 'totalAktiva',
            'hutangPendek', 'totalHutangPendek',
            'hutangPanjang', 'totalHutangPanjang', 'totalHutang',
            'modalAkhir', 'totalPasiva', 'detailProfilUMKM'
        ));
    }
}
