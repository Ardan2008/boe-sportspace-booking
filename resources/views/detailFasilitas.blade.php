<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @php
        $seoImage = $fasilitas->image ? asset('storage/fasilitas/' . $fasilitas->image) : url('/image/logo/tutwuri-logo.svg');
        $seoDesc = Str::limit(strip_tags($fasilitas->deskripsi ?? ''), 160);
        $seoKeywords = $fasilitas->nama . ', BOE Malang, booking lapangan, BBPPMPV, ' . $fasilitas->tipe;
        if (is_array($fasilitas->labels)) {
            $seoKeywords .= ', ' . implode(', ', $fasilitas->labels);
        }
    @endphp

    <x-seo.head
        :title="$fasilitas->nama . ' - BOE-Sport Space'"
        :description="$seoDesc"
        :keywords="$seoKeywords"
        :image="$seoImage"
        :url="url()->current()"
        type="website"
        :jsonLd="[
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => url('/')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Fasilitas', 'item' => url('/#booking')],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => $fasilitas->nama, 'item' => url()->current()],
                ],
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => $fasilitas->nama,
                'description' => $seoDesc,
                'image' => $seoImage,
                'category' => ucfirst($fasilitas->tipe),
            ],
        ]"
    />

    <style>
        body { font-family: 'Poppins', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-gray-100 min-h-screen">
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
     class="pt-12 pb-20 px-4 md:px-6">

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
                            @if($fasilitas->tipe === 'lapangan' && $fasilitas->jumlah_lapangan)
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Jumlah Lapangan</span>
                                <span class="text-xs font-black text-[#1d6fa5]">{{ $fasilitas->jumlah_lapangan }} Lapangan</span>
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
                        @php
                            $galleryUrls = collect($fasilitas->gallery)
                                ->map(fn($g) => asset('storage/fasilitas/gallery/' . $g))
                                ->values()->toArray();
                            $galleryJson = json_encode($galleryUrls);
                        @endphp
                        <p class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400 mb-3">Gallery</p>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach($fasilitas->gallery as $gIdx => $gimg)
                            <img src="{{ asset('storage/fasilitas/gallery/' . $gimg) }}"
                                 alt="Gallery"
                                 @click="openLightbox({{ $galleryJson }}, {{ $gIdx }})"
                                 class="w-full h-24 object-cover rounded-xl border border-slate-100 hover:scale-105 transition-transform cursor-pointer shadow-sm">
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>{{-- /hero card --}}

         {{-- ══════════════════════════════════════════════
              ROOM TYPE CARDS  (lapangan only)
              ══════════════════════════════════════════════ --}}
        @if($fasilitas->tipe === 'lapangan' && $fasilitas->paket_harian && count($fasilitas->paket_harian))
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-px flex-1 bg-slate-200"></div>
                <h2 class="text-sm font-black uppercase tracking-[0.25em] text-[#1d6fa5] whitespace-nowrap">Tipe Lapangan Tersedia</h2>
                <div class="h-px flex-1 bg-slate-200"></div>
            </div>

            @php
                $allSame   = (bool) ($fasilitas->all_same ?? false);
                $roomCount = count($fasilitas->paket_harian);
                $rt0       = $fasilitas->paket_harian[0] ?? [];

                // 1 lapangan → selalu satu kartu
                if ($roomCount <= 1) {
                    $allSame = true;
                }
                // DB masih 0 (data lama) tapi semua rooms kontennya identik → perlakukan sama
                elseif (!$allSame && $roomCount > 1) {
                    $first    = $fasilitas->paket_harian[0];
                    $detected = true;
                    foreach (array_slice($fasilitas->paket_harian, 1) as $r) {
                        $tiFirst = is_array($first['tipe'] ?? null) ? $first['tipe'] : (array) ($first['tipe'] ?? '');
                        $tiR     = is_array($r['tipe'] ?? null)     ? $r['tipe']     : (array) ($r['tipe']     ?? '');
                        sort($tiFirst); sort($tiR);
                        if ($tiFirst !== $tiR
                            || (string)($r['panjang']        ?? '') !== (string)($first['panjang']        ?? '')
                            || (string)($r['lebar']          ?? '') !== (string)($first['lebar']          ?? '')
                            || (float) ($r['harga_harian']   ?? 0)  !== (float) ($first['harga_harian']   ?? 0)
                            || (float) ($r['harga_mingguan'] ?? 0)  !== (float) ($first['harga_mingguan'] ?? 0)
                            || (float) ($r['harga_bulanan']  ?? 0)  !== (float) ($first['harga_bulanan']  ?? 0)
                            || (float) ($r['harga_tahunan']  ?? 0)  !== (float) ($first['harga_tahunan']  ?? 0)) {
                            $detected = false;
                            break;
                        }
                    }
                    if ($detected) $allSame = true;
                }
            @endphp

            {{-- ═══════════════════════════════════════════════════
                 CASE A — allSame = true
                 Satu lapangan ATAU semua lapangan identik →
                 tampilkan SATU kartu. Badge jumlah hanya muncul
                 kalau lapangan > 1.
            ═══════════════════════════════════════════════════ --}}
            @if($allSame)
            @php
                $fas     = $rt0['fasilitas'] ?? [];
                $fasKeys = array_keys($fas);
                $fasMap  = array_map(fn($k) => [$k, str_replace('_', ' ', ucwords($k, '_'))], $fasKeys);
                $bookUrl = route('formBooking', ['id' => $fasilitas->id]);
                $tiers   = [
                    'harian'   => ['label' => 'Hari',   'val' => $rt0['harga_harian']   ?? null],
                    'mingguan' => ['label' => 'Minggu', 'val' => $rt0['harga_mingguan'] ?? null],
                    'bulanan'  => ['label' => 'Bulan',  'val' => $rt0['harga_bulanan']  ?? null],
                    'tahunan'  => ['label' => 'Tahun',  'val' => $rt0['harga_tahunan']  ?? null],
                ];
                $tipeName = is_array($rt0['tipe'] ?? null) && count($rt0['tipe'])
                    ? implode(', ', $rt0['tipe'])
                    : (is_string($rt0['tipe'] ?? null) && ($rt0['tipe'] ?? '') !== '' ? $rt0['tipe'] : $fasilitas->nama);
            @endphp
            <div class="border-2 border-slate-100 hover:border-blue-200 rounded-2xl overflow-hidden bg-white shadow-sm hover:shadow-md transition-all">
                <div class="p-5 flex flex-col gap-4">

                    {{-- Header: nama tipe + badge ketersediaan (hanya jika > 1 lapangan) --}}
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-black text-slate-900 text-sm">{{ $tipeName }}</p>
                        @if($roomCount > 1)
                        <span class="inline-flex items-center gap-1 text-[9px] font-black text-[#1d6fa5] bg-blue-50 border border-blue-100 px-2.5 py-1 rounded-full uppercase tracking-widest">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            {{ $roomCount }} lapangan tersedia
                        </span>
                        @endif
                        @if(!empty($rt0['kode_blok']))
                        <span class="text-[9px] font-bold text-slate-400 uppercase bg-slate-100 px-2 py-0.5 rounded-full">Blok {{ $rt0['kode_blok'] }}</span>
                        @endif
                    </div>

                    {{-- Pricing --}}
                    <div class="flex flex-wrap items-center gap-3">
                        @foreach($tiers as $tier)
                            @if(!empty($tier['val']) && $tier['val'] > 0)
                            <span class="inline-flex items-center gap-1.5 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                                <span class="text-sm font-black text-slate-800">Rp {{ number_format($tier['val'],0,',','.') }}</span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase">/{{ $tier['label'] }}</span>
                            </span>
                            @endif
                        @endforeach
                    </div>

                    {{-- Ukuran --}}
                    @if(!empty($rt0['panjang']) && !empty($rt0['lebar']))
                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 w-fit">
                        <span class="text-[9px] font-black text-slate-400 uppercase">Ukuran</span>
                        <span class="text-xs font-black text-slate-700">{{ $rt0['panjang'] }}×{{ $rt0['lebar'] }} m²</span>
                    </div>
                    @endif

                    {{-- Fasilitas chips --}}
                    @php
                        $hasAnyFas = false;
                        foreach ($fasMap as [$fk, $fl]) { if (!empty($fas[$fk]) && $fas[$fk] > 0) { $hasAnyFas = true; break; } }
                    @endphp
                    @if($hasAnyFas)
                    <div class="flex flex-wrap gap-1">
                        @foreach($fasMap as [$fk, $fl])
                            @if(!empty($fas[$fk]) && $fas[$fk] > 0)
                            <span class="inline-flex items-center gap-1 text-[9px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full">
                                {{ $fl }} × {{ $fas[$fk] }}
                            </span>
                            @endif
                        @endforeach
                    </div>
                    @endif

                    {{-- Booking CTA --}}
                    <div class="mt-auto pt-3 border-t border-slate-100">
                        <a href="{{ $bookUrl }}"
                           class="inline-flex items-center gap-2 bg-[#1d6fa5] hover:bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest px-5 py-3 rounded-xl transition-all shadow-sm hover:shadow-md">
                            Booking Sekarang
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            @else
            {{-- ═══════════════════════════════════════════════════
                 CASE B — allSame = false
                 Setiap lapangan punya spesifikasi berbeda →
                 tampilkan satu kartu per lapangan dengan foto,
                 tipe, dan harga masing-masing.
            ═══════════════════════════════════════════════════ --}}
            <div class="space-y-4">
            @foreach($fasilitas->paket_harian as $rtIdx => $rt)
            @php
                $photos = [];
                $foto   = $rt['foto'][0] ?? null;
                if ($foto) {
                    $photos[] = asset('storage/fasilitas/rooms/' . $foto);
                }
                $photosJson = json_encode($photos);
                $fas        = $rt['fasilitas'] ?? [];
                $fasKeys    = array_keys($fas);
                $fasMap     = array_map(fn($k) => [$k, str_replace('_', ' ', ucwords($k, '_'))], $fasKeys);
                $bookUrl    = route('formBooking', ['id' => $fasilitas->id, 'tipe_id' => $rtIdx]);
                $tiers = [
                    'harian'   => ['label' => 'Hari',   'val' => $rt['harga_harian']   ?? null],
                    'mingguan' => ['label' => 'Minggu', 'val' => $rt['harga_mingguan'] ?? null],
                    'bulanan'  => ['label' => 'Bulan',  'val' => $rt['harga_bulanan']  ?? null],
                    'tahunan'  => ['label' => 'Tahun',  'val' => $rt['harga_tahunan']  ?? null],
                ];
            @endphp

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

                    {{-- Foto thumbnail — hanya jika ada foto --}}
                    @if(count($photos) > 0)
                    <div class="relative w-full sm:w-48 aspect-[4/3] shrink-0 overflow-hidden bg-gray-100 rounded-t-2xl sm:rounded-none sm:rounded-l-2xl cursor-pointer"
                         @mouseenter="hovered = true"
                         @mouseleave="hovered = false"
                         @click.stop="triggerLightbox($event)">
                        <img src="{{ $photos[0] }}"
                             alt="Foto Lapangan"
                             :class="hovered ? 'blur-sm brightness-75 scale-105' : ''"
                             class="w-full h-full object-cover transition-all duration-300">
                        @if(count($photos) > 1)
                        <div class="absolute top-2 right-2 bg-black/50 text-white text-[9px] font-black px-2 py-0.5 rounded-full z-10 pointer-events-none">
                            {{ count($photos) }} foto
                        </div>
                        @endif
                        <div x-show="hovered"
                             x-transition:enter="transition ease-out duration-150"
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
                    </div>
                    @endif

                    {{-- Info panel --}}
                    <div class="flex-1 p-5 flex flex-col gap-4">
                        {{-- Nama tipe + blok --}}
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-black text-slate-900 text-sm">
                                {{ is_array($rt['tipe'] ?? null) ? implode(', ', $rt['tipe']) : ($rt['tipe'] ?? ('Lapangan ' . ($rtIdx + 1))) }}
                            </p>
                            @if(!empty($rt['kode_blok']))
                            <span class="text-[9px] font-bold text-slate-400 uppercase bg-slate-100 px-2 py-0.5 rounded-full">Blok {{ $rt['kode_blok'] }}</span>
                            @endif
                        </div>

                        {{-- Pricing --}}
                        <div class="flex flex-wrap items-center gap-3">
                            @foreach($tiers as $tier)
                                @if(!empty($tier['val']) && $tier['val'] > 0)
                                <span class="inline-flex items-center gap-1.5 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                                    <span class="text-sm font-black text-slate-800">Rp {{ number_format($tier['val'],0,',','.') }}</span>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">/{{ $tier['label'] }}</span>
                                </span>
                                @endif
                            @endforeach
                        </div>

                        {{-- Ukuran --}}
                        @if(!empty($rt['panjang']) && !empty($rt['lebar']))
                        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 w-fit">
                            <span class="text-[9px] font-black text-slate-400 uppercase">Ukuran</span>
                            <span class="text-xs font-black text-slate-700">{{ $rt['panjang'] }}×{{ $rt['lebar'] }} m²</span>
                        </div>
                        @endif

                        {{-- Fasilitas chips --}}
                        @php
                            $hasAnyFas = false;
                            foreach ($fasMap as [$fk, $fl]) { if (!empty($fas[$fk]) && $fas[$fk] > 0) { $hasAnyFas = true; break; } }
                        @endphp
                        @if($hasAnyFas)
                        <div class="flex flex-wrap gap-1">
                            @foreach($fasMap as [$fk, $fl])
                                @if(!empty($fas[$fk]) && $fas[$fk] > 0)
                                <span class="inline-flex items-center gap-1 text-[9px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full">
                                    {{ $fl }} × {{ $fas[$fk] }}
                                </span>
                                @endif
                            @endforeach
                        </div>
                        @endif

                        {{-- Booking CTA --}}
                        <div class="mt-auto pt-3 border-t border-slate-100">
                            <a href="{{ $bookUrl }}"
                               class="inline-flex items-center gap-2 bg-[#1d6fa5] hover:bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest px-5 py-3 rounded-xl transition-all shadow-sm hover:shadow-md">
                                Booking Lapangan Ini
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            </div>
            @endif

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
             class="fixed inset-0 z-[9999] bg-black/90 backdrop-blur-md flex items-center justify-center p-4">
            <div class="relative max-w-3xl w-full">
                {{-- Track --}}
                <div class="overflow-hidden rounded-4xl bg-gray-900 shadow-2xl relative">
                    <button @click="lbOpen = false"
                        class="absolute top-3 right-3 z-10 p-2 bg-black/50 rounded-full text-white hover:bg-red-600 hover:text-white transition-all shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    <div class="flex transition-all duration-500 ease-in-out"
                         :style="'width:' + ((lbPhotos||[]).length * 100) + '%; transform: translateX(-' + (lbIdx * (100 / ((lbPhotos||[]).length || 1))) + '%)'">
                        <template x-for="(src, si) in (lbPhotos || [])" :key="si">
                            <div style="flex: 0 0 auto"
                                 :style="'width:' + (100 / ((lbPhotos||[]).length || 1)) + '%'">
                                <img :src="src" class="w-full max-h-[75vh] object-contain block mx-auto select-none">
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

{{-- Auto-refresh badge setiap 15 detik --}}
<script>
(function() {
    var fasId = {{ $fasilitas->id }};
    function refreshBadges() {
        fetch('/api/fasilitas/' + fasId + '/room-stock')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.stock) return;
                data.stock.forEach(function(item, idx) {
                    var el = document.getElementById('avail-badge-' + idx);
                    if (el) {
                        var txt = item.jumlah > 0 ? 'Tersedia ' + item.jumlah + ' Kamar' : 'Kamar Penuh';
                        if (el.textContent !== txt) {
                            el.textContent = txt;
                        }
                    }
                });
            })
            .catch(function(e) {});
    }
    setInterval(refreshBadges, 15000);
})();
</script>
</body>
</html>
