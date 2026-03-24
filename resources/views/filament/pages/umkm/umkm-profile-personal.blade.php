<x-filament-panels::page>
    {{-- Page content --}}
    <form wire:submit="save" class="space-y-6">
        
        {{-- Render skema form --}}
        {{ $this->form }}

        {{-- Gunakan komponen button bawaan yang dijamin valid --}}
        <div class="mt-4">
            <x-filament::button type="submit" color="primary" style="margin-top: 1rem;">
                Simpan Profil
            </x-filament::button>
        </div>
        
    </form>
</x-filament-panels::page>
