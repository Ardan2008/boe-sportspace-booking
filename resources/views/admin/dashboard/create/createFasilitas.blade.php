<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <title>BOE-Sport Space | Add Fasilitas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @keyframes vShake {
            0%,100% { transform: translateX(0); }
            20%      { transform: translateX(-5px); }
            40%      { transform: translateX(5px); }
            60%      { transform: translateX(-4px); }
            80%      { transform: translateX(4px); }
        }

        @keyframes toastIn {
            from { transform: translateX(110%); opacity: 0; }
            to   { transform: translateX(0);    opacity: 1; }
        }
        @keyframes toastOut {
            from { transform: translateX(0);    opacity: 1; }
            to   { transform: translateX(110%); opacity: 0; }
        }
        @keyframes toastProgress {
            from { width: 100%; }
            to   { width: 0;    }
        }

        .v-field {
            transition: border-color .2s, box-shadow .2s, background-color .2s;
        }
        .v-field.v-error {
            border-color: #E24B4A !important;
            background-color: #fff9f9 !important;
            box-shadow: 0 0 0 4px rgba(226,75,74,.10) !important;
            animation: vShake .35s ease;
        }
        .v-field.v-success {
            border-color: #97C459 !important;
            background-color: #fafffe !important;
            box-shadow: 0 0 0 4px rgba(97,196,89,.10) !important;
        }

        .v-hint {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 600;
            padding-left: 2px;
            overflow: hidden;
            max-height: 0;
            opacity: 0;
            transition: max-height .25s ease, opacity .2s ease, margin-top .2s ease;
        }
        .v-hint.show {
            max-height: 40px;
            opacity: 1;
            margin-top: 6px;
        }
        .v-hint.hint-error   { color: #A32D2D; }
        .v-hint.hint-warning { color: #854F0B; }
        .v-hint.hint-success { color: #3B6D11; }

        .v-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s;
        }
        .v-icon.show { opacity: 1; }

        .v-progress-wrap {
            height: 3px;
            border-radius: 99px;
            background: #e2e8f0;
            margin-top: 8px;
            overflow: hidden;
            opacity: 0;
            transition: opacity .2s;
        }
        .v-progress-wrap.show { opacity: 1; }
        .v-progress-bar {
            height: 100%;
            border-radius: 99px;
            width: 0%;
            transition: width .3s ease, background-color .3s ease;
        }

        .v-char-counter {
            position: absolute;
            right: 14px;
            bottom: 12px;
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            pointer-events: none;
            transition: color .2s;
        }
        .v-char-counter.warn   { color: #BA7517; }
        .v-char-counter.danger { color: #E24B4A; }

        #v-toast-stack {
            position: fixed;
            bottom: 24px;
            right: 24px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 9999;
            pointer-events: none;
        }
        .v-toast {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            background: #ffffff;
            border-radius: 18px;
            padding: 14px 16px;
            min-width: 290px;
            max-width: 350px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 8px 32px rgba(0,0,0,.10);
            pointer-events: all;
            position: relative;
            overflow: hidden;
            animation: toastIn .3s cubic-bezier(.34,1.56,.64,1) forwards;
        }
        .v-toast.removing { animation: toastOut .25s ease forwards; }
        .v-toast-progress {
            position: absolute;
            bottom: 0; left: 0;
            height: 3px;
            border-radius: 0 0 18px 18px;
            animation: toastProgress linear forwards;
        }
        .v-toast-icon    { font-size: 20px; flex-shrink: 0; margin-top: 1px; }
        .v-toast-body    { flex: 1; }
        .v-toast-title   { font-size: 13px; font-weight: 700; margin-bottom: 2px; }
        .v-toast-msg     { font-size: 11.5px; color: #64748b; line-height: 1.5; }
        .v-toast-close   {
            background: none; border: none; cursor: pointer;
            color: #94a3b8; font-size: 16px; flex-shrink: 0;
            margin-top: 1px; transition: color .15s; padding: 0;
        }
        .v-toast-close:hover { color: #1e293b; }

        .v-toast.toast-error   .v-toast-icon     { color: #E24B4A; }
        .v-toast.toast-error   .v-toast-progress { background: #E24B4A; }
        .v-toast.toast-error   .v-toast-title    { color: #A32D2D; }
        .v-toast.toast-success .v-toast-icon     { color: #639922; }
        .v-toast.toast-success .v-toast-progress { background: #97C459; }
        .v-toast.toast-success .v-toast-title    { color: #3B6D11; }
        .v-toast.toast-warning .v-toast-icon     { color: #BA7517; }
        .v-toast.toast-warning .v-toast-progress { background: #EF9F27; }
        .v-toast.toast-warning .v-toast-title    { color: #854F0B; }

        #v-confirm-overlay {
            position: fixed; inset: 0;
            background: rgba(15,23,42,.45);
            backdrop-filter: blur(6px);
            z-index: 10000;
            display: flex; align-items: center; justify-content: center;
            opacity: 0; pointer-events: none;
            transition: opacity .2s;
        }
        #v-confirm-overlay.show { opacity: 1; pointer-events: all; }
        #v-confirm-box {
            background: #fff;
            border-radius: 28px;
            padding: 36px 32px 28px;
            max-width: 380px; width: 90%;
            box-shadow: 0 32px 64px rgba(0,0,0,.14);
            transform: scale(.92) translateY(12px);
            transition: transform .25s cubic-bezier(.34,1.56,.64,1);
            text-align: center;
        }
        #v-confirm-overlay.show #v-confirm-box { transform: scale(1) translateY(0); }
        .v-confirm-icon-wrap {
            width: 56px; height: 56px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
            font-size: 26px;
        }
        .v-confirm-title { font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
        .v-confirm-text  { font-size: 13px; color: #64748b; line-height: 1.6; margin-bottom: 24px; }
        .v-confirm-btns  { display: flex; gap: 10px; }
        .v-confirm-btns button {
            flex: 1; padding: 12px 0;
            border-radius: 14px; border: none;
            font-size: 12px; font-weight: 800;
            letter-spacing: .08em; text-transform: uppercase;
            cursor: pointer; transition: opacity .15s, transform .1s;
        }
        .v-confirm-btns button:active { transform: scale(.97); }
        .v-confirm-btn-cancel { background: #f1f5f9; color: #64748b; }
        .v-confirm-btn-cancel:hover { background: #e2e8f0; }
        .v-confirm-btn-ok     { background: #1d6fa5; color: #fff; }
        .v-confirm-btn-ok:hover { background: #155d8a; }
        .v-confirm-btn-ok.danger { background: #E24B4A; }
        .v-confirm-btn-ok.danger:hover { background: #A32D2D; }

        #dropzone.v-dz-error { border-color: #E24B4A !important; box-shadow: 0 0 0 4px rgba(226,75,74,.10); }

        .swal2-shown { padding-right: 0 !important; }

        .kamar-stepper-wrap {
            display: flex;
            align-items: center;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            transition: border-color .2s, box-shadow .2s;
        }
        .kamar-stepper-wrap:focus-within {
            border-color: #1d6fa5;
            box-shadow: 0 0 0 4px rgba(29,111,165,.10);
        }
        .kamar-stepper-wrap.v-error {
            border-color: #E24B4A !important;
            box-shadow: 0 0 0 4px rgba(226,75,74,.10) !important;
            animation: vShake .35s ease;
        }
        .kamar-stepper-btn {
            flex-shrink: 0;
            width: 52px;
            height: 56px;
            background: #f8fafc;
            border: none;
            font-size: 22px;
            font-weight: 900;
            color: #1d6fa5;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s, color .15s, transform .1s;
            user-select: none;
            line-height: 1;
        }
        .kamar-stepper-btn:hover  { background: #dbeafe; color: #1558a0; }
        .kamar-stepper-btn:active { transform: scale(.90); }
        .kamar-stepper-btn:disabled {
            color: #cbd5e1;
            cursor: not-allowed;
            background: #f8fafc;
        }
        .kamar-stepper-input {
            flex: 1;
            min-width: 0;
            border: none;
            border-left: 1.5px solid #e2e8f0;
            border-right: 1.5px solid #e2e8f0;
            text-align: center;
            font-size: 22px;
            font-weight: 900;
            color: #0f172a;
            background: transparent;
            outline: none;
            padding: 0;
            height: 56px;
            -moz-appearance: textfield;
        }
        .kamar-stepper-input::-webkit-outer-spin-button,
        .kamar-stepper-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

        .kamar-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 8px;
            padding: 5px 12px;
            border-radius: 99px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .05em;
            background: #f0f9ff;
            color: #0369a1;
            border: 1px solid #bae6fd;
            transition: all .2s;
        }

        /* ── Room Slider ── */
        .room-section {
            border-radius: 2rem;
            border: 2px dashed #e2e8f0;
            background: #fafcff;
            padding: 2rem;
            transition: border-color .3s, box-shadow .3s;
        }
        .room-section:hover {
            border-color: #bae6fd;
            box-shadow: 0 4px 24px rgba(29,111,165,0.04);
        }
        .room-slider-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .room-nav-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border-radius: 1rem;
            border: 1.5px solid #e2e8f0;
            background: #fff;
            font-size: 11px;
            font-weight: 800;
            color: #1d6fa5;
            cursor: pointer;
            transition: all .2s;
            user-select: none;
            white-space: nowrap;
            letter-spacing: .05em;
            text-transform: uppercase;
        }
        .room-nav-btn:hover:not(:disabled) {
            background: #dbeafe;
            border-color: #93c5fd;
        }
        .room-nav-btn:active:not(:disabled) { transform: scale(.96); }
        .room-nav-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
        .room-dots {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .room-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #dbe0e8;
            transition: all .3s;
            cursor: pointer;
        }
        .room-dot.active {
            width: 28px;
            border-radius: 99px;
            background: #1d6fa5;
        }
        .room-dot:hover:not(.active) {
            background: #94a3b8;
        }
        .room-card {
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 1.5rem;
            padding: 1.75rem;
            transition: all .3s;
        }
        .room-card:focus-within {
            border-color: #1d6fa5;
            box-shadow: 0 0 0 4px rgba(29,111,165,.06);
        }

        /* Hybrid dropdown */
        .room-type-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 0.875rem 1.25rem;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 1rem;
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
            cursor: pointer;
            transition: border-color .2s, box-shadow .2s;
        }
        .room-type-btn:hover {
            border-color: #93c5fd;
        }
        .room-type-btn:focus {
            outline: none;
            border-color: #1d6fa5;
            box-shadow: 0 0 0 4px rgba(29,111,165,.10);
        }
        .room-type-dropdown {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow: 0 12px 32px rgba(0,0,0,.08);
            z-index: 50;
            overflow: hidden;
        }
        .room-type-option {
            display: block;
            width: 100%;
            padding: 0.75rem 1.25rem;
            border: none;
            background: transparent;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
            text-align: left;
            transition: background .1s;
        }
        .room-type-option:hover {
            background: #f1f5f9;
        }
        .room-type-option:not(:last-child) {
            border-bottom: 1px solid #f1f5f9;
        }
        .room-type-option.lainnya {
            border-top: 1.5px dashed #e2e8f0;
            color: #1d6fa5;
            font-weight: 800;
        }

        /* Room foto grid */
        .room-foto-slot {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            border: 2px dashed #e2e8f0;
            background: #fafafa;
            height: 8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: border-color .3s;
        }
        .room-foto-slot:hover {
            border-color: #1d6fa5;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] font-sans antialiased text-slate-800">

    {{-- Background blobs --}}
    <div class="fixed top-0 left-0 w-full h-full -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-100 blur-[120px] rounded-full opacity-50"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] bg-indigo-100 blur-[120px] rounded-full opacity-50"></div>
    </div>

    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 flex justify-center items-center" x-data="facilityCreator()">
        <div class="w-full max-w-5xl bg-white/80 backdrop-blur-xl rounded-[3rem] shadow-[0_32px_64px_-15px_rgba(0,0,0,0.08)] border border-white overflow-hidden transition-all duration-500 hover:shadow-blue-200/40">

            {{-- Header --}}
            <div class="pt-10 pb-6 px-10 text-center">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 mb-4 bg-blue-50/50 rounded-full border border-blue-100 shadow-sm">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#1d6fa5]"></span>
                    </span>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#1d6fa5]" x-text="'Management Portal | ' + (tipe === 'lapangan' ? 'Lapangan' : 'Renang')"></span>
                </div>
                <h2 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight uppercase">
                    Add <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#1d6fa5] to-blue-400" x-text="tipe === 'lapangan' ? 'Lapangan' : 'Renang'"></span> Data
                </h2>
                <div class="h-1 w-12 bg-gradient-to-r from-[#1d6fa5] to-blue-400 mx-auto mt-4 rounded-full"></div>

                {{-- Type Switcher --}}
                <div class="flex justify-center gap-4 mt-8">
                    <button type="button"
                        @click="tipe = 'lapangan'; selectedLabels = []"
                        :class="tipe === 'lapangan' ? 'bg-[#1d6fa5] text-white shadow-lg' : 'bg-slate-100 text-slate-400 hover:bg-slate-200'"
                        class="px-8 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all duration-300">Lapangan</button>
                    <button type="button"
                        @click="tipe = 'kolam_renang'; selectedLabels = []"
                        :class="tipe === 'kolam_renang' ? 'bg-[#1d6fa5] text-white shadow-lg' : 'bg-slate-100 text-slate-400 hover:bg-slate-200'"
                        class="px-8 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all duration-300">Renang</button>
                </div>
            </div>

            {{-- Form --}}
            <form id="mainForm" action="/admin/fasilitas/store" method="POST" enctype="multipart/form-data"
                  class="p-8 lg:p-12 pt-6 space-y-8" novalidate>
                @csrf
                <input type="hidden" name="tipe" :value="tipe">
                <input type="hidden" name="all_same" id="allSameInput" :value="allSame ? '1' : '0'">
                <input type="hidden" name="paket_harian" id="paketHarianInput" value="">
                <input type="hidden" name="rooms_data" id="roomsDataInput" value="">

                {{-- ═══════════════════════════════════════════════════════════════
                     SECTION 1 — MAIN FACILITY INFO (single column)
                ═══════════════════════════════════════════════════════════════ --}}

                {{-- 1. Nama Fasilitas --}}
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Fasilitas</label>
                    <div class="relative">
                        <input type="text" name="nama" id="namaFasilitas" maxlength="60"
                            class="v-field w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none shadow-sm font-semibold"
                            required>
                        <svg id="icon-nama-ok" class="v-icon text-green-500 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg id="icon-nama-err" class="v-icon text-red-500 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </div>
                    <div class="v-progress-wrap" id="pb-nama-wrap">
                        <div class="v-progress-bar" id="pb-nama"></div>
                    </div>
                    <p class="v-hint" id="hint-nama"></p>
                </div>

                {{-- 2. Deskripsi Singkat --}}
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Deskripsi Singkat</label>
                    <div class="relative">
                        <textarea name="deskripsi" id="deskripsiFasilitas" rows="3" maxlength="200"
                            class="v-field w-full px-6 py-4 pb-8 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none shadow-sm resize-none font-medium leading-relaxed"
                            required></textarea>
                        <span class="v-char-counter" id="cc-desc">200</span>
                    </div>
                    <p class="v-hint" id="hint-desc"></p>
                </div>

                {{-- 3. Detail Fasilitas --}}
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Detail Fasilitas</label>
                    <textarea name="detail" rows="5"
                        class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none shadow-sm resize-none font-medium leading-relaxed"></textarea>
                </div>

                {{-- 4. Jam Operasional --}}
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Jam Operasional</label>
                    <div class="relative">
                        <input type="text" name="jam_operasional" id="jamOperasional"
                            placeholder="08.00 - 22.00"
                            class="v-field w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none shadow-sm font-semibold"
                            required>
                    </div>
                    <p class="v-hint" id="hint-jam"></p>
                </div>

                {{-- 5. Thumbnail --}}
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Thumbnail Cards</label>
                    <div id="dropzone"
                        class="relative overflow-hidden rounded-[2rem] border-2 border-dashed border-slate-200 bg-slate-50/50 hover:border-[#1d6fa5] transition-all duration-500 h-48 flex items-center justify-center group/drop cursor-pointer">
                        <img id="preview" class="absolute inset-0 w-full h-full object-cover hidden z-10" src="" alt="Preview thumbnail">
                        <div id="ui-content" class="relative z-20 flex flex-col items-center transition-opacity duration-300">
                            <div class="p-4 bg-white/90 backdrop-blur rounded-2xl shadow-lg mb-2 transform group-hover/drop:scale-110 transition-all duration-500">
                                <svg class="w-6 h-6 text-[#1d6fa5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="text-[10px] font-black uppercase tracking-[0.1em] text-slate-500">Pilih Foto Utama</p>
                        </div>
                        <input type="file" id="fileInput" name="image" accept="image/*"
                            class="absolute inset-0 opacity-0 cursor-pointer z-30" required>
                    </div>
                    <p class="v-hint" id="hint-thumb"></p>
                </div>

                {{-- 6. Gallery (3 Foto) --}}
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Preview Gallery (3 Foto)</label>
                    <div class="grid grid-cols-3 gap-3">
                        <template x-for="i in [0, 1, 2]" :key="i">
                            <div class="relative overflow-hidden rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 hover:border-[#1d6fa5] transition-all duration-500 h-32 flex items-center justify-center group/gal cursor-pointer">
                                <img :src="galleryPreviews[i]" class="absolute inset-0 w-full h-full object-cover z-10" x-show="galleryPreviews[i]" alt="">
                                <div class="relative z-20 flex flex-col items-center" x-show="!galleryPreviews[i]">
                                    <svg class="w-5 h-5 text-slate-300 group-hover/gal:text-[#1d6fa5] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </div>
                                <input :id="'galleryInput' + i" :name="'gallery[' + i + ']'"
                                    type="file" accept="image/*"
                                    class="absolute inset-0 opacity-0 cursor-pointer z-30"
                                    @change="
                                        if (window.validateGalleryFile($event.target, i)) {
                                            const file = $event.target.files[0];
                                            if (file) {
                                                const reader = new FileReader();
                                                reader.onload = (e) => galleryPreviews[i] = e.target.result;
                                                reader.readAsDataURL(file);
                                            }
                                        }
                                    ">
                            </div>
                        </template>
                    </div>
                </div>

                {{-- 7. Jumlah Tersedia — stepper --}}
                <div x-show="tipe === 'lapangan' || tipe === 'kolam_renang'" x-cloak>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">
                        <span x-text="tipe === 'lapangan' ? 'Jumlah Lapangan Tersedia' : 'Jumlah Kolam Tersedia'"></span>
                    </label>

                    <div class="kamar-stepper-wrap" id="kamarStepperWrap">

                        <button type="button"
                            id="btnKamarMinus"
                            class="kamar-stepper-btn"
                            onclick="window.kamarStep(-1)"
                            :aria-label="'Kurangi ' + (tipe === 'lapangan' ? 'lapangan' : 'kolam')">
                            −
                        </button>

                        <input
                            type="number"
                            name="jumlah_lapangan"
                            id="jumlahLapangan"
                            min="1"
                            max="999"
                            value="1"
                            class="kamar-stepper-input"
                            oninput="window.kamarOnInput(this)"
                            :aria-label="'Jumlah ' + (tipe === 'lapangan' ? 'lapangan' : 'kolam')">

                        <button type="button"
                            id="btnKamarPlus"
                            class="kamar-stepper-btn"
                            onclick="window.kamarStep(1)"
                            :aria-label="'Tambah ' + (tipe === 'lapangan' ? 'lapangan' : 'kolam')">
                            +
                        </button>

                    </div>

                    <div class="kamar-badge">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span id="kamarBadgeText">1 <span x-text="tipe === 'lapangan' ? 'lapangan' : 'kolam'"></span> tersedia</span>
                    </div>

                    <p class="v-hint" id="hint-kamar"></p>
                </div>

                {{-- ═══════════════════════════════════════════════════════════════
                     SECTION 2 — ROOM SPECIFICATIONS (conditional)
                ═══════════════════════════════════════════════════════════════ --}}

                <div x-show="(tipe === 'lapangan' || tipe === 'kolam_renang') && jumlahLapangan >= 1" x-cloak class="room-section w-full">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-black uppercase tracking-[0.15em] text-slate-400">
                            Spesifikasi
                        </h3>
                        <div class="flex items-center gap-3">
                            <template x-if="jumlahLapangan > 1">
                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400" x-text="allSame ? 'Sama' : 'Beda'"></span>
                                    <div class="relative">
                                        <input type="checkbox" x-model="allSame" class="sr-only">
                                        <div class="w-9 h-5 rounded-full transition-colors" :class="allSame ? 'bg-[#1d6fa5]' : 'bg-slate-300'"></div>
                                        <div class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform" :class="allSame ? 'translate-x-4' : ''"></div>
                                    </div>
                                </label>
                            </template>
                            <span class="text-[10px] font-black uppercase tracking-widest text-[#1d6fa5] bg-blue-50 px-3 py-1 rounded-full border border-blue-100"
                                x-text="'Total ' + jumlahLapangan + ' ' + (tipe === 'lapangan' ? 'lapangan' : 'kolam')"></span>
                        </div>
                    </div>

                    {{-- Single room — no slider (also used when allSame is on) --}}
                    <div x-show="jumlahLapangan === 1 || allSame">
                        <template x-if="allSame && jumlahLapangan > 1">
                            <div class="mb-3 px-4 py-2 bg-blue-50 border border-blue-200 rounded-xl">
                                <p class="text-[10px] font-bold text-[#1d6fa5] uppercase tracking-widest">Spesifikasi akan diterapkan ke semua <span x-text="jumlahLapangan"></span> <span x-text="tipe === 'lapangan' ? 'lapangan' : 'kolam'"></span></p>
                            </div>
                        </template>
                        <template x-for="(room, rIdx) in allSame && jumlahLapangan > 1 ? [rooms[0]] : rooms" :key="rIdx">
                            <div class="room-card space-y-5" @change="syncPaketHarian()" @input="syncPaketHarian()">
                                {{-- Room header --}}
                                <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400" x-text="tipe === 'lapangan' ? 'Lapangan' : 'Kolam'"></span>
                                    <span class="text-xs font-black text-[#1d6fa5]">1</span>
                                </div>

                                {{-- Tipe — hanya muncul jika lebih dari 1 dan spesifikasi beda --}}
                                <div x-show="jumlahLapangan > 1 && !allSame">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2" x-text="tipe === 'lapangan' ? 'Tipe Lapangan' : 'Tipe Kolam'"></label>
                                    <div class="flex flex-col gap-2">
                                        <div class="flex flex-wrap gap-1.5" x-show="rooms[rIdx].tipe.length">
                                            <template x-for="(tag, tIdx) in rooms[rIdx].tipe" :key="tIdx">
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-700 bg-slate-100 border border-slate-200 rounded-lg px-2 py-1 group/tag">
                                                    <span x-text="tag"></span>
                                                    <button type="button" @click="removeTipeTag(rooms[rIdx], tIdx)" class="text-red-300 hover:text-red-600 opacity-0 group-hover/tag:opacity-100 transition-opacity">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </span>
                                            </template>
                                        </div>
                                        <input type="text" @keydown.enter.prevent="addTipeTag(rooms[rIdx], $event)" @input.stop placeholder="Ketik lalu Enter..." class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-semibold outline-none focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] transition-all">
                                    </div>
                                </div>

                                {{-- Ukuran --}}
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Ukuran</label>
                                    <div class="flex items-center gap-2">
                                        <div class="relative flex-1">
                                            <input type="number" x-model="room.panjang" min="0" step="0.1" placeholder="0" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none font-bold text-sm text-center">
                                        </div>
                                        <span class="text-lg font-black text-slate-400 flex-shrink-0">×</span>
                                        <div class="relative flex-1">
                                            <input type="number" x-model="room.lebar" min="0" step="0.1" placeholder="0" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none font-bold text-sm text-center">
                                        </div>
                                        <span class="text-xs font-black text-slate-400 flex-shrink-0 whitespace-nowrap">m²</span>
                                    </div>
                                </div>

                                {{-- Foto --}}
                                <div class="grid grid-cols-3 gap-2" x-show="jumlahLapangan > 1 && !allSame">
                                    <template x-for="fIdx in [0, 1, 2]" :key="fIdx">
                                        <div class="room-foto-slot">
                                            <img :src="room.fotoPreviews[fIdx]" class="absolute inset-0 w-full h-full object-cover z-10 rounded-[inherit]" x-show="room.fotoPreviews[fIdx]" alt="">
                                            <div class="relative z-20 flex flex-col items-center" x-show="!room.fotoPreviews[fIdx]">
                                                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </div>
                                            <input type="file" accept="image/*"
                                                :name="'room_fotos[' + rIdx + '][' + fIdx + ']'"
                                                class="room-foto-input absolute inset-0 opacity-0 cursor-pointer z-30"
                                                @change="handleRoomFoto($event, rIdx, fIdx)">
                                        </div>
                                    </template>
                                </div>

                                {{-- Fasilitas --}}
                                <div x-show="jumlahLapangan > 1" class="space-y-3">
                                    <div class="grid grid-cols-5 sm:grid-cols-5 gap-2">
                                        <template x-for="(item, fIdx) in room.fasilitasKeys" :key="item.key">
                                            <div class="relative group flex flex-col items-center bg-white border border-slate-200 rounded-xl px-2 py-3 transition-all duration-200 hover:border-[#1d6fa5] hover:shadow-sm">
                                                <span class="text-[8px] font-black text-slate-500 uppercase tracking-wider mb-1 leading-tight text-center" x-text="item.label"></span>
                                                <input type="number" x-model.number="room.fasilitas[item.key]" min="0" placeholder="0"
                                                    class="w-12 h-7 text-center bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none font-bold text-xs">
                                                <button type="button" @click="removeFasilitas(room, fIdx)"
                                                    class="absolute -top-1.5 -right-1.5 w-5 h-5 flex items-center justify-center rounded-full bg-red-500/80 text-white text-xs opacity-0 group-hover:opacity-100 transition-all">&times;</button>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="flex gap-2">
                                        <input type="text" x-model="room.newFasilitasLabel" @keydown.enter.prevent="addFasilitas(room)"
                                            placeholder="Tambah fasilitas..."
                                            class="flex-1 px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-[10px] font-bold outline-none focus:border-[#1d6fa5] transition-all">
                                        <button type="button" @click="addFasilitas(room)"
                                            class="px-4 py-2 bg-[#1d6fa5] text-white rounded-xl hover:bg-slate-800 transition-all font-black text-sm">+</button>
                                    </div>
                                </div>

                                {{-- Harga — 2x2 grid --}}
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Harga</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        {{-- Harian/Jam --}}
                                        <div>
                                            <span class="block text-[9px] font-bold text-slate-500 mb-1 ml-1 uppercase tracking-wider" x-text="tipe === 'lapangan' ? 'Jam' : 'Harian'"></span>
                                            <div class="relative">
                                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-black text-[#1d6fa5] text-xs pointer-events-none">Rp</span>
                                                <input type="text"
                                                    @input="
                                                        const raw = $event.target.value.replace(/\D/g, '');
                                                        room.harga_harian = raw !== '' ? Number(raw) : '';
                                                        $event.target.value = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
                                                    "
                                                    :class="(!room.harga_harian || Number(room.harga_harian) <= 0) ? 'v-error' : 'v-success'"
                                                    class="w-full pl-9 pr-3 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none font-bold text-sm">
                                                <input type="hidden" :name="'rooms[' + rIdx + '][harga_harian]'" :value="room.harga_harian">
                                            </div>
                                        </div>
                                        {{-- Mingguan --}}
                                        <div>
                                            <span class="block text-[9px] font-bold text-slate-500 mb-1 ml-1 uppercase tracking-wider">Mingguan</span>
                                            <div class="relative">
                                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-black text-[#1d6fa5] text-xs pointer-events-none">Rp</span>
                                                <input type="text"
                                                    @input="
                                                        const raw = $event.target.value.replace(/\D/g, '');
                                                        room.harga_mingguan = raw !== '' ? Number(raw) : '';
                                                        $event.target.value = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
                                                    "
                                                    :class="(!room.harga_mingguan || Number(room.harga_mingguan) <= 0) ? 'v-error' : 'v-success'"
                                                    class="w-full pl-9 pr-3 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none font-bold text-sm">
                                                <input type="hidden" :name="'rooms[' + rIdx + '][harga_mingguan]'" :value="room.harga_mingguan">
                                            </div>
                                        </div>
                                        {{-- Bulanan --}}
                                        <div>
                                            <span class="block text-[9px] font-bold text-slate-500 mb-1 ml-1 uppercase tracking-wider">Bulanan</span>
                                            <div class="relative">
                                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-black text-[#1d6fa5] text-xs pointer-events-none">Rp</span>
                                                <input type="text"
                                                    @input="
                                                        const raw = $event.target.value.replace(/\D/g, '');
                                                        room.harga_bulanan = raw !== '' ? Number(raw) : '';
                                                        $event.target.value = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
                                                    "
                                                    :class="(!room.harga_bulanan || Number(room.harga_bulanan) <= 0) ? 'v-error' : 'v-success'"
                                                    class="w-full pl-9 pr-3 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none font-bold text-sm">
                                                <input type="hidden" :name="'rooms[' + rIdx + '][harga_bulanan]'" :value="room.harga_bulanan">
                                            </div>
                                        </div>
                                        {{-- Tahunan --}}
                                        <div>
                                            <span class="block text-[9px] font-bold text-slate-500 mb-1 ml-1 uppercase tracking-wider">Tahunan</span>
                                            <div class="relative">
                                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-black text-[#1d6fa5] text-xs pointer-events-none">Rp</span>
                                                <input type="text"
                                                    @input="
                                                        const raw = $event.target.value.replace(/\D/g, '');
                                                        room.harga_tahunan = raw !== '' ? Number(raw) : '';
                                                        $event.target.value = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
                                                    "
                                                    :class="(!room.harga_tahunan || Number(room.harga_tahunan) <= 0) ? 'v-error' : 'v-success'"
                                                    class="w-full pl-9 pr-3 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none font-bold text-sm">
                                                <input type="hidden" :name="'rooms[' + rIdx + '][harga_tahunan]'" :value="room.harga_tahunan">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Slider — multiple rooms --}}
                    <div x-show="jumlahLapangan > 1 && !allSame">
                        {{-- Slider Navigation --}}
                        <div class="room-slider-nav">
                            <button type="button" @click="prevRoom()" :disabled="currentRoomIndex === 0" class="room-nav-btn">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Previous
                            </button>

                            <div class="flex items-center gap-3">
                                <span class="text-xs font-black text-slate-500 whitespace-nowrap" x-text="(tipe === 'lapangan' ? 'Lapangan ' : 'Kolam ') + (currentRoomIndex + 1) + ' / ' + rooms.length"></span>
                                <div class="room-dots">
                                    <template x-for="(_, dIdx) in rooms" :key="dIdx">
                                        <button type="button"
                                            @click="currentRoomIndex = dIdx"
                                            :class="dIdx === currentRoomIndex ? 'active' : ''"
                                            class="room-dot"></button>
                                    </template>
                                </div>
                            </div>

                            <button type="button" @click="nextRoom()" :disabled="currentRoomIndex === rooms.length - 1" class="room-nav-btn">
                                Next
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Room cards --}}
                        <template x-for="(room, rIdx) in rooms" :key="rIdx">
                            <div x-show="currentRoomIndex === rIdx" class="room-card space-y-5" @change="syncPaketHarian()" @input="syncPaketHarian()">
                                {{-- Room header --}}
                                <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400" x-text="tipe === 'lapangan' ? 'Lapangan' : 'Kolam'"></span>
                                    <span class="text-xs font-black text-[#1d6fa5]" x-text="rIdx + 1"></span>
                                </div>

                                {{-- Tipe — hanya muncul jika lebih dari 1 dan spesifikasi beda --}}
                                <div x-show="jumlahLapangan > 1 && !allSame">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2" x-text="tipe === 'lapangan' ? 'Tipe Lapangan' : 'Tipe Kolam'"></label>
                                    <div class="flex flex-col gap-2">
                                        <div class="flex flex-wrap gap-1.5" x-show="rooms[rIdx].tipe.length">
                                            <template x-for="(tag, tIdx) in rooms[rIdx].tipe" :key="tIdx">
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-700 bg-slate-100 border border-slate-200 rounded-lg px-2 py-1 group/tag">
                                                    <span x-text="tag"></span>
                                                    <button type="button" @click="removeTipeTag(rooms[rIdx], tIdx)" class="text-red-300 hover:text-red-600 opacity-0 group-hover/tag:opacity-100 transition-opacity">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </span>
                                            </template>
                                        </div>
                                        <input type="text" @keydown.enter.prevent="addTipeTag(rooms[rIdx], $event)" @input.stop placeholder="Ketik lalu Enter..." class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-semibold outline-none focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] transition-all">
                                    </div>
                                </div>

                                {{-- Ukuran --}}
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Ukuran</label>
                                    <div class="flex items-center gap-2">
                                        <div class="relative flex-1">
                                            <input type="number" x-model="room.panjang" min="0" step="0.1" placeholder="0" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none font-bold text-sm text-center">
                                        </div>
                                        <span class="text-lg font-black text-slate-400 flex-shrink-0">×</span>
                                        <div class="relative flex-1">
                                            <input type="number" x-model="room.lebar" min="0" step="0.1" placeholder="0" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none font-bold text-sm text-center">
                                        </div>
                                        <span class="text-xs font-black text-slate-400 flex-shrink-0 whitespace-nowrap">m²</span>
                                    </div>
                                </div>

                                {{-- Foto --}}
                                <div class="grid grid-cols-3 gap-2" x-show="jumlahLapangan > 1 && !allSame">
                                    <template x-for="fIdx in [0, 1, 2]" :key="fIdx">
                                        <div class="room-foto-slot">
                                            <img :src="room.fotoPreviews[fIdx]" class="absolute inset-0 w-full h-full object-cover z-10 rounded-[inherit]" x-show="room.fotoPreviews[fIdx]" alt="">
                                            <div class="relative z-20 flex flex-col items-center" x-show="!room.fotoPreviews[fIdx]">
                                                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </div>
                                            <input type="file" accept="image/*"
                                                :name="'room_fotos[' + rIdx + '][' + fIdx + ']'"
                                                class="room-foto-input absolute inset-0 opacity-0 cursor-pointer z-30"
                                                @change="handleRoomFoto($event, rIdx, fIdx)">
                                        </div>
                                    </template>
                                </div>

                                {{-- Fasilitas --}}
                                <div x-show="jumlahLapangan > 1" class="space-y-3">
                                    <div class="grid grid-cols-5 sm:grid-cols-5 gap-2">
                                        <template x-for="(item, fIdx) in room.fasilitasKeys" :key="item.key">
                                            <div class="relative group flex flex-col items-center bg-white border border-slate-200 rounded-xl px-2 py-3 transition-all duration-200 hover:border-[#1d6fa5] hover:shadow-sm">
                                                <span class="text-[8px] font-black text-slate-500 uppercase tracking-wider mb-1 leading-tight text-center" x-text="item.label"></span>
                                                <input type="number" x-model.number="room.fasilitas[item.key]" min="0" placeholder="0"
                                                    class="w-12 h-7 text-center bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none font-bold text-xs">
                                                <button type="button" @click="removeFasilitas(room, fIdx)"
                                                    class="absolute -top-1.5 -right-1.5 w-5 h-5 flex items-center justify-center rounded-full bg-red-500/80 text-white text-xs opacity-0 group-hover:opacity-100 transition-all">&times;</button>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="flex gap-2">
                                        <input type="text" x-model="room.newFasilitasLabel" @keydown.enter.prevent="addFasilitas(room)"
                                            placeholder="Tambah fasilitas..."
                                            class="flex-1 px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-[10px] font-bold outline-none focus:border-[#1d6fa5] transition-all">
                                        <button type="button" @click="addFasilitas(room)"
                                            class="px-4 py-2 bg-[#1d6fa5] text-white rounded-xl hover:bg-slate-800 transition-all font-black text-sm">+</button>
                                    </div>
                                </div>

                                {{-- Harga 2x2 --}}
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Harga</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        {{-- Harian/Jam --}}
                                        <div>
                                            <span class="block text-[9px] font-bold text-slate-500 mb-1 ml-1 uppercase tracking-wider" x-text="tipe === 'lapangan' ? 'Jam' : 'Harian'"></span>
                                            <div class="relative">
                                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-black text-[#1d6fa5] text-xs pointer-events-none">Rp</span>
                                                <input type="text"
                                                    @input="
                                                        const raw = $event.target.value.replace(/\D/g, '');
                                                        room.harga_harian = raw !== '' ? Number(raw) : '';
                                                        $event.target.value = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
                                                    "
                                                    :class="(!room.harga_harian || Number(room.harga_harian) <= 0) ? 'v-error' : 'v-success'"
                                                    class="w-full pl-9 pr-3 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none font-bold text-sm">
                                                <input type="hidden" :name="'rooms[' + rIdx + '][harga_harian]'" :value="room.harga_harian">
                                            </div>
                                        </div>
                                        <div>
                                            <span class="block text-[9px] font-bold text-slate-500 mb-1 ml-1 uppercase tracking-wider">Mingguan</span>
                                            <div class="relative">
                                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-black text-[#1d6fa5] text-xs pointer-events-none">Rp</span>
                                                <input type="text"
                                                    @input="
                                                        const raw = $event.target.value.replace(/\D/g, '');
                                                        room.harga_mingguan = raw !== '' ? Number(raw) : '';
                                                        $event.target.value = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
                                                    "
                                                    :class="(!room.harga_mingguan || Number(room.harga_mingguan) <= 0) ? 'v-error' : 'v-success'"
                                                    class="w-full pl-9 pr-3 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none font-bold text-sm">
                                                <input type="hidden" :name="'rooms[' + rIdx + '][harga_mingguan]'" :value="room.harga_mingguan">
                                            </div>
                                        </div>
                                        <div>
                                            <span class="block text-[9px] font-bold text-slate-500 mb-1 ml-1 uppercase tracking-wider">Bulanan</span>
                                            <div class="relative">
                                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-black text-[#1d6fa5] text-xs pointer-events-none">Rp</span>
                                                <input type="text"
                                                    @input="
                                                        const raw = $event.target.value.replace(/\D/g, '');
                                                        room.harga_bulanan = raw !== '' ? Number(raw) : '';
                                                        $event.target.value = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
                                                    "
                                                    :class="(!room.harga_bulanan || Number(room.harga_bulanan) <= 0) ? 'v-error' : 'v-success'"
                                                    class="w-full pl-9 pr-3 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none font-bold text-sm">
                                                <input type="hidden" :name="'rooms[' + rIdx + '][harga_bulanan]'" :value="room.harga_bulanan">
                                            </div>
                                        </div>
                                        <div>
                                            <span class="block text-[9px] font-bold text-slate-500 mb-1 ml-1 uppercase tracking-wider">Tahunan</span>
                                            <div class="relative">
                                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-black text-[#1d6fa5] text-xs pointer-events-none">Rp</span>
                                                <input type="text"
                                                    @input="
                                                        const raw = $event.target.value.replace(/\D/g, '');
                                                        room.harga_tahunan = raw !== '' ? Number(raw) : '';
                                                        $event.target.value = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
                                                    "
                                                    :class="(!room.harga_tahunan || Number(room.harga_tahunan) <= 0) ? 'v-error' : 'v-success'"
                                                    class="w-full pl-9 pr-3 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none font-bold text-sm">
                                                <input type="hidden" :name="'rooms[' + rIdx + '][harga_tahunan]'" :value="room.harga_tahunan">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ═══════════════════════════════════════════════════════════════
                     ADDITIONAL FIELDS
                ═══════════════════════════════════════════════════════════════ --}}

                {{-- Max Durasi / Kapasitas --}}
                {{-- Max Durasi Sewa (4-column grid) --}}
                <div x-show="tipe === 'lapangan' || tipe === 'kolam_renang'" x-cloak>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Max Durasi Sewa</label>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">

                        {{-- Max Durasi Jam/Hari --}}
                        <div>
                            <label class="block text-[10px] font-black text-[#1265A8] uppercase tracking-widest mb-2 ml-1" x-text="tipe === 'lapangan' ? 'Jam' : 'Hari'"></label>
                            <input type="number" name="max_durasi_hari" min="0" value="0"
                                class="w-full px-6 py-3 text-center bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none shadow-sm font-semibold text-gray-800 transition-all duration-300">
                        </div>

                        {{-- Max Durasi Minggu --}}
                        <div>
                            <label class="block text-[10px] font-black text-[#1265A8] uppercase tracking-widest mb-2 ml-1">Minggu</label>
                            <input type="number" name="max_durasi_minggu" min="0" value="0"
                                class="w-full px-6 py-3 text-center bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none shadow-sm font-semibold text-gray-800 transition-all duration-300">
                        </div>

                        {{-- Max Durasi Bulan --}}
                        <div>
                            <label class="block text-[10px] font-black text-[#1265A8] uppercase tracking-widest mb-2 ml-1">Bulan</label>
                            <input type="number" name="max_durasi_bulan" min="0" value="0"
                                class="w-full px-6 py-3 text-center bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none shadow-sm font-semibold text-gray-800 transition-all duration-300">
                        </div>

                        {{-- Max Durasi Tahun --}}
                        <div>
                            <label class="block text-[10px] font-black text-[#1265A8] uppercase tracking-widest mb-2 ml-1">Tahun</label>
                            <input type="number" name="max_durasi_tahun" min="0" value="0"
                                class="w-full px-6 py-3 text-center bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none shadow-sm font-semibold text-gray-800 transition-all duration-300">
                        </div>

                    </div>
                </div>

                {{-- Labels / Fitur --}}
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Labels / Fitur</label>
                    <div class="flex flex-wrap gap-2 mb-3">
                        <template x-for="label in labels[tipe]" :key="label">
                            <div class="relative group">
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="labels[]" :value="label" x-model="selectedLabels" class="hidden">
                                    <span :class="selectedLabels.includes(label) ? 'bg-[#1d6fa5] text-white border-[#1d6fa5]' : 'bg-white text-slate-400 border-slate-200'"
                                        class="px-4 py-2 rounded-xl border text-[10px] font-black uppercase tracking-widest transition-all duration-300 block"
                                        x-text="label"></span>
                                </label>
                                <button type="button" @click="removeLabel(label)"
                                    class="absolute inset-0 flex items-center justify-center rounded-xl bg-red-500/80 text-white text-lg transition-all opacity-0 group-hover:opacity-100">&times;</button>
                            </div>
                        </template>
                    </div>
                    <div class="flex gap-2">
                        <input type="text" x-model="customLabel" @keydown.enter.prevent="addCustomLabel()"
                            placeholder="Tambah fitur custom..."
                            class="flex-1 px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-[10px] font-bold outline-none focus:border-[#1d6fa5] transition-all">
                        <button type="button" @click="addCustomLabel()"
                            class="px-4 py-2 bg-[#1d6fa5] text-white rounded-xl hover:bg-slate-800 transition-all font-black text-sm">+</button>
                    </div>
                </div>

                {{-- Action buttons --}}
                <div class="flex flex-col sm:flex-row-reverse gap-4 pt-6 mt-4 border-t border-slate-100/50">
                    <button type="submit" id="btnSimpan"
                        class="group relative w-full sm:w-2/3 overflow-hidden rounded-2xl bg-[#1d6fa5] px-8 py-5 transition-all duration-300 hover:bg-slate-800 hover:shadow-[0_20px_40px_-12px_rgba(29,111,165,0.35)] active:scale-[0.98]">
                        <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-500 group-hover:translate-x-full"></div>
                        <div class="relative flex items-center justify-center gap-3">
                            <svg id="spinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span id="btnText" class="text-sm font-black uppercase tracking-[0.2em] text-white">Simpan Data</span>
                            <svg id="btnIcon" class="h-5 w-5 text-blue-400 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </div>
                    </button>

                    <div class="w-full sm:w-1/3">
                        <a href="#" id="btn-batal-venue"
                            class="group w-full flex items-center justify-center gap-2 py-5 px-8 rounded-2xl border-2 border-slate-100 bg-white transition-all duration-300 hover:border-red-100 hover:bg-red-50 active:scale-[0.98] relative overflow-hidden cursor-pointer no-underline">
                            <div id="loader-batal-venue" class="absolute inset-0 flex items-center justify-center bg-red-50 opacity-0 invisible transition-all duration-300">
                                <svg class="animate-spin h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                            <div id="text-batal-venue" class="flex items-center gap-2 transition-all duration-300">
                                <span class="text-xs font-black uppercase tracking-widest text-slate-400 group-hover:text-red-500">Batal</span>
                            </div>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Loading overlay --}}
    <div id="loadingOverlay" class="fixed inset-0 z-[100] flex items-center justify-center hidden bg-slate-50/60 backdrop-blur-sm transition-all duration-500">
        <div class="flex flex-col items-center">
            <div class="relative w-16 h-16 mb-8">
                <div class="absolute inset-0 border-4 border-slate-200 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-[#1d6fa5] border-t-transparent rounded-full animate-spin"></div>
            </div>
            <div class="text-center">
                <h3 class="text-lg font-medium text-slate-800 tracking-tight">Memproses data</h3>
                <div class="flex justify-center gap-1 mt-2">
                    <span class="w-1.5 h-1.5 bg-[#1d6fa5] rounded-full animate-bounce [animation-delay:-0.3s]"></span>
                    <span class="w-1.5 h-1.5 bg-[#1d6fa5] rounded-full animate-bounce [animation-delay:-0.15s]"></span>
                    <span class="w-1.5 h-1.5 bg-[#1d6fa5] rounded-full animate-bounce"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast stack --}}
    <div id="v-toast-stack" aria-live="polite" aria-label="Notifikasi"></div>

    {{-- Custom confirm dialog --}}
    <div id="v-confirm-overlay" role="dialog" aria-modal="true" aria-labelledby="v-confirm-title">
        <div id="v-confirm-box">
            <div class="v-confirm-icon-wrap" id="v-confirm-icon-wrap"></div>
            <div class="v-confirm-title" id="v-confirm-title"></div>
            <div class="v-confirm-text"  id="v-confirm-text"></div>
            <div class="v-confirm-btns">
                <button class="v-confirm-btn-cancel" id="v-confirm-cancel"></button>
                <button class="v-confirm-btn-ok"     id="v-confirm-ok"></button>
            </div>
        </div>
    </div>

    <script>
    window.kamarStep = function (delta) {
        const input  = document.getElementById('jumlahLapangan');
        if (!input) return;
        const cur  = parseInt(input.value) || 1;
        const next = Math.min(999, Math.max(1, cur + delta));
        input.value = next;
        window.kamarSyncUI(next);
    };

    window.kamarOnInput = function (el) {
        let val = parseInt(el.value);
        if (isNaN(val) || val < 1) val = 1;
        if (val > 999) val = 999;
        el.value = val;
        window.kamarSyncUI(val);
    };

    window.kamarSyncUI = function (val) {
        const btnMin = document.getElementById('btnKamarMinus');
        const badge  = document.getElementById('kamarBadgeText');
        const hint   = document.getElementById('hint-kamar');
        const wrap   = document.getElementById('kamarStepperWrap');
        const tipe   = document.querySelector('input[name="tipe"]')?.value || 'lapangan';
        const label  = tipe === 'lapangan' ? 'lapangan' : 'kolam';

        if (btnMin) btnMin.disabled = (val <= 1);
        if (badge)  badge.textContent = val + ' ' + label + ' tersedia';

        if (wrap && val >= 1) {
            wrap.classList.remove('v-error');
        }
        if (hint && val >= 1) {
            hint.className = 'v-hint';
        }

        document.dispatchEvent(new CustomEvent('kamar-changed', { detail: { value: val } }));
    };

    document.addEventListener('DOMContentLoaded', () => {

        const MAX_FILE_SIZE = 2 * 1024 * 1024;
        const MAX_POST_SIZE = 8 * 1024 * 1024;

        const $ = id => document.getElementById(id);

        function formatBytes(bytes) {
            const k = 1024, s = ['Bytes','KB','MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return `${parseFloat((bytes / Math.pow(k, i)).toFixed(2))} ${s[i]}`;
        }

        function checkTotalSize() {
            let total = 0;
            if ($('fileInput').files[0]) total += $('fileInput').files[0].size;
            for (let i = 0; i < 3; i++) {
                const g = $('galleryInput' + i);
                if (g && g.files[0]) total += g.files[0].size;
            }
            document.querySelectorAll('.room-foto-input').forEach(el => {
                if (el.files[0]) total += el.files[0].size;
            });
            return total;
        }

        window.kamarSyncUI(1);

        function showToast(type, title, msg, duration = 4500) {
            const stack = $('v-toast-stack');
            const icons = {
                error:   `<svg class="v-toast-icon" style="color:#E24B4A" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>`,
                success: `<svg class="v-toast-icon" style="color:#639922" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>`,
                warning: `<svg class="v-toast-icon" style="color:#BA7517" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>`,
            };
            const t = document.createElement('div');
            t.className = `v-toast toast-${type}`;
            t.innerHTML = `
                ${icons[type]}
                <div class="v-toast-body">
                    <div class="v-toast-title">${title}</div>
                    <div class="v-toast-msg">${msg}</div>
                </div>
                <button class="v-toast-close" aria-label="Tutup">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <div class="v-toast-progress" style="animation-duration:${duration}ms"></div>
            `;
            stack.appendChild(t);
            const close = () => {
                t.classList.add('removing');
                setTimeout(() => t.remove(), 280);
            };
            t.querySelector('.v-toast-close').onclick = close;
            setTimeout(close, duration);
        }

        function showConfirm({ icon, iconBg, iconColor, title, text, okLabel, okDanger, cancelLabel }) {
            return new Promise(resolve => {
                const overlay  = $('v-confirm-overlay');
                const iconWrap = $('v-confirm-icon-wrap');

                const svgs = {
                    question: `<svg width="28" height="28" fill="none" stroke="${iconColor}" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><circle cx="12" cy="17" r=".5" fill="${iconColor}"/></svg>`,
                    warning:  `<svg width="28" height="28" fill="none" stroke="${iconColor}" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`,
                };

                iconWrap.style.background = iconBg;
                iconWrap.innerHTML = svgs[icon] || svgs.question;
                $('v-confirm-title').textContent = title;
                $('v-confirm-text').textContent  = text;

                const btnOk     = $('v-confirm-ok');
                const btnCancel = $('v-confirm-cancel');
                btnOk.textContent     = okLabel     || 'OK';
                btnCancel.textContent = cancelLabel || 'Batal';
                btnOk.className = 'v-confirm-btn-ok' + (okDanger ? ' danger' : '');

                overlay.classList.add('show');

                const onOk = () => { cleanup(); resolve(true);  };
                const onNo = () => { cleanup(); resolve(false); };
                const onBg = (e) => { if (e.target === overlay) onNo(); };

                btnOk.addEventListener('click', onOk);
                btnCancel.addEventListener('click', onNo);
                overlay.addEventListener('click', onBg);

                function cleanup() {
                    overlay.classList.remove('show');
                    btnOk.removeEventListener('click', onOk);
                    btnCancel.removeEventListener('click', onNo);
                    overlay.removeEventListener('click', onBg);
                }
            });
        }

        function setFieldState(inputEl, state, hintEl, hintText, hintClass, iconOkEl, iconErrEl) {
            inputEl.classList.remove('v-error', 'v-success');
            if (iconOkEl) iconOkEl.classList.remove('show');
            if (iconErrEl) iconErrEl.classList.remove('show');

            if (state === 'error') {
                inputEl.classList.add('v-error');
                if (iconErrEl) iconErrEl.classList.add('show');
            } else if (state === 'success') {
                inputEl.classList.add('v-success');
                if (iconOkEl) iconOkEl.classList.add('show');
            }

            if (hintEl) {
                hintEl.className = 'v-hint';
                if (hintText) {
                    hintEl.textContent = hintText;
                    hintEl.classList.add('show', hintClass || 'hint-error');
                }
            }
        }

        function clearField(inputEl, hintEl, iconOkEl, iconErrEl) {
            inputEl.classList.remove('v-error', 'v-success');
            if (hintEl)    { hintEl.className = 'v-hint'; }
            if (iconOkEl)  iconOkEl.classList.remove('show');
            if (iconErrEl) iconErrEl.classList.remove('show');
        }

        /* ── NAMA FASILITAS ── */
        const namaInput   = $('namaFasilitas');
        const hintNama    = $('hint-nama');
        const iconNamaOk  = $('icon-nama-ok');
        const iconNamaErr = $('icon-nama-err');
        const pbWrap      = $('pb-nama-wrap');
        const pb          = $('pb-nama');
        const rgxNama     = /^[a-zA-Z\s]+$/;

        namaInput.addEventListener('input', function () {
            const val = this.value;
            const len = val.trim().length;

            if (!val) {
                clearField(namaInput, hintNama, iconNamaOk, iconNamaErr);
                pbWrap.classList.remove('show');
                return;
            }

            pbWrap.classList.add('show');
            pb.style.width = Math.min(len / 60 * 100, 100) + '%';

            if (!rgxNama.test(val)) {
                pb.style.backgroundColor = '#E24B4A';
                setFieldState(namaInput, 'error', hintNama, 'Hanya huruf dan spasi yang diperbolehkan.', 'hint-error', iconNamaOk, iconNamaErr);
            } else if (len < 2) {
                pb.style.backgroundColor = '#EF9F27';
                setFieldState(namaInput, 'error', hintNama, 'Minimal 2 karakter.', 'hint-warning', iconNamaOk, iconNamaErr);
            } else {
                pb.style.backgroundColor = '#97C459';
                setFieldState(namaInput, 'success', hintNama, 'Nama terlihat bagus!', 'hint-success', iconNamaOk, iconNamaErr);
            }
        });

        /* ── DESKRIPSI ── */
        const descInput = $('deskripsiFasilitas');
        const hintDesc  = $('hint-desc');
        const ccDesc    = $('cc-desc');

        descInput.addEventListener('input', function () {
            const left = 200 - this.value.length;
            ccDesc.textContent = left;
            ccDesc.className = 'v-char-counter' + (left < 10 ? ' danger' : left < 30 ? ' warn' : '');

            if (this.value.trim()) {
                setFieldState(descInput, 'success', hintDesc, '', '');
            } else {
                clearField(descInput, hintDesc, null, null);
            }
        });

        /* ── JAM OPERASIONAL ── */
        const jamInput = $('jamOperasional');
        const hintJam  = $('hint-jam');
        const rgxJam   = /^\d{2}\.\d{2}\s*-\s*\d{2}\.\d{2}$/;

        jamInput.addEventListener('input', function () {
            const val = this.value.trim();
            if (!val) {
                clearField(this, hintJam, null, null);
                return;
            }
            if (rgxJam.test(val)) {
                setFieldState(this, 'success', hintJam, 'Format jam OK', 'hint-success', null, null);
            } else {
                setFieldState(this, 'error', hintJam, 'Gunakan format: 08.00 - 22.00', 'hint-error', null, null);
            }
        });

        /* ── THUMBNAIL ── */
        const fileInput  = $('fileInput');
        const preview    = $('preview');
        const uiContent  = $('ui-content');
        const dropzone   = $('dropzone');
        const hintThumb  = $('hint-thumb');

        fileInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            if (file.size > MAX_FILE_SIZE) {
                showToast('warning', 'File terlalu besar',
                    `Ukuran (${formatBytes(file.size)}) melebihi batas 2MB. Pilih file yang lebih kecil.`);
                this.value = '';
                dropzone.classList.add('v-dz-error');
                setFieldState(dropzone, 'error', hintThumb, 'File melebihi 2MB.', 'hint-error', null, null);
                return;
            }

            dropzone.classList.remove('v-dz-error');
            clearField(dropzone, hintThumb, null, null);

            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                uiContent.classList.add('opacity-0');
                dropzone.classList.remove('border-dashed');
                dropzone.classList.add('border-solid', 'border-[#1d6fa5]');
            };
            reader.readAsDataURL(file);
        });

        /* ── GALLERY VALIDATION ── */
        window.validateGalleryFile = function (input, index) {
            const file = input.files[0];
            if (file && file.size > MAX_FILE_SIZE) {
                showToast('warning', `Foto galeri ${index + 1} terlalu besar`,
                    `Ukuran (${formatBytes(file.size)}) melebihi batas 2MB.`);
                input.value = '';
                return false;
            }
            return true;
        };

        /* ── ROOM FOTO VALIDATION ── */
        window.validateRoomFoto = function (input, fotoIndex) {
            const file = input.files[0];
            if (file && file.size > MAX_FILE_SIZE) {
                showToast('warning', `Foto kamar ${fotoIndex + 1} terlalu besar`,
                    `Ukuran (${formatBytes(file.size)}) melebihi batas 2MB.`);
                input.value = '';
                return false;
            }
            return true;
        };

        /* ── FORM SUBMIT ── */
        $('mainForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const currentTipe = this.querySelector('input[name="tipe"]').value;
            let errors = [];

            // Nama
            const namaVal = namaInput.value.trim();
            if (!namaVal || !rgxNama.test(namaVal) || namaVal.length < 2) {
                const msg = !namaVal ? 'Nama wajib diisi.'
                          : !rgxNama.test(namaVal) ? 'Nama hanya boleh berisi huruf dan spasi.'
                          : 'Nama minimal 2 karakter.';
                setFieldState(namaInput, 'error', hintNama, msg, 'hint-error', iconNamaOk, iconNamaErr);
                errors.push('Nama Fasilitas');
            }

            // Deskripsi
            if (!descInput.value.trim()) {
                setFieldState(descInput, 'error', hintDesc, 'Deskripsi singkat wajib diisi.', 'hint-error', null, null);
                errors.push('Deskripsi Singkat');
            }

            // Jam Operasional
            if (!jamInput.value.trim()) {
                setFieldState(jamInput, 'error', hintJam, 'Jam operasional wajib diisi.', 'hint-error', null, null);
                errors.push('Jam Operasional');
            } else if (!rgxJam.test(jamInput.value.trim())) {
                setFieldState(jamInput, 'error', hintJam, 'Gunakan format: 08.00 - 22.00', 'hint-error', null, null);
                errors.push('Jam Operasional');
            } else {
                setFieldState(jamInput, 'success', hintJam, '', '');
            }

            // Kapasitas (khusus Lapangan & Kolam)
            if (currentTipe === 'lapangan' || currentTipe === 'kolam_renang') {

                const kamarInput = $('jumlahLapangan');
                const kamarWrap  = $('kamarStepperWrap');
                const hintKamar  = $('hint-kamar');
                const kamarVal   = parseInt(kamarInput?.value);
                const label = currentTipe === 'lapangan' ? 'Lapangan' : 'Kolam';
                if (!kamarInput || isNaN(kamarVal) || kamarVal < 1) {
                    kamarWrap.classList.add('v-error');
                    if (hintKamar) {
                        hintKamar.textContent = 'Jumlah ' + label.toLowerCase() + ' minimal 1.';
                        hintKamar.className   = 'v-hint show hint-error';
                    }
                    errors.push('Jumlah ' + label);
                }
            }

            // Foto utama
            if (!fileInput.files[0]) {
                dropzone.classList.add('v-dz-error');
                setFieldState(dropzone, 'error', hintThumb, 'Foto utama (thumbnail) wajib diunggah.', 'hint-error', null, null);
                errors.push('Foto Utama');
            }

            // Harga sewa & tipe room
            if (currentTipe === 'lapangan' || currentTipe === 'kolam_renang') {
                const alpine = window.__alpineRoot;
                if (alpine && alpine.rooms) {
                    const missingPrice = alpine.rooms.some(r =>
                        !r.harga_harian || Number(r.harga_harian) <= 0 ||
                        !r.harga_mingguan || Number(r.harga_mingguan) <= 0 ||
                        !r.harga_bulanan || Number(r.harga_bulanan) <= 0 ||
                        !r.harga_tahunan || Number(r.harga_tahunan) <= 0
                    );
                    if (missingPrice) errors.push('Harga Sewa');
                    if (alpine.jumlahLapangan > 1 && !alpine.allSame) {
                        const missingTipe = alpine.rooms.some(r => {
                            if (Array.isArray(r.tipe)) return r.tipe.length === 0 || r.tipe.every(t => !t.trim());
                            return !r.tipe || r.tipe.trim() === '';
                        });
                        if (missingTipe) errors.push('Tipe ' + (currentTipe === 'lapangan' ? 'Lapangan' : 'Kolam'));
                    }
                }
            }

            // Total ukuran file
            const totalSize = checkTotalSize();
            if (totalSize > MAX_POST_SIZE) {
                showToast('error', 'Total file terlalu besar',
                    `Total (${formatBytes(totalSize)}) melebihi batas server 8MB. Perkecil ukuran foto.`, 6000);
                errors.push('Ukuran File');
            }

            if (errors.length > 0) {
                showToast('error',
                    `${errors.length} field belum valid`,
                    'Periksa: ' + errors.join(', ') + '.');
                const firstErr = document.querySelector('.v-error, .v-dz-error');
                if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            const confirmed = await showConfirm({
                icon: 'question',
                iconBg: '#e6f1fb',
                iconColor: '#1d6fa5',
                title: 'Simpan Data?',
                text: 'Pastikan semua informasi venue sudah benar sebelum disimpan.',
                okLabel: 'Ya, Simpan',
                cancelLabel: 'Periksa Lagi',
            });

            if (confirmed) eksekusiSimpanData();
        });

        function eksekusiSimpanData() {
            if (window.__alpineRoot && window.__alpineRoot.syncPaketHarian) {
                window.__alpineRoot.syncPaketHarian();
            }

            const form     = $('mainForm');
            const overlay  = $('loadingOverlay');
            const btnSimpan = $('btnSimpan');
            const btnText  = $('btnText');
            const btnIcon  = $('btnIcon');
            const spinner  = $('spinner');

            overlay.classList.remove('hidden');
            btnSimpan.disabled = true;
            spinner.classList.remove('hidden');
            btnText.textContent = 'Menyimpan...';
            btnIcon.classList.add('hidden');

            const formData = new FormData(form);

            const pending = (window.__alpineRoot && window.__alpineRoot._pendingFotos) ? window.__alpineRoot._pendingFotos : {};
            Object.keys(pending).forEach(function(rIdx) {
                Object.keys(pending[rIdx]).forEach(function(fIdx) {
                    var file = pending[rIdx][fIdx];
                    var key  = 'room_fotos[' + rIdx + '][' + fIdx + ']';
                    formData.set(key, file, file.name);
                });
            });

            fetch('/admin/fasilitas/store', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const ct   = response.headers.get('content-type') || '';
                const data = ct.includes('application/json') ? await response.json() : null;
                if (!response.ok) throw new Error(data?.message || `Server error: ${response.status}`);
                return data;
            })
            .then(data => {
                overlay.classList.add('hidden');
                if (data?.success) {
                    showToast('success', 'Data tersimpan!', data.message || 'Fasilitas berhasil ditambahkan.');
                    setTimeout(() => { window.location.href = '/admin/dashboard/dataFasilitas'; }, 1500);
                }
            })
            .catch(error => {
                overlay.classList.add('hidden');
                showToast('error', 'Gagal menyimpan', error.message || 'Terjadi kesalahan sistem.', 6000);
                btnSimpan.disabled = false;
                spinner.classList.add('hidden');
                btnText.textContent = 'Simpan Data';
                btnIcon.classList.remove('hidden');
            });
        }

        /* ── TOMBOL BATAL ── */
        const btnBatal = $('btn-batal-venue');

        if (btnBatal) {
            btnBatal.addEventListener('click', async function (e) {
                e.preventDefault();

                const confirmed = await showConfirm({
                    icon: 'warning',
                    iconBg: '#FCEBEB',
                    iconColor: '#E24B4A',
                    title: 'Batalkan Pengisian?',
                    text: 'Data yang sudah diisi tidak akan tersimpan.',
                    okLabel: 'Ya, Batalkan',
                    okDanger: true,
                    cancelLabel: 'Kembali',
                });

                if (confirmed) {
                    const loader = $('loader-batal-venue');
                    const text   = $('text-batal-venue');
                    text.classList.add('opacity-0', 'scale-95');
                    loader.classList.remove('invisible', 'opacity-0');
                    this.classList.add('pointer-events-none');

                    setTimeout(() => {
                        if (history.length > 1) {
                            history.back();
                        } else {
                            window.location.href = '/admin/dashboard/dataFasilitas';
                        }
                    }, 700);
                }
            });
        }

    });

    document.addEventListener('alpine:init', () => {

        Alpine.data('facilityCreator', () => ({
            tipe: 'lapangan',
            labels: {
                lapangan: ['Lampu Penerangan', 'Parkir', 'Toilet', 'Mushola', 'Kantin', 'Tempat Duduk'],
                kolam_renang:   ['Loker', 'Bilik Bilas', 'Toilet', 'Lifeguard', 'Tempat Duduk', 'Parkir', 'Mushola']
            },
            selectedLabels: [],
            customLabel: '',
            galleryPreviews: [null, null, null],

            jumlahLapangan: 1,
            rooms: [],
            currentRoomIndex: 0,
            allSame: true,

            defaultFasKeys() {
                if (this.tipe === 'kolam_renang') {
                    return [
                        {key:'loker',label:'Loker'},{key:'bilik_bilas',label:'Bilik Bilas'},
                        {key:'toilet',label:'Toilet'},{key:'lifeguard',label:'Lifeguard'},
                        {key:'tempat_duduk',label:'Tempat Duduk'},{key:'parkir',label:'Parkir'},
                        {key:'mushola',label:'Mushola'},{key:'ruang_ganti',label:'Ruang Ganti'},
                        {key:'wifi',label:'WiFi'},
                    ];
                }
                return [
                    {key:'lampu',label:'Lampu'},{key:'parkir',label:'Parkir'},
                    {key:'toilet',label:'Toilet'},{key:'mushola',label:'Mushola'},
                    {key:'kursi_tribun',label:'Kursi Tribun'},{key:'ruang_ganti',label:'Ruang Ganti'},
                    {key:'papan_skor',label:'Papan Skor'},{key:'sound_system',label:'Sound System'},
                    {key:'air_minum',label:'Air Minum'},{key:'wifi',label:'WiFi'},
                ];
            },

            createEmptyRoom() {
                const fasKeys = this.defaultFasKeys();
                const fas = {};
                fasKeys.forEach(f => { fas[f.key] = 0; });
                return {
                    tipe: [], jumlah: 1, kode_blok: '', foto: [], fotoPreviews: [null, null, null],
                    harga_harian: '', harga_mingguan: '', harga_bulanan: '', harga_tahunan: '',
                    keunggulan: '', panjang: '', lebar: '', newFasilitasLabel: '',
                    fasilitas: fas, fasilitasKeys: [...fasKeys],
                };
            },

            init() {
                window.__alpineRoot = this;
                this.rooms = [this.createEmptyRoom()];

                this.$watch('tipe', (newVal) => {
                    this.rooms = [this.createEmptyRoom()];
                    this.jumlahLapangan = 1;
                    const kamarInput = document.getElementById('jumlahLapangan');
                    if (kamarInput) { kamarInput.value = 1; }
                    this.$nextTick(() => {
                        if (newVal === 'lapangan' || newVal === 'kolam_renang') {
                            const ki = document.getElementById('jumlahLapangan');
                            if (ki) {
                                this.jumlahLapangan = parseInt(ki.value) || 1;
                                this.initRooms();
                            }
                        }
                    });
                });

                this.$watch('allSame', (val) => {
                    if (val && this.jumlahLapangan > 1) this.syncAllSame();
                    this.syncPaketHarian();
                });

                document.addEventListener('kamar-changed', (e) => {
                    this.jumlahLapangan = e.detail.value;
                    this.initRooms();
                });
                this.$nextTick(() => {
                    const kamarInput = document.getElementById('jumlahLapangan');
                    if (kamarInput) {
                        this.jumlahLapangan = parseInt(kamarInput.value) || 1;
                        this.initRooms();
                    }
                });
            },

            initRooms() {
                const target = this.jumlahLapangan;
                while (this.rooms.length < target) {
                    this.rooms.push(this.createEmptyRoom());
                }
                while (this.rooms.length > target) {
                    this.rooms.pop();
                }
                if (this.currentRoomIndex >= this.rooms.length) {
                    this.currentRoomIndex = Math.max(0, this.rooms.length - 1);
                }
                this.syncPaketHarian();
            },

            syncAllSame() {
                if (!this.allSame || this.jumlahLapangan <= 1) return;
                const src = this.rooms[0];
                for (let i = 1; i < this.rooms.length; i++) {
                    this.rooms[i] = {
                        ...src,
                        tipe: [...(src.tipe || [])],
                        foto: [...(src.foto || [])],
                        nomor_lapangan: [...(src.nomor_lapangan || [])],
                        fotoPreviews: [...(src.fotoPreviews || [null, null, null])],
                        fasilitas: { ...(src.fasilitas || {}) },
                    };
                }
            },

            syncPaketHarian() {
                if (this.allSame && this.jumlahLapangan > 1) {
                    this.syncAllSame();
                }
                const payload = this.rooms.map(r => {
                    const { fotoPreviews, fasilitasKeys, newFasilitasLabel, ...rest } = r;
                    rest.harga_harian   = rest.harga_harian   !== '' && rest.harga_harian   != null ? Number(rest.harga_harian)   : 0;
                    rest.harga_mingguan = rest.harga_mingguan !== '' && rest.harga_mingguan != null ? Number(rest.harga_mingguan) : 0;
                    rest.harga_bulanan  = rest.harga_bulanan  !== '' && rest.harga_bulanan  != null ? Number(rest.harga_bulanan)  : 0;
                    rest.harga_tahunan  = rest.harga_tahunan  !== '' && rest.harga_tahunan  != null ? Number(rest.harga_tahunan)  : 0;
                    if (!Array.isArray(rest.tipe)) rest.tipe = rest.tipe ? [rest.tipe] : [];
                    return rest;
                });
                const el = document.getElementById('paketHarianInput');
                if (el) el.value = JSON.stringify(payload);
                const rd = document.getElementById('roomsDataInput');
                if (rd) rd.value = JSON.stringify(payload);
            },

            prevRoom() {
                if (this.currentRoomIndex > 0) {
                    this.currentRoomIndex--;
                }
            },

            nextRoom() {
                if (this.currentRoomIndex < this.rooms.length - 1) {
                    this.currentRoomIndex++;
                }
            },

            addTipeTag(room, event) {
                const val = event.target.value.trim();
                if (!val) return;
                if (!Array.isArray(room.tipe)) room.tipe = [];
                if (!room.tipe.includes(val)) {
                    room.tipe.push(val);
                    this.syncPaketHarian();
                }
                event.target.value = '';
            },
            removeTipeTag(room, idx) {
                if (!Array.isArray(room.tipe)) return;
                room.tipe.splice(idx, 1);
                this.syncPaketHarian();
            },

            handleRoomFoto(event, roomIndex, fotoIndex) {
                const file = event.target.files[0];
                if (!file) return;

                const MAX = 2 * 1024 * 1024;
                if (file.size > MAX) {
                    Swal.fire({
                        title: 'File Terlalu Besar',
                        text: 'Foto lapangan maksimal 2 MB.',
                        icon: 'warning',
                        confirmButtonColor: '#1265A8',
                        confirmButtonText: 'OK',
                        customClass: { popup: 'rounded-[2.5rem] p-8' }
                    });
                    event.target.value = '';
                    return;
                }

                if (!this._pendingFotos) this._pendingFotos = {};
                if (!this._pendingFotos[roomIndex]) this._pendingFotos[roomIndex] = {};
                this._pendingFotos[roomIndex][fotoIndex] = file;

                const reader = new FileReader();
                reader.onload = (e) => {
                    const room = this.rooms[roomIndex];
                    if (!room) return;
                    const previews = Array.isArray(room.fotoPreviews)
                        ? [...room.fotoPreviews]
                        : [null, null, null];
                    while (previews.length < 3) previews.push(null);
                    previews[fotoIndex] = e.target.result;
                    this.rooms[roomIndex] = { ...this.rooms[roomIndex], fotoPreviews: previews };
                    this.syncPaketHarian();
                };
                reader.readAsDataURL(file);
            },

            addCustomLabel() {
                if (this.customLabel.trim() !== '') {
                    const label = this.customLabel.trim();
                    if (!this.labels[this.tipe].includes(label)) this.labels[this.tipe].push(label);
                    if (!this.selectedLabels.includes(label))    this.selectedLabels.push(label);
                    this.customLabel = '';
                }
            },

            removeLabel(label) {
                this.labels[this.tipe] = this.labels[this.tipe].filter(l => l !== label);
                this.selectedLabels = this.selectedLabels.filter(l => l !== label);
            },

            addFasilitas(room) {
                const label = room.newFasilitasLabel?.trim();
                if (!label) return;
                const key = label.toLowerCase().replace(/\s+/g, '_');
                if (room.fasilitasKeys.some(f => f.key === key)) return;
                room.fasilitasKeys.push({ key, label });
                room.fasilitas[key] = 0;
                room.newFasilitasLabel = '';
            },

            removeFasilitas(room, index) {
                const item = room.fasilitasKeys[index];
                if (!item) return;
                room.fasilitasKeys.splice(index, 1);
                delete room.fasilitas[item.key];
            },

        }));


    });
    </script>
</body>
</html>