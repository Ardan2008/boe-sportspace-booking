<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <title>BOE-Space Reserve | Add Fasilitas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* ── Shake animation (error field) ── */
        @keyframes vShake {
            0%,100% { transform: translateX(0); }
            20%      { transform: translateX(-5px); }
            40%      { transform: translateX(5px); }
            60%      { transform: translateX(-4px); }
            80%      { transform: translateX(4px); }
        }

        /* ── Toast slide-in / slide-out ── */
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

        /* ── Field states ── */
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

        /* ── Inline hint (slide-down) ── */
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

        /* ── Field status icon ── */
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

        /* ── Progress bar (nama) ── */
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

        /* ── Character counter ── */
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

        /* ── Toast stack ── */
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

        /* ── Confirm dialog ── */
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

        /* Dropzone error ring */
        #dropzone.v-dz-error { border-color: #E24B4A !important; box-shadow: 0 0 0 4px rgba(226,75,74,.10); }

        .swal2-shown { padding-right: 0 !important; }

        /* ══════════════════════════════
           Stepper Jumlah Kamar
        ══════════════════════════════ */
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
        /* Error state pada wrap */
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

        /* Badge info kamar */
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
    </style>
</head>
<body class="bg-[#F8FAFC] font-sans antialiased text-slate-800">

    {{-- Background blobs --}}
    <div class="fixed top-0 left-0 w-full h-full -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-100 blur-[120px] rounded-full opacity-50"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] bg-indigo-100 blur-[120px] rounded-full opacity-50"></div>
    </div>

    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 flex justify-center items-center" x-data="{
        tipe: 'asrama',
        labels: {
            asrama: ['Shower', 'AC', 'Wifi', 'Parkir', 'TV', 'Lemari'],
            aula:   ['Wifi', 'Sound System', 'AC', 'Kursi', 'Meja', 'Panggung', 'Proyektor']
        },
        selectedLabels: [],
        customLabel: '',
        galleryPreviews: [null, null, null],
        addCustomLabel() {
            if (this.customLabel.trim() !== '') {
                const label = this.customLabel.trim();
                if (!this.labels[this.tipe].includes(label)) this.labels[this.tipe].push(label);
                if (!this.selectedLabels.includes(label))    this.selectedLabels.push(label);
                this.customLabel = '';
            }
        }
    }">
        <div class="w-full max-w-5xl bg-white/80 backdrop-blur-xl rounded-[3rem] shadow-[0_32px_64px_-15px_rgba(0,0,0,0.08)] border border-white overflow-hidden transition-all duration-500 hover:shadow-blue-200/40">

            {{-- Header --}}
            <div class="pt-10 pb-6 px-10 text-center">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 mb-4 bg-blue-50/50 rounded-full border border-blue-100 shadow-sm">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#1d6fa5]"></span>
                    </span>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#1d6fa5]" x-text="'Management Portal | ' + tipe"></span>
                </div>
                <h2 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight uppercase">
                    Add <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#1d6fa5] to-blue-400" x-text="tipe === 'asrama' ? 'Asrama' : 'Aula'"></span> Data
                </h2>
                <div class="h-1 w-12 bg-gradient-to-r from-[#1d6fa5] to-blue-400 mx-auto mt-4 rounded-full"></div>

                {{-- Type Switcher --}}
                <div class="flex justify-center gap-4 mt-8">
                    <button type="button"
                        @click="tipe = 'asrama'; selectedLabels = []"
                        :class="tipe === 'asrama' ? 'bg-[#1d6fa5] text-white shadow-lg' : 'bg-slate-100 text-slate-400 hover:bg-slate-200'"
                        class="px-8 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all duration-300">Asrama</button>
                    <button type="button"
                        @click="tipe = 'aula'; selectedLabels = []"
                        :class="tipe === 'aula' ? 'bg-[#1d6fa5] text-white shadow-lg' : 'bg-slate-100 text-slate-400 hover:bg-slate-200'"
                        class="px-8 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all duration-300">Aula</button>
                </div>
            </div>

            {{-- Form --}}
            <form id="mainForm" action="/admin/fasilitas/store" method="POST" enctype="multipart/form-data"
                  class="p-8 lg:p-12 pt-6" novalidate>
                @csrf
                <input type="hidden" name="tipe" :value="tipe">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

                    {{-- ══════════════════════════════
                         KOLOM KIRI
                    ══════════════════════════════ --}}
                    <div class="space-y-6">

                        {{-- Nama Fasilitas --}}
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

                        {{-- Deskripsi Singkat --}}
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

                        {{-- Detail Fasilitas --}}
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Detail Fasilitas</label>
                            <textarea name="detail" rows="5"
                                class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none shadow-sm resize-none font-medium leading-relaxed"></textarea>
                        </div>

                        {{-- Jam Operasional + Durasi/Kapasitas --}}
                        <div class="grid grid-cols-2 gap-4">
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
                            <div x-show="tipe === 'asrama'" x-cloak>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Max Durasi (Hari)</label>
                                <div class="relative">
                                    <input type="number" name="max_durasi_harian" id="maxDurasi" min="1"
                                        class="v-field w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none shadow-sm font-semibold"
                                        :required="tipe === 'asrama'">
                                </div>
                                <p class="v-hint" id="hint-durasi"></p>
                            </div>
                            <div x-show="tipe === 'aula'" x-cloak>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Kapasitas (Orang)</label>
                                <div class="relative">
                                    <input type="number" name="max_dewasa_aula" id="maxDewasaAula" min="1" placeholder="Total"
                                        class="v-field w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none shadow-sm font-semibold"
                                        :required="tipe === 'aula'">
                                </div>
                                <p class="v-hint" id="hint-kap-aula"></p>
                            </div>
                        </div>

                        {{-- ══════════════════════════════
                            JUMLAH KAMAR 
                        ══════════════════════════════ --}}
                        <div x-show="tipe === 'asrama'" x-cloak>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">
                                Jumlah Kamar Tersedia
                            </label>

                            <div class="kamar-stepper-wrap" id="kamarStepperWrap">

                                {{-- Tombol Kurangi --}}
                                <button type="button"
                                    id="btnKamarMinus"
                                    class="kamar-stepper-btn"
                                    onclick="window.kamarStep(-1)"
                                    aria-label="Kurangi kamar">
                                    −
                                </button>

                                {{-- Input Angka --}}
                                <input
                                    type="number"
                                    name="jumlah_kamar"
                                    id="jumlahKamar"
                                    min="1"
                                    max="999"
                                    value="1"
                                    class="kamar-stepper-input"
                                    oninput="window.kamarOnInput(this)"
                                    aria-label="Jumlah kamar">

                                {{-- Tombol Tambah --}}
                                <button type="button"
                                    id="btnKamarPlus"
                                    class="kamar-stepper-btn"
                                    onclick="window.kamarStep(1)"
                                    aria-label="Tambah kamar">
                                    +
                                </button>

                            </div>

                            {{-- Badge info --}}
                            <div class="kamar-badge">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span id="kamarBadgeText">1 kamar tersedia</span>
                            </div>

                            <p class="v-hint" id="hint-kamar"></p>
                        </div>

                        {{-- Kapasitas Asrama --}}
                        <div x-show="tipe === 'asrama'" x-cloak class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Cap. Dewasa (Kamar)</label>
                                <div class="relative">
                                    <input type="number" name="max_dewasa_asrama" id="maxDewasaAsrama" min="1"
                                        class="v-field w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none shadow-sm font-semibold"
                                        :required="tipe === 'asrama'">
                                </div>
                                <p class="v-hint" id="hint-kap-dewasa"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Cap. Anak (Kamar)</label>
                                <div class="relative">
                                    <input type="number" name="max_anak" min="0"
                                        class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none shadow-sm font-semibold"
                                        :required="tipe === 'asrama'">
                                </div>
                            </div>
                        </div>

                        {{-- Labels / Fitur --}}
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Labels / Fitur</label>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <template x-for="label in labels[tipe]" :key="label">
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="labels[]" :value="label" x-model="selectedLabels" class="hidden">
                                        <span :class="selectedLabels.includes(label) ? 'bg-[#1d6fa5] text-white border-[#1d6fa5]' : 'bg-white text-slate-400 border-slate-200'"
                                            class="px-4 py-2 rounded-xl border text-[10px] font-black uppercase tracking-widest transition-all duration-300 block"
                                            x-text="label"></span>
                                    </label>
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
                    </div>

                    {{-- ══════════════════════════════
                         KOLOM KANAN
                    ══════════════════════════════ --}}
                    <div class="space-y-6">

                        {{-- Biaya --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Biaya Harian</label>
                                <div class="relative">
                                    <span class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-[#1d6fa5] text-sm pointer-events-none">Rp</span>
                                    <input type="text" id="hargaDisplay"
                                        class="v-field w-full pl-12 pr-10 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none font-bold"
                                        required>
                                    <input type="hidden" name="harga" id="hargaReal">
                                    <svg id="icon-harga-ok" class="v-icon text-green-500 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <svg id="icon-harga-err" class="v-icon text-red-500 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                                <p class="v-hint" id="hint-harga"></p>
                            </div>
                            {{-- Biaya Bulanan --}}
                            <div x-show="tipe === 'asrama'" x-cloak>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Biaya Bulanan</label>
                                <div class="relative">
                                    <span class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-[#1d6fa5] text-sm pointer-events-none">Rp</span>
                                    <input type="text" id="hargaBulananDisplay"
                                        class="v-field w-full pl-12 pr-10 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1d6fa5] outline-none font-bold">
                                    <input type="hidden" name="harga_bulanan" id="hargaBulananReal">
                                    <svg id="icon-bulanan-ok" class="v-icon text-green-500 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <svg id="icon-bulanan-err" class="v-icon text-red-500 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                                <p class="v-hint" id="hint-bulanan"></p>
                            </div>
                        </div>

                        {{-- Thumbnail --}}
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

                        {{-- Gallery --}}
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
    /* ═══════════════════════════════════════════════════════════════
       STEPPER JUMLAH KAMAR
       Didefinisikan di window scope (LUAR DOMContentLoaded)
       agar bisa dipanggil dari onclick="window.kamarStep()" di HTML.
    ═══════════════════════════════════════════════════════════════ */

    window.kamarStep = function (delta) {
        const input  = document.getElementById('jumlahKamar');
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

        if (btnMin) btnMin.disabled = (val <= 1);
        if (badge)  badge.textContent = val + ' kamar tersedia';

        // Hapus error state jika nilai sudah valid
        if (wrap && val >= 1) {
            wrap.classList.remove('v-error');
        }
        if (hint && val >= 1) {
            hint.className = 'v-hint';
        }
    };


    document.addEventListener('DOMContentLoaded', () => {

        /* ═══════════════════════════════════════
           UTILITIES
        ═══════════════════════════════════════ */

        const MAX_FILE_SIZE = 2 * 1024 * 1024;  // 2 MB
        const MAX_POST_SIZE = 8 * 1024 * 1024;  // 8 MB

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
            return total;
        }

        // Inisialisasi state awal stepper kamar setelah DOM siap
        window.kamarSyncUI(1);


        /* ═══════════════════════════════════════
           TOAST SYSTEM
        ═══════════════════════════════════════ */

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


        /* ═══════════════════════════════════════
           CONFIRM DIALOG SYSTEM
        ═══════════════════════════════════════ */

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


        /* ═══════════════════════════════════════
           FIELD STATE HELPERS
        ═══════════════════════════════════════ */

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


        /* ═══════════════════════════════════════
           NAMA FASILITAS — real-time + progress bar
        ═══════════════════════════════════════ */

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


        /* ═══════════════════════════════════════
           DESKRIPSI — char counter
        ═══════════════════════════════════════ */

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


        /* ═══════════════════════════════════════
           BIAYA HARIAN — currency masking + real-time
        ═══════════════════════════════════════ */

        const hargaDisplay = $('hargaDisplay');
        const hargaReal    = $('hargaReal');
        const hintHarga    = $('hint-harga');
        const iconHargaOk  = $('icon-harga-ok');
        const iconHargaErr = $('icon-harga-err');

        hargaDisplay.addEventListener('input', function () {
            const raw = this.value.replace(/\D/g, '');
            hargaReal.value = raw;
            this.value = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';

            if (!raw) {
                clearField(hargaDisplay, hintHarga, iconHargaOk, iconHargaErr);
            } else if (parseInt(raw) < 1000) {
                setFieldState(hargaDisplay, 'error', hintHarga, 'Biaya terlalu kecil, minimal Rp 1.000.', 'hint-warning', iconHargaOk, iconHargaErr);
            } else {
                setFieldState(hargaDisplay, 'success', hintHarga, 'Rp ' + new Intl.NumberFormat('id-ID').format(raw), 'hint-success', iconHargaOk, iconHargaErr);
            }
        });


        /* ═══════════════════════════════════════
           BIAYA BULANAN — currency masking + real-time
        ═══════════════════════════════════════ */

        const hargaBulananDisplay = $('hargaBulananDisplay');
        const hargaBulananReal    = $('hargaBulananReal');
        const hintBulanan         = $('hint-bulanan');
        const iconBulananOk       = $('icon-bulanan-ok');
        const iconBulananErr      = $('icon-bulanan-err');

        hargaBulananDisplay.addEventListener('input', function () {
            const raw = this.value.replace(/\D/g, '');
            hargaBulananReal.value = raw;
            this.value = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';

            if (!raw) {
                clearField(hargaBulananDisplay, hintBulanan, iconBulananOk, iconBulananErr);
            } else if (parseInt(raw) < 1000) {
                setFieldState(hargaBulananDisplay, 'error', hintBulanan, 'Biaya terlalu kecil, minimal Rp 1.000.', 'hint-warning', iconBulananOk, iconBulananErr);
            } else {
                setFieldState(hargaBulananDisplay, 'success', hintBulanan, 'Rp ' + new Intl.NumberFormat('id-ID').format(raw), 'hint-success', iconBulananOk, iconBulananErr);
            }
        });


        /* ═══════════════════════════════════════
           THUMBNAIL UPLOAD — preview + dropzone
        ═══════════════════════════════════════ */

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


        /* ═══════════════════════════════════════
           GALLERY FILE VALIDATION (global)
        ═══════════════════════════════════════ */

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


        /* ═══════════════════════════════════════
           FORM SUBMIT — validasi + konfirmasi
        ═══════════════════════════════════════ */

        $('mainForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const currentTipe = this.querySelector('input[name="tipe"]').value;
            let errors = [];

            // ── Nama ──
            const namaVal = namaInput.value.trim();
            if (!namaVal || !rgxNama.test(namaVal) || namaVal.length < 2) {
                const msg = !namaVal ? 'Nama wajib diisi.'
                          : !rgxNama.test(namaVal) ? 'Nama hanya boleh berisi huruf dan spasi.'
                          : 'Nama minimal 2 karakter.';
                setFieldState(namaInput, 'error', hintNama, msg, 'hint-error', iconNamaOk, iconNamaErr);
                errors.push('Nama Fasilitas');
            }

            // ── Deskripsi ──
            if (!descInput.value.trim()) {
                setFieldState(descInput, 'error', hintDesc, 'Deskripsi singkat wajib diisi.', 'hint-error', null, null);
                errors.push('Deskripsi Singkat');
            }

            // ── Jam Operasional ──
            const jamInput = $('jamOperasional');
            const hintJam  = $('hint-jam');
            if (!jamInput.value.trim()) {
                setFieldState(jamInput, 'error', hintJam, 'Jam operasional wajib diisi.', 'hint-error', null, null);
                errors.push('Jam Operasional');
            } else {
                setFieldState(jamInput, 'success', hintJam, '', '');
            }

            // ── Biaya Harian ──
            if (!hargaReal.value || parseInt(hargaReal.value) < 1000) {
                const msg = !hargaReal.value ? 'Biaya harian wajib diisi.' : 'Biaya harian minimal Rp 1.000.';
                setFieldState(hargaDisplay, 'error', hintHarga, msg, 'hint-error', iconHargaOk, iconHargaErr);
                errors.push('Biaya Harian');
            }

            // ── Kapasitas & Kamar (khusus Asrama) ──
            if (currentTipe === 'asrama') {

                // Kapasitas dewasa
                const kdInput = $('maxDewasaAsrama');
                const hintKd  = $('hint-kap-dewasa');
                if (!kdInput || !kdInput.value || parseInt(kdInput.value) < 1) {
                    setFieldState(kdInput, 'error', hintKd, 'Kapasitas dewasa minimal 1.', 'hint-error', null, null);
                    errors.push('Kapasitas Dewasa');
                } else {
                    setFieldState(kdInput, 'success', hintKd, '', '');
                }

                // Jumlah kamar — validasi pada wrap bukan input
                // agar animasi shake terlihat di seluruh stepper
                const kamarInput = $('jumlahKamar');
                const kamarWrap  = $('kamarStepperWrap');
                const hintKamar  = $('hint-kamar');
                const kamarVal   = parseInt(kamarInput?.value);
                if (!kamarInput || isNaN(kamarVal) || kamarVal < 1) {
                    kamarWrap.classList.add('v-error');
                    if (hintKamar) {
                        hintKamar.textContent = 'Jumlah kamar minimal 1.';
                        hintKamar.className   = 'v-hint show hint-error';
                    }
                    errors.push('Jumlah Kamar');
                }
            }

            // ── Kapasitas Aula ──
            if (currentTipe === 'aula') {
                const kaInput = $('maxDewasaAula');
                const hintKa  = $('hint-kap-aula');
                if (!kaInput || !kaInput.value || parseInt(kaInput.value) < 1) {
                    setFieldState(kaInput, 'error', hintKa, 'Kapasitas orang minimal 1.', 'hint-error', null, null);
                    errors.push('Kapasitas Aula');
                } else {
                    setFieldState(kaInput, 'success', hintKa, '', '');
                }
            }

            // ── Foto utama ──
            if (!fileInput.files[0]) {
                dropzone.classList.add('v-dz-error');
                setFieldState(dropzone, 'error', hintThumb, 'Foto utama (thumbnail) wajib diunggah.', 'hint-error', null, null);
                errors.push('Foto Utama');
            }

            // ── Total ukuran file ──
            const totalSize = checkTotalSize();
            if (totalSize > MAX_POST_SIZE) {
                showToast('error', 'Total file terlalu besar',
                    `Total (${formatBytes(totalSize)}) melebihi batas server 8MB. Perkecil ukuran foto.`, 6000);
                errors.push('Ukuran File');
            }

            // ── Tampilkan ringkasan error ──
            if (errors.length > 0) {
                showToast('error',
                    `${errors.length} field belum valid`,
                    'Periksa: ' + errors.join(', ') + '.');
                const firstErr = document.querySelector('.v-error, .v-dz-error');
                if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            // ── Konfirmasi simpan ──
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


        /* ═══════════════════════════════════════
           EKSEKUSI FETCH + LOADING
        ═══════════════════════════════════════ */

        function eksekusiSimpanData() {
            const form      = $('mainForm');
            const formData  = new FormData(form);
            const overlay   = $('loadingOverlay');
            const btnSimpan = $('btnSimpan');
            const btnText   = $('btnText');
            const btnIcon   = $('btnIcon');
            const spinner   = $('spinner');

            overlay.classList.remove('hidden');
            btnSimpan.disabled = true;
            spinner.classList.remove('hidden');
            btnText.textContent = 'Menyimpan...';
            btnIcon.classList.add('hidden');

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


        /* ═══════════════════════════════════════
           TOMBOL BATAL
        ═══════════════════════════════════════ */

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
    </script>
</body>
</html>