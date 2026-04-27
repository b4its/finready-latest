<?php

namespace App\Filament\Pages\Investor;

use App\Models\UmkmProfile;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\On; // Tambahkan ini

class InvestorUmkmListPages extends Page
{
    protected static ?string $title = 'Daftar UMKM';
    protected string $view = 'filament.pages.investor.investor.investor-umkm';

    // 1. Properti untuk menyimpan data UMKM yang akan ditampilkan di Modal
    public ?array $selectedUmkm = null;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-building-storefront';
    }

    public function getHeading(): string | Htmlable
    {
        return ''; 
    }

    // 2. Method untuk dipanggil saat tombol Buka Profil diklik
    public function openProfileModal(int $id)
    {
        // Cari data spesifik dari list berdasarkan ID
        $this->selectedUmkm = collect($this->umkmList)->firstWhere('id', $id);
        
        // Perintahkan Alpine/Livewire untuk membuka modal bawaan Filament
        $this->dispatch('open-modal', id: 'detail-profil-modal');
    }

public function getUmkmListProperty(): array
    {
        // 1. Tambahkan 'sosialMedia' di dalam method with()
        $umkms = UmkmProfile::with(['user', 'sosialMedia'])->get();

        return $umkms->map(function ($umkm) {
            $isNibVerified = !empty($umkm->nib);
            $nibStatus = $isNibVerified ? 'Terverifikasi' : 'Tidak Terverifikasi';
            $nibColor = $isNibVerified ? 'text-blue-600 dark:text-blue-400' : 'text-red-500 dark:text-red-400';

            $levelBadge = strtoupper($umkm->level ?? 'LEARNING');
            $statusColor = match(strtolower($umkm->level)) {
                'ready' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
                'discipline' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
                'structured' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                default => 'bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-300',
            };

            // 2. Mapping data sosial media
            $sosmedData = $umkm->sosialMedia->map(function($sosmed) {
                return [
                    'name' => strtolower($sosmed->name), // di-lowercase untuk mempermudah pengecekan icon
                    'link' => $sosmed->link,
                ];
            })->toArray();

            return [
                'id' => $umkm->id,
                'status_badge' => $levelBadge,
                'status_color' => $statusColor,
                'name' => $umkm->name ?? 'Belum ada nama',
                'category' => strtoupper($umkm->jenisUsaha ?? 'UMUM'),
                'owner' => $umkm->user->name ?? 'Pemilik Tidak Diketahui',
                'phone' => $umkm->phone ?? '-',
                'email' => $umkm->email ?? '-',
                'alamat' => $umkm->alamat ?? '-',
                'nib' => $umkm->nib ?? '-',
                'modal_awal' => 'Rp ' . number_format($umkm->modal_awal ?? 0, 0, ',', '.'),
                'profit_margin' => '0%',
                'nib_status' => $nibStatus,
                'nib_color' => $nibColor,
                'social_media' => $sosmedData, // 3. Masukkan ke dalam array utama
            ];
        })->toArray();
    }
}