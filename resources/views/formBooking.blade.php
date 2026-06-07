<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <title>BOE-Space Reserve | Form Reservasi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        [x-cloak] { display: none !important; }
        .step-transition { transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); }

        /* ── Calendar Status Colors ── */
        .status-ready       { background-color: #d1fae5; color: #065f46; cursor: pointer; }
        .status-ready:hover { background-color: #a7f3d0; }
        .status-pending     { background-color: #fef9c3; color: #854d0e; cursor: not-allowed; }
        .status-booked      { background-color: #dbeafe; color: #1e40af; cursor: not-allowed; }
        .status-blocked     { background-color: #1e293b; color: #f1f5f9; cursor: not-allowed; }
        .status-maintenance { background-color: #fee2e2; color: #991b1b; cursor: not-allowed; }
        .status-past        { background-color: #f1f5f9; color: #94a3b8; cursor: not-allowed; }
        .status-closed      { background-color: #e2e8f0; color: #94a3b8; opacity: 0.5; cursor: not-allowed; }
        .status-in-range    { background-color: #bfdbfe; color: #1e40af; }
        .status-conflict    { background-color: #fca5a5; color: #7f1d1d; cursor: not-allowed; }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-4px); }
            40%, 80% { transform: translateX(4px); }
        }
        .animate-shake { animation: shake 0.4s ease-in-out; }

        .custom-select {
            appearance: none; -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 1rem center; background-size: 1.2rem; padding-right: 2.5rem;
        }

        /* ── Person Dot (guest indicators) ── */
        .room-slot { transition: all 0.3s ease; position: relative; }
        .room-slot .person-dot { width:20px;height:20px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;transition:all .3s ease; }
        .person-dot.filled { background:#2563eb;color:#fff; }
        .person-dot.empty  { background:#e2e8f0;color:#94a3b8;border:2px dashed #cbd5e1; }
        .person-dot.child  { background:#f59e0b;color:#fff; }

        @keyframes popIn { 0%{transform:scale(.7);opacity:0} 70%{transform:scale(1.05);opacity:1} 100%{transform:scale(1)} }
        .pop-in { animation: popIn .35s ease forwards; }

        .field-error { border-color:#f87171 !important; background-color:rgba(254,242,242,.4) !important; }
        .field-ok    { border-color:#34d399 !important; background-color:rgba(236,253,245,.3) !important; }

        /* ── Room Type Horizontal Card ── */
        .room-card { transition: all .3s cubic-bezier(.4,0,.2,1); }
        .room-card.selected { border-color: #2563eb; background: linear-gradient(135deg, #eff6ff, #dbeafe); box-shadow: 0 8px 24px rgba(37,99,235,.15); }
        .room-card:not(.selected):hover { border-color: #93c5fd; box-shadow: 0 4px 16px rgba(37,99,235,.08); }

        /* ── Photo Thumbnail (hover + lightbox) ── */
        .photo-lb-overlay { position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,.85); backdrop-filter:blur(6px);
            display:flex; align-items:center; justify-content:center; padding:1rem; }
        .photo-lb-track   { display:flex; transition: transform .45s cubic-bezier(.4,0,.2,1); }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-gray-100 min-h-screen font-['Poppins']">

<main class="flex flex-col items-center justify-start pt-32 pb-20 px-4"
    x-cloak
    x-data="bookingForm({
        facilities: {{ $facilities->toJson() }},
        selectedFacilityId: '{{ $selectedId ?? '' }}'
    })"
    @open-lightbox.window="__lbPhotos = $event.detail.photos; __lbIdx = $event.detail.idx; __lbOpen = true">

    <div class="w-full max-w-2xl bg-white/80 backdrop-blur-xl p-8 md:p-12 rounded-[3.5rem] shadow-2xl border border-white/60 relative overflow-hidden">

        {{-- Progress Bar (3 steps) --}}
        <div class="absolute top-0 left-0 w-full h-2 bg-gray-100">
            <div class="h-full bg-blue-600 transition-all duration-700"
                :style="'width: ' + (step === 1 ? 33 : step === 2 ? 66 : 100) + '%'"></div>
        </div>

        {{-- ══════════════════════════════════════════════
             STEP 1 — Pilih Tipe Sewa (UNCHANGED)
             ══════════════════════════════════════════════ --}}
        <div x-show="step === 1" x-transition class="step-transition">

            <div x-show="currentFacility" class="mb-8 px-6 py-4 rounded-[1.5rem] flex items-center gap-4"
                 style="background:linear-gradient(135deg,#1e3a5f,#0f172a)">
                <div class="w-12 h-12 bg-blue-500/20 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            x-show="currentFacility?.tipe==='aula'"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            x-show="currentFacility?.tipe==='asrama'"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[9px] font-black text-blue-400 uppercase tracking-[0.25em] mb-0.5">Fasilitas yang Dipilih</p>
                    <p class="text-white font-black text-base truncate" x-text="currentFacility?.nama"></p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-[10px] font-bold text-blue-300 uppercase" x-text="currentFacility?.tipe?.toUpperCase()"></span>
                        <span class="text-blue-600">·</span>
                        <span class="text-[10px] text-slate-400">Kapasitas: <span class="text-blue-300 font-bold" x-text="currentFacility?.max_dewasa || '–'"></span> orang</span>
                    </div>
                </div>
                <span class="bg-emerald-500/20 text-emerald-400 text-[9px] font-black px-3 py-1.5 rounded-full uppercase border border-emerald-500/30 flex-shrink-0">Tersedia</span>
            </div>

            <div class="text-center mb-10">
                <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] bg-blue-50 px-4 py-1.5 rounded-full border border-blue-100">Langkah 1/3</span>
                <h2 class="text-3xl font-black text-gray-900 mt-6 uppercase leading-tight">Pilih Tipe Sewa</h2>
                <p class="text-sm text-gray-400 font-medium mt-2">Tentukan durasi pemesanan Anda di BOE Malang.</p>
            </div>

            <div class="space-y-3">
                <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 ml-1 flex items-center gap-1.5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Jenis Paket Sewa <span class="text-red-500">*</span>
                </label>
                <select x-model="packageType"
                        class="custom-select w-full px-5 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl outline-none font-bold text-sm text-gray-700 transition-all duration-200 focus:border-blue-500 focus:bg-white"
                        :class="packageType ? 'border-blue-400 bg-blue-50/20' : (step1Submitted && !packageType ? 'field-error' : '')">
                    <option value="" disabled selected>-- Pilih Jenis Sewa --</option>
                    <option value="harian">Harian — Per Hari</option>
                    <option value="mingguan">Mingguan — Per Minggu (7 Hari)</option>
                    <option value="bulanan">Bulanan — Per Bulan</option>
                    <option value="tahunan">Tahunan — Per Tahun</option>
                </select>

                <div x-show="step1Submitted && !packageType" x-transition class="flex items-center gap-1.5 ml-1 mt-1">
                    <svg class="w-3 h-3 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span class="text-[10px] text-red-500 font-semibold">Jenis sewa wajib dipilih</span>
                </div>

                <template x-if="packageType">
                    <div x-transition class="p-4 bg-blue-50 border border-blue-100 rounded-2xl">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-black text-blue-700 mb-0.5" x-text="packageLabels[packageType]?.title"></p>
                                <p class="text-[11px] text-blue-600 font-medium" x-text="packageLabels[packageType]?.desc"></p>
                                <p class="text-[10px] text-blue-500 font-semibold mt-1" x-text="tarifLabel"></p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-10 flex justify-between gap-4">
                <button @click="confirmCancel()" class="flex-1 py-4 px-6 text-gray-400 hover:text-red-500 font-bold uppercase tracking-widest text-xs transition-colors">Batal</button>
                <button @click="submitStep1()"
                    class="flex-[2] py-4 px-6 font-black rounded-2xl uppercase tracking-widest text-xs shadow-lg transition-all"
                    :class="packageType ? 'bg-blue-600 text-white shadow-blue-200 hover:bg-blue-700' : 'bg-gray-100 text-gray-400'">
                    Lanjut →
                </button>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
             STEP 2 — Konfigurasi Paket + Kalender
             (Room type cards, guest config, calendar)
             ══════════════════════════════════════════════ --}}
        <div x-show="step === 2" x-transition class="step-transition">
            <div class="text-center mb-8">
                <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] bg-blue-50 px-4 py-1.5 rounded-full border border-blue-100">Langkah 2/3</span>
                <h2 class="text-3xl font-black text-gray-900 mt-6 uppercase leading-tight">Konfigurasi Paket</h2>
                <p class="text-sm text-gray-400 font-medium mt-2">
                    <span x-text="currentFacility?.nama"></span> · <span x-text="packageLabels[packageType]?.title"></span>
                </p>
            </div>

            {{-- Facility badge --}}
            <div class="mb-5 p-4 rounded-[1.5rem] flex items-center gap-4" style="background:linear-gradient(135deg,#1e293b,#0f172a)">
                <div class="w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x-show="currentFacility?.tipe==='asrama'"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x-show="currentFacility?.tipe==='aula'"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Menyewa</p>
                    <p class="text-white font-black text-sm truncate" x-text="currentFacility?.nama"></p>
                </div>
                <span class="bg-blue-500/20 text-blue-300 text-[9px] font-black px-3 py-1.5 rounded-full uppercase" x-text="currentFacility?.tipe"></span>
            </div>

            <div class="space-y-5">

                {{-- ── PILIH TIPE KAMAR — Premium Horizontal Cards (asrama only) ── --}}
                <div x-show="currentFacility?.tipe === 'asrama' && roomTypes.length > 0"
                     class="space-y-4">
                    <div class="flex items-center justify-between mb-1">
                        <h4 class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Pilih Tipe Kamar</h4>
                        <span x-show="selected_tipe_id !== null" x-transition
                            class="text-[9px] text-blue-600 font-black bg-blue-50 px-2 py-1 rounded-full border border-blue-100 cursor-pointer hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all"
                            @click="resetRoomType()">× Reset</span>
                    </div>

                    {{-- ── Premium Horizontal Room Card Loop ── --}}
                    <template x-for="(rt, idx) in roomTypes" :key="idx">
                        <div class="room-card border-2 rounded-2xl overflow-hidden cursor-pointer select-none"
                             :class="selected_tipe_id === idx ? 'selected border-blue-500' : 'border-gray-200 bg-white'"
                             @click="selectRoomType(idx)"
                             x-data="{
                                 hovered: false,
                                 get photos() {
                                     const raw = (rt.foto && rt.foto.length) ? rt.foto : [];
                                     const first = raw.find(p => p) || null;
                                     const result = [];
                                     for (let i = 0; i < 3; i++) {
                                         const src = (raw[i] && String(raw[i]).trim()) ? raw[i] : first;
                                         if (src) result.push('/storage/fasilitas/rooms/' + src);
                                     }
                                     return result;
                                 },
                                 openLightbox(e) {
                                     e.stopPropagation();
                                     const photos = this.photos.length ? this.photos : ['/storage/fasilitas/' + (rt.image || '')];
                                     $dispatch('open-lightbox', { photos: photos, idx: 0 });
                                 }
                             }"
                             @mouseenter="hovered = true"
                             @mouseleave="hovered = false">
                            <div class="flex flex-col sm:flex-row">
                                {{-- ── Left: Static photo thumbnail with hover + lightbox ── --}}
                                <div class="relative w-full sm:w-44 aspect-[4/3] flex-shrink-0 overflow-hidden bg-gray-100 cursor-pointer"
                                     @click.stop="openLightbox($event)">
                                    <template x-if="photos.length > 0">
                                        <div class="w-full h-full relative">
                                            {{-- Static first-photo thumbnail --}}
                                            <img :src="photos[0]"
                                                 class="w-full h-full object-cover transition-all duration-300"
                                                 :class="hovered ? 'blur-sm brightness-75 scale-105' : ''">
                                            {{-- Photo count badge --}}
                                            <div x-show="photos.length > 1"
                                                 class="absolute top-2 right-2 bg-black/50 text-white text-[9px] font-black px-2 py-0.5 rounded-full z-10 pointer-events-none"
                                                 x-text="photos.length + ' foto'"></div>
                                            {{-- Hover eye icon --}}
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
                                    </template>
                                    <template x-if="photos.length === 0">
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    </template>
                                    <div x-show="selected_tipe_id === idx" x-transition
                                         class="absolute top-2 left-2 z-30 bg-blue-600 text-white text-[8px] font-black px-2 py-1 rounded-lg shadow">✓ DIPILIH</div>
                                </div>{{-- /static photo --}}

                                {{-- ── Right: Info Panel ── --}}
                                <div class="flex-1 p-4 flex flex-col gap-3">
                                    {{-- Name + radio + count + blok --}}
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <template x-if="selected_tipe_id !== idx">
                                            <div class="w-4 h-4 rounded-full border-2 border-gray-300 flex-shrink-0"></div>
                                        </template>
                                        <template x-if="selected_tipe_id === idx">
                                            <div class="w-4 h-4 rounded-full border-2 border-blue-500 bg-blue-500 flex-shrink-0 flex items-center justify-center">
                                                <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                            </div>
                                        </template>
                                        <p class="font-black text-gray-900 text-sm" x-text="rt.tipe || ('Tipe ' + (idx + 1))"></p>
                                        <span x-show="rt.kode_blok" class="text-[9px] font-bold text-slate-400 uppercase bg-slate-100 px-2 py-0.5 rounded-full" x-text="'Blok ' + rt.kode_blok"></span>
                                        <span class="text-[9px] font-bold px-2 py-0.5 rounded-full ml-auto"
                                              :class="selected_tipe_id === idx ? (availabilityFetched ? (maxStock > 0 ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' : 'text-red-700 bg-red-50 border border-red-200') : 'text-emerald-700 bg-emerald-50 border border-emerald-200') : 'text-emerald-700 bg-emerald-50 border border-emerald-200'"
                                              x-text="selected_tipe_id === idx ? (availabilityFetched ? (maxStock > 0 ? 'Tersedia ' + maxStock + ' Kamar' : 'Kamar Penuh') : 'Tersedia ' + (rt.jumlah || 0) + ' Kamar') : 'Tersedia ' + (rt.jumlah || 0) + ' Kamar'"></span>
                                    </div>
                                    {{-- Keunggulan --}}
                                    <p x-show="rt.keunggulan" class="text-[11px] text-slate-500 font-medium leading-snug line-clamp-2" x-text="rt.keunggulan"></p>
                                    {{-- Pricing — refined typography --}}
                                    <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                                        <template x-if="packageType === 'harian' && rt.harga_harian > 0">
                                            <span><span class="text-sm font-black text-slate-800">Rp <span x-text="new Intl.NumberFormat('id-ID').format(rt.harga_harian)"></span></span> <span class="text-[10px] font-medium text-slate-400">/hari</span></span>
                                        </template>
                                        <template x-if="packageType === 'mingguan' && rt.harga_mingguan > 0">
                                            <span><span class="text-sm font-black text-slate-800">Rp <span x-text="new Intl.NumberFormat('id-ID').format(rt.harga_mingguan)"></span></span> <span class="text-[10px] font-medium text-slate-400">/minggu</span></span>
                                        </template>
                                        <template x-if="packageType === 'bulanan' && rt.harga_bulanan > 0">
                                            <span><span class="text-sm font-black text-slate-800">Rp <span x-text="new Intl.NumberFormat('id-ID').format(rt.harga_bulanan)"></span></span> <span class="text-[10px] font-medium text-slate-400">/bulan</span></span>
                                        </template>
                                        <template x-if="packageType === 'tahunan' && rt.harga_tahunan > 0">
                                            <span><span class="text-sm font-black text-slate-800">Rp <span x-text="new Intl.NumberFormat('id-ID').format(rt.harga_tahunan)"></span></span> <span class="text-[10px] font-medium text-slate-400">/tahun</span></span>
                                        </template>
                                        <span x-show="selectedRoomMaxDurasi > 0 && selected_tipe_id === idx"
                                            class="text-[10px] font-medium text-slate-400"
                                            x-text="'Maks: ' + selectedRoomMaxDurasi + ' ' + (packageType === 'harian' ? 'hari' : packageType === 'mingguan' ? 'minggu' : packageType === 'bulanan' ? 'bulan' : 'tahun')"></span>
                                    </div>
                                    {{-- Specs --}}
                                    <div class="grid grid-cols-3 gap-1.5">
                                        <div x-show="rt.panjang && rt.lebar" class="bg-slate-50 rounded-xl px-2 py-1.5 text-center">
                                            <p class="text-[8px] font-black text-slate-400 uppercase leading-none mb-0.5">Ukuran</p>
                                            <p class="text-[10px] font-black text-slate-700" x-text="rt.panjang + '×' + rt.lebar + ' m²'"></p>
                                        </div>
                                        <div class="bg-slate-50 rounded-xl px-2 py-1.5 text-center">
                                            <p class="text-[8px] font-black text-slate-400 uppercase leading-none mb-0.5">Kapasitas</p>
                                            <p class="text-[10px] font-black text-slate-700"
                                               x-text="(rt.max_dewasa || 1) + ' Dws' + (rt.max_anak > 0 ? '+' + rt.max_anak + 'Ank' : '')"></p>
                                        </div>
                                        <div x-show="rt.ranjang" class="bg-slate-50 rounded-xl px-2 py-1.5 text-center">
                                            <p class="text-[8px] font-black text-slate-400 uppercase leading-none mb-0.5">Kasur</p>
                                            <p class="text-[10px] font-black text-slate-700" x-text="rt.ranjang"></p>
                                        </div>
                                    </div>
                                    {{-- Fasilitas chips — unified slate theme --}}
                                    <div x-show="rt.fasilitas" class="flex flex-wrap gap-1">
                                        <template x-if="rt.fasilitas?.ac > 0"><span class="inline-flex items-center gap-1 text-[9px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full">AC × <span x-text="rt.fasilitas.ac"></span></span></template>
                                        <template x-if="rt.fasilitas?.kipas_angin > 0"><span class="inline-flex items-center gap-1 text-[9px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full">Kipas × <span x-text="rt.fasilitas.kipas_angin"></span></span></template>
                                        <template x-if="rt.fasilitas?.meja_kursi > 0"><span class="inline-flex items-center gap-1 text-[9px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full">Meja & Kursi × <span x-text="rt.fasilitas.meja_kursi"></span></span></template>
                                        <template x-if="rt.fasilitas?.lemari_locker > 0"><span class="inline-flex items-center gap-1 text-[9px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full">Lemari × <span x-text="rt.fasilitas.lemari_locker"></span></span></template>
                                        <template x-if="rt.fasilitas?.stopkontak > 0"><span class="inline-flex items-center gap-1 text-[9px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full">Stopkontak × <span x-text="rt.fasilitas.stopkontak"></span></span></template>
                                        <template x-if="rt.fasilitas?.kamar_mandi_dalam > 0"><span class="inline-flex items-center gap-1 text-[9px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full">KM Dalam × <span x-text="rt.fasilitas.kamar_mandi_dalam"></span></span></template>
                                        <template x-if="rt.fasilitas?.water_heater > 0"><span class="inline-flex items-center gap-1 text-[9px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full">Water Heater × <span x-text="rt.fasilitas.water_heater"></span></span></template>
                                        <template x-if="rt.fasilitas?.bantal_set_sprei > 0"><span class="inline-flex items-center gap-1 text-[9px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full">Bantal & Sprei × <span x-text="rt.fasilitas.bantal_set_sprei"></span></span></template>
                                        <template x-if="rt.fasilitas?.gantungan_baju > 0"><span class="inline-flex items-center gap-1 text-[9px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full">Gantungan × <span x-text="rt.fasilitas.gantungan_baju"></span></span></template>
                                        <template x-if="rt.fasilitas?.kaca_rias > 0"><span class="inline-flex items-center gap-1 text-[9px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full">Kaca Rias × <span x-text="rt.fasilitas.kaca_rias"></span></span></template>
                                    </div>
                                    {{-- Room counter (when selected) --}}
                                    <div x-show="selected_tipe_id === idx" x-transition
                                         class="flex items-center justify-between pt-2 border-t border-blue-100 mt-auto">
                                        <template x-if="availabilityFetched && maxStock === 0">
                                            <div class="w-full flex items-center justify-between">
                                                <span class="text-[10px] font-black text-red-600">Kamar Penuh</span>
                                                <button @click.stop="alert('Maaf, semua kamar pada tipe ini sudah penuh untuk tanggal yang Anda pilih.')"
                                                    class="text-[9px] font-black px-3 py-1.5 bg-gray-400 text-white rounded-xl cursor-not-allowed opacity-80"
                                                    :disabled="true">Tidak Tersedia</button>
                                            </div>
                                        </template>
                                        <template x-if="!(availabilityFetched && maxStock === 0)">
                                            <div class="w-full flex items-center justify-between">
                                                <p class="text-[10px] font-bold text-blue-600">Jumlah kamar:</p>
                                                <div class="flex items-center gap-2 bg-white border-2 border-blue-200 rounded-2xl px-2 py-1 shadow-sm">
                                                    <button @click.stop="decRooms()"
                                                        class="w-8 h-8 flex items-center justify-center rounded-xl text-blue-600 hover:bg-blue-100 font-black text-lg transition-all"
                                                        :class="rooms <= 1 ? 'opacity-40 cursor-not-allowed' : ''">−</button>
                                                    <span class="w-6 text-center font-black text-blue-700 text-sm" x-text="rooms"></span>
                                                    <button @click.stop="incRooms()"
                                                        class="w-8 h-8 flex items-center justify-center rounded-xl font-black text-lg transition-all"
                                                        :class="rooms >= maxRoomsFromFacility ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'text-blue-600 hover:bg-blue-100'">+</button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>{{-- /info panel --}}
                            </div>{{-- /flex row --}}
                        </div>
                    </template>

                    <p x-show="step2Errors.roomType" x-transition class="text-[10px] text-red-500 font-semibold flex items-center gap-1">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        Pilih tipe kamar terlebih dahulu
                    </p>
                </div>

                {{-- ── DURASI ── --}}
                <div class="p-5 bg-gray-50 rounded-3xl border-2 transition-all"
                     :class="step2Errors.duration ? 'border-red-300 bg-red-50/30' : 'border-gray-100'">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-black text-gray-800 uppercase tracking-tighter text-sm">Durasi Sewa</h4>
                            <p class="text-[10px] font-bold uppercase tracking-widest mt-0.5"
                               :class="step2Errors.duration ? 'text-red-400' : 'text-gray-400'"
                               x-text="packageType==='harian'?'Satuan: Hari':packageType==='mingguan'?'Satuan: Minggu':packageType==='bulanan'?'Satuan: Bulan':'Satuan: Tahun'"></p>
                            <p x-show="selectedRoomMaxDurasi > 0" class="text-[9px] text-amber-600 font-bold mt-0.5"
                               x-text="'Maks: ' + selectedRoomMaxDurasi + ' (dari DB)'"></p>
                        </div>
                        <div class="flex items-center gap-4">
                            <button @click="decDuration()"
                                class="w-11 h-11 bg-white shadow-sm rounded-2xl flex items-center justify-center font-black text-xl text-blue-600 hover:bg-blue-600 hover:text-white transition-all"
                                :class="duration<=1?'opacity-40 cursor-not-allowed':''">−</button>
                            <span class="text-2xl font-black w-8 text-center"
                                  :class="step2Errors.duration?'text-red-600':'text-gray-800'"
                                  x-text="duration"></span>
                            <button @click="incDuration()"
                                class="w-11 h-11 bg-white shadow-sm rounded-2xl flex items-center justify-center font-black text-xl text-blue-600 hover:bg-blue-600 hover:text-white transition-all">+</button>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-200">
                        <p class="text-[10px] text-gray-400 font-semibold">
                            Total: <span class="text-gray-700 font-black" x-text="totalDays + ' hari'"></span>
                        </p>
                    </div>
                    <p x-show="step2Errors.duration" x-transition class="text-[10px] text-red-500 font-semibold mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        Durasi minimal 1
                    </p>
                </div>

                {{-- ── JUMLAH KAMAR legacy counter (shown when NO paket_harian room types) — asrama only ── --}}
                <div x-show="currentFacility?.tipe === 'asrama' && roomTypes.length === 0"
                     class="p-5 rounded-3xl border-2 transition-all"
                     :class="step2Errors.rooms ? 'border-red-300 bg-red-50/30' : 'border-blue-200 bg-blue-50'">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <h4 class="font-black text-blue-800 uppercase tracking-tighter text-sm">Jumlah Kamar</h4>
                            <p class="text-[10px] font-bold text-blue-600">1 Kamar = Maks <strong>2 Dewasa</strong> + Maks <strong>2 Anak</strong></p>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <button @click="decRooms()"
                                class="w-11 h-11 bg-white shadow-sm rounded-2xl flex items-center justify-center font-black text-xl text-blue-600 hover:bg-red-500 hover:text-white transition-all"
                                :class="rooms<=1?'opacity-40 cursor-not-allowed':''">−</button>
                            <span class="text-2xl font-black text-blue-700 w-8 text-center" x-text="rooms"></span>
                            <button @click="incRooms()"
                                class="w-11 h-11 text-white shadow-sm rounded-2xl flex items-center justify-center font-black text-xl transition-all"
                                :class="rooms >= maxRoomsFromFacility ? 'bg-gray-300 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'">+</button>
                        </div>
                    </div>
                    <p x-show="step2Errors.rooms" x-transition class="text-[10px] text-red-500 font-semibold mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span x-text="step2Errors.roomsMsg"></span>
                    </p>
                </div>

                {{-- ── ROOM SLOT VISUAL (guest indicators) — asrama only ── --}}
                <div x-show="currentFacility?.tipe === 'asrama'"
                     x-transition class="p-5 rounded-3xl border-2 border-blue-100 bg-blue-50/30">
                    <div class="grid gap-3 mb-3" :style="'grid-template-columns: repeat(' + Math.min(rooms,3) + ', 1fr)'">
                        <template x-for="r in rooms" :key="r">
                            <div class="room-slot bg-white border-2 border-blue-200 rounded-2xl p-3 pop-in">
                                <p class="text-[9px] font-black text-blue-500 uppercase tracking-wider mb-2" x-text="'KAMAR ' + r + (availableRooms && availableRooms[r-1] ? ' (' + availableRooms[r-1] + ')' : '')"></p>
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex items-center gap-1.5">
                                        <template x-for="slot in [1,2]" :key="'a'+slot">
                                            <div class="person-dot" :class="occupantSlot(r, slot) === 'adult' ? 'filled' : 'empty'">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            </div>
                                        </template>
                                        <span class="text-[8px] text-blue-400 font-bold">Dewasa</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <template x-for="slot in [3,4]" :key="'c'+slot">
                                            <div class="person-dot" :class="occupantSlot(r, slot) === 'child' ? 'child' : 'empty'">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            </div>
                                        </template>
                                        <span class="text-[8px] text-amber-400 font-bold">Anak</span>
                                    </div>
                                    <p class="text-[8px] text-slate-400 font-semibold mt-1" x-text="roomLabel(r)"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1"><div class="w-3 h-3 rounded-full bg-blue-600"></div><span class="text-[9px] text-blue-600 font-bold">Dewasa</span></div>
                        <div class="flex items-center gap-1"><div class="w-3 h-3 rounded-full bg-amber-400"></div><span class="text-[9px] text-amber-600 font-bold">Anak</span></div>
                        <div class="flex items-center gap-1"><div class="w-3 h-3 rounded-full bg-slate-200 border-2 border-dashed border-slate-300"></div><span class="text-[9px] text-slate-400 font-bold">Kosong</span></div>
                    </div>
                </div>

                {{-- ── DEWASA ── --}}
                <div class="p-5 bg-gray-50 rounded-3xl border-2 transition-all"
                     :class="step2Errors.adults ? 'border-red-300 bg-red-50/30' : 'border-gray-100'">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-black text-gray-800 uppercase tracking-tighter text-sm"
                                x-text="currentFacility?.tipe==='aula' ? 'Total Peserta / Kapasitas' : 'Jumlah Tamu Dewasa'"></h4>
                            <p class="text-[10px] font-bold mt-0.5"
                               :class="step2Errors.adults ? 'text-red-400' : 'text-gray-400'">
                                <span x-show="currentFacility?.tipe==='asrama'"
                                      x-text="'Maks dewasa: ' + maxAdultsAllowed + ' (dari ' + (rooms*2) + ' slot kamar)'"></span>
                                <span x-show="currentFacility?.tipe==='aula'"
                                      x-text="'Maks. ' + (currentFacility?.max_dewasa||'–') + ' orang'"></span>
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <button @click="decAdults()"
                                class="w-11 h-11 bg-white shadow-sm rounded-2xl flex items-center justify-center font-black text-xl text-blue-600 hover:bg-blue-600 hover:text-white transition-all"
                                :class="adults<=1?'opacity-40 cursor-not-allowed':''">−</button>
                            <span class="text-2xl font-black w-8 text-center"
                                  :class="step2Errors.adults?'text-red-600':'text-gray-800'"
                                  x-text="adults"></span>
                            <button @click="incAdults()"
                                class="w-11 h-11 rounded-2xl flex items-center justify-center font-black text-xl transition-all"
                                :class="adults >= maxAdultsAllowed
                                    ? 'bg-amber-100 text-amber-500 hover:bg-amber-200'
                                    : 'bg-white shadow-sm text-blue-600 hover:bg-blue-600 hover:text-white'">
                                <span x-show="adults < maxAdultsAllowed">+</span>
                                <svg x-show="adults >= maxAdultsAllowed" class="w-5 h-5 text-amber-500"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div x-show="showRoomHint && currentFacility?.tipe==='asrama'" x-transition
                         class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-2xl flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-[11px] text-blue-700 font-semibold">Slot penuh — tambah kamar untuk menambah tamu dewasa.</p>
                    </div>
                    <div x-show="showRoomHint && currentFacility?.tipe==='aula'" x-transition
                         class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-2xl flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-[11px] text-amber-700 font-semibold">Kapasitas aula penuh. Maks. <span x-text="currentFacility?.max_dewasa"></span> peserta.</p>
                    </div>
                    <p x-show="step2Errors.adults" x-transition class="text-[10px] text-red-500 font-semibold mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span x-text="step2Errors.adultsMsg"></span>
                    </p>
                </div>

                {{-- ── ANAK (asrama only) ── --}}
                <div x-show="currentFacility?.tipe === 'asrama'"
                     class="p-5 bg-gray-50 rounded-3xl border-2 transition-all"
                     :class="step2Errors.childAges ? 'border-red-300 bg-red-50/30' : 'border-gray-100'">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-black text-gray-800 uppercase tracking-tighter text-sm">Jumlah Anak</h4>
                            <p class="text-[10px] font-bold mt-0.5"
                               :class="step2Errors.childAges ? 'text-red-400' : 'text-gray-400'"
                               x-text="'Maks anak: ' + maxChildrenAllowed + ' (slot anak per kamar)'"></p>
                        </div>
                        <div class="flex items-center gap-4">
                            <button @click="decChildren()"
                                class="w-11 h-11 bg-white shadow-sm rounded-2xl flex items-center justify-center font-black text-xl text-blue-600 hover:bg-blue-600 hover:text-white transition-all"
                                :class="children<=0?'opacity-40 cursor-not-allowed':''">−</button>
                            <span class="text-2xl font-black text-gray-800 w-8 text-center" x-text="children"></span>
                            <button @click="incChildren()"
                                class="w-11 h-11 rounded-2xl flex items-center justify-center font-black text-xl transition-all"
                                :class="children >= maxChildrenAllowed
                                    ? 'bg-amber-100 text-amber-500 hover:bg-amber-200'
                                    : 'bg-white shadow-sm text-blue-600 hover:bg-blue-600 hover:text-white'">
                                <span x-show="children < maxChildrenAllowed">+</span>
                                <svg x-show="children >= maxChildrenAllowed" class="w-5 h-5 text-amber-500"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div x-show="showChildHint" x-transition class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-2xl flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-[11px] text-amber-700 font-semibold">Slot penuh — tambah kamar untuk menambah anak.</p>
                    </div>
                </div>

                {{-- ── INPUT UMUR ANAK ── --}}
                <div x-show="currentFacility?.tipe==='asrama' && children > 0" x-transition
                    class="p-5 bg-blue-50/30 rounded-3xl border border-blue-100">
                    <h4 class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1">Umur Anak (Tahun)</h4>
                    <p class="text-[10px] text-blue-400 font-medium mb-4">
                        Anak &lt; 12 tahun = <span class="font-black text-emerald-600">Gratis</span> ·
                        Anak ≥ 12 tahun = <span class="font-black text-amber-600">Tarif Dewasa</span>
                    </p>
                    <div class="grid grid-cols-3 gap-3">
                        <template x-for="(age, idx) in childAges" :key="idx">
                            <div class="text-center">
                                <label class="text-[9px] text-blue-500 font-bold uppercase block mb-1">Anak <span x-text="idx+1"></span></label>
                                <input type="number" x-model.number="childAges[idx]" min="0" max="17" placeholder="0"
                                    class="w-full p-2.5 bg-white border-2 rounded-xl text-center font-bold text-sm outline-none focus:border-blue-400 transition-all"
                                    :class="step2Submitted && (childAges[idx] === '' || childAges[idx] === null || childAges[idx] === undefined)
                                        ? 'field-error'
                                        : (childAges[idx] !== '' && childAges[idx] !== null && childAges[idx] !== undefined)
                                            ? (parseInt(childAges[idx]) >= 12 ? 'border-amber-400 bg-amber-50/30' : 'border-emerald-400 bg-emerald-50/30')
                                            : 'border-gray-200'">
                                <div x-show="childAges[idx] !== '' && childAges[idx] !== null && childAges[idx] !== undefined" x-transition class="mt-1.5 text-center">
                                    <template x-if="parseInt(childAges[idx]) >= 12">
                                        <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 text-[8px] font-black px-2 py-0.5 rounded-full border border-amber-200">Tarif Dewasa</span>
                                    </template>
                                    <template x-if="childAges[idx] !== '' && childAges[idx] !== null && childAges[idx] !== undefined && parseInt(childAges[idx]) < 12">
                                        <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 text-[8px] font-black px-2 py-0.5 rounded-full border border-emerald-200">Gratis</span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                    <p x-show="step2Errors.childAges" x-transition class="text-[10px] text-red-500 font-semibold mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        Mohon isi umur semua anak
                    </p>
                </div>

                {{-- ── RINGKASAN MINI ── --}}
                <div class="mt-2 p-4 bg-slate-900 rounded-2xl">
                    <div class="grid gap-3 text-center"
                        :style="currentFacility?.tipe==='asrama' ? 'grid-template-columns: repeat(3, 1fr)' : 'grid-template-columns: repeat(2, 1fr)'">
                        <div>
                            <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Durasi</p>
                            <p class="text-white font-black text-sm"
                               x-text="duration + ' ' + (packageType==='harian'?'Hari':packageType==='mingguan'?'Minggu':packageType==='bulanan'?'Bln':'Thn')"></p>
                        </div>
                        <div x-show="currentFacility?.tipe==='asrama'">
                            <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Kamar</p>
                            <p class="font-black text-sm"
                               :class="((adults + billableChildren) > rooms*2 || freeChildren > rooms*2) ? 'text-amber-400' : 'text-white'"
                               x-text="rooms + ' Kamar'"></p>
                        </div>
                        <div>
                            <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Tamu Berbayar</p>
                            <p class="font-black text-sm text-white" x-text="totalBillableGuests + ' Orang'"></p>
                        </div>
                    </div>
                </div>

            </div>{{-- /space-y-5 --}}

            {{-- ══════════════════════════════════════════════
                 CALENDAR — merged at bottom of Form 2
                 Exact same UI from schedule_booking / Step 3
                 ══════════════════════════════════════════════ --}}
            <div class="mt-8">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-1 h-6 bg-blue-600 rounded-full"></div>
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-tight">Pilih Tanggal Check-In</h3>
                </div>
                <p class="text-[11px] text-gray-400 font-medium mb-4">Semua hari dalam durasi sewa harus tersedia (hijau)</p>

                <div x-show="step2Submitted && !selectedDate" x-transition
                     class="mb-4 p-3 bg-red-50 border border-red-200 rounded-2xl flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="text-[11px] text-red-600 font-bold">Pilih tanggal check-in terlebih dahulu</p>
                </div>

                <div class="bg-white rounded-[2.5rem] overflow-hidden border-2 border-black/10 shadow-xl relative">
                    {{-- Loading overlay --}}
                    <div x-show="isLoadingCalendar" class="absolute inset-0 z-50 bg-white/60 backdrop-blur-sm flex flex-col items-center justify-center gap-4">
                        <div class="w-10 h-10 border-4 border-slate-100 border-t-blue-600 rounded-full animate-spin"></div>
                        <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Sinkronisasi Jadwal...</p>
                    </div>

                    {{-- Calendar header --}}
                    <div class="p-6 md:p-8 flex items-center justify-between bg-white border-b border-gray-100">
                        <div>
                            <h3 class="text-xl md:text-2xl font-black uppercase tracking-tighter text-gray-900" x-text="monthName"></h3>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em]" x-text="currentYear"></p>
                        </div>
                        <div class="flex gap-2">
                            <button @click="prevMonth()" class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-100 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button @click="nextMonth()" class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-100 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Legend --}}
                    <div class="px-6 md:px-8 py-3 flex flex-wrap gap-x-4 gap-y-2 bg-gray-50/50 border-b border-gray-100">
                        <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-emerald-300"></div><span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Ready</span></div>
                        <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-yellow-300"></div><span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Pending</span></div>
                        <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-blue-300"></div><span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Booked</span></div>
                        <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-red-300"></div><span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Tidak Tersedia</span></div>
                        <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-blue-400"></div><span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Rentang Dipilih</span></div>
                    </div>

                    {{-- Day labels --}}
                    <div class="grid grid-cols-7 gap-px bg-gray-100">
                        <template x-for="d in ['MIN','SEN','SEL','RAB','KAM','JUM','SAB']">
                            <div class="bg-gray-50 py-3 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest" x-text="d"></div>
                        </template>

                        {{-- Day cells --}}
                        <template x-for="(item, idx) in daysInMonth" :key="idx">
                            <div class="h-16 sm:h-20 md:h-24 relative group flex flex-col items-center justify-center transition-all"
                                 :class="getDayCellClass(item)"
                                 @click="selectDate(item.date)">
                                <div x-show="item.day"
                                     class="relative z-10 text-sm md:text-base font-black transition-all duration-300"
                                     :class="{ 'ring-4 ring-black/20 rounded-full w-8 h-8 md:w-10 md:h-10 flex items-center justify-center bg-gray-900 text-white shadow-lg scale-110': selectedDate && item.date && item.date.getTime()===selectedDate.getTime() }"
                                     x-text="item.day"></div>
                                <div x-show="item.day && !['ready','closed','past','in-range'].includes(getDateStatus(item.date))"
                                     class="absolute bottom-1.5 w-1.5 h-1.5 rounded-full"
                                     :class="{ 'bg-yellow-500': getDateStatus(item.date)==='pending', 'bg-blue-600': getDateStatus(item.date)==='booked', 'bg-red-600': getDateStatus(item.date)==='maintenance'||getDateStatus(item.date)==='blocked' }">
                                </div>
                                <template x-if="item.day && !['ready','closed','past'].includes(getDateStatus(item.date))">
                                    <div class="absolute bottom-1 left-0 right-0 text-center opacity-0 group-hover:opacity-100 transition-opacity z-20">
                                        <span class="bg-black/80 text-white text-[7px] font-black uppercase px-2 py-0.5 rounded shadow-lg whitespace-nowrap" x-text="getDayInfo(item.date)"></span>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Date selection feedback --}}
                <div x-show="selectedDate" x-transition class="mt-4 p-4 rounded-2xl"
                     :class="hasConflictInRange ? 'bg-red-50 border border-red-200' : 'bg-emerald-50 border border-emerald-200'">
                    <div class="flex items-start gap-3">
                        <svg x-show="!hasConflictInRange" class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <svg x-show="hasConflictInRange" class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <p class="text-xs font-black" :class="hasConflictInRange ? 'text-red-700' : 'text-emerald-700'"
                               x-text="hasConflictInRange ? 'Ada hari dalam range yang tidak tersedia!' : 'Semua hari tersedia ✓'"></p>
                            <p class="text-[11px] font-semibold mt-0.5" :class="hasConflictInRange ? 'text-red-600' : 'text-emerald-600'"
                               x-text="'Check-in: ' + formatDisplay(selectedDate) + '  →  Check-out: ' + formatDisplay(endDate)"></p>
                        </div>
                    </div>
                </div>
            </div>{{-- /calendar block --}}

            <div class="mt-8 flex justify-between gap-4">
                <button @click="prevStep()" class="flex-1 py-4 px-6 bg-slate-100 text-slate-400 font-bold rounded-2xl uppercase tracking-widest text-xs">Kembali</button>
                <button @click="availabilityFetched && maxStock === 0 && selected_tipe_id !== null
                        ? alert('Maaf, semua kamar pada tipe ini sudah penuh untuk tanggal yang Anda pilih.')
                        : submitStep2()"
                    class="flex-[2] py-4 px-6 font-black rounded-2xl uppercase tracking-widest text-xs shadow-lg transition-all"
                    :class="availabilityFetched && maxStock === 0 && selected_tipe_id !== null
                        ? 'bg-gray-400 text-white cursor-not-allowed opacity-80'
                        : 'bg-blue-600 text-white shadow-blue-200 hover:bg-blue-700'">
                    Konfirmasi Data Diri →
                </button>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
             STEP 3 — Data Diri (was Step 4, UNCHANGED)
             ══════════════════════════════════════════════ --}}
        <div x-show="step === 3" x-transition class="step-transition">

            <div class="text-center mb-8">
                <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] bg-blue-50 px-4 py-1.5 rounded-full border border-blue-100">Langkah Akhir</span>
                <h2 class="text-3xl font-black text-gray-900 mt-6 uppercase leading-tight">Konfirmasi Data</h2>
                <p class="text-sm text-gray-400 font-medium mt-2">Detail Pemohon</p>
            </div>

            <div class="space-y-5">
                <div :class="{'animate-shake': step4Shake.name}">
                    <label class="text-[9px] font-black uppercase tracking-widest ml-1 mb-2 flex items-center gap-1 transition-colors"
                        :class="step4Errors.name?'text-red-500':step4Success.name?'text-emerald-500':'text-gray-400'">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" x-model="step4Name" @input="s4ValidateField('name')" placeholder="Masukkan nama lengkap Anda"
                            class="w-full px-5 py-3.5 bg-gray-50 border-2 rounded-2xl outline-none font-medium text-sm transition-all pr-10"
                            :class="step4Errors.name?'field-error':step4Success.name?'field-ok':'border-gray-200 focus:border-blue-500 focus:bg-white'">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2" x-show="step4Errors.name">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <span class="absolute right-4 top-1/2 -translate-y-1/2" x-show="step4Success.name" x-transition>
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                    </div>
                    <p x-show="step4Errors.name" x-transition class="text-[10px] text-red-500 font-semibold mt-1.5 ml-1">Nama minimal 3 karakter, hanya huruf dan spasi</p>
                    <p x-show="step4Success.name" x-transition class="text-[10px] text-emerald-600 font-semibold mt-1.5 ml-1">Nama valid ✓</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 relative" style="z-index:50;">
                    <div :class="{'animate-shake': step4Shake.provinsi}" style="z-index:40; position:relative;">
                        <label class="text-[9px] font-black uppercase tracking-widest ml-1 mb-2 flex items-center gap-1 transition-colors"
                            :class="step4Errors.provinsi?'text-red-500':step4Success.provinsi?'text-emerald-500':'text-gray-400'">
                            Provinsi Asal <span class="text-red-500">*</span>
                        </label>
                        <div @click.stop="step4OpenProvinsi=!step4OpenProvinsi;step4OpenKabupaten=false" @click.away="step4OpenProvinsi=false"
                            class="w-full px-5 py-3.5 bg-gray-50 border-2 rounded-2xl flex justify-between items-center cursor-pointer font-medium text-sm transition-all"
                            :class="step4Errors.provinsi?'field-error':step4Success.provinsi?'field-ok':'border-gray-200 hover:border-gray-300'">
                            <span :class="step4ProvinsiName?'text-gray-800':'text-gray-400'" x-text="step4ProvinsiName||'Pilih Provinsi...'"></span>
                            <svg class="w-4 h-4 transition-transform" :class="step4OpenProvinsi?'text-blue-500 rotate-180':'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                        <div x-show="step4OpenProvinsi" x-transition @click.stop
                            class="absolute left-0 w-full mt-2 bg-white border border-gray-100 shadow-xl rounded-2xl overflow-hidden" style="z-index:100;">
                            <div class="p-3 border-b border-gray-50">
                                <input x-model="step4SearchProvinsi" type="text" placeholder="Cari Provinsi..."
                                    class="w-full bg-gray-50 text-xs px-4 py-2.5 rounded-xl outline-none border border-gray-200 focus:border-blue-400">
                            </div>
                            <div class="max-h-48 overflow-y-auto">
                                <div x-show="step4LoadingProvinsi" class="px-5 py-4 text-xs text-gray-400 text-center">Memuat provinsi...</div>
                                <template x-for="p in step4FilteredProvinces" :key="p.id">
                                    <div @click="s4SelectProvinsi(p)" class="px-5 py-2.5 cursor-pointer text-xs font-semibold text-gray-700 transition-colors"
                                        :class="step4Provinsi===p.id?'bg-blue-50 text-blue-600':'hover:bg-gray-50'" x-text="p.name"></div>
                                </template>
                                <div x-show="!step4LoadingProvinsi && step4FilteredProvinces.length===0" class="px-5 py-4 text-xs text-gray-400 text-center">Tidak ditemukan</div>
                            </div>
                        </div>
                        <p x-show="step4Errors.provinsi" x-transition class="text-[10px] text-red-500 font-semibold mt-1.5 ml-1">Pilih provinsi terlebih dahulu</p>
                    </div>

                    <div :class="{'animate-shake': step4Shake.kabupaten}" style="z-index:30; position:relative;">
                        <label class="text-[9px] font-black uppercase tracking-widest ml-1 mb-2 flex items-center gap-1 transition-colors"
                            :class="step4Errors.kabupaten?'text-red-500':step4Success.kabupaten?'text-emerald-500':'text-gray-400'">
                            Kabupaten / Kota <span class="text-red-500">*</span>
                        </label>
                        <div @click.stop="if(!step4Provinsi){s4TriggerError('provinsi')}else{step4OpenKabupaten=!step4OpenKabupaten;step4OpenProvinsi=false}" @click.away="step4OpenKabupaten=false"
                            class="w-full px-5 py-3.5 bg-gray-50 border-2 rounded-2xl flex justify-between items-center font-medium text-sm transition-all"
                            :class="!step4Provinsi?'opacity-50 cursor-not-allowed border-gray-200':step4Errors.kabupaten?'field-error cursor-pointer':step4Success.kabupaten?'field-ok cursor-pointer':'border-gray-200 hover:border-gray-300 cursor-pointer'">
                            <span :class="step4KabupatenName?'text-gray-800':'text-gray-400'" x-text="step4LoadingKabupaten?'Memuat data kota...':step4KabupatenName||'Pilih Kota/Kabupaten...'"></span>
                            <svg class="w-4 h-4 transition-transform" :class="step4OpenKabupaten?'text-blue-500 rotate-180':'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                        <div x-show="step4OpenKabupaten && step4Provinsi" x-transition @click.stop
                            class="absolute left-0 w-full mt-2 bg-white border border-gray-100 shadow-xl rounded-2xl overflow-hidden" style="z-index:100;">
                            <div class="p-3 border-b border-gray-50">
                                <input x-model="step4SearchKabupaten" type="text" placeholder="Cari Kota..."
                                    class="w-full bg-gray-50 text-xs px-4 py-2.5 rounded-xl outline-none border border-gray-200 focus:border-blue-400">
                            </div>
                            <div class="max-h-48 overflow-y-auto">
                                <div x-show="step4LoadingKabupaten" class="px-5 py-4 text-xs text-gray-400 text-center">Memuat data kabupaten...</div>
                                <template x-for="k in step4FilteredRegencies" :key="k.id">
                                    <div @click="s4SelectKabupaten(k)" class="px-5 py-2.5 cursor-pointer text-xs font-semibold text-gray-700 transition-colors"
                                        :class="step4Kabupaten===k.id?'bg-blue-50 text-blue-600':'hover:bg-gray-50'" x-text="k.name"></div>
                                </template>
                                <div x-show="!step4LoadingKabupaten && step4FilteredRegencies.length===0" class="px-5 py-4 text-xs text-gray-400 text-center">Tidak ditemukan</div>
                            </div>
                        </div>
                        <p x-show="step4Errors.kabupaten" x-transition class="text-[10px] text-red-500 font-semibold mt-1.5 ml-1">Pilih kota/kabupaten terlebih dahulu</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 relative z-0">
                    <div :class="{'animate-shake': step4Shake.whatsapp}">
                        <label class="text-[9px] font-black uppercase tracking-widest ml-1 mb-2 flex items-center gap-1 transition-colors"
                            :class="step4Errors.whatsapp?'text-red-500':step4Success.whatsapp?'text-emerald-500':'text-gray-400'">
                            Nomor WhatsApp <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" x-model="step4Whatsapp" @input="s4ValidateField('whatsapp')" placeholder="08xxxxxxxxx" maxlength="14"
                                class="w-full px-5 py-3.5 bg-gray-50 border-2 rounded-2xl outline-none font-medium text-sm transition-all pr-10"
                                :class="step4Errors.whatsapp?'field-error':step4Success.whatsapp?'field-ok':'border-gray-200 focus:border-blue-500 focus:bg-white'">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2" x-show="step4Errors.whatsapp">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <span class="absolute right-4 top-1/2 -translate-y-1/2" x-show="step4Success.whatsapp" x-transition>
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                        </div>
                        <p x-show="step4Errors.whatsapp" x-transition class="text-[10px] text-red-500 font-semibold mt-1.5 ml-1">Nomor valid 9–14 digit angka</p>
                        <p x-show="step4Success.whatsapp" x-transition class="text-[10px] text-emerald-600 font-semibold mt-1.5 ml-1">Nomor valid ✓</p>
                    </div>

                    <div :class="{'animate-shake': step4Shake.email}">
                        <label class="text-[9px] font-black uppercase tracking-widest ml-1 mb-2 flex items-center gap-1 transition-colors"
                            :class="step4Errors.email?'text-red-500':step4Success.email?'text-emerald-500':'text-gray-400'">
                            Email Aktif <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="email" x-model="step4Email" @input="s4ValidateField('email')" placeholder="nama@email.com"
                                class="w-full px-5 py-3.5 bg-gray-50 border-2 rounded-2xl outline-none font-medium text-sm transition-all pr-10"
                                :class="step4Errors.email?'field-error':step4Success.email?'field-ok':'border-gray-200 focus:border-blue-500 focus:bg-white'">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2" x-show="step4Errors.email">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <span class="absolute right-4 top-1/2 -translate-y-1/2" x-show="step4Success.email" x-transition>
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                        </div>
                        <p x-show="step4Errors.email" x-transition class="text-[10px] text-red-500 font-semibold mt-1.5 ml-1">Format email tidak sesuai</p>
                        <p x-show="step4Success.email" x-transition class="text-[10px] text-emerald-600 font-semibold mt-1.5 ml-1">Email valid ✓</p>
                    </div>
                </div>

                {{-- Foto Identitas --}}
                <div :class="{'animate-shake': step4Shake.foto}"
                    class="p-5 bg-white border-2 rounded-3xl transition-all"
                    :style="step4Errors.foto?'border-color:#f87171':step4Success.foto?'border-color:#34d399':'border-color:#e5e7eb'">
                    <label class="text-[9px] font-black uppercase tracking-widest flex items-center gap-1 mb-4 transition-colors"
                        :class="step4Errors.foto?'text-red-500':step4Success.foto?'text-emerald-500':'text-gray-400'">
                        Upload Foto Identitas (KTP/SIM) <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1 space-y-3">
                            <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed rounded-2xl cursor-pointer transition-all"
                                :class="step4Errors.foto?'border-red-400 bg-red-50/30':step4Success.foto?'border-emerald-400 bg-emerald-50/30':'border-gray-300 bg-gray-50 hover:bg-gray-100 hover:border-blue-400'">
                                <div class="flex flex-col items-center text-center px-4">
                                    <svg x-show="!step4Success.foto&&!step4Errors.foto" class="w-7 h-7 mb-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    <svg x-show="step4Success.foto" class="w-7 h-7 mb-1.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <svg x-show="step4Errors.foto" class="w-7 h-7 mb-1.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <p class="text-xs font-semibold" :class="step4Errors.foto?'text-red-500':step4Success.foto?'text-emerald-600':'text-gray-500'"
                                       x-text="step4Success.foto ? step4FotoFileName : 'Klik untuk unggah file'"></p>
                                    <p class="text-[10px] mt-0.5" :class="step4Errors.foto?'text-red-400':step4Success.foto?'text-emerald-500':'text-gray-400'"
                                       x-text="step4Success.foto ? 'Foto siap digunakan ✓' : 'JPG, JPEG, PNG (Maks. 2MB)'"></p>
                                </div>
                                <input type="file" class="hidden" accept="image/jpeg,image/png,image/jpg" @change="s4HandleFileChange($event)" />
                            </label>
                            <p x-show="step4Errors.foto" x-transition class="text-[10px] text-red-500 font-semibold ml-1" x-text="step4FotoErrorMsg"></p>
                            <div class="p-3 bg-blue-50 rounded-xl flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <p class="text-[10px] text-blue-700 font-medium leading-relaxed">Dokumen hanya digunakan untuk validasi reservasi dan dihapus otomatis setelah masa sewa berakhir.</p>
                            </div>
                        </div>
                        <div class="w-full md:w-44 flex flex-col items-center justify-center border-2 rounded-2xl overflow-hidden relative min-h-[7rem] transition-all"
                            :class="step4Errors.foto?'border-red-200 bg-red-50/20':step4Success.foto?'border-emerald-300 bg-emerald-50/20':'border-gray-200 bg-gray-50'">
                            <template x-if="step4FotoPreview">
                                <div class="w-full h-full absolute inset-0">
                                    <img :src="step4FotoPreview" class="object-cover w-full h-full" alt="Preview Identitas">
                                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-2">
                                        <p class="text-[9px] text-white font-black text-center tracking-widest">PREVIEW</p>
                                    </div>
                                </div>
                            </template>
                            <template x-if="!step4FotoPreview">
                                <div class="text-center p-4">
                                    <svg class="w-7 h-7 mx-auto mb-1.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="text-[10px] font-bold text-gray-400">Belum ada foto</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ringkasan Reservasi --}}
            <div class="bg-[#0f172a] rounded-[1.75rem] overflow-hidden mt-6">
                <div class="px-6 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-bold tracking-[.12em] uppercase text-blue-400">Ringkasan Reservasi</span>
                        <span class="text-[10px] font-semibold bg-[#1e3a5f] text-blue-300 px-3 py-1 rounded-full">Draft</span>
                    </div>
                    <div class="mb-4 p-3 bg-[#1e293b] rounded-2xl">
                        <p class="text-[9px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Fasilitas</p>
                        <p class="text-[16px] font-bold text-slate-100 truncate" x-text="currentFacility?.nama"></p>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="bg-blue-500/20 text-blue-400 text-[9px] font-black px-2 py-1 rounded-lg uppercase" x-text="currentFacility?.tipe"></span>
                            <span x-show="currentFacility?.tipe==='asrama' && selected_tipe_id !== null"
                                class="bg-purple-500/20 text-purple-400 text-[9px] font-black px-2 py-1 rounded-lg uppercase"
                                x-text="(roomTypes[selected_tipe_id]?.tipe || 'Tipe ' + (selected_tipe_id+1)) + ' · ' + rooms + ' kamar'"></span>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-2.5 mb-4">
                        <div class="bg-[#1e293b] rounded-xl p-3">
                            <p class="text-[9px] text-slate-500 uppercase tracking-wider mb-1">Paket</p>
                            <p class="text-[13px] font-bold text-slate-100 capitalize" x-text="packageType"></p>
                        </div>
                        <div class="bg-[#1e293b] rounded-xl p-3">
                            <p class="text-[9px] text-slate-500 uppercase tracking-wider mb-1">Check-In</p>
                            <p class="text-[13px] font-bold text-slate-100"
                               x-text="selectedDate ? new Intl.DateTimeFormat('id-ID',{day:'numeric',month:'short'}).format(selectedDate) : '-'"></p>
                        </div>
                        <div class="bg-[#1e293b] rounded-xl p-3">
                            <p class="text-[9px] text-slate-500 uppercase tracking-wider mb-1">Durasi</p>
                            <p class="text-[13px] font-bold text-slate-100"
                               x-text="duration + ' ' + (packageType==='harian'?'Hari':packageType==='mingguan'?'Minggu':packageType==='bulanan'?'Bln':'Thn')"></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 bg-[#1e293b] rounded-xl px-3.5 py-2.5 mb-5">
                        <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-[11px] text-slate-400 font-medium"
                            x-text="adults + ' Dewasa' + (children>0 ? ' + '+children+' Anak ('+totalOccupants+' total)' : '')"></span>
                    </div>
                </div>
                <div class="bg-[#0a1628] px-6 py-4 flex items-center justify-between border-t border-slate-800">
                    <div>
                        <p class="text-[9px] font-semibold text-slate-500 uppercase tracking-widest mb-1">Total Estimasi</p>
                        <p class="text-[26px] font-extrabold text-blue-400 leading-none"
                           x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalPrice)"></p>
                        <p class="text-[10px] text-slate-600 font-medium mt-1">Sudah termasuk pajak & layanan</p>
                    </div>
                    <div class="w-11 h-11 bg-[#1e3a5f] rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                    </div>
                </div>
            </div>

            {{-- reCAPTCHA --}}
            <div class="mt-6">
                <div class="g-recaptcha"
                    data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"
                    data-callback="onRecaptchaSuccess"
                    data-expired-callback="onRecaptchaExpired">
                </div>
                <div x-show="captchaError" x-transition class="mt-2 flex items-center gap-1.5">
                    <svg class="w-3 h-3 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span class="text-[10px] text-red-500 font-semibold">Selesaikan verifikasi terlebih dahulu</span>
                </div>
            </div>

            <div class="mt-8 space-y-4">
                <div class="flex justify-between gap-4">
                    <button @click="prevStep()" class="flex-1 py-4 px-6 bg-slate-100 text-slate-400 font-bold rounded-2xl uppercase tracking-widest text-xs">Kembali</button>
                    <button @click="doSubmit()" class="flex-[2] py-4 px-6 bg-blue-600 text-white font-black rounded-2xl uppercase tracking-widest text-xs shadow-lg shadow-blue-200 hover:bg-black transition-all">Submit Reservasi</button>
                </div>
                <button @click="confirmCancel()" class="w-full py-4 text-red-500 font-bold uppercase tracking-widest text-[10px] bg-red-50 rounded-2xl border border-red-100">Batal Booking</button>
            </div>
        </div>{{-- /step 3 --}}

    </div>

    <div class="mt-12 text-center">
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.4em]">© 2026 BBPPMPV BOE MALANG</p>
    </div>

    {{-- ══════════════════════════════════════════════
         INDEPENDENT LIGHTBOX OVERLAY
         x-teleport moves this to <body> at runtime,
         escaping all overflow-hidden / backdrop-blur
         ancestors while staying in bookingForm scope.
         ══════════════════════════════════════════════ --}}
    <template x-teleport="body">
        <div x-show="__lbOpen"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[9999] bg-black/90 backdrop-blur-md flex items-center justify-center p-4"
             @click.self="__lbOpen = false"
             @keydown.escape.window="__lbOpen = false">
            <div class="relative max-w-3xl w-full">
                {{-- Close button --}}
                <button @click="__lbOpen = false"
                    class="absolute -top-12 right-0 text-white/80 hover:text-red-400 transition-colors z-10 flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-widest text-white/50">Tutup</span>
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                {{-- Photo track --}}
                <div class="overflow-hidden rounded-[2rem] bg-gray-900 shadow-2xl">
                    <div class="flex transition-all duration-500 ease-in-out"
                         :style="'transform:translateX(-' + (__lbIdx * 100) + '%)'">
                        <template x-for="(src, si) in (__lbPhotos || [])" :key="si">
                            <div style="min-width:100%; flex-shrink:0">
                                <img :src="src" class="w-full max-h-[75vh] object-contain mx-auto select-none">
                            </div>
                        </template>
                    </div>
                </div>
                {{-- Navigation --}}
                <div class="flex justify-center items-center gap-4 mt-5">
                    <button @click="__lbIdx = Math.max(0, __lbIdx - 1)"
                        :disabled="__lbIdx === 0"
                        class="px-6 py-2.5 bg-white/10 hover:bg-white/20 disabled:opacity-30 text-white rounded-xl font-bold transition-all text-sm">
                        ← Prev
                    </button>
                    {{-- Dot indicators --}}
                    <div class="flex gap-2 items-center">
                        <template x-for="(s, di) in (__lbPhotos || [])" :key="di">
                            <div @click="__lbIdx = di"
                                 class="rounded-full cursor-pointer transition-all duration-300"
                                 :class="di === __lbIdx ? 'w-3 h-3 bg-white' : 'w-2 h-2 bg-white/40 hover:bg-white/70'"></div>
                        </template>
                    </div>
                    <button @click="__lbIdx = Math.min((__lbPhotos || []).length - 1, __lbIdx + 1)"
                        :disabled="__lbIdx >= (__lbPhotos || []).length - 1"
                        class="px-6 py-2.5 bg-white/10 hover:bg-white/20 disabled:opacity-30 text-white rounded-xl font-bold transition-all text-sm">
                        Next →
                    </button>
                </div>
                {{-- Counter --}}
                <p class="text-center text-white/40 text-xs font-bold mt-3 uppercase tracking-widest"
                   x-text="'Foto ' + (__lbIdx + 1) + ' dari ' + (__lbPhotos || []).length"></p>
            </div>
        </div>
    </template>

</main>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('bookingForm', (config) => ({
        step: 1,
        packageType: '',
        duration: 1,
        adults: 1,
        children: 0,
        childAges: [],
        rooms: 1,
        selected_tipe_id: null,   // index into roomTypes array (radio selection)
        selectedDate: null,
        facilities: config.facilities || [],
        selectedFacilityId: config.selectedFacilityId || '',
        currentMonth: new Date().getMonth(),
        currentYear:  new Date().getFullYear(),
        daysInMonth:  [],
        calendarEvents: [],
        isLoadingCalendar: false,
        availableRooms: [],
        maxStock: 0,
        availabilityFetched: false,
        showRoomHint:  false,
        showChildHint: false,

        // ── Lightbox state (independent overlay) ──
        __lbOpen:   false,
        __lbPhotos: [],
        __lbIdx:    0,

        step1Submitted: false,
        step2Submitted: false,
        step2Errors: {
            duration: false,
            rooms: false, roomsMsg: '',
            adults: false, adultsMsg: '',
            childAges: false,
            roomType: false
        },

        // ── Step 3 (Data Diri) state ──
        step4Name: '',
        step4Provinsi: '', step4ProvinsiName: '',
        step4Kabupaten: '', step4KabupatenName: '',
        step4Whatsapp: '', step4Email: '',
        step4FotoPreview: null, step4FotoFile: null,
        step4FotoFileName: '', step4FotoErrorMsg: '',
        step4SearchProvinsi: '', step4SearchKabupaten: '',
        step4Provinces: [], step4Regencies: [],
        step4RegenciesCache: {},
        step4LoadingProvinsi: false, step4LoadingKabupaten: false,
        step4OpenProvinsi: false, step4OpenKabupaten: false,
        captchaToken: '', captchaError: false,
        step4Submitted: false,
        step4Errors:  { name:false,provinsi:false,kabupaten:false,whatsapp:false,email:false,foto:false },
        step4Success: { name:false,provinsi:false,kabupaten:false,whatsapp:false,email:false,foto:false },
        step4Shake:   { name:false,provinsi:false,kabupaten:false,whatsapp:false,email:false,foto:false },

        packageLabels: {
            harian:   { title:'Harian',   desc:'Cocok untuk kebutuhan jangka pendek, dihitung per hari.' },
            mingguan: { title:'Mingguan', desc:'Paket 7 hari, lebih hemat dari harian.' },
            bulanan:  { title:'Bulanan',  desc:'Lebih hemat untuk kebutuhan jangka panjang.' },
            tahunan:  { title:'Tahunan',  desc:'Harga terbaik untuk kontrak panjang.' },
        },

        async init() {
            this.updateDaysInMonth();
            this.$watch('children', val => {
                const count = parseInt(val) || 0;
                while (this.childAges.length < count) this.childAges.push('');
                this.childAges = this.childAges.slice(0, count);
            });
            this.$watch('currentMonth', () => { this.updateDaysInMonth(); this.fetchCalendarData(); });
            this.$watch('currentYear',  () => { this.updateDaysInMonth(); this.fetchCalendarData(); });
            this.$watch('selectedFacilityId', () => { this.fetchCalendarData(); });
            this.$watch('selectedDate', () => { this.availabilityFetched = false; this.fetchRoomAvailability(); });
            this.$watch('duration', () => { this.fetchRoomAvailability(); });
            this.$watch('selected_tipe_id', () => { this.availabilityFetched = false; this.availableRooms = []; this.maxStock = 0; this.fetchRoomAvailability(); });
            this.$watch('step', val => {
                if (val === 3) {
                    if (this.step4Provinces.length === 0) this.s4LoadProvinces();
                    setTimeout(() => { if (window.grecaptcha) window.grecaptcha.reset(); }, 300);
                }
                if (val === 2) { this.fetchCalendarData(); }
            });
            // ── Auto-select tipe_id from URL query param ──
            const _urlTipeId = new URLSearchParams(window.location.search).get('tipe_id');
            if (_urlTipeId !== null) {
                const _idx = this.roomTypes.findIndex((rt, i) => String(rt.id ?? i) === String(_urlTipeId));
                if (_idx >= 0) {
                    this.selected_tipe_id = _idx;
                    this.rooms = 1;
                }
            }
        },

        // ── Room type helpers (from paket_harian DB field) ───────────
        get roomTypes() {
            const f = this.currentFacility;
            if (!f || !f.paket_harian) return [];
            const pt = Array.isArray(f.paket_harian) ? f.paket_harian : [];
            return pt;
        },

        selectRoomType(idx) {
            if (this.selected_tipe_id === idx) return; // already selected
            this.selected_tipe_id = idx;
            this.rooms = 1; // reset room count when changing type
            this.step2Errors.roomType = false;
        },

        resetRoomType() {
            this.selected_tipe_id = null;
            this.rooms = 1;
        },

        // Max durasi from the selected room type's parent facility field
        get selectedRoomMaxDurasi() {
            const f = this.currentFacility;
            if (!f) return 0;
            if (this.packageType === 'harian')   return f.max_durasi_harian  || f.max_durasi_hari  || 0;
            if (this.packageType === 'mingguan') return f.max_durasi_minggu || 0;
            if (this.packageType === 'bulanan')  return f.max_durasi_bulan   || 0;
            if (this.packageType === 'tahunan')  return f.max_durasi_tahun   || 0;
            return 0;
        },

        // ── Getters ──────────────────────────────────────────────────
        get currentFacility() {
            return this.facilities.find(f => f.id == this.selectedFacilityId) || null;
        },

        get maxRoomsFromFacility() {
            const f = this.currentFacility;
            if (!f || f.tipe !== 'asrama') return 999;
            // If a room type is selected, its jumlah field limits; otherwise use facility total
            if (this.selected_tipe_id !== null && this.roomTypes.length > 0) {
                const rt = this.roomTypes[this.selected_tipe_id];
                return (rt && rt.jumlah) ? rt.jumlah : (f.jumlah_kamar || 999);
            }
            return f.jumlah_kamar || 999;
        },

        get totalOccupants() { return this.adults + this.children; },

        get roomsNeeded() {
            const adultSlots = this.adults + this.billableChildren;
            const childSlots = this.freeChildren;
            return Math.max(Math.ceil(adultSlots / 2), Math.ceil(childSlots / 2));
        },

        get maxAdultsAllowed() {
            const f = this.currentFacility;
            if (!f) return 999;
            if (f.tipe === 'asrama') return Math.max(0, (this.rooms * 2) - this.billableChildren);
            return f.max_dewasa || 999;
        },

        get maxChildrenAllowed() {
            const f = this.currentFacility;
            if (!f || f.tipe !== 'asrama') return 999;
            return Math.min(f.max_anak || 999, this.rooms * 2);
        },

        get billableChildren() {
            return this.childAges.filter(a => a !== '' && a !== null && a !== undefined && parseInt(a) >= 12).length;
        },
        get freeChildren() {
            return this.childAges.filter(a => a !== '' && a !== null && a !== undefined && parseInt(a) < 12).length;
        },
        get totalBillableGuests() { return this.adults + this.billableChildren; },

        get totalDays() {
            const d = parseInt(this.duration) || 1;
            if (this.packageType === 'mingguan') return d * 7;
            if (this.packageType === 'bulanan')  return d * 30;
            if (this.packageType === 'tahunan')  return d * 365;
            return d;
        },

        get endDate() {
            if (!this.selectedDate) return null;
            const start = new Date(this.selectedDate); start.setHours(0,0,0,0);
            const d = parseInt(this.duration) || 1;
            let end;
            if (this.packageType === 'bulanan') {
                end = new Date(start.getFullYear(), start.getMonth() + d, start.getDate());
                end.setDate(end.getDate() - 1);
            } else if (this.packageType === 'tahunan') {
                end = new Date(start.getFullYear() + d, start.getMonth(), start.getDate());
                end.setDate(end.getDate() - 1);
            } else {
                end = new Date(start);
                end.setDate(start.getDate() + this.totalDays - 1);
            }
            end.setHours(0,0,0,0);
            return end;
        },

        get tarifLabel() {
            const f = this.currentFacility; if (!f) return '';
            // Use selected room type pricing first, fall back to facility-level prices
            let h = 0, hb = 0, hm = 0, ht = 0;
            if (this.selected_tipe_id !== null && this.roomTypes.length > 0) {
                const rt = this.roomTypes[this.selected_tipe_id] || {};
                h  = parseFloat(rt.harga_harian  || 0);
                hm = parseFloat(rt.harga_mingguan || 0);
                hb = parseFloat(rt.harga_bulanan  || 0);
                ht = parseFloat(rt.harga_tahunan  || 0);
            }
            if (h === 0)  h  = parseFloat(f.harga        || 0);
            if (hb === 0) hb = parseFloat(f.harga_bulanan || h * 30);
            if (hm === 0) hm = h * 7;
            if (ht === 0) ht = hb * 12;
            const fmt = n => 'Rp ' + new Intl.NumberFormat('id-ID').format(n);
            if (this.packageType === 'harian')   return 'Tarif: ' + fmt(h)  + ' / hari';
            if (this.packageType === 'mingguan') return 'Tarif: ' + fmt(hm) + ' / minggu';
            if (this.packageType === 'bulanan')  return 'Tarif: ' + fmt(hb) + ' / bulan';
            if (this.packageType === 'tahunan')  return 'Tarif: ' + fmt(ht) + ' / tahun';
            return '';
        },

        get totalPrice() {
            const f = this.currentFacility; if (!f) return 0;
            let h = 0, hb = 0, hm = 0, ht = 0;
            if (this.selected_tipe_id !== null && this.roomTypes.length > 0) {
                const rt = this.roomTypes[this.selected_tipe_id] || {};
                h  = parseFloat(rt.harga_harian  || 0);
                hm = parseFloat(rt.harga_mingguan || 0);
                hb = parseFloat(rt.harga_bulanan  || 0);
                ht = parseFloat(rt.harga_tahunan  || 0);
            }
            if (h === 0)  h  = parseFloat(f.harga        || 0);
            if (hb === 0) hb = parseFloat(f.harga_bulanan || h * 30);
            if (hm === 0) hm = h * 7;
            if (ht === 0) ht = hb * 12;
            const d = parseInt(this.duration) || 0;
            const mult = f.tipe === 'asrama' ? this.rooms : 1;
            if (this.packageType === 'harian')   return d * h  * mult;
            if (this.packageType === 'mingguan') return d * hm * mult;
            if (this.packageType === 'bulanan')  return d * hb * mult;
            if (this.packageType === 'tahunan')  return d * ht * mult;
            return 0;
        },

        get hasConflictInRange() {
            if (!this.selectedDate || !this.endDate) return false;
            const start = new Date(this.selectedDate); start.setHours(0,0,0,0);
            const end   = new Date(this.endDate);      end.setHours(0,0,0,0);
            let cursor = new Date(start);
            while (cursor <= end) {
                if (this.getDateStatus(new Date(cursor)) !== 'ready') return true;
                cursor.setDate(cursor.getDate() + 1);
            }
            return false;
        },

        // ── Visual helpers ────────────────────────────────────────────
        occupantSlot(roomNumber, slot) {
            if (slot <= 2) {
                const adultIdx = (roomNumber - 1) * 2 + (slot - 1);
                return adultIdx < this.adults ? 'adult' : 'empty';
            }
            const childIdx = (roomNumber - 1) * 2 + (slot - 3);
            return childIdx < this.children ? 'child' : 'empty';
        },

        roomLabel(roomNumber) {
            const adultStart = (roomNumber - 1) * 2;
            let a = 0;
            for (let i = adultStart; i < adultStart + 2; i++) { if (i < this.adults) a++; }
            const childStart = (roomNumber - 1) * 2;
            let c = 0;
            for (let i = childStart; i < childStart + 2; i++) { if (i < this.children) c++; }
            if (a === 0 && c === 0) return 'Kosong';
            const parts = [];
            if (a > 0) parts.push(a + ' Dewasa');
            if (c > 0) parts.push(c + ' Anak');
            return parts.join(' · ');
        },

        // ── Step navigation ───────────────────────────────────────────
        submitStep1() {
            this.step1Submitted = true;
            if (!this.packageType) return;
            this.step++;
        },

        submitStep2() {
            this.step2Submitted = true;
            const f = this.currentFacility;
            let ok = true;

            // 0. Room type selection (asrama with paket_harian data)
            if (f?.tipe === 'asrama' && this.roomTypes.length > 0 && this.selected_tipe_id === null) {
                this.step2Errors.roomType = true; ok = false;
            } else {
                this.step2Errors.roomType = false;
            }

            // 1. Durasi
            if (!this.duration || parseInt(this.duration) < 1) {
                this.step2Errors.duration = true; ok = false;
            } else { this.step2Errors.duration = false; }

            // 2. Adults
            if (!this.adults || parseInt(this.adults) < 1) {
                this.step2Errors.adults = true; this.step2Errors.adultsMsg = 'Jumlah tamu minimal 1 orang'; ok = false;
            } else if (f?.tipe === 'aula' && this.adults > (f.max_dewasa || 999)) {
                this.step2Errors.adults = true; this.step2Errors.adultsMsg = 'Melebihi kapasitas aula (' + f.max_dewasa + ' orang)'; ok = false;
            } else if (f?.tipe === 'asrama' && this.adults > this.maxAdultsAllowed) {
                this.step2Errors.adults = true; this.step2Errors.adultsMsg = 'Melebihi kapasitas kamar (maks ' + this.maxAdultsAllowed + ' dewasa)'; ok = false;
            } else { this.step2Errors.adults = false; this.step2Errors.adultsMsg = ''; }

            // 3. ChildAges
            if (this.children > 0) {
                const anyEmpty = this.childAges.some(a => a === '' || a === null || a === undefined);
                this.step2Errors.childAges = anyEmpty;
                if (anyEmpty) ok = false;
            } else { this.step2Errors.childAges = false; }

            // 4. Room capacity (asrama)
            if (f?.tipe === 'asrama' && !this.step2Errors.adults && !this.step2Errors.childAges) {
                const totalAdultSlot = this.adults + this.billableChildren;
                if (totalAdultSlot > this.rooms * 2 || this.freeChildren > this.rooms * 2) {
                    this.step2Errors.rooms = true;
                    const butuh = Math.max(Math.ceil(totalAdultSlot / 2), Math.ceil(this.freeChildren / 2));
                    this.step2Errors.roomsMsg = 'Kamar kurang! Butuh min. ' + butuh + ' kamar. Tambah ' + (butuh - this.rooms) + ' lagi.';
                    ok = false;
                } else { this.step2Errors.rooms = false; this.step2Errors.roomsMsg = ''; }
            }

            // 5. Date selection (calendar merged here)
            if (!this.selectedDate) { ok = false; }
            if (this.selectedDate && this.hasConflictInRange) { ok = false; }

            if (!ok) return;
            this.step++;
        },

        prevStep() { if (this.step > 1) this.step--; },

        // ── Increment / Decrement ─────────────────────────────────────
        incDuration() {
            const max = this.selectedRoomMaxDurasi;
            if (max > 0 && this.duration >= max) {
                Swal.fire('Peringatan', 'Maksimal durasi untuk tipe kamar ini adalah ' + max + ' ' + (this.packageType === 'harian' ? 'hari' : this.packageType === 'mingguan' ? 'minggu' : this.packageType === 'bulanan' ? 'bulan' : 'tahun') + '.', 'warning');
                return;
            }
            this.duration++;
        },
        decDuration() { if (this.duration > 1) this.duration--; },

        incRooms() {
            if (this.rooms >= this.maxRoomsFromFacility) {
                Swal.fire('Kapasitas Penuh', 'Tidak dapat menambah kamar lagi (maks ' + this.maxRoomsFromFacility + ' kamar).', 'warning');
                return;
            }
            if (this.maxStock > 0 && this.rooms >= this.maxStock) {
                Swal.fire('Stok Habis', 'Hanya ' + this.maxStock + ' kamar tersedia untuk tanggal tersebut.', 'warning');
                return;
            }
            this.rooms++;
            const totalAdultSlot = this.adults + this.billableChildren;
            if (totalAdultSlot <= this.rooms * 2 && this.freeChildren <= this.rooms * 2) {
                this.step2Errors.rooms = false; this.step2Errors.roomsMsg = '';
            }
        },

        decRooms() {
            if (this.rooms <= 1) return;
            const newRooms = this.rooms - 1;
            const newCap   = newRooms * 2;
            const adultsAfter   = Math.min(this.adults, newCap);
            const childrenAfter = Math.min(this.children, newCap);
            const adultsRemoved   = this.adults   - adultsAfter;
            const childrenRemoved = this.children - childrenAfter;

            if (adultsRemoved > 0 || childrenRemoved > 0) {
                const parts = [];
                if (adultsRemoved   > 0) parts.push('<strong>' + adultsRemoved   + ' dewasa</strong>');
                if (childrenRemoved > 0) parts.push('<strong>' + childrenRemoved + ' anak</strong>');
                Swal.fire({
                    title: 'Kurangi Kamar?',
                    html: 'Menghapus kamar akan otomatis mengurangi ' + parts.join(' dan ') + '.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444', cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Ya, Kurangi', cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: { popup: 'rounded-[2rem] p-8' }
                }).then(result => {
                    if (result.isConfirmed) {
                        this.rooms    = newRooms;
                        this.adults   = adultsAfter;
                        this.children = childrenAfter;
                        this.childAges = this.childAges.slice(0, childrenAfter);
                        this.step2Errors.rooms = false; this.step2Errors.roomsMsg = '';
                    }
                });
                return;
            }
            this.rooms--;
        },

        incAdults() {
            if (this.adults >= this.maxAdultsAllowed) {
                this.showRoomHint = true; setTimeout(() => { this.showRoomHint = false; }, 4000); return;
            }
            this.adults++;
        },
        decAdults() { if (this.adults > 1) this.adults--; },
        incChildren() {
            if (this.children >= this.maxChildrenAllowed) {
                this.showChildHint = true; setTimeout(() => { this.showChildHint = false; }, 4000); return;
            }
            this.children++;
        },
        decChildren() { if (this.children > 0) this.children--; },

        // ── Calendar ──────────────────────────────────────────────────
        updateDaysInMonth() {
            const lastDay  = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
            const startDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
            this.daysInMonth = [];
            for (let i = 0; i < startDay; i++) this.daysInMonth.push({ day: null, date: null });
            for (let i = 1; i <= lastDay; i++)  this.daysInMonth.push({ day: i, date: new Date(this.currentYear, this.currentMonth, i) });
        },

        async fetchCalendarData() {
            if (!this.selectedFacilityId) return;
            this.isLoadingCalendar = true;
            try {
                const res = await fetch('/schedule_booking/data?fasilitas_id=' + this.selectedFacilityId +
                    '&year=' + this.currentYear + '&month=' + (this.currentMonth + 1) + '&t=' + Date.now());
                if (!res.ok) throw new Error('HTTP ' + res.status);
                this.calendarEvents = await res.json();
            } catch (e) {
                console.error('Gagal fetch kalender:', e);
                this.calendarEvents = [];
            } finally {
                this.isLoadingCalendar = false;
            }
        },

        async fetchRoomAvailability() {
            if (!this.selectedDate || !this.endDate || this.selected_tipe_id === null) {
                // No dates selected yet — reset fetch flag but keep master capacity visible
                this.availabilityFetched = false;
                this.availableRooms = [];
                this.maxStock = 0;
                return;
            }
            const rt = this.roomTypes[this.selected_tipe_id];
            if (!rt) {
                this.availabilityFetched = false;
                this.availableRooms = [];
                this.maxStock = 0;
                return;
            }
            try {
                const params = new URLSearchParams({
                    fasilitas_id: this.selectedFacilityId,
                    check_in_date: this.formatDateLocal(this.selectedDate),
                    check_out_date: this.formatDateLocal(this.endDate),
                });
                if (rt.id) {
                    params.append('tipe_kamar_id', rt.id);
                } else if (rt.tipe) {
                    params.append('tipe_kamar_nama', rt.tipe);
                } else {
                    this.availabilityFetched = false;
                    this.availableRooms = [];
                    this.maxStock = 0;
                    return;
                }
                const res = await fetch('/api/check-room-availability?' + params.toString());
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();
                if (data.success) {
                    this.maxStock = data.total_kamar_tersedia || 0;
                    this.availableRooms = data.nomor_kamar_tersedia || [];
                    this.availabilityFetched = true;
                } else {
                    this.availableRooms = [];
                    this.maxStock = 0;
                    this.availabilityFetched = true;
                }
            } catch (e) {
                console.error('Gagal fetch ketersediaan kamar:', e);
                this.availableRooms = [];
                this.maxStock = 0;
                this.availabilityFetched = false;
            }
        },

        getDateStatus(date) {
            if (!date) return 'closed';
            const today = new Date(); today.setHours(0,0,0,0);
            const d = new Date(date); d.setHours(0,0,0,0);
            if (d < today) return 'past';
            for (const ev of this.calendarEvents) {
                const s = new Date(ev.tgl_mulai); s.setHours(0,0,0,0);
                const e = new Date(ev.tgl_selesai); e.setHours(0,0,0,0);
                if (d >= s && d <= e) {
                    if (ev.color === 'yellow') return 'pending';
                    if (ev.color === 'blue')   return 'booked';
                    if (ev.color === 'black')  return 'blocked';
                    if (ev.color === 'red')    return 'maintenance';
                }
            }
            return 'ready';
        },

        getDayInfo(date) {
            if (!date) return '';
            const d = new Date(date); d.setHours(0,0,0,0);
            for (const ev of this.calendarEvents) {
                const s = new Date(ev.tgl_mulai); s.setHours(0,0,0,0);
                const e = new Date(ev.tgl_selesai); e.setHours(0,0,0,0);
                if (d >= s && d <= e) {
                    if (ev.status === 'maintenance') return 'Perbaikan: ' + (ev.reason || 'Maintenance');
                    return ev.status.toUpperCase();
                }
            }
            return '';
        },

        isInRange(date) {
            if (!this.selectedDate || !date || !this.endDate) return false;
            const d = new Date(date); d.setHours(0,0,0,0);
            const s = new Date(this.selectedDate); s.setHours(0,0,0,0);
            const e = new Date(this.endDate); e.setHours(0,0,0,0);
            return d >= s && d <= e;
        },

        getDayCellClass(item) {
            if (!item.day) return 'bg-white';
            const status  = this.getDateStatus(item.date);
            const inRange = this.isInRange(item.date);
            if (status === 'past')   return 'status-past';
            if (status === 'closed') return 'bg-white status-closed';
            if (inRange) {
                if (status !== 'ready') return 'status-conflict';
                return 'status-in-range';
            }
            if (status === 'pending')     return 'status-pending';
            if (status === 'booked')      return 'status-booked';
            if (status === 'blocked')     return 'status-blocked';
            if (status === 'maintenance') return 'status-maintenance';
            return 'status-ready';
        },

        selectDate(date) {
            if (!date) return;
            const status = this.getDateStatus(date);
            if (status !== 'ready') return;
            if (this.selectedDate && date.getTime() === this.selectedDate.getTime()) {
                this.selectedDate = null; return;
            }
            this.selectedDate = date;
        },

        prevMonth() {
            if (this.currentMonth === 0) { this.currentMonth = 11; this.currentYear--; }
            else { this.currentMonth--; }
        },
        nextMonth() {
            if (this.currentMonth === 11) { this.currentMonth = 0; this.currentYear++; }
            else { this.currentMonth++; }
        },

        get monthName() {
            return new Intl.DateTimeFormat('id-ID', { month: 'long' }).format(new Date(this.currentYear, this.currentMonth));
        },

        formatDisplay(date) {
            if (!date) return '-';
            return new Intl.DateTimeFormat('id-ID', { day:'numeric', month:'short', year:'numeric' }).format(date);
        },

        formatDateLocal(date) {
            if (!date) return '';
            const d  = new Date(date);
            const y  = d.getFullYear();
            const m  = String(d.getMonth()+1).padStart(2,'0');
            const dd = String(d.getDate()).padStart(2,'0');
            return y+'-'+m+'-'+dd;
        },

        // ── Step 3 / Data Diri methods ────────────────────────────────
        async s4LoadProvinces() {
            this.step4LoadingProvinsi = true;
            try {
                const cached = sessionStorage.getItem('_provinces_cache');
                if (cached) { this.step4Provinces = JSON.parse(cached); this.step4LoadingProvinsi = false; return; }
            } catch(e) {}
            const controller = new AbortController();
            const timeout = setTimeout(() => controller.abort(), 8000);
            try {
                const r = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json', { signal: controller.signal });
                if (!r.ok) throw new Error('HTTP ' + r.status);
                this.step4Provinces = await r.json();
                try { sessionStorage.setItem('_provinces_cache', JSON.stringify(this.step4Provinces)); } catch(e) {}
            } catch(e) {
                if (e.name === 'AbortError') Swal.fire('Koneksi Lambat', 'Gagal memuat data provinsi. Periksa koneksi internet Anda.', 'error');
                this.step4Provinces = [];
            } finally { clearTimeout(timeout); this.step4LoadingProvinsi = false; }
        },

        async s4FetchKabupaten(id) {
            this.step4LoadingKabupaten = true;
            this.step4Regencies = []; this.step4Kabupaten = ''; this.step4KabupatenName = '';
            this.step4Success.kabupaten = false; this.step4Errors.kabupaten = false; this.step4SearchKabupaten = '';
            if (this.step4RegenciesCache[id]) {
                this.step4Regencies = this.step4RegenciesCache[id]; this.step4LoadingKabupaten = false; return;
            }
            const controller = new AbortController();
            const timeout = setTimeout(() => controller.abort(), 8000);
            try {
                const r = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/regencies/' + id + '.json', { signal: controller.signal });
                if (!r.ok) throw new Error('HTTP ' + r.status);
                this.step4Regencies = await r.json();
                this.step4RegenciesCache[id] = this.step4Regencies;
            } catch(e) {
                this.step4Regencies = [];
                if (e.name === 'AbortError') Swal.fire('Timeout', 'Gagal memuat data kabupaten. Coba lagi.', 'error');
            } finally { clearTimeout(timeout); this.step4LoadingKabupaten = false; }
        },

        get step4FilteredProvinces() {
            if (!this.step4SearchProvinsi.trim()) return this.step4Provinces;
            return this.step4Provinces.filter(p => p.name.toLowerCase().includes(this.step4SearchProvinsi.toLowerCase().trim()));
        },
        get step4FilteredRegencies() {
            if (!this.step4SearchKabupaten.trim()) return this.step4Regencies;
            return this.step4Regencies.filter(k => k.name.toLowerCase().includes(this.step4SearchKabupaten.toLowerCase().trim()));
        },

        s4TriggerError(f) {
            this.step4Errors[f] = true; this.step4Success[f] = false; this.step4Shake[f] = true;
            setTimeout(() => { this.step4Shake[f] = false; }, 400);
        },
        s4TriggerSuccess(f) { this.step4Errors[f] = false; this.step4Success[f] = true; this.step4Shake[f] = false; },

        s4ValidateField(f) {
            if (f === 'name') {
                const t = this.step4Name.trim(); if (!t) { this.step4Errors.name=false;this.step4Success.name=false;return; }
                (/^[a-zA-Z\s]{3,}$/.test(t) ? this.s4TriggerSuccess : this.s4TriggerError).call(this,'name');
            }
            if (f === 'whatsapp') {
                const n = this.step4Whatsapp.trim(); if (!n) { this.step4Errors.whatsapp=false;this.step4Success.whatsapp=false;return; }
                (/^[0-9]{9,14}$/.test(n) ? this.s4TriggerSuccess : this.s4TriggerError).call(this,'whatsapp');
            }
            if (f === 'email') {
                const e = this.step4Email.trim(); if (!e) { this.step4Errors.email=false;this.step4Success.email=false;return; }
                (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(e) ? this.s4TriggerSuccess : this.s4TriggerError).call(this,'email');
            }
        },

        s4SelectProvinsi(p) {
            this.step4Provinsi = p.id; this.step4ProvinsiName = p.name;
            this.step4OpenProvinsi = false; this.step4SearchProvinsi = '';
            this.s4TriggerSuccess('provinsi'); this.s4FetchKabupaten(p.id);
        },
        s4SelectKabupaten(k) {
            this.step4Kabupaten = k.id; this.step4KabupatenName = k.name;
            this.step4OpenKabupaten = false; this.step4SearchKabupaten = '';
            this.s4TriggerSuccess('kabupaten');
        },

        s4HandleFileChange(e) {
            const file = e.target.files[0]; if (!file) return;
            if (!['image/jpeg','image/png','image/jpg'].includes(file.type)) {
                this.step4FotoErrorMsg = 'Format file harus JPG, JPEG, atau PNG!';
                this.s4TriggerError('foto'); this.step4FotoPreview=null; this.step4FotoFileName=''; e.target.value=''; return;
            }
            if (file.size > 2*1024*1024) {
                this.step4FotoErrorMsg = 'Ukuran file terlalu besar! Maksimal 2MB.';
                this.s4TriggerError('foto'); this.step4FotoPreview=null; this.step4FotoFileName=''; e.target.value=''; return;
            }
            this.step4FotoFile = file;
            this.step4FotoFileName = file.name.length > 24 ? file.name.substring(0,24)+'...' : file.name;
            const reader = new FileReader();
            reader.onload = ev => { this.step4FotoPreview = ev.target.result; this.s4TriggerSuccess('foto'); };
            reader.readAsDataURL(file);
        },

        s4ValidateAll() {
            let ok = true;
            if (!this.step4Name.trim() || !/^[a-zA-Z\s]{3,}$/.test(this.step4Name.trim())) { this.s4TriggerError('name'); ok=false; }
            if (!this.step4ProvinsiName) { this.s4TriggerError('provinsi'); ok=false; }
            if (!this.step4KabupatenName) { this.s4TriggerError('kabupaten'); ok=false; }
            if (!/^[0-9]{9,14}$/.test(this.step4Whatsapp.trim())) { this.s4TriggerError('whatsapp'); ok=false; }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.step4Email.trim())) { this.s4TriggerError('email'); ok=false; }
            if (!this.step4FotoFile) { this.step4FotoErrorMsg='Foto identitas wajib diunggah'; this.s4TriggerError('foto'); ok=false; }
            return ok;
        },

        doSubmit() {
            this.step4Submitted = true;
            if (!this.s4ValidateAll()) {
                Swal.fire({ title:'Data Tidak Lengkap', text:'Mohon perbaiki semua field yang ditandai merah.', icon:'warning', confirmButtonColor:'#1265A8' });
                return;
            }
            if (!this.captchaToken) {
                this.captchaError = true;
                Swal.fire({ title:'Verifikasi Diperlukan', text:'Harap selesaikan verifikasi captcha terlebih dahulu.', icon:'warning', confirmButtonColor:'#1265A8' });
                return;
            }

            const fd = new FormData();
            fd.append('name', this.step4Name.trim());
            fd.append('whatsapp', this.step4Whatsapp.trim());
            fd.append('email', this.step4Email.trim());
            fd.append('provinsi', this.step4ProvinsiName);
            fd.append('kabupaten', this.step4KabupatenName);
            fd.append('foto_identitas', this.step4FotoFile);
            fd.append('fasilitas_id', this.selectedFacilityId);
            fd.append('package_type', this.packageType);
            fd.append('duration', this.duration);
            fd.append('adults', this.adults);
            fd.append('billable_children', this.billableChildren);
            fd.append('free_children', this.freeChildren);
            fd.append('total_billable_guests', this.totalBillableGuests);
            fd.append('children_count', this.children);
            fd.append('rooms_count', this.rooms);
            fd.append('max_per_room', 2);
            // Send selected room type info for admin context
            if (this.selected_tipe_id !== null && this.roomTypes.length > 0) {
                const rt = this.roomTypes[this.selected_tipe_id] || {};
                fd.append('selected_tipe', rt.tipe || '');
                fd.append('selected_kode_blok', rt.kode_blok || '');
                // Send the numeric DB id so the backend can save tipe_kamar_id correctly
                if (rt.id) {
                    fd.append('tipe_kamar_id', rt.id);
                }
                // Send the allocated room numbers so the backend can persist them immediately
                if (this.availableRooms && this.availableRooms.length > 0) {
                    const allocated = this.availableRooms.slice(0, this.rooms);
                    allocated.forEach(n => fd.append('allocated_rooms[]', n));
                }
            }
            this.childAges.forEach(a => fd.append('child_age[]', a));
            fd.append('tgl_mulai', this.formatDateLocal(this.selectedDate));
            fd.append('tgl_selesai', this.formatDateLocal(this.endDate));
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            fd.append('_token', csrfToken);
            fd.append('g-recaptcha-response', this.captchaToken);
            fd.append('cf-turnstile-response', this.captchaToken);

            Swal.fire({
                title: 'Mengunggah Foto...',
                html: '<div style="margin-top:8px"><div style="background:#e2e8f0;border-radius:99px;height:8px;overflow:hidden"><div id="swal-bar" style="height:100%;width:0%;background:#2563eb;transition:width .3s ease;border-radius:99px"></div></div><p id="swal-pct" style="margin-top:6px;font-size:12px;color:#64748b;font-weight:600">0%</p></div>',
                allowOutsideClick: false, showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });

            const xhr = new XMLHttpRequest();
            const TIMEOUT_MS = 30000;
            const state = { timedOut: false, aborted: false };

            xhr.upload.onprogress = (e) => {
                if (!e.lengthComputable) return;
                const pct = Math.round((e.loaded / e.total) * 100);
                const bar = document.getElementById('swal-bar');
                const pctEl = document.getElementById('swal-pct');
                if (bar) bar.style.width = pct + '%';
                if (pctEl) pctEl.textContent = pct + '%';
                if (pct >= 100) {
                    const titleEl = Swal.getTitle();
                    if (titleEl) titleEl.textContent = 'Memproses Reservasi...';
                    if (pctEl) pctEl.textContent = 'Mohon tunggu sebentar...';
                }
            };

            const timeoutId = setTimeout(() => {
                state.timedOut = true; state.aborted = true; xhr.abort();
                Swal.fire({ title:'Waktu Habis', text:'Server tidak merespons dalam 30 detik.', icon:'error', confirmButtonColor:'#1265A8', confirmButtonText:'Coba Lagi' });
            }, TIMEOUT_MS);

            xhr.onreadystatechange = () => {
                if (xhr.readyState !== 4) return;
                clearTimeout(timeoutId);
                if (state.timedOut) return;
                if (window.grecaptcha) window.grecaptcha.reset();
                this.captchaToken = '';

                if (xhr.status >= 200 && xhr.status < 300) {
                    let d; try { d = JSON.parse(xhr.responseText); } catch(e) { Swal.fire('Error','Respons server tidak valid.','error'); return; }
                    if (d.success) {
                        try { sessionStorage.setItem('booking_success','true'); } catch(e) {}
                        Swal.fire({ title:'Reservasi Terkirim!', text:'Permintaan Anda sedang diproses.', icon:'success', confirmButtonColor:'#1265A8', timer:2000, showConfirmButton:false })
                            .then(() => { window.location.href='/'; });
                    } else { Swal.fire('Gagal!', d.message||'Terjadi kesalahan.', 'error'); }
                } else if (xhr.status === 422) {
                    let d; try { d = JSON.parse(xhr.responseText); } catch(e) { d={}; }
                    const msgs = d.errors ? Object.values(d.errors).flat().join('\n') : (d.message||'Data tidak valid.');
                    Swal.fire('Validasi Gagal', msgs, 'error');
                } else if (xhr.status === 0) {
                    if (!state.aborted) Swal.fire('Koneksi Terputus','Tidak dapat terhubung ke server.','error');
                } else { Swal.fire('Error ' + xhr.status,'Terjadi kesalahan server. Coba beberapa saat lagi.','error'); }
            };

            xhr.open('POST', '{{ route("bookings.store") }}');
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.send(fd);
        },

        confirmCancel() {
            Swal.fire({ title:'Batal Booking?', text:'Semua progres pengisian akan hilang.', icon:'warning',
                showCancelButton:true, confirmButtonColor:'#ef4444', confirmButtonText:'Ya, Batalkan', cancelButtonText:'Tidak'
            }).then(r => { if (r.isConfirmed) window.location.href='/'; });
        },
    }));
});

function onRecaptchaSuccess(token) {
    const el = document.querySelector('[x-data]');
    if (el && el._x_dataStack) { el._x_dataStack[0].captchaToken = token; el._x_dataStack[0].captchaError = false; }
}
function onRecaptchaExpired() {
    const el = document.querySelector('[x-data]');
    if (el && el._x_dataStack) { el._x_dataStack[0].captchaToken = ''; el._x_dataStack[0].captchaError = true; }
}
</script>
</body>
</html>
