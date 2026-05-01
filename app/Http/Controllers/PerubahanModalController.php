<?php

namespace App\Http\Controllers;

use App\Models\AkunKeuangan;
use App\Models\JurnalUmum;
use App\Models\SaldoAwal;
use App\Models\UmkmProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PerubahanModalController extends Controller
{
    //
    public function index(Request $request)
    {
        $bulan = $request->query('bulan', date('m'));
        $tahun = $request->query('tahun', date('Y'));
        $idUsers = (int) ($request->query('idUsers') ?? auth()->id() ?? 1);
        $detailProfilUMKM = UmkmProfile::where('idUsers', $idUsers)->first();

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

        // 2. Siapkan Variabel Penampung
        $totalPendapatan = 0;
        $totalBeban = 0;
        $modalAwal = 0;
        $prive = 0;

        // 3. Kalkulasi Saldo per Akun
        foreach ($akunKeuangans as $akun) {
            // Hitung Saldo Awal
            $akunSaldoAwals = $semuaSaldoAwal->filter(function($s) use ($akun) {
                return $s->detailAkunKeuangan && $s->detailAkunKeuangan->idAkunKeuangan == $akun->id;
            });
            $saldoAwalDebet = $akunSaldoAwals->sum('debet');
            $saldoAwalKredit = $akunSaldoAwals->sum('kredit');

            // Hitung Mutasi Jurnal
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

            // 4. Kelompokkan ke Laba/Rugi dan Modal
            if (in_array($akun->category, ['pendapatan', 'lain-lain'])) {
                $totalPendapatan += ($totalKredit - $totalDebet); // Pendapatan Normal Kredit
            } 
            elseif ($akun->category === 'beban_biaya') {
                $totalBeban += ($totalDebet - $totalKredit); // Beban Normal Debet
            } 
            elseif ($akun->category === 'modal') {
                // Deteksi Prive berdasarkan nama (Prive Normal Debet, Modal Normal Kredit)
                if (str_contains(strtolower($akun->name), 'prive')) {
                    $prive += ($totalDebet - $totalKredit);
                } else {
                    $modalAwal += ($totalKredit - $totalDebet);
                }
            }
        }

        // 5. Rumus Perubahan Modal
        $labaBersih = $totalPendapatan - $totalBeban;
        $isLaba = $labaBersih >= 0;
        
        $penambahanModal = $modalAwal + $labaBersih;
        $modalAkhir = $penambahanModal - $prive;

        // 6. Format String Periode
        $bulanFormatted = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $lastDayOfMonth = Carbon::createFromDate($tahun, $bulanFormatted, 1)->endOfMonth()->format('d');
        $namaBulan = Carbon::createFromFormat('m', $bulanFormatted)->translatedFormat('F');
        $periodeString = "Per {$lastDayOfMonth} " . ucfirst($namaBulan) . " {$tahun}";

        return view('dokumen.perubahan_modal', compact(
            'periodeString', 
            'modalAwal', 
            'labaBersih', 
            'isLaba', 
            'penambahanModal', 
            'prive', 
            'modalAkhir', 'detailProfilUMKM'
        ));
    }
}
