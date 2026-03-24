<?php

namespace App\Livewire;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema; // WAJIB DI FILAMENT 5.x: Menggunakan Schema, bukan Form
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class EditProfileModal extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        // PERBAIKAN: Cek apakah user sudah login sebelum mengisi form
        if (Auth::check()) {
            $this->form->fill([
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ]);
        }
    }

    // WAJIB DI FILAMENT 5.x: Argumen dan Return type menggunakan Schema
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required()
                    // Auth::user() aman di sini, karena kalau null Filament akan mengabaikannya
                    ->unique(table: 'users', ignorable: Auth::user()), 
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->label('Password Baru'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        // PERBAIKAN: Jangan proses save jika tidak ada user (mencegah aksi ilegal)
        if (!Auth::check()) {
            return;
        }

        $data = $this->form->getState();
        Auth::user()->update($data);

        Notification::make()
            ->title('Profile updated!')
            ->success()
            ->send();

        $this->dispatch('close-modal', id: 'edit-profile-modal');
    }

    public function render()
    {
        // PERBAIKAN: Jika belum login, render div kosong agar modal sama sekali tidak muncul di HTML
        if (!Auth::check()) {
            return <<<'HTML'
            <div></div>
            HTML;
        }

        // Menggunakan Alpine directive inline murni tanpa blok JS eksplisit
        return <<<'HTML'
        <div x-data x-on:hashchange.window="if(location.hash === '#edit-profile') { $dispatch('open-modal', { id: 'edit-profile-modal' }); history.replaceState(null, '', location.pathname + location.search); }">
            <x-filament::modal id="edit-profile-modal" width="md">
                <x-slot name="heading">
                    Edit Profile
                </x-slot>

                <form wire:submit="save">
                    {{ $this->form }}

                    <div class="flex justify-end gap-x-3" style="margin-top:1em;">
                        <x-filament::button color="gray" type="button" x-on:click="$dispatch('close-modal', { id: 'edit-profile-modal' })">
                            Batalkan
                        </x-filament::button>
                        <x-filament::button type="submit">
                            Simpan
                        </x-filament::button>
                    </div>
                </form>
            </x-filament::modal>
        </div>
        HTML;
    }
}