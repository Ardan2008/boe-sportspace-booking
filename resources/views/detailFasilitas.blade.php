<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>{{ $fasilitas->nama }} — BOE-Space Reserve</title>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-gray-100 min-h-screen">
<x-layout.navbar />

{{-- ══════════════════════════════════════════════════
     MAIN PAGE WRAPPER  (Alpine scope for lightbox)
     ══════════════════════════════════════════════════ --}}
<div x-data="{
        lbOpen: false,
        lbPhotos: [],
        lbIdx: 0,
        openLightbox(photos, idx) {
            this.lbPhotos = photos;
            this.lbIdx   = idx || 0;
            this.lbOpen  = true;
        }
     }"
     @keydown.escape.window="lbOpen = false"
     class="pt-28 pb-20 px-4 md:px-6">

    <div class="max-w-4xl mx-auto">

        {{-- ── BREADCRUMB ── --}}
        <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-8">
            <a href="/#booking" class="hover:text-[#1d6fa5] transition-colors">Fasilitas</a>
            <span>›</span>
            <span class="text-slate-600">{{ $fasilitas->nama }}</span>
        </div>

        {{-- ── HERO CARD ── --}}
        <div class="bg-white rounded-[3rem] overflow-hidden shadow-2xl border border-slate-100 mb-8">
            {{-- Cover image --}}
            <div class="relative h-72 md:h-96 w-full overflow-hidden">
                <img src="{{ asset('storage/fasilitas/' . $fasilitas->image) }}"
                     alt="{{ $fasilitas->nama }}"
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                {{-- Tipe badge --}}
                <span class="absolute top-6 left-6 text-[10px] font-black uppercase tracking-[0.25em] bg-white/90 text-[#1d6fa5] px-4 py-2 rounded-full backdrop-blur-md shadow">
                    {{ ucfirst($fasilitas->tipe) }}
                </span>
                {{-- Price tag --}}
                <div class="absolute top-6 right-6 bg-white/90 backdrop-blur-md px-4 py-2.5 rounded-2xl shadow-xl">
                    <p class="text-[8px] font-bold text-blue-600 uppercase tracking-[0.2em] leading-none mb-1">Price Range</p>
                    <p class="text-sm font-black text-gray-900 leading-none">{{ $fasilitas->harga_thumbnail }}</p>
                </div>
                {{-- Title overlay --}}
                <div class="absolute bottom-6 left-6 right-6">
                    <h1 class="text-3xl md:text-5xl font-black text-white uppercase tracking-tighter drop-shadow-lg">
                        {{ $fasilitas->nama }}
                    </h1>
                </div>
            </div>

            {{-- Info grid --}}
            <div class="p-8 md:p-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Left: description --}}
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400 mb-2">Deskripsi</p>
                        <p class="text-slate-600 leading-relaxed text-sm font-medium italic mb-6">{{ $fasilitas->deskripsi }}</p>
                        @if($fasilitas->detail)
                        <p class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400 mb-2">Detail Fasilitas</p>
                        <p class="text-slate-800 leading-relaxed text-sm font-bold">{{ $fasilitas->detail }}</p>
                        @endif
                    </div>
                    {{-- Right: operational info --}}
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400 mb-3">Informasi Operasional</p>
                        <div class="space-y-3 bg-slate-50 p-5 rounded-2xl border border-slate-100 mb-6">
                            @if($fasilitas->tipe === 'asrama' && $fasilitas->jumlah_kamar)
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Jumlah Kamar</span>
                                <span class="text-xs font-black text-[#1d6fa5]">{{ $fasilitas->jumlah_kamar }} Kamar</span>
                            </div>
                            @endif
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                                    {{ $fasilitas->tipe === 'aula' ? 'Cap. Orang' : 'Cap. Dewasa' }}
                                </span>
                                <span class="text-xs font-black text-slate-800">{{ $fasilitas->max_dewasa ?? '-' }}</span>
                            </div>
                            @if($fasilitas->tipe === 'asrama')
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Cap. Anak</span>
                                <span class="text-xs font-black text-slate-800">{{ $fasilitas->max_anak ?? '-' }}</span>
                            </div>
                            @endif
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Jam Operasional</span>
                                <span class="text-xs font-black text-[#1d6fa5]">{{ $fasilitas->jam_operasional ?? '-' }}</span>
                            </div>
                        </div>
                        {{-- Labels --}}
                        @if($fasilitas->labels && count($fasilitas->labels))
                        <p class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400 mb-3">Fitur & Layanan</p>
                        <div class="flex flex-wrap gap-2 mb-6">
                            @foreach($fasilitas->labels as $lbl)
                            <span class="px-3 py-1.5 bg-blue-50 text-[#1d6fa5] text-[10px] font-black uppercase tracking-widest rounded-xl border border-blue-100 shadow-sm">{{ $lbl }}</span>
                            @endforeach
                        </div>
                        @endif
                        {{-- Gallery --}}
                        @if($fasilitas->gallery && count($fasilitas->gallery))
                        <p class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400 mb-3">Gallery</p>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach($fasilitas->gallery as $gimg)
                            <img src="{{ asset('storage/fasilitas/gallery/' . $gimg) }}"
                                 alt="Gallery"
                                 @click="openLightbox(['{{ asset('storage/fasilitas/gallery/' . $gimg) }}'], 0)"
                                 class="w-full h-24 object-cover rounded-xl border border-slate-100 hover:scale-105 transition-transform cursor-pointer shadow-sm">
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>{{-- /hero card --}}

        {{-- ══════════════════════════════════════════════
             ROOM TYPE CARDS  (asrama only)
             ══════════════════════════════════════════════ --}}
        @if($fasilitas->tipe === 'asrama' && $fasilitas->paket_harian && count($fasilitas->paket_harian))
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-px flex-1 bg-slate-200"></div>
                <h2 class="text-sm font-black uppercase tracking-[0.25em] text-[#1d6fa5] whitespace-nowrap">Tipe Kamar Tersedia</h2>
                <div class="h-px flex-1 bg-slate-200"></div>
            </div>

            <div class="space-y-4">
            @foreach($fasilitas->paket_harian as $rtIdx => $rt)
            @php
                $photos = [];
                $raw = $rt['foto'] ?? [];
                $first = collect($raw)->first(fn($p) => $p);
                for ($i = 0; $i < 3; $i++) {
                    $src = (!empty($raw[$i]) && trim($raw[$i])) ? $raw[$i] : $first;
                    if ($src) $photos[] = asset('storage/fasilitas/rooms/' . $src);
                }
                $photosJson = json_encode($photos);
                $fas = $rt['fasilitas'] ?? [];
                $fasMap = [
                    ['ac','AC'],['kipas_angin','Kipas Angin'],['meja_kursi','Meja & Kursi'],
                    ['lemari_locker','Lemari/Locker'],['stopkontak','Stopkontak'],
                    ['kamar_mandi_dalam','KM Dalam'],['water_heater','Water Heater'],
                    ['bantal_set_sprei','Bantal & Sprei'],['gantungan_baju','Gantungan'],
                    ['kaca_rias','Kaca Rias'],
                ];
                $bookUrl = route('formBooking', ['id' => $fasilitas->id, 'tipe_id' => $rtIdx]);
            @endphp

            {{-- ── Single room-type card (Alpine x-data for static photo + hover lightbox) ── --}}
            <div class="border-2 border-slate-100 hover:border-blue-200 rounded-2xl overflow-hidden bg-white shadow-sm hover:shadow-md transition-all"
                 x-data="{
                     hovered: false,
                     photos: {{ $photosJson }},
                     triggerLightbox(e) {
                         e.stopPropagation();
                         $dispatch('open-detail-lb', { photos: this.photos, idx: 0 });
                     }
                 }"
                 @open-detail-lb.window="openLightbox($event.detail.photos, $event.detail.idx)">
                <div class="flex flex-col sm:flex-row">

                    {{-- ── Static photo thumbnail with hover blur + eye icon ── --}}
                    <div class="relative w-full sm:w-48 aspect-[4/3] flex-shrink-0 overflow-hidden bg-gray-100 rounded-t-2xl sm:rounded-none sm:rounded-l-2xl cursor-pointer"
                         @mouseenter="hovered = true"
                         @mouseleave="hovered = false"
                         @click.stop="triggerLightbox($event)">

                        @if(count($photos) > 0)
                        {{-- Static first-photo thumbnail --}}
                        <img src="{{ $photos[0] }}"
                             alt="Foto Kamar"
                             :class="hovered ? 'blur-sm brightness-75 scale-105' : ''"
                             class="w-full h-full object-cover transition-all duration-300">

                        {{-- Photo count badge (shows how many photos available) --}}
                        @if(count($photos) > 1)
                        <div class="absolute top-2 right-2 bg-black/50 text-white text-[9px] font-black px-2 py-0.5 rounded-full z-10 pointer-events-none">
                            {{ count($photos) }} foto
                        </div>
                        @endif

                        {{-- Hover eye icon --}}
                        <div x-show="hovered" x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-75"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute inset-0 flex items-center justify-center z-20 pointer-events-none">
                            <div class="w-12 h-12 rounded-full bg-white/90 flex items-center justify-center shadow-xl">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                        </div>
                        @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        @endif
                    </div>{{-- /static photo thumbnail --}}

                    {{-- ── Info panel ── --}}
                    <div class="flex-1 p-5 flex flex-col gap-3">
                        {{-- Name + blok + count --}}
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-black text-slate-900 text-sm">{{ $rt['tipe'] ?? ('Tipe ' . ($rtIdx + 1)) }}</p>
                            @if(!empty($rt['kode_blok']))
                            <span class="text-[9px] font-bold text-slate-400 uppercase bg-slate-100 px-2 py-0.5 rounded-full">Blok {{ $rt['kode_blok'] }}</span>
                            @endif
                            <span class="text-[9px] font-bold {{ ($rt['jumlah'] ?? 0) > 0 ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' : 'text-red-700 bg-red-50 border border-red-200' }} px-2 py-0.5 rounded-full ml-auto">
                                {{ ($rt['jumlah'] ?? 0) > 0 ? 'Tersedia ' . $rt['jumlah'] . ' Kamar' : 'Kamar Penuh' }}
                            </span>
                        </div>
                        {{-- Keunggulan --}}
                        @if(!empty($rt['keunggulan']))
                        <p class="text-[11px] text-slate-500 font-medium leading-snug">{{ $rt['keunggulan'] }}</p>
                        @endif
                        {{-- Pricing --}}
                        <div class="flex flex-wrap items-baseline gap-x-4 gap-y-1">
                            @if(!empty($rt['harga_harian']) && $rt['harga_harian'] > 0)
                            <span class="inline-flex items-baseline gap-1">
                                <span class="text-sm font-black text-slate-800">Rp {{ number_format($rt['harga_harian'],0,',','.') }}</span>
                                <span class="text-[10px] font-medium text-slate-400">/hari</span>
                            </span>
                            @endif
                            @if(!empty($rt['harga_mingguan']) && $rt['harga_mingguan'] > 0)
                            <span class="inline-flex items-baseline gap-1">
                                <span class="text-sm font-black text-slate-800">Rp {{ number_format($rt['harga_mingguan'],0,',','.') }}</span>
                                <span class="text-[10px] font-medium text-slate-400">/minggu</span>
                            </span>
                            @endif
                            @if(!empty($rt['harga_bulanan']) && $rt['harga_bulanan'] > 0)
                            <span class="inline-flex items-baseline gap-1">
                                <span class="text-sm font-black text-slate-800">Rp {{ number_format($rt['harga_bulanan'],0,',','.') }}</span>
                                <span class="text-[10px] font-medium text-slate-400">/bulan</span>
                            </span>
                            @endif
                            @if(!empty($rt['harga_tahunan']) && $rt['harga_tahunan'] > 0)
                            <span class="inline-flex items-baseline gap-1">
                                <span class="text-sm font-black text-slate-800">Rp {{ number_format($rt['harga_tahunan'],0,',','.') }}</span>
                                <span class="text-[10px] font-medium text-slate-400">/tahun</span>
                            </span>
                            @endif
                        </div>
                        {{-- Specs --}}
                        <div class="grid grid-cols-3 gap-1.5">
                            @if(!empty($rt['panjang']) && !empty($rt['lebar']))
                            <div class="bg-slate-50 rounded-xl px-2 py-1.5 text-center">
                                <p class="text-[8px] font-black text-slate-400 uppercase leading-none mb-0.5">Ukuran</p>
                                <p class="text-[10px] font-black text-slate-700">{{ $rt['panjang'] }}×{{ $rt['lebar'] }} m²</p>
                            </div>
                            @endif
                            <div class="bg-slate-50 rounded-xl px-2 py-1.5 text-center">
                                <p class="text-[8px] font-black text-slate-400 uppercase leading-none mb-0.5">Kapasitas</p>
                                <p class="text-[10px] font-black text-slate-700">
                                    {{ $rt['max_dewasa'] ?? 1 }} Dws{{ ($rt['max_anak'] ?? 0) > 0 ? '+' . $rt['max_anak'] . 'Ank' : '' }}
                                </p>
                            </div>
                            @if(!empty($rt['ranjang']))
                            <div class="bg-slate-50 rounded-xl px-2 py-1.5 text-center">
                                <p class="text-[8px] font-black text-slate-400 uppercase leading-none mb-0.5">Kasur</p>
                                <p class="text-[10px] font-black text-slate-700">{{ $rt['ranjang'] }}</p>
                            </div>
                            @endif
                        </div>

                        {{-- Fasilitas chips --}}
                        @php $hasChips = false; @endphp
                        <div class="flex flex-wrap gap-1">
                            @foreach($fasMap as [$key, $label])
                                @if(!empty($fas[$key]) && $fas[$key] > 0)
                                @php $hasChips = true; @endphp
                                <span class="inline-flex items-center gap-1 text-[9px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full">
                                    {{ $label }} × {{ $fas[$key] }}
                                </span>
                                @endif
                            @endforeach
                        </div>
                        {{-- Booking CTA --}}
                        <div class="mt-auto pt-3 border-t border-slate-100">
                            @if(($rt['jumlah'] ?? 0) > 0)
                            <a href="{{ $bookUrl }}"
                               class="inline-flex items-center gap-2 bg-[#1d6fa5] hover:bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest px-5 py-3 rounded-xl transition-all shadow-sm hover:shadow-md">
                                Booking Kamar Ini
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                            @else
                            <button disabled
                               onclick="alert('Maaf, semua kamar pada tipe ini sudah penuh untuk tanggal yang Anda pilih.')"
                               class="inline-flex items-center gap-2 bg-gray-400 text-white text-[10px] font-black uppercase tracking-widest px-5 py-3 rounded-xl cursor-not-allowed opacity-70 pointer-events-none">
                                Kamar Penuh
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            </button>
                            @endif
                        </div>
                    </div>{{-- /info panel --}}
                </div>
            </div>{{-- /room card --}}
            @endforeach
            </div>
        </div>
        @endif

        {{-- ── Bottom CTA ── --}}
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{ route('formBooking', ['id' => $fasilitas->id]) }}"
               class="flex-1 bg-[#1d6fa5] hover:bg-slate-900 text-white py-5 rounded-[1.5rem] font-black uppercase tracking-[0.2em] text-[10px] transition-all flex items-center justify-center gap-3 shadow-lg shadow-blue-100">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Book Now
            </a>
            <a href="/#booking"
               class="flex-1 bg-slate-50 hover:bg-slate-100 text-slate-500 py-5 rounded-[1.5rem] font-black uppercase tracking-[0.2em] text-[10px] transition-all flex items-center justify-center">
                ← Kembali
            </a>
        </div>

    </div>{{-- /max-w-4xl --}}

    {{-- ══════════════════════════════════════════════
         LIGHTBOX OVERLAY  (true top-level, fixed inset-0)
         Lives inside the Alpine scope for lbOpen access.
         x-teleport pushes it to <body> at runtime,
         escaping any ancestor overflow/stacking context.
         ══════════════════════════════════════════════ --}}
    <template x-teleport="body">
        <div x-show="lbOpen"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[9999] bg-black/90 backdrop-blur-md flex items-center justify-center p-4"
             @click.self="lbOpen = false">
            <div class="relative max-w-3xl w-full">
                {{-- Close --}}
                <button @click="lbOpen = false"
                    class="absolute -top-12 right-0 text-white/80 hover:text-red-400 transition-colors flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-widest text-white/50">Tutup</span>
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                {{-- Track --}}
                <div class="overflow-hidden rounded-[2rem] bg-gray-900 shadow-2xl">
                    <div class="flex transition-all duration-500 ease-in-out"
                         :style="'transform: translateX(-' + (lbIdx * 100) + '%)'">
                        <template x-for="(src, si) in (lbPhotos || [])" :key="si">
                            <div style="min-width:100%; flex-shrink:0">
                                <img :src="src" class="w-full max-h-[75vh] object-contain mx-auto select-none">
                            </div>
                        </template>
                    </div>
                </div>
                {{-- Navigation --}}
                <div class="flex justify-center items-center gap-4 mt-5">
                    <button @click="lbIdx = Math.max(0, lbIdx - 1)"
                        :disabled="lbIdx === 0"
                        class="px-6 py-2.5 bg-white/10 hover:bg-white/20 disabled:opacity-30 text-white rounded-xl font-bold transition-all text-sm">← Prev</button>
                    <div class="flex gap-2 items-center">
                        <template x-for="(s, di) in (lbPhotos || [])" :key="di">
                            <div @click="lbIdx = di"
                                 class="rounded-full cursor-pointer transition-all duration-300"
                                 :class="di === lbIdx ? 'w-3 h-3 bg-white' : 'w-2 h-2 bg-white/40 hover:bg-white/70'"></div>
                        </template>
                    </div>
                    <button @click="lbIdx = Math.min((lbPhotos || []).length - 1, lbIdx + 1)"
                        :disabled="lbIdx >= (lbPhotos || []).length - 1"
                        class="px-6 py-2.5 bg-white/10 hover:bg-white/20 disabled:opacity-30 text-white rounded-xl font-bold transition-all text-sm">Next →</button>
                </div>
                <p class="text-center text-white/40 text-xs font-bold mt-3 uppercase tracking-widest"
                   x-text="'Foto ' + (lbIdx + 1) + ' dari ' + (lbPhotos || []).length"></p>
            </div>
        </div>
    </template>

</div>{{-- /x-data wrapper --}}
</body>
</html>
