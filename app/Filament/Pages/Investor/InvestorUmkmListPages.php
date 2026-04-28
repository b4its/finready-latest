<?php

namespace App\Filament\Pages\Investor;

use App\Models\LearnProgress;
use App\Models\PengajuanDataKeuangan;
use App\Models\UmkmProfile;
use App\Models\AkunKeuangan;
use App\Models\DetailJurnalUmum;
use App\Models\JurnalUmum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class InvestorUmkmListPages extends Page
{
    protected static ?string $title = 'Daftar UMKM';
    protected string $view = 'filament.pages.investor.investor.investor-umkm';
    protected static ?string $slug = 'daftar-umkm';

    // Properti Umum
    public string $searchQuery = '';
    public ?array $selectedUmkm = null;
    public array $chartData = [
        'debet' => 0,
        'kredit' => 0,
        'label' => ''
    ];

    // Properti Form Pengajuan
    public string $alasanPengajuan = '';

    // Properti Form Cetak Dokumen
    public string $cetakJenisDokumen = 'buku_besar';
    public string $cetakBulan = '';
    public string $cetakTahun = '';
    public array $availablePeriods = [];

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-building-storefront';
    }

    public function openProfileModal(int $id)
    {
        if (isset($this->selectedUmkm['id']) && $this->selectedUmkm['id'] == $id) {
            $this->selectedUmkm = null;
            $this->chartData = [];
            $this->availablePeriods = [];
        } else {
            $this->selectedUmkm = collect($this->umkmList)->firstWhere('id', $id);
            
            if ($this->selectedUmkm && isset($this->selectedUmkm['user_id'])) {
                $this->chartData = $this->getMutasiData($this->selectedUmkm['user_id']);
                $this->loadAvailablePeriods($this->selectedUmkm['user_id']);
            }
        }
    }

    protected function loadAvailablePeriods($userId)
    {
        $jurnals = JurnalUmum::where('idUsers', $userId)
            ->whereNotNull('periode')
            ->selectRaw('YEAR(periode) as tahun, MONTH(periode) as bulan')
            ->distinct()
            ->get();

        $periods = [];
        foreach ($jurnals as $j) {
            $tahun = (string) $j->tahun;
            $bulan = str_pad($j->bulan, 2, '0', STR_PAD_LEFT);
            
            if (!isset($periods[$tahun])) {
                $periods[$tahun] = [];
            }
            if (!in_array($bulan, $periods[$tahun])) {
                $periods[$tahun][] = $bulan;
            }
        }

        krsort($periods);
        foreach ($periods as &$bulans) {
            rsort($bulans);
        }

        $this->availablePeriods = $periods;

        if (!empty($periods)) {
            $this->cetakTahun = array_key_first($periods);
            $this->cetakBulan = $periods[$this->cetakTahun][0] ?? '';
        } else {
            $this->cetakTahun = '';
            $this->cetakBulan = '';
        }
    }

    public function updatedCetakTahun($value)
    {
        if (isset($this->availablePeriods[$value]) && !empty($this->availablePeriods[$value])) {
            $this->cetakBulan = $this->availablePeriods[$value][0];
        } else {
            $this->cetakBulan = '';
        }
    }

    public function prosesCetak()
    {
        $this->validate([
            'cetakJenisDokumen' => 'required|string',
            'cetakTahun' => 'required|numeric',
            'cetakBulan' => 'required|string|size:2',
        ], [
            'cetakTahun.required' => 'Tahun tidak tersedia (Data Jurnal Kosong)',
            'cetakBulan.required' => 'Bulan tidak tersedia (Data Jurnal Kosong)',
        ]);

        $route = match($this->cetakJenisDokumen) {
            'jurnal_umum' => 'jurnal_umum.index',
            'buku_besar' => 'buku_besar.index',
            'neraca_saldo' => 'neraca_saldo.index',
            'jurnal_penyesuaian' => 'jurnal_penyesuaian.index',
            'laba_rugi' => 'laba_rugi.index',
            'perubahan_modal' => 'perubahan_modal.index',
            'neraca' => 'neraca.index',
            'arus_kas' => 'arus_kas.index',
            default => 'buku_besar.index',
        };

        $this->dispatch('close-modal', id: 'modal-cetak-dokumen');

        return redirect()->to(route($route, [
            'bulan' => $this->cetakBulan, 
            'tahun' => $this->cetakTahun,
            'idUsers' => $this->selectedUmkm['user_id'],
        ]));
    }

    protected function getMutasiData($userId): array
    {
        $label = 'Total Seluruh Periode';

        $kasIds = AkunKeuangan::where(function($query) use ($userId) {
                $query->where('idUsers', $userId)->orWhereNull('idUsers');
            })
            ->get()
            ->filter(fn($akun) => str_contains(strtolower($akun->name), 'kas') || str_contains(strtolower($akun->name), 'bank'))
            ->pluck('id')
            ->toArray();

        if (empty($kasIds)) {
            return ['debet' => 0, 'kredit' => 0, 'label' => $label];
        }

        $mutasi = DetailJurnalUmum::whereHas('jurnalUmum', function ($query) use ($userId, $kasIds) {
                $query->where(function($q) use ($userId) {
                        $q->where('idUsers', $userId)->orWhereNull('idUsers');
                    })
                    ->whereIn('idAkunKeuangan', $kasIds); 
            })
            ->selectRaw("
                SUM(CASE WHEN UPPER(TRIM(is_debet)) = 'D' OR is_debet = '1' THEN amount ELSE 0 END) as total_debet,
                SUM(CASE WHEN UPPER(TRIM(is_debet)) != 'D' AND is_debet != '1' THEN amount ELSE 0 END) as total_kredit
            ")
            ->first();

        return [
            'debet' => (float) ($mutasi->total_debet ?? 0),
            'kredit' => (float) ($mutasi->total_kredit ?? 0),
            'label' => $label
        ];
    }

    #[Computed]
    public function umkmList(): array
    {
        // LOGIKA BARU: Ambil status pengajuan terbaru per umkm_target
        // Urutkan berdasarkan ID ASC agar pluck otomatis mengambil data terbaru
        $pengajuanStatus = PengajuanDataKeuangan::where('idUsers', Auth::id())
            ->orderBy('id', 'asc')
            ->pluck('status_pengajuan', 'umkm_target')
            ->toArray();

        $query = UmkmProfile::with(['user', 'sosialMedia'])
            ->whereHas('user', fn($q) => $q->where('role', 'umkm'));

        if (!empty($this->searchQuery)) {
            $searchTerm = '%' . $this->searchQuery . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhereHas('user', fn($userQuery) => $userQuery->where('name', 'like', $searchTerm));
            });
        }



        return $query->get()->map(function ($umkm) use ($pengajuanStatus) {
            $isNibVerified = !empty($umkm->nib);
            $total_poin_progress = LearnProgress::where('idUsers', $umkm->user->id)
                ->whereNotNull('point')
                ->sum("point"); 

            $indikator_level = $total_poin_progress > 1500 
                                ? "Ready" 
                                : ($total_poin_progress > 1000 
                                    ? "Structured" 
                                    : ($total_poin_progress > 500 ? "Discipline" : "Learning"));
            return [
                'id' => $umkm->id,
                'user_id' => $umkm->idUsers, 
                'name' => $umkm->name ?? 'Belum ada nama',
                'category' => strtoupper($umkm->jenisUsaha ?? 'UMUM'),
                'owner' => $umkm->user->name ?? '-',
                'phone' => $umkm->phone ?? '-',
                'email' => $umkm->email ?? '-',
                'alamat' => $umkm->alamat ?? '-',
                'nib' => $umkm->nib ?? '-',
                'modal_awal' => 'Rp ' . number_format($umkm->modal_awal ?? 0, 0, ',', '.'),
                'status_badge' => strtoupper($indikator_level ?? 'LEARNING'),
                'status_color' => $this->getStatusColor($indikator_level),
                'nib_status' => $isNibVerified ? 'Terverifikasi' : 'Tidak Terverifikasi',
                'nib_color' => $isNibVerified ? 'text-blue-600 dark:text-blue-400' : 'text-red-500 dark:text-red-400',
                'social_media' => $umkm->sosialMedia->map(fn($s) => [
                    'name' => strtolower($s->name), 
                    'link' => $s->link
                ])->toArray(),
                
                // Meneruskan Status Pengajuan (0, 1, atau null)
                'status_pengajuan_keuangan' => $pengajuanStatus[$umkm->idUsers] ?? null,
            ];
        })->toArray();
    }

    private function getStatusColor($level): string {
        return match(strtolower($level)) {
            'Ready' => 'bg-green-100 text-green-700',
            'Discipline' => 'bg-yellow-100 text-yellow-700',
            'Structured' => 'bg-blue-100 text-blue-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function submitPengajuan()
    {
        $this->validate([
            'alasanPengajuan' => 'required|string|min:5',
        ], [
            'alasanPengajuan.required' => 'Alasan pengajuan wajib diisi.',
            'alasanPengajuan.min' => 'Alasan terlalu singkat, berikan detail tambahan.'
        ]);

        PengajuanDataKeuangan::create([
            'idUsers' => Auth::id(),
            'title' => Auth::user()->name . ' Mengajukan permintaan untuk dapat melihat data keuangan Umkm',
            'umkm_target' => $this->selectedUmkm['user_id'],
            'keterangan' => $this->alasanPengajuan,
            'status_pengajuan' => 0, // Default pending
        ]);

        $this->reset('alasanPengajuan');
        $this->dispatch('close-modal', id: 'modal-pengajuan');
        
        Notification::make()
            ->title('Pengajuan Terkirim')
            ->success()
            ->send();
    }
}