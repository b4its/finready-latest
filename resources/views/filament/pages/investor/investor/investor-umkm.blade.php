<x-filament-panels::page>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-2 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar UMKM</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Monitoring Leveling dan Kesehatan Finansial</p>
        </div>
        
        <div class="w-full md:w-[400px]">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400" />
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="searchQuery"
                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white transition-all shadow-sm" 
                    placeholder="Cari nama UMKM atau Pemilik..."
                >
            </div>
        </div>
    </div>

    @if(count($this->umkmList) === 0)
        <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-8 text-center shadow-sm">
            <x-heroicon-o-building-storefront class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600 mb-3" />
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tidak ada UMKM ditemukan</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Coba sesuaikan kata kunci pencarian Anda.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($this->umkmList as $umkm)
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row justify-between gap-8 transition-all hover:shadow-md">
                    
                    <div class="flex-1 flex flex-col gap-4">
                        <div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-wide {{ $umkm['status_color'] }}">
                                {{ $umkm['status_badge'] }}
                            </span>
                        </div>
                        
                        <div class="-mt-1">
                            <h2 class="text-xl font-extrabold text-gray-900 dark:text-white">{{ $umkm['name'] }}</h2>
                            <p class="text-xs font-bold text-blue-600 dark:text-blue-500 tracking-wider uppercase mt-1">{{ $umkm['category'] }}</p>
                        </div>
                        
                        <div class="text-sm text-gray-500 dark:text-gray-400 space-y-2 mt-1">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-user class="h-4 w-4 text-gray-400" />
                                {{ $umkm['owner'] }}
                            </div>
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-phone class="h-4 w-4 text-gray-400" />
                                {{ $umkm['phone'] }}
                            </div>
                        </div>
                        
                        <div class="mt-2">
                            <button type="button" 
                                    wire:click="openProfileModal({{ $umkm['id'] }})"
                                    class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent text-sm font-semibold rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900 w-full sm:w-auto transition-colors cursor-pointer">
                                Buka Profil 
                            </button>
                        </div>
                    </div>

                    <div class="flex-1 flex flex-col justify-between gap-6 border-t md:border-t-0 border-gray-100 dark:border-gray-800 pt-6 md:pt-0">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="border border-gray-100 dark:border-gray-800 rounded-xl p-4 bg-gray-50/50 dark:bg-gray-800/50">
                                <p class="text-[11px] font-bold text-gray-900 dark:text-gray-300 uppercase tracking-wider mb-2">Status NIB</p>
                                <p class="text-sm font-semibold {{ $umkm['nib_color'] ?? 'text-gray-600' }}">
                                    {{ $umkm['nib_status'] }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 mt-auto justify-start flex-wrap">
                            @php
                                $iconClasses = 'p-2 rounded-lg border border-blue-100 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:border-blue-200 dark:border-blue-900 dark:bg-blue-900/20 dark:text-blue-400 transition-colors cursor-pointer';
                            @endphp
                            
                            @forelse($umkm['social_media'] as $sosmed)
                                <a href="{{ $sosmed['link'] }}" target="_blank" rel="noopener noreferrer" class="{{ $iconClasses }}" title="{{ ucfirst($sosmed['name']) }}">
                                    @if(str_contains($sosmed['name'], 'instagram') || str_contains($sosmed['name'], 'ig'))
                                        <x-heroicon-o-camera class="w-5 h-5"/>
                                    @elseif(str_contains($sosmed['name'], 'web') || str_contains($sosmed['name'], 'site'))
                                        <x-heroicon-o-globe-alt class="w-5 h-5"/>
                                    @elseif(str_contains($sosmed['name'], 'youtube') || str_contains($sosmed['name'], 'yt'))
                                        <x-heroicon-o-video-camera class="w-5 h-5"/>
                                    @else
                                        <x-heroicon-o-link class="w-5 h-5"/>
                                    @endif
                                </a>
                            @empty
                                <span class="text-xs text-gray-400 dark:text-gray-500 italic">Belum ada sosial media</span>
                            @endforelse
                        </div>
                    </div>
                    
                </div>
            @endforeach
        </div>
    @endif

    <x-filament::modal id="detail-profil-modal" width="2xl">
        @if($selectedUmkm)
            <x-slot name="heading">
                Detail Profil UMKM
            </x-slot>

            <div class="space-y-6 pt-2">
                <div class="flex items-center gap-4 border-b border-gray-100 dark:border-gray-800 pb-4">
                    <div class="h-16 w-16 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-xl">
                        {{ substr($selectedUmkm['name'], 0, 1) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $selectedUmkm['name'] }}</h3>
                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400 mt-1">{{ $selectedUmkm['category'] }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-b border-gray-100 dark:border-gray-800 pb-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Nama Pemilik</p>
                        <p class="text-base text-gray-900 dark:text-white font-medium">{{ $selectedUmkm['owner'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Nomor Induk Berusaha (NIB)</p>
                        <p class="text-base text-gray-900 dark:text-white font-medium">{{ $selectedUmkm['nib'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Telepon</p>
                        <p class="text-base text-gray-900 dark:text-white font-medium">{{ $selectedUmkm['phone'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Email</p>
                        <p class="text-base text-gray-900 dark:text-white font-medium">{{ $selectedUmkm['email'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Level Skala</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $selectedUmkm['status_color'] }}">
                            {{ $selectedUmkm['status_badge'] }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Modal Awal</p>
                        <p class="text-base text-gray-900 dark:text-white font-medium">{{ $selectedUmkm['modal_awal'] }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Alamat Lengkap</p>
                        <p class="text-base text-gray-900 dark:text-white font-medium">{{ $selectedUmkm['alamat'] }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Sosial Media & Website</p>
                    <div class="flex items-center gap-3 flex-wrap">
                        @php
                            $modalIconClasses = 'flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors text-sm font-medium';
                        @endphp
                        
                        @forelse($selectedUmkm['social_media'] as $sosmed)
                            <a href="{{ $sosmed['link'] }}" target="_blank" rel="noopener noreferrer" class="{{ $modalIconClasses }}">
                                @if(str_contains($sosmed['name'], 'instagram') || str_contains($sosmed['name'], 'ig'))
                                    <x-heroicon-o-camera class="w-4 h-4 text-pink-500"/>
                                @elseif(str_contains($sosmed['name'], 'web') || str_contains($sosmed['name'], 'site'))
                                    <x-heroicon-o-globe-alt class="w-4 h-4 text-blue-500"/>
                                @elseif(str_contains($sosmed['name'], 'youtube') || str_contains($sosmed['name'], 'yt'))
                                    <x-heroicon-o-video-camera class="w-4 h-4 text-red-500"/>
                                @else
                                    <x-heroicon-o-link class="w-4 h-4 text-gray-500"/>
                                @endif
                                {{ ucfirst($sosmed['name']) }}
                            </a>
                        @empty
                            <span class="text-sm text-gray-500 italic">Tidak ada tautan sosial media yang terdaftar.</span>
                        @endforelse
                    </div>
                </div>
                
            </div>
            
            <x-slot name="footer">
                <div class="flex justify-end gap-x-3">
                    <x-filament::button color="gray" x-on:click="close">
                        Tutup
                    </x-filament::button>
                </div>
            </x-slot>
        @else
            <div class="flex justify-center py-8">
                <x-filament::loading-indicator class="h-8 w-8 text-primary-500" />
            </div>
        @endif
    </x-filament::modal>

</x-filament-panels::page>