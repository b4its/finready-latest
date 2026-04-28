<x-filament-panels::page>
    {{-- Memuat Library Tambahan --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
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
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm transition-all hover:shadow-md flex flex-col overflow-hidden">
                    
                    {{-- Header Card --}}
                    <div class="p-6 flex flex-col md:flex-row justify-between gap-8">
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
                                    <span wire:loading.remove wire:target="openProfileModal({{ $umkm['id'] }})">
                                        @if(!empty($selectedUmkm) && $selectedUmkm['id'] == $umkm['id'])
                                            Tutup Profil
                                        @else
                                            Buka Profil
                                        @endif
                                    </span>
                                    <span wire:loading wire:target="openProfileModal({{ $umkm['id'] }})">Memuat...</span>
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

                    {{-- Area Expanded Detail --}}
                    @if(!empty($selectedUmkm) && $selectedUmkm['id'] == $umkm['id'])
                        <div class="border-t border-gray-100 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-800/20 p-6 animate-fade-in-down">
                            <div class="space-y-6 pt-2">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-b border-gray-100 dark:border-gray-800 pb-6">
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Nomor Induk Berusaha (NIB)</p>
                                        <p class="text-base text-gray-900 dark:text-white font-medium">{{ $selectedUmkm['nib'] }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Email</p>
                                        <p class="text-base text-gray-900 dark:text-white font-medium">{{ $selectedUmkm['email'] }}</p>
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

                                {{-- Chart Area --}}
                                <div class="border-t border-gray-100 dark:border-gray-800 pt-6">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Analisis Mutasi Kas</p>
                                    
                                    @if(($chartData['debet'] ?? 0) > 0 || ($chartData['kredit'] ?? 0) > 0)
                                        <div x-data="{
                                                initChart() {
                                                    const debet = parseFloat({{ $chartData['debet'] ?? 0 }});
                                                    const kredit = parseFloat({{ $chartData['kredit'] ?? 0 }});
                                                    
                                                    const options = {
                                                        series: [{ name: 'Nominal', data: [debet, kredit] }],
                                                        chart: { type: 'bar', height: 250, toolbar: { show: false }, animations: { enabled: true, speed: 800 } },
                                                        plotOptions: { bar: { borderRadius: 8, distributed: true, columnWidth: '60%', dataLabels: { position: 'top' } } },
                                                        colors: ['#10B981', '#F59E0B'],
                                                        dataLabels: {
                                                            enabled: true,
                                                            formatter: function (val) { return 'Rp ' + val.toLocaleString('id-ID'); },
                                                            offsetY: -25,
                                                            style: { fontSize: '11px', colors: ['#9ca3af'], fontWeight: 'bold' }
                                                        },
                                                        legend: { show: false },
                                                        xaxis: { categories: ['Uang Masuk', 'Uang Keluar'], labels: { style: { colors: '#9ca3af', fontSize: '12px' } } },
                                                        yaxis: { show: false },
                                                        grid: { show: false },
                                                        tooltip: { theme: 'dark', y: { formatter: function(val) { return 'Rp ' + val.toLocaleString('id-ID'); } } }
                                                    };

                                                    const chart = new ApexCharts(this.$refs.chartContainer, options);
                                                    chart.render();
                                                }
                                            }" 
                                            x-init="setTimeout(() => initChart(), 100)"
                                            class="bg-gray-50/50 dark:bg-gray-800/50 rounded-xl p-4">
                                            
                                            <div x-ref="chartContainer" style="min-height: 250px;"></div>
                                        </div>

                                        <div class="mt-4 flex justify-between items-center bg-gray-50 dark:bg-gray-800/50 p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                                            <div class="text-[11px]">
                                                <span class="block text-emerald-500 font-bold">Masuk: Rp {{ number_format($chartData['debet'] ?? 0, 0, ',', '.') }}</span>
                                                <span class="block text-amber-500 font-bold">Keluar: Rp {{ number_format($chartData['kredit'] ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                            <p class="text-[10px] text-gray-400 italic">* {{ $chartData['label'] ?? '' }}</p>
                                        </div>
                                    @else
                                        <div class="text-center py-8 border-2 border-dashed border-gray-100 dark:border-gray-800 rounded-xl">
                                            <p class="text-sm text-gray-400 italic">Data keuangan belum tersedia.</p>
                                        </div>
                                    @endif
                                </div>

                                {{-- Action Buttons Area --}}
                                <div class="border-t border-gray-100 dark:border-gray-800 pt-6 flex flex-col md:flex-row items-center justify-center gap-4">
                                    
                                    @if($umkm['status_pengajuan_keuangan'] == 2)
                                        {{-- Tampilan jika TELAH DIDANAI (Status 2) --}}
                                        <button type="button" disabled class="w-full md:w-auto inline-flex justify-center items-center gap-2 text-white bg-green-600 border border-transparent font-medium leading-5 rounded-lg text-sm px-6 py-3 cursor-not-allowed shadow-sm transition-all">
                                            <x-heroicon-o-check-badge class="w-5 h-5" />
                                            UMKM Telah Didanai (Rp {{ number_format($umkm['nominal_pendanaan'] ?? 0, 0, ',', '.') }})
                                        </button>

                                    @elseif($umkm['status_pengajuan_keuangan'] == 1)
                                        {{-- Tampilan jika DISETUJUI --}}
                                        <button type="button" x-on:click="$dispatch('open-modal', { id: 'modal-cetak-dokumen' })" class="w-full md:w-auto inline-flex justify-center items-center gap-2 text-emerald-700 bg-emerald-50 border border-emerald-400 hover:bg-emerald-500 hover:text-white focus:ring-4 focus:ring-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-600 dark:text-emerald-400 dark:hover:bg-emerald-600 dark:hover:text-white dark:focus:ring-emerald-900 font-medium leading-5 rounded-lg text-sm px-6 py-3 focus:outline-none transition-all duration-200 cursor-pointer shadow-sm">
                                            <x-heroicon-o-printer class="w-5 h-5" />
                                            Cetak Dokumen
                                        </button>

                                        @if(!empty($umkm['waktu_pertemuan']))
                                            {{-- Jika Waktu Pertemuan Sudah Diatur, Munculkan Opsi Beri Pendanaan --}}
                                            <div class="w-full md:w-auto inline-flex justify-center items-center gap-2 text-indigo-700 bg-indigo-50 border border-indigo-400 dark:bg-indigo-900/20 dark:border-indigo-600 dark:text-indigo-400 font-medium leading-5 rounded-lg text-sm px-6 py-3 shadow-sm">
                                                <x-heroicon-o-calendar class="w-5 h-5" />
                                                Jadwal: {{ \Carbon\Carbon::parse($umkm['waktu_pertemuan'])->format('d M Y H:i') }}
                                            </div>

                                            <button type="button" x-on:click="$dispatch('open-modal', { id: 'modal-beri-pendanaan' })" class="w-full md:w-auto inline-flex justify-center items-center gap-2 text-teal-700 bg-teal-50 border border-teal-400 hover:bg-teal-500 hover:text-white focus:ring-4 focus:ring-teal-200 dark:bg-teal-900/20 dark:border-teal-600 dark:text-teal-400 dark:hover:bg-teal-600 dark:hover:text-white dark:focus:ring-teal-900 font-medium leading-5 rounded-lg text-sm px-6 py-3 focus:outline-none transition-all duration-200 cursor-pointer shadow-sm">
                                                <x-heroicon-o-banknotes class="w-5 h-5" />
                                                Beri Pendanaan
                                            </button>
                                        @else
                                            {{-- Jika Belum Ada Pertemuan --}}
                                            <button type="button" x-on:click="$dispatch('open-modal', { id: 'modal-ajukan-pertemuan' })" class="w-full md:w-auto inline-flex justify-center items-center gap-2 text-indigo-700 bg-indigo-50 border border-indigo-400 hover:bg-indigo-500 hover:text-white focus:ring-4 focus:ring-indigo-200 dark:bg-indigo-900/20 dark:border-indigo-600 dark:text-indigo-400 dark:hover:bg-indigo-600 dark:hover:text-white dark:focus:ring-indigo-900 font-medium leading-5 rounded-lg text-sm px-6 py-3 focus:outline-none transition-all duration-200 cursor-pointer shadow-sm">
                                                <x-heroicon-o-calendar-days class="w-5 h-5" />
                                                Ajukan Pertemuan
                                            </button>
                                        @endif

                                    @elseif($umkm['status_pengajuan_keuangan'] === 0)
                                        {{-- Tampilan jika PENDING --}}
                                        <button type="button" disabled class="w-full md:w-auto inline-flex justify-center items-center gap-2 text-gray-500 bg-gray-100 border border-gray-200 font-medium leading-5 rounded-lg text-sm px-6 py-3 cursor-not-allowed shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 transition-all">
                                            <x-heroicon-o-clock class="w-5 h-5" />
                                            Menunggu persetujuan dari pihak UMKM
                                        </button>

                                    @else
                                        {{-- Tampilan DEFAULT (Belum Ada Data) --}}
                                        <button type="button" x-on:click="$dispatch('open-modal', { id: 'modal-pengajuan' })" class="w-full md:w-auto inline-flex justify-center items-center gap-2 text-yellow-700 bg-yellow-50 border border-yellow-400 hover:bg-yellow-500 hover:text-white focus:ring-4 focus:ring-yellow-200 dark:bg-yellow-900/20 dark:border-yellow-600 dark:text-yellow-400 dark:hover:bg-yellow-600 dark:hover:text-white dark:focus:ring-yellow-900 font-medium leading-5 rounded-lg text-sm px-6 py-3 focus:outline-none transition-all duration-200 cursor-pointer shadow-sm">
                                            <x-heroicon-o-document-text class="w-5 h-5" />
                                            Ajukan Informasi Lanjutan
                                        </button>
                                    @endif
                                    
                                    <a href="https://wa.me/{{ $selectedUmkm['phone'] ?? '' }}?text=Halo,%20perkenalkan%20saya%20{{ Auth::user()->name }},%20bolehkah%20kita%20berdiskusi%20lebih%20lanjut%20mengenai%20usaha%20yang%20sedang%20anda%20jalani?" target="_blank" class="w-full md:w-auto inline-flex justify-center items-center gap-2 text-green-700 bg-green-50 border border-green-400 hover:bg-green-500 hover:text-white focus:ring-4 focus:ring-green-200 dark:bg-green-900/20 dark:border-green-600 dark:text-green-400 dark:hover:bg-green-600 dark:hover:text-white dark:focus:ring-green-900 font-medium leading-5 rounded-lg text-sm px-6 py-3 focus:outline-none transition-all duration-200 cursor-pointer shadow-sm">
                                        <svg class="w-5 h-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.653a11.883 11.883 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                        Hubungi UMKM melalui WhatsApp
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- MODAL 1: PENGAJUAN DATA --}}
    <x-filament::modal id="modal-pengajuan" width="lg" display-classes="block">
        <x-slot name="heading">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                    <x-heroicon-o-document-text class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                </div>
                <span class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                    Form Pengajuan Data
                </span>
            </div>
        </x-slot>

        <div class="py-4 space-y-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl p-4">
                <div class="flex gap-3">
                    <x-heroicon-s-information-circle class="h-5 w-5 text-blue-600 dark:text-blue-400 shrink-0" />
                    <p class="text-sm text-blue-800 dark:text-blue-300 leading-relaxed">
                        Anda sedang mengajukan permintaan informasi lanjutan untuk 
                        <span class="font-bold underline decoration-blue-500/50 underline-offset-2">
                            {{ $selectedUmkm['name'] ?? 'UMKM Terpilih' }}
                        </span>. Mohon berikan alasan yang jelas.
                    </p>
                </div>
            </div>
            
            <div class="space-y-2">
                <label for="alasan" class="inline-block text-sm font-semibold text-gray-800 dark:text-gray-200 ml-1">
                    Alasan Pengajuan Data
                </label>

                <div class="relative group">
                    <textarea 
                        id="alasan" 
                        rows="5"
                        wire:model="alasanPengajuan"
                        class="block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-yellow-500 focus:ring-yellow-500/20 focus:ring-4 placeholder:text-gray-400 dark:placeholder:text-gray-500 transition-all duration-200 text-sm leading-relaxed p-3"
                        placeholder="Tuliskan alasan lengkap di sini..."></textarea>
                    
                    <div class="absolute bottom-3 right-3 text-[10px] text-gray-400 dark:text-gray-500 pointer-events-none italic">
                        Gunakan bahasa yang formal
                    </div>
                </div>
                @error('alasanPengajuan') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>
        
        <x-slot name="footer">
            <div class="flex items-center justify-end gap-3 w-full border-t border-gray-100 dark:border-gray-800 pt-5">
                <x-filament::button color="gray" tag="button" variant="ghost" x-on:click="close" class="font-medium">
                    Batal
                </x-filament::button>

                <x-filament::button 
                    type="button"
                    wire:click="submitPengajuan"
                    color="warning" 
                    icon="heroicon-m-paper-airplane"
                    icon-position="after"
                    wire:loading.attr="disabled"
                    class="font-bold min-w-[140px]">
                    <span wire:loading.remove wire:target="submitPengajuan">Kirim Pengajuan</span>
                    <span wire:loading wire:target="submitPengajuan">Mengirim...</span>
                </x-filament::button>
            </div>
        </x-slot>
    </x-filament::modal>

    {{-- MODAL 2: CETAK DOKUMEN --}}
    <x-filament::modal id="modal-cetak-dokumen" width="md" display-classes="block">
        <x-slot name="heading">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                    <x-heroicon-o-printer class="h-6 w-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <span class="block text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                        Cetak Laporan Keuangan
                    </span>
                    <span class="block text-sm font-normal text-gray-500 dark:text-gray-400 mt-1">
                        Pilih jenis dokumen dan periode yang tersedia.
                    </span>
                </div>
            </div>
        </x-slot>

        <div class="py-4 space-y-5">
            <div class="space-y-2">
                <label class="inline-block text-sm font-semibold text-gray-800 dark:text-gray-200">Jenis Dokumen</label>
                <select wire:model="cetakJenisDokumen" class="block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-emerald-500 focus:ring-emerald-500/20 text-sm py-2.5 px-3">
                    <option value="jurnal_umum">Jurnal Umum</option>
                    <option value="buku_besar">Buku Besar Umum</option>
                    <option value="neraca_saldo">Neraca Saldo</option>
                    <option value="jurnal_penyesuaian">Jurnal Penyesuaian</option>
                    <option value="laba_rugi">Laba Rugi</option>
                    <option value="perubahan_modal">Perubahan Modal</option>
                    <option value="neraca">Neraca</option>
                    <option value="arus_kas">Arus Kas</option>
                </select>
                @error('cetakJenisDokumen') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            @if(empty($availablePeriods))
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-xl p-4 text-center">
                    <p class="text-sm text-red-600 dark:text-red-400 font-medium">UMKM ini belum memiliki riwayat Jurnal Umum untuk dicetak.</p>
                </div>
            @else
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="inline-block text-sm font-semibold text-gray-800 dark:text-gray-200">Tahun</label>
                        <select wire:model.live="cetakTahun" class="block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-emerald-500 focus:ring-emerald-500/20 text-sm py-2.5 px-3">
                            @foreach(array_keys($availablePeriods) as $tahun)
                                <option value="{{ $tahun }}">{{ $tahun }}</option>
                            @endforeach
                        </select>
                        @error('cetakTahun') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="inline-block text-sm font-semibold text-gray-800 dark:text-gray-200">Bulan</label>
                        <select wire:model="cetakBulan" class="block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-emerald-500 focus:ring-emerald-500/20 text-sm py-2.5 px-3">
                            @if(isset($availablePeriods[$cetakTahun]))
                                @php
                                    $namaBulan = [
                                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                                        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                                        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                                        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                    ];
                                @endphp
                                @foreach($availablePeriods[$cetakTahun] as $bulan)
                                    <option value="{{ $bulan }}">{{ $namaBulan[$bulan] ?? $bulan }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('cetakBulan') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="flex items-center justify-end gap-3 w-full border-t border-gray-100 dark:border-gray-800 pt-5">
                <x-filament::button color="gray" tag="button" variant="ghost" x-on:click="close" class="font-medium">
                    Batal
                </x-filament::button>

                <x-filament::button 
                    type="button" 
                    wire:click="prosesCetak" 
                    color="success" 
                    icon="heroicon-m-printer" 
                    icon-position="after" 
                    wire:loading.attr="disabled"
                    :disabled="empty($availablePeriods)"
                    class="font-bold min-w-[140px]">
                    <span wire:loading.remove wire:target="prosesCetak">Proses Cetak</span>
                    <span wire:loading wire:target="prosesCetak">Memproses...</span>
                </x-filament::button>
            </div>
        </x-slot>
    </x-filament::modal>

    {{-- MODAL 3: AJUKAN PERTEMUAN --}}
    <x-filament::modal id="modal-ajukan-pertemuan" width="md" display-classes="block">
        <x-slot name="heading">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                    <x-heroicon-o-calendar-days class="h-6 w-6 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <span class="block text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                        Atur Jadwal Pertemuan
                    </span>
                    <span class="block text-sm font-normal text-gray-500 dark:text-gray-400 mt-1">
                        Tentukan waktu untuk mendiskusikan investasi.
                    </span>
                </div>
            </div>
        </x-slot>

        <div class="py-4 space-y-5">
            <div class="space-y-2">
                <label for="waktuPertemuan" class="inline-block text-sm font-semibold text-gray-800 dark:text-gray-200">
                    Tanggal & Waktu Pertemuan
                </label>
                <input 
                    type="datetime-local" 
                    id="waktuPertemuan" 
                    wire:model="waktuPertemuan" 
                    class="block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-indigo-500 focus:ring-indigo-500/20 focus:ring-4 transition-all duration-200 text-sm p-3"
                >
                @error('waktuPertemuan') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>

        <x-slot name="footer">
            <div class="flex items-center justify-end gap-3 w-full border-t border-gray-100 dark:border-gray-800 pt-5">
                <x-filament::button color="gray" tag="button" variant="ghost" x-on:click="close" class="font-medium">
                    Batal
                </x-filament::button>

                <x-filament::button 
                    type="button" 
                    wire:click="submitPertemuan" 
                    color="indigo" 
                    icon="heroicon-m-calendar" 
                    icon-position="after" 
                    wire:loading.attr="disabled"
                    class="font-bold min-w-[140px]">
                    <span wire:loading.remove wire:target="submitPertemuan">Simpan Jadwal</span>
                    <span wire:loading wire:target="submitPertemuan">Menyimpan...</span>
                </x-filament::button>
            </div>
        </x-slot>
    </x-filament::modal>

    {{-- MODAL 4: BERI PENDANAAN --}}
    <x-filament::modal id="modal-beri-pendanaan" width="md" display-classes="block">
        <x-slot name="heading">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-teal-100 dark:bg-teal-900/30 rounded-lg">
                    <x-heroicon-o-banknotes class="h-6 w-6 text-teal-600 dark:text-teal-400" />
                </div>
                <div>
                    <span class="block text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                        Beri Pendanaan
                    </span>
                    <span class="block text-sm font-normal text-gray-500 dark:text-gray-400 mt-1">
                        Masukkan nominal investasi yang disepakati.
                    </span>
                </div>
            </div>
        </x-slot>

        <div class="py-4 space-y-5">
            <div class="space-y-2" x-data="{ 
                raw: $wire.entangle('nominalPendanaan'),
                formatRupiah(value) {
                    if (!value) return '';
                    let val = value.toString().replace(/[^0-9]/g, '');
                    return val.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }
            }">
                <label for="nominalPendanaan" class="inline-block text-sm font-semibold text-gray-800 dark:text-gray-200">
                    Nominal Pendanaan (Rp)
                </label>
                <input 
                    type="text" 
                    id="nominalPendanaan" 
                    x-bind:value="formatRupiah(raw)"
                    x-on:input="
                        let cleanValue = $event.target.value.replace(/[^0-9]/g, '');
                        raw = cleanValue;
                        $event.target.value = formatRupiah(cleanValue);
                    "
                    placeholder="Contoh: 10.000.000"
                    class="block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-teal-500 focus:ring-teal-500/20 focus:ring-4 transition-all duration-200 text-sm p-3"
                >
                @error('nominalPendanaan') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>

        <x-slot name="footer">
            <div class="flex items-center justify-end gap-3 w-full border-t border-gray-100 dark:border-gray-800 pt-5">
                <x-filament::button color="gray" tag="button" variant="ghost" x-on:click="close" class="font-medium">
                    Batal
                </x-filament::button>

                <x-filament::button 
                    type="button" 
                    wire:click="submitPendanaan" 
                    color="success" 
                    icon="heroicon-m-check-circle" 
                    icon-position="after" 
                    wire:loading.attr="disabled"
                    class="font-bold min-w-[140px]">
                    <span wire:loading.remove wire:target="submitPendanaan">Konfirmasi Pendanaan</span>
                    <span wire:loading wire:target="submitPendanaan">Memproses...</span>
                </x-filament::button>
            </div>
        </x-slot>
    </x-filament::modal>
</x-filament-panels::page>