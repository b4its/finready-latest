<?php

namespace App\Livewire;

use App\Models\KotakMBG;
use App\Models\Profile;
use App\Models\UmkmProfile;
use App\Models\InvestorProfile;
use App\Models\User;
use App\Models\SosialMedia;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Livewire\Component;

class EditProfileModal extends Component implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    public ?array $data = [];

    public function mount(): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Inisialisasi data Sosial Media jika user adalah UMKM
            $sosialMediaData = [];
            if ($user->role === 'umkm' && $user->umkmProfile) {
                $sosialMediaData = $user->umkmProfile->sosialMedia->map(function ($item) {
                    return [
                        'name' => $item->name,
                        'link' => $item->link,
                    ];
                })->toArray();
            }

            // Inisialisasi data. Prefix digunakan agar field dengan nama sama tidak bentrok.
            $this->form->fill([
                'name' => $user->name,
                'email' => $user->email,
                
                // Data Profile (Personal - Wajib)
                'profile_nik' => $user->profile?->nik,
                'profile_phone' => $user->profile?->phone,
                'profile_alamat' => $user->profile?->alamat,

                // Data UMKM Profile (Hanya UMKM)
                'umkm_name' => $user->umkmProfile?->name,
                'umkm_jenisUsaha' => $user->umkmProfile?->jenisUsaha,
                'umkm_nib' => $user->umkmProfile?->nib,
                'umkm_email' => $user->umkmProfile?->email,
                'umkm_phone' => $user->umkmProfile?->phone,
                'umkm_alamat' => $user->umkmProfile?->alamat,
                'umkm_modal_awal' => $user->umkmProfile?->modal_awal,
                
                // Data Relasi HasMany Sosial Media
                'umkm_sosial_media' => $sosialMediaData,

                // Data Investor Profile (Hanya Investor)
                'investor_name' => $user->investorProfile?->name,
            ]);
        }
    }

    public function form(Schema $form): Schema
    {
        $role = Auth::user()?->role;

        return $form
            ->schema([
                Section::make('Informasi Akun')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Akun')
                            ->required(),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(table: 'users', ignorable: Auth::user()), 
                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->label('Password Baru'),
                    ])->columns(2),

                Section::make('Informasi Personal')
                    ->schema([
                        TextInput::make('profile_nik')
                            ->label('NIK')
                            ->numeric(),
                        TextInput::make('profile_phone')
                            ->label('Nomor Telepon'),
                        Textarea::make('profile_alamat')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Profil Usaha')
                    ->visible($role === 'umkm')
                    ->schema([
                        TextInput::make('umkm_name')
                            ->label('Nama Usaha'),
                        TextInput::make('umkm_jenisUsaha')
                            ->label('Jenis Usaha'),
                        TextInput::make('umkm_nib')
                            ->label('NIB'),
                        TextInput::make('umkm_email')
                            ->label('Email Usaha')
                            ->email(),
                        TextInput::make('umkm_phone')
                            ->label('Nomor Telepon Usaha'),
                        TextInput::make('umkm_modal_awal')
                            ->label('Modal Awal')
                            ->numeric()
                            ->default(0),
                        Textarea::make('umkm_alamat')
                            ->label('Alamat Usaha')
                            ->rows(3)
                            ->columnSpanFull(),
                            
                        // Implementasi Repeater untuk Relasi HasMany Sosial Media
                        Repeater::make('umkm_sosial_media')
                            ->label('Sosial Media')
                            ->addActionLabel('Tambah Sosial Media')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Platform Sosial Media')
                                    ->required(),
                                TextInput::make('link')
                                    ->label('Link')
                                    ->url()
                                    ->required(),
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->defaultItems(0),
                    ])->columns(2),

                Section::make('Profil Investor')
                    ->visible($role === 'investor')
                    ->schema([
                        TextInput::make('investor_name')
                            ->label('Nama Investor'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        if (!Auth::check()) {
            return;
        }

        $data = $this->form->getState();
        $user = Auth::user();

        // 1. Simpan Data Users
        $userData = Arr::only($data, ['name', 'email', 'password']);
        $user->update($userData);

        // 2. Simpan Data Profile (Personal)
        $user->profile()->updateOrCreate(
            ['idUsers' => $user->id],
            [
                'nik' => $data['profile_nik'] ?? null,
                'phone' => $data['profile_phone'] ?? null,
                'alamat' => $data['profile_alamat'] ?? null,
            ]
        );

        // 3. Simpan Data Spesifik Berdasarkan Role
        if ($user->role === 'umkm') {
            $umkmProfile = UmkmProfile::updateOrCreate(
                ['idUsers' => $user->id],
                [
                    'name' => $data['umkm_name'] ?? null,
                    'jenisUsaha' => $data['umkm_jenisUsaha'] ?? null,
                    'nib' => $data['umkm_nib'] ?? null,
                    'email' => $data['umkm_email'] ?? null,
                    'phone' => $data['umkm_phone'] ?? null,
                    'alamat' => $data['umkm_alamat'] ?? null,
                    'modal_awal' => $data['umkm_modal_awal'] ?? 0,
                ]
            );

            // 4. Proses Sinkronisasi Relasi Sosial Media (HasMany)
            if (isset($data['umkm_sosial_media'])) {
                // Hapus data lama untuk mencegah duplikasi atau data yatim piatu
                $umkmProfile->sosialMedia()->delete();
                
                // Konversi format array dari repeater lalu simpan
                $sosialMediaArray = array_values($data['umkm_sosial_media']);
                if (count($sosialMediaArray) > 0) {
                    $umkmProfile->sosialMedia()->createMany($sosialMediaArray);
                }
            }

        } elseif ($user->role === 'investor') {
            InvestorProfile::updateOrCreate(
                ['idUsers' => $user->id],
                [
                    'name' => $data['investor_name'] ?? null,
                ]
            );
        }

        Notification::make()
            ->title('Profile updated!')
            ->success()
            ->send();

        $this->dispatch('close-modal', id: 'edit-profile-modal');
    }

public function render()
    {
        if (!Auth::check()) {
            return <<<'HTML'
            <div></div>
            HTML;
        }

        return <<<'HTML'
        <div x-data x-on:hashchange.window="if(location.hash === '#edit-profile') { $dispatch('open-modal', { id: 'edit-profile-modal' }); history.replaceState(null, '', location.pathname + location.search); }">
            <x-filament::modal id="edit-profile-modal" width="3xl">
                <x-slot name="heading">
                    Edit Profile
                </x-slot>

                <form wire:submit="save">
                    {{ $this->form }}

                    <div class="flex justify-end gap-x-3" style="margin-top:1.5em;">
                        <x-filament::button color="gray" type="button" x-on:click="$dispatch('close-modal', { id: 'edit-profile-modal' })">
                            Batalkan
                        </x-filament::button>
                        <x-filament::button type="submit">
                            Simpan
                        </x-filament::button>
                    </div>
                </form>
            </x-filament::modal>

            <x-filament-actions::modals />
        </div>
        HTML;
    }
}
