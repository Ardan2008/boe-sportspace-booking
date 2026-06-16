<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <title>BOE-Sport Space | Edit Fasilitas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .swal2-shown { padding-right: 0 !important; }
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }

        @keyframes shake {
            0%   { transform: translateX(0); }
            15%  { transform: translateX(-6px); }
            30%  { transform: translateX(6px); }
            45%  { transform: translateX(-5px); }
            60%  { transform: translateX(5px); }
            75%  { transform: translateX(-3px); }
            90%  { transform: translateX(3px); }
            100% { transform: translateX(0); }
        }
        .shake {
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }

        .field-error {
            border-color: #ef4444 !important;
            ring-color: #fecaca !important;
            box-shadow: 0 0 0 4px rgba(239,68,68,0.12) !important;
        }
        .field-error:focus {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 4px rgba(239,68,68,0.18) !important;
        }

        .error-msg {
            display: none;
            color: #ef4444;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.05em;
            margin-top: 5px;
            margin-left: 4px;
        }
        .error-msg.visible {
            display: block;
        }

        .dropzone-error {
            border-color: #ef4444 !important;
        }

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
            border-color: #1265A8;
            box-shadow: 0 0 0 4px rgba(18,101,168,.10);
        }
        .kamar-stepper-btn {
            flex-shrink: 0;
            width: 52px;
            height: 56px;
            background: #f8fafc;
            border: none;
            font-size: 22px;
            font-weight: 900;
            color: #1265A8;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s, color .15s, transform .1s;
            user-select: none;
        }
        .kamar-stepper-btn:hover  { background: #dbeafe; color: #1558a0; }
        .kamar-stepper-btn:active { transform: scale(.90); }
        .kamar-stepper-btn:disabled { color: #cbd5e1; cursor: not-allowed; background: #f8fafc; }
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
        .kamar-badge-edit {
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
        }

        .room-slider-btn {
            flex-shrink: 0;
            width: 44px;
            height: 44px;
            border-radius: 999px;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            color: #1265A8;
            font-size: 18px;
            font-weight: 900;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .15s;
            user-select: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .room-slider-btn:hover { background: #dbeafe; border-color: #1265A8; }
        .room-slider-btn:active { transform: scale(.92); }
        .room-slider-btn:disabled { color: #cbd5e1; border-color: #e2e8f0; background: #f8fafc; cursor: not-allowed; }

        .room-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            border: 2px solid #cbd5e1;
            background: transparent;
            cursor: pointer;
            transition: all .25s;
        }
        .room-dot.active {
            background: #1265A8;
            border-color: #1265A8;
            box-shadow: 0 0 0 3px rgba(18,101,168,.15);
        }
        .room-dot:hover:not(.active) {
            border-color: #1265A8;
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

        .field-success {
            border-color: #97C459 !important;
            box-shadow: 0 0 0 4px rgba(151,196,89,0.12) !important;
        }

        @keyframes vShake {
            0%,100% { transform: translateX(0); }
            20%     { transform: translateX(-5px); }
            40%     { transform: translateX(5px); }
            60%     { transform: translateX(-4px); }
            80%     { transform: translateX(4px); }
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
    </style>
</head>
<body class="bg-[#F8FAFC] font-sans antialiased text-slate-800">

    <div class="fixed top-0 left-0 w-full h-full -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-100 blur-[120px] rounded-full opacity-50"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] bg-indigo-100 blur-[120px] rounded-full opacity-50"></div>
    </div>

    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 flex justify-center items-center" x-data="facilityEditor()">
        <div class="w-full max-w-5xl bg-white/80 backdrop-blur-xl rounded-[3rem] shadow-[0_32px_64px_-15px_rgba(0,0,0,0.08)] border border-white overflow-hidden transition-all duration-500">

            <div class="pt-10 pb-6 px-10 text-center">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 mb-4 bg-blue-50/50 rounded-full border border-blue-100 shadow-sm">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#1265A8]"></span>
                    </span>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#1265A8]">Update Mode | <span x-text="tipe"></span></span>
                </div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight uppercase">
                    Edit <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#1265A8] to-blue-400" x-text="tipe === 'lapangan' ? 'Lapangan' : 'Renang'">Facility</span> Data
                </h2>
                <div class="h-1.5 w-12 bg-gradient-to-r from-[#1265A8] to-blue-400 mx-auto mt-4 rounded-full"></div>

                <div class="flex justify-center gap-4 mt-8">
                    <button type="button" @click="confirmTypeChange('lapangan')" :class="tipe === 'lapangan' ? 'bg-[#1265A8] text-white shadow-lg' : 'bg-slate-100 text-slate-400 hover:bg-slate-200'" class="px-8 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all duration-300">Lapangan</button>
                    <button type="button" @click="confirmTypeChange('kolam_renang')" :class="tipe === 'kolam_renang' ? 'bg-[#1265A8] text-white shadow-lg' : 'bg-slate-100 text-slate-400 hover:bg-slate-200'" class="px-8 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all duration-300">Renang</button>
                    <input type="hidden" name="tipe" :value="tipe">
                </div>
            </div>

            <form id="mainForm" action="{{ route('fasilitas.update', $fasilitas->id) }}" method="POST" enctype="multipart/form-data" class="p-8 lg:p-12 pt-6" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" name="tipe" :value="tipe">
                <input type="hidden" name="all_same" id="allSameInput" :value="allSame ? '1' : '0'">
                <input type="hidden" name="paket_harian" id="paketHarianInput" value="">
                <input type="hidden" name="rooms_data" id="roomsDataInput" value="">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

                    <div class="space-y-6">

                        <div class="group">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Fasilitas</label>
                            <div class="relative">
                                <input type="text" id="inputNama" name="nama" maxlength="60" value="{{ old('nama', $fasilitas->nama) }}"
                                    class="v-field w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all duration-300 shadow-sm font-semibold" required>
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

                        <div class="group">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Deskripsi Singkat</label>
                            <div class="relative">
                                <textarea id="inputDeskripsi" name="deskripsi" rows="3" maxlength="200"
                                    class="w-full px-6 py-4 pb-8 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all duration-300 shadow-sm resize-none font-medium leading-relaxed" required>{{ old('deskripsi', $fasilitas->deskripsi) }}</textarea>
                                <span class="v-char-counter" id="cc-desc">200</span>
                            </div>
                            <span class="error-msg" id="errDeskripsi">⚠ Deskripsi tidak boleh kosong.</span>
                        </div>

                        <div class="group">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Detail Fasilitas</label>
                            <textarea name="detail" rows="5"
                                class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all duration-300 shadow-sm resize-none font-medium leading-relaxed">{{ old('detail', $fasilitas->detail) }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Jam Operasional</label>
                                <input type="text" id="inputJam" name="jam_operasional" value="{{ old('jam_operasional', $fasilitas->jam_operasional) }}" placeholder="08.00 - 22.00"
                                    class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all duration-300 shadow-sm font-semibold">
                                <span class="error-msg" id="errJam">⚠ Gunakan format: 08.00 - 22.00</span>
                            </div>

                        </div>

                        {{-- Max Durasi Sewa — full-width 4-column grid --}}
                        <div x-show="tipe === 'lapangan' || tipe === 'kolam_renang'">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Max Durasi Sewa</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">

                                <div>
                                    <label class="block text-[10px] font-black text-[#1265A8] uppercase tracking-widest mb-2 ml-1" x-text="tipe === 'lapangan' ? 'Jam' : 'Hari'"></label>
                                    <input type="number" name="max_durasi_hari" min="0"
                                        value="{{ old('max_durasi_hari', $fasilitas->max_durasi_hari) }}"
                                        class="w-full px-4 py-3 text-center bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all duration-300 shadow-sm font-semibold text-gray-800">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-[#1265A8] uppercase tracking-widest mb-2 ml-1">Minggu</label>
                                    <input type="number" name="max_durasi_minggu" min="0"
                                        value="{{ old('max_durasi_minggu', $fasilitas->max_durasi_minggu) }}"
                                        class="w-full px-4 py-3 text-center bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all duration-300 shadow-sm font-semibold text-gray-800">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-[#1265A8] uppercase tracking-widest mb-2 ml-1">Bulan</label>
                                    <input type="number" name="max_durasi_bulan" min="0"
                                        value="{{ old('max_durasi_bulan', $fasilitas->max_durasi_bulan) }}"
                                        class="w-full px-4 py-3 text-center bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all duration-300 shadow-sm font-semibold text-gray-800">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-[#1265A8] uppercase tracking-widest mb-2 ml-1">Tahun</label>
                                    <input type="number" name="max_durasi_tahun" min="0"
                                        value="{{ old('max_durasi_tahun', $fasilitas->max_durasi_tahun) }}"
                                        class="w-full px-4 py-3 text-center bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all duration-300 shadow-sm font-semibold text-gray-800">
                                </div>

                            </div>
                        </div>

                        <div x-show="tipe === 'lapangan' || tipe === 'kolam_renang'">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">
                                <span x-text="tipe === 'lapangan' ? 'Jumlah Lapangan Tersedia' : 'Jumlah Kolam Tersedia'"></span>
                            </label>
                            <div class="kamar-stepper-wrap" id="kamarStepperWrap">
                                <button type="button" class="kamar-stepper-btn"
                                    @click="adjustKamar(-1)"
                                    :disabled="jumlahLapangan <= 1"
                                    :aria-label="'Kurangi ' + (tipe === 'lapangan' ? 'lapangan' : 'kolam')">−</button>
                                <input type="number"
                                    name="jumlah_lapangan"
                                    x-model.number="jumlahLapangan"
                                    min="1" max="999"
                                    class="kamar-stepper-input"
                                    :aria-label="'Jumlah ' + (tipe === 'lapangan' ? 'lapangan' : 'kolam')">
                                <button type="button" class="kamar-stepper-btn"
                                    @click="adjustKamar(1)"
                                    :aria-label="'Tambah ' + (tipe === 'lapangan' ? 'lapangan' : 'kolam')">+</button>
                            </div>
                            <div class="kamar-badge-edit">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span x-text="jumlahLapangan + ' ' + (tipe === 'lapangan' ? 'lapangan' : 'kolam') + ' tersedia'"></span>
                            </div>
                        </div>

                        <div class="group">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Labels / Fitur</label>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <template x-for="label in labels[tipe]" :key="label">
                                    <div class="relative group">
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="labels[]" :value="label" x-model="selectedLabels" class="hidden">
                                            <span :class="selectedLabels.includes(label) ? 'bg-[#1265A8] text-white border-[#1265A8]' : 'bg-white text-slate-400 border-slate-200'"
                                                class="px-4 py-2 rounded-xl border text-[10px] font-black uppercase tracking-widest transition-all duration-300 block"
                                                x-text="label"></span>
                                        </label>
                                        <button type="button" @click="removeLabel(label)"
                                            class="absolute inset-0 flex items-center justify-center rounded-xl bg-red-500/80 text-white text-lg transition-all opacity-0 group-hover:opacity-100">&times;</button>
                                    </div>
                                </template>
                            </div>
                            <div class="flex gap-2">
                                <input type="text" x-model="customLabel" @keydown.enter.prevent="addCustomLabel()" placeholder="Tambah fitur custom..."
                                    class="flex-1 px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-[10px] font-bold outline-none focus:border-[#1265A8] transition-all">
                                <button type="button" @click="addCustomLabel()" class="px-4 py-2 bg-[#1265A8] text-white rounded-xl hover:bg-slate-800 transition-all font-black text-sm">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-6">
                            <div class="w-full">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Thumbnail Cards</label>
                                <div id="dropzone" class="relative overflow-hidden rounded-[2rem] border-2 border-dashed border-slate-200 bg-slate-50/50 hover:border-[#1265A8] transition-all duration-500 h-48 flex items-center justify-center group/drop cursor-pointer">
                                    <img id="preview" src="{{ $fasilitas->image ? asset('storage/fasilitas/' . $fasilitas->image) : '' }}" class="absolute inset-0 w-full h-full object-cover z-10" style="{{ $fasilitas->image ? '' : 'display:none' }}">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/drop:opacity-100 transition-opacity duration-300 z-20 flex flex-col items-center justify-center text-white">
                                        <span class="text-[10px] font-black uppercase tracking-widest">Change Photo</span>
                                    </div>
                                    <div id="dropzonePlaceholder" class="relative z-20 flex flex-col items-center gap-2 {{ $fasilitas->image ? 'hidden' : '' }}">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-300">Upload Thumbnail</span>
                                        <span class="text-[9px] text-slate-300 font-medium">Max 2 MB</span>
                                    </div>
                                    <input type="file" id="fileInput" name="image" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-30">
                                </div>
                                <span class="error-msg" id="errImage">⚠ Ukuran gambar melebihi 2 MB. Pilih file yang lebih kecil.</span>
                            </div>

                            <div class="w-full">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Preview Gallery (3 Foto)</label>
                                <div class="grid grid-cols-3 gap-3">
                                    <template x-for="i in [0, 1, 2]" :key="i">
                                        <div class="relative overflow-hidden rounded-2xl transition-all duration-500 h-32 flex items-center justify-center group/gal cursor-pointer"
                                            :class="galleryErrors[i] ? 'border-2 border-red-400 bg-red-50' : 'border-2 border-dashed border-slate-200 bg-slate-50/50 hover:border-[#1265A8]'">
                                            <img :src="galleryPreviews[i]" class="absolute inset-0 w-full h-full object-cover z-10" x-show="galleryPreviews[i]">
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/gal:opacity-100 transition-opacity duration-300 z-20 flex flex-col items-center justify-center text-white" x-show="galleryPreviews[i]">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </div>
                                            <div class="relative z-20 flex flex-col items-center gap-1" x-show="!galleryPreviews[i] && !galleryErrors[i]">
                                                <svg class="w-5 h-5 text-slate-300 group-hover/gal:text-[#1265A8] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                                <span class="text-[8px] text-slate-300 font-bold uppercase tracking-widest">Max 2MB</span>
                                            </div>
                                            <div class="relative z-20 flex flex-col items-center gap-1" x-show="galleryErrors[i]">
                                                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                                <span class="text-[8px] text-red-400 font-black uppercase tracking-widest" x-text="'Foto ' + (i+1) + ' >2MB'"></span>
                                            </div>
                                            <input :id="'galleryInput' + i" :name="'gallery[' + i + ']'" type="file" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-35"
                                                @change="handleGalleryChange($event, i)">
                                        </div>
                                    </template>
                                </div>
                                <span class="error-msg" id="errGallery">⚠ Salah satu foto gallery melebihi 2 MB.</span>
                            </div>
                        </div>

                        <div x-show="tipe === 'lapangan' || tipe === 'kolam_renang'" x-cloak class="w-full bg-gradient-to-br from-blue-50/40 to-indigo-50/30 rounded-3xl border border-blue-100/60 p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#1265A8]/10 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-[#1265A8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xs font-black uppercase tracking-widest text-slate-500" x-text="tipe === 'lapangan' ? 'Lapangan Specifications' : 'Kolam Specifications'"></h3>
                                        <p class="text-[10px] font-semibold text-slate-400" x-show="!allSame" x-text="(tipe === 'lapangan' ? 'Lapangan ' : 'Kolam ') + (currentRoomIndex + 1) + ' dari ' + rooms.length"></p>
                                        <p class="text-[10px] font-semibold text-[#1265A8]" x-show="allSame" x-text="'Semua ' + rooms.length + ' ' + (tipe === 'lapangan' ? 'lapangan' : 'kolam') + ' sama'"></p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <template x-if="rooms.length > 1">
                                        <label class="flex items-center gap-2 cursor-pointer select-none mr-2">
                                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400" x-text="allSame ? 'Sama' : 'Beda'"></span>
                                            <div class="relative">
                                                <input type="checkbox" x-model="allSame" class="sr-only">
                                                <div class="w-9 h-5 rounded-full transition-colors" :class="allSame ? 'bg-[#1265A8]' : 'bg-slate-300'"></div>
                                                <div class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform" :class="allSame ? 'translate-x-4' : ''"></div>
                                            </div>
                                        </label>
                                    </template>
                                    <button type="button" class="room-slider-btn" @click="prevRoom" :disabled="currentRoomIndex <= 0 || allSame">‹</button>
                                    <button type="button" class="room-slider-btn" @click="nextRoom" :disabled="currentRoomIndex >= rooms.length - 1 || allSame">›</button>
                                </div>
                            </div>

                            <div class="flex justify-center gap-2" x-show="!allSame">
                                <template x-for="(r, ri) in rooms" :key="ri">
                                    <div @click="currentRoomIndex = ri"
                                        :class="ri === currentRoomIndex ? 'room-dot active' : 'room-dot'"
                                        :title="(tipe === 'lapangan' ? 'Lapangan ' : 'Kolam ') + (ri + 1)"></div>
                                </template>
                            </div>

                            <div class="relative">
                                <template x-for="(room, ri) in rooms" :key="ri">
                                    <div x-show="ri === (allSame ? 0 : currentRoomIndex)"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 translate-x-8"
                                        x-transition:enter-end="opacity-100 translate-x-0"
                                         :data-room-index="ri"
                                         class="bg-white rounded-2xl border border-slate-200/80 p-5" @change="syncPaketHarian()" @input="syncPaketHarian()">

                                        <div x-show="jumlahLapangan > 1 && !allSame">
                                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-0.5" x-text="tipe === 'lapangan' ? 'Tipe Lapangan' : 'Tipe Kolam'"></label>
                                            <input type="text" x-model="rooms[ri].tipe" placeholder="Tipe..." class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-semibold outline-none focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] transition-all">
                                        </div>

                                        <div>
                                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-0.5">Ukuran</label>
                                            <div class="flex items-center gap-2">
                                                <div class="relative flex-1">
                                                    <input type="number" x-model="rooms[ri].panjang" min="0" step="0.1"
                                                        placeholder="0"
                                                        class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none font-bold text-sm text-center">
                                                </div>
                                                <span class="text-lg font-black text-slate-400 flex-shrink-0">×</span>
                                                <div class="relative flex-1">
                                                    <input type="number" x-model="rooms[ri].lebar" min="0" step="0.1"
                                                        placeholder="0"
                                                        class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none font-bold text-sm text-center">
                                                </div>
                                                <span class="text-xs font-black text-slate-400 flex-shrink-0 whitespace-nowrap">m²</span>
                                            </div>
                                        </div>

                                        {{-- Foto Preview --}}
                                        <div x-show="!allSame" class="space-y-2">
                                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-0.5">Foto Preview</label>
                                            <div class="grid grid-cols-3 gap-2">
                                                <template x-for="fi in [0, 1, 2]" :key="fi">
                                                    <div class="relative overflow-hidden rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/50 hover:border-[#1265A8] transition-all duration-300 h-20 flex items-center justify-center group cursor-pointer">
                                                        <img :src="fotoPreviews[ri]?.[fi]" class="absolute inset-0 w-full h-full object-cover z-10" x-show="fotoPreviews[ri]?.[fi]">
                                                        <div class="relative z-20 flex flex-col items-center" x-show="!fotoPreviews[ri]?.[fi]">
                                                            <svg class="w-4 h-4 text-slate-300 group-hover:text-[#1265A8] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                                            </svg>
                                                        </div>
                                                        <input type="file" accept="image/*"
                                                            :name="'room_fotos[' + ri + '][' + fi + ']'"
                                                            class="absolute inset-0 opacity-0 cursor-pointer z-30"
                                                            @change="handleRoomFoto($event, ri, fi)">
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- Fasilitas --}}
                                        <div x-show="jumlahLapangan > 1" class="space-y-3">
                                            <div class="grid grid-cols-5 sm:grid-cols-5 gap-1.5">
                                                <template x-for="(item, fIdx) in rooms[ri].fasilitasKeys" :key="item.key">
                                                    <div class="relative group flex flex-col items-center bg-white border border-slate-200 rounded-xl px-1.5 py-2.5 transition-all duration-200 hover:border-[#1265A8] hover:shadow-sm">
                                                        <span class="text-[7px] font-black text-slate-500 uppercase tracking-wider mb-0.5 leading-tight text-center" x-text="item.label"></span>
                                                        <input type="number" x-model.number="rooms[ri].fasilitas[item.key]" min="0" placeholder="0"
                                                            class="w-11 h-6 text-center bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-[#1265A8] outline-none font-bold text-xs">
                                                        <button type="button" @click="removeFasilitas(ri, fIdx)"
                                                            class="absolute -top-1.5 -right-1.5 w-5 h-5 flex items-center justify-center rounded-full bg-red-500/80 text-white text-xs opacity-0 group-hover:opacity-100 transition-all">&times;</button>
                                                    </div>
                                                </template>
                                            </div>
                                            <div class="flex gap-2">
                                                <input type="text" x-model="rooms[ri].newFasilitasLabel" @keydown.enter.prevent="addFasilitas(ri)"
                                                    placeholder="Tambah fasilitas..."
                                                    class="flex-1 px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-[10px] font-bold outline-none focus:border-[#1265A8] transition-all">
                                                <button type="button" @click="addFasilitas(ri)"
                                                    class="px-4 py-2 bg-[#1265A8] text-white rounded-xl hover:bg-slate-800 transition-all font-black text-sm">+</button>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-0.5">Harga Sewa</label>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5"><span x-text="tipe === 'lapangan' ? 'Jam' : 'Harian'"></span> <span class="text-red-400">*</span></label>
                                                    <div class="relative">
                                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 font-black text-[#1265A8] text-xs">Rp</span>
                                                        <input type="text"
                                                            data-price-field="harga_harian"
                                                            :value="(room.harga_harian !== '' && room.harga_harian != null) ? new Intl.NumberFormat('id-ID').format(room.harga_harian) : ''"
                                                            @input="room.harga_harian = $event.target.value.replace(/\D/g, '') !== '' ? Number($event.target.value.replace(/\D/g, '')) : ''; $event.target.value = (room.harga_harian !== '' && room.harga_harian != null) ? new Intl.NumberFormat('id-ID').format(room.harga_harian) : ''"
                                                            :class="(!room.harga_harian || Number(room.harga_harian) <= 0) ? 'field-error' : ''"
                                                            class="w-full pl-8 pr-3 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all font-bold text-sm"
                                                            placeholder="0">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Mingguan <span class="text-red-400">*</span></label>
                                                    <div class="relative">
                                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 font-black text-[#1265A8] text-xs">Rp</span>
                                                        <input type="text"
                                                            data-price-field="harga_mingguan"
                                                            :value="(room.harga_mingguan !== '' && room.harga_mingguan != null) ? new Intl.NumberFormat('id-ID').format(room.harga_mingguan) : ''"
                                                            @input="room.harga_mingguan = $event.target.value.replace(/\D/g, '') !== '' ? Number($event.target.value.replace(/\D/g, '')) : ''; $event.target.value = (room.harga_mingguan !== '' && room.harga_mingguan != null) ? new Intl.NumberFormat('id-ID').format(room.harga_mingguan) : ''"
                                                            :class="(!room.harga_mingguan || Number(room.harga_mingguan) <= 0) ? 'field-error' : ''"
                                                            class="w-full pl-8 pr-3 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all font-bold text-sm"
                                                            placeholder="0">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Bulanan <span class="text-red-400">*</span></label>
                                                    <div class="relative">
                                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 font-black text-[#1265A8] text-xs">Rp</span>
                                                        <input type="text"
                                                            data-price-field="harga_bulanan"
                                                            :value="(room.harga_bulanan !== '' && room.harga_bulanan != null) ? new Intl.NumberFormat('id-ID').format(room.harga_bulanan) : ''"
                                                            @input="room.harga_bulanan = $event.target.value.replace(/\D/g, '') !== '' ? Number($event.target.value.replace(/\D/g, '')) : ''; $event.target.value = (room.harga_bulanan !== '' && room.harga_bulanan != null) ? new Intl.NumberFormat('id-ID').format(room.harga_bulanan) : ''"
                                                            :class="(!room.harga_bulanan || Number(room.harga_bulanan) <= 0) ? 'field-error' : ''"
                                                            class="w-full pl-8 pr-3 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all font-bold text-sm"
                                                            placeholder="0">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Tahunan <span class="text-red-400">*</span></label>
                                                    <div class="relative">
                                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 font-black text-[#1265A8] text-xs">Rp</span>
                                                        <input type="text"
                                                            data-price-field="harga_tahunan"
                                                            :value="(room.harga_tahunan !== '' && room.harga_tahunan != null) ? new Intl.NumberFormat('id-ID').format(room.harga_tahunan) : ''"
                                                            @input="room.harga_tahunan = $event.target.value.replace(/\D/g, '') !== '' ? Number($event.target.value.replace(/\D/g, '')) : ''; $event.target.value = (room.harga_tahunan !== '' && room.harga_tahunan != null) ? new Intl.NumberFormat('id-ID').format(room.harga_tahunan) : ''"
                                                            :class="(!room.harga_tahunan || Number(room.harga_tahunan) <= 0) ? 'field-error' : ''"
                                                            class="w-full pl-8 pr-3 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all font-bold text-sm"
                                                            placeholder="0">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                            <button type="submit" class="group relative flex-[2] flex items-center justify-center gap-3 py-5 rounded-2xl bg-[#1265A8] hover:bg-slate-900 text-white transition-all duration-500 active:scale-[0.97] shadow-lg shadow-blue-900/10">
                                <span class="text-xs font-black uppercase tracking-[0.2em]">Simpan Perubahan</span>
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                            <button type="button" id="btn-batal" class="flex-1 flex items-center justify-center py-5 rounded-2xl border-2 border-slate-100 bg-white hover:border-red-100 hover:bg-red-50 transition-all duration-500 group">
                                <span class="text-xs font-black uppercase tracking-widest text-slate-400 group-hover:text-red-500">Batal</span>
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div id="loadingOverlay" class="fixed inset-0 z-[100] flex items-center justify-center hidden bg-white/80 backdrop-blur-md">
        <div class="flex flex-col items-center">
            <div class="relative w-16 h-16 mb-4">
                <div class="absolute inset-0 border-4 border-slate-100 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-[#1265A8] border-t-transparent rounded-full animate-spin"></div>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#1265A8] animate-pulse">Menyimpan Perubahan...</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('facilityEditor', () => ({
                tipe: '{{ $fasilitas->tipe ?? 'lapangan' }}',
                jumlahLapangan: {{ $fasilitas->jumlah_lapangan ?? 1 }},
                rooms: @json($rooms),
                currentRoomIndex: 0,
                allSame: {{ $fasilitas->jumlah_lapangan <= 1 ? 'true' : ($fasilitas->all_same ? 'true' : 'false') }},
                customRoomType: '',
                showCustomRoomInput: false,

                labels: {
                    lapangan: [...new Set(['Lampu Penerangan', 'Parkir', 'Toilet', 'Mushola', 'Kantin', 'Tempat Duduk', ...@json($fasilitas->labels ?? [])])],
                    kolam_renang: ['Loker', 'Bilik Bilas', 'Toilet', 'Lifeguard', 'Tempat Duduk', 'Parkir', 'Mushola', ...@json($fasilitas->labels ?? [])]
                },
                selectedLabels: @json($fasilitas->labels ?? []),
                customLabel: '',
                galleryPreviews: [
                    @if(isset($fasilitas->gallery[0])) '{{ asset('storage/fasilitas/gallery/' . $fasilitas->gallery[0]) }}' @else null @endif,
                    @if(isset($fasilitas->gallery[1])) '{{ asset('storage/fasilitas/gallery/' . $fasilitas->gallery[1]) }}' @else null @endif,
                    @if(isset($fasilitas->gallery[2])) '{{ asset('storage/fasilitas/gallery/' . $fasilitas->gallery[2]) }}' @else null @endif
                ],
                galleryErrors: [false, false, false],
                fotoPreviews: @json(
                    collect($rooms)->map(fn($r) =>
                        collect($r['foto'] ?? [])->map(fn($f) => $f ? asset('storage/fasilitas/rooms/' . $f) : null)
                            ->pad(3, null)->toArray()
                    )->toArray()
                ),

                init() {
                    window.__alpineRoot = this;

                    if (!this.rooms || this.rooms.length === 0) {
                        this.rooms = this.generateDefaultRooms();
                    }

                    this.rooms.forEach(r => {
                        if (!r.nomor_lapangan) {
                            r.nomor_lapangan = [];
                        }

                        if (typeof r.tipe === 'string') {
                            r.tipe = r.tipe || '';
                        }
                        if (r.keunggulan === undefined) {
                            r.keunggulan = '';
                        }
                        if (r.panjang === undefined) {
                            r.panjang = '';
                        }
                        if (r.lebar === undefined) {
                            r.lebar = '';
                        }
                        if (!r.fasilitas) {
                            r.fasilitas = { lampu: 0, parkir: 0, toilet: 0, mushola: 0, kursi_tribun: 0, ruang_ganti: 0, papan_skor: 0, sound_system: 0, air_minum: 0, wifi: 0 };
                        }
                        r.fasilitasKeys = Object.keys(r.fasilitas).map(k => ({ key: k, label: k.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ') }));
                        r.newFasilitasLabel = '';
                        r.harga_harian   = r.harga_harian   !== '' && r.harga_harian   != null ? Number(r.harga_harian)   : '';
                        r.harga_mingguan = r.harga_mingguan !== '' && r.harga_mingguan != null ? Number(r.harga_mingguan) : '';
                        r.harga_bulanan  = r.harga_bulanan  !== '' && r.harga_bulanan  != null ? Number(r.harga_bulanan)  : '';
                        r.harga_tahunan  = r.harga_tahunan  !== '' && r.harga_tahunan  != null ? Number(r.harga_tahunan)  : '';
                    });

                    this.$watch('jumlahLapangan', (newVal) => {
                        if (newVal > this.rooms.length) {
                            for (let i = this.rooms.length; i < newVal; i++) {
                                this.rooms.push(this.createEmptyRoom(i));
                            }
                        } else if (newVal < this.rooms.length) {
                            this.rooms.splice(newVal);
                            if (this.currentRoomIndex >= newVal) {
                                this.currentRoomIndex = Math.max(0, newVal - 1);
                            }
                        }
                        this.initFotoPreviews();
                        this.syncPaketHarian();
                    });



                    this.$watch('allSame', (val) => {
                        if (val && this.jumlahLapangan > 1) this.syncAllSame();
                        this.syncPaketHarian();
                    });


                    if (this.rooms.length > 0) {
                        this.syncPaketHarian();
                    }

                    this.initFotoPreviews();
                },

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

                createEmptyRoom(index) {
                    const fasKeys = this.defaultFasKeys();
                    const fas = {};
                    fasKeys.forEach(f => { fas[f.key] = 0; });
                    return {
                        tipe: '',
                        jumlah: 1,
                        nomor_lapangan: [],
                        kode_blok: '',
                        harga_harian: index === 0 ? '{{ $fasilitas->harga }}' : '',
                        harga_mingguan: '',
                        harga_bulanan: index === 0 ? '{{ $fasilitas->harga_bulanan }}' : '',
                        harga_tahunan: '',
                        keunggulan: '',
                        panjang: '',
                        lebar: '',
                        fasilitas: fas,
                        newFasilitasLabel: '',
                        fasilitasKeys: [...fasKeys],
                        foto: [],
                    };
                },

                syncAllSame() {
                    if (!this.allSame || this.jumlahLapangan <= 1) return;
                    const src = this.rooms[0];
                    for (let i = 1; i < this.rooms.length; i++) {
                        this.rooms[i] = {
                            ...src,
                            tipe: src.tipe || '',
                            nomor_lapangan: [...(src.nomor_lapangan || [])],
                            fasilitas: { ...(src.fasilitas || {}) },
                        };
                    }
                },

                generateDefaultRooms() {
                    const rooms = [];
                    const count = this.jumlahLapangan;
                    for (let i = 0; i < count; i++) {
                        rooms.push(this.createEmptyRoom(i));
                    }
                    return rooms;
                },

                addFasilitas(ri) {
                    const room = this.rooms[ri];
                    if (!room) return;
                    const label = room.newFasilitasLabel?.trim();
                    if (!label) return;
                    const key = label.toLowerCase().replace(/\s+/g, '_');
                    if (room.fasilitasKeys.some(f => f.key === key)) return;
                    room.fasilitasKeys.push({ key, label });
                    room.fasilitas[key] = 0;
                    room.newFasilitasLabel = '';
                },

                removeFasilitas(ri, index) {
                    const room = this.rooms[ri];
                    if (!room) return;
                    const item = room.fasilitasKeys[index];
                    if (!item) return;
                    room.fasilitasKeys.splice(index, 1);
                    delete room.fasilitas[item.key];
                },

                initFotoPreviews() {
                    const arr = [];
                    for (let i = 0; i < this.rooms.length; i++) {
                        if (this.fotoPreviews[i]) {
                            arr[i] = [...this.fotoPreviews[i]];
                            while (arr[i].length < 3) arr[i].push(null);
                        } else if (this.rooms[i].foto && Array.isArray(this.rooms[i].foto)) {
                            arr[i] = this.rooms[i].foto.map(f => f ? '/storage/fasilitas/rooms/' + f : null);
                            while (arr[i].length < 3) arr[i].push(null);
                        } else {
                            arr[i] = [null, null, null];
                        }
                    }
                    this.fotoPreviews = arr;
                },

                handleRoomFoto(event, roomIdx, fotoIdx) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const MAX = 2 * 1024 * 1024;
                    if (file.size > MAX) {
                        event.target.value = '';
                        Swal.fire({
                            title: 'File Terlalu Besar',
                            text: 'Foto ' + (fotoIdx + 1) + ' melebihi batas 2MB.',
                            icon: 'warning',
                            confirmButtonColor: '#1265A8',
                            confirmButtonText: 'OK',
                            customClass: { popup: 'rounded-[2.5rem] p-8' }
                        });
                        return;
                    }

                    if (!this.fotoPreviews[roomIdx]) {
                        this.fotoPreviews[roomIdx] = [null, null, null];
                    }

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.fotoPreviews[roomIdx][fotoIdx] = e.target.result;
                        this.fotoPreviews = [...this.fotoPreviews];
                    };
                    reader.readAsDataURL(file);
                },

                syncPaketHarian() {
                    if (this.allSame && this.jumlahLapangan > 1) {
                        this.syncAllSame();
                    }
                    const payload = this.rooms.map(r => {
                        const { fasilitasKeys, newFasilitasLabel, fotoPreviews, ...rest } = r;
                        // Ensure prices are stored as numbers, not empty strings
                        rest.harga_harian   = rest.harga_harian   !== '' && rest.harga_harian   != null ? Number(rest.harga_harian)   : 0;
                        rest.harga_mingguan = rest.harga_mingguan !== '' && rest.harga_mingguan != null ? Number(rest.harga_mingguan) : 0;
                        rest.harga_bulanan  = rest.harga_bulanan  !== '' && rest.harga_bulanan  != null ? Number(rest.harga_bulanan)  : 0;
                        rest.harga_tahunan  = rest.harga_tahunan  !== '' && rest.harga_tahunan  != null ? Number(rest.harga_tahunan)  : 0;
                        return rest;
                    });
                    const el = document.getElementById('paketHarianInput');
                    if (el) el.value = JSON.stringify(payload);
                    const rd = document.getElementById('roomsDataInput');
                    if (rd) rd.value = JSON.stringify(payload);


                },

                adjustKamar(delta) {
                    const next = Math.min(999, Math.max(1, this.jumlahLapangan + delta));
                    this.jumlahLapangan = next;
                },

                nextRoom() {
                    if (this.currentRoomIndex < this.rooms.length - 1) {
                        this.currentRoomIndex++;
                    }
                },

                prevRoom() {
                    if (this.currentRoomIndex > 0) {
                        this.currentRoomIndex--;
                    }
                },

                addCustomLabel() {
                    if (this.customLabel.trim() !== '') {
                        const label = this.customLabel.trim();
                        if (!this.labels[this.tipe].includes(label)) this.labels[this.tipe].push(label);
                        if (!this.selectedLabels.includes(label)) this.selectedLabels.push(label);
                        this.customLabel = '';
                    }
                },

                removeLabel(label) {
                    this.labels[this.tipe] = this.labels[this.tipe].filter(l => l !== label);
                    this.selectedLabels = this.selectedLabels.filter(l => l !== label);
                },

                handleGalleryChange(event, index) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const MAX = 2 * 1024 * 1024;
                    if (file.size > MAX) {
                        event.target.value = '';
                        this.galleryPreviews[index] = null;
                        this.galleryErrors[index] = true;
                        showError(document.getElementById('errGallery'));
                        const container = event.target.closest('div[class*="relative overflow-hidden rounded-2xl"]');
                        if (container) triggerShake(container);
                        return;
                    }

                    this.galleryErrors[index] = false;
                    clearError(document.getElementById('errGallery'));

                    const reader = new FileReader();
                    reader.onload = (e) => { this.galleryPreviews[index] = e.target.result; };
                    reader.readAsDataURL(file);
                },



                confirmTypeChange(newType) {
                    if (this.tipe === newType) return;
                    Swal.fire({
                        title: 'Peringatan',
                        text: 'Data yang telah anda isi akan otomatis terhapus, Apakah anda yakin ingin mengubah type fasilitas?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#1265A8',
                        cancelButtonColor: '#94a3b8',
                        confirmButtonText: 'Ubah Type',
                        cancelButtonText: 'Batalkan',
                        reverseButtons: true,
                        customClass: { popup: 'rounded-[2.5rem] p-8' }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.tipe = newType;
                            this.selectedLabels = [];
                        }
                    });
                }
            }));
        });

        function showError(msgEl, inputEl) {
            if (msgEl) msgEl.classList.add('visible');
            if (inputEl) {
                inputEl.classList.add('field-error');
                triggerShake(inputEl);
            }
        }
        function clearError(msgEl, inputEl) {
            if (msgEl) msgEl.classList.remove('visible');
            if (inputEl) inputEl.classList.remove('field-error');
        }
        function triggerShake(el) {
            el.classList.remove('shake');
            void el.offsetWidth;
            el.classList.add('shake');
            el.addEventListener('animationend', () => el.classList.remove('shake'), { once: true });
        }

        function formatRupiah(inputEl, hiddenEl) {
            inputEl.addEventListener('input', function () {
                let raw = this.value.replace(/\D/g, '');
                hiddenEl.value = raw;
                this.value = raw === '' ? '' : new Intl.NumberFormat('id-ID').format(raw);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const MAX_SIZE   = 2 * 1024 * 1024;
            const urlAsal    = "/admin/dashboard/dataFasilitas";

            const fileInput  = document.getElementById('fileInput');
            const preview    = document.getElementById('preview');
            const dropzone   = document.getElementById('dropzone');
            const placeholder = document.getElementById('dropzonePlaceholder');
            const errImage   = document.getElementById('errImage');

            fileInput.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;

                if (file.size > MAX_SIZE) {
                    this.value = '';
                    preview.style.display = 'none';
                    placeholder.classList.remove('hidden');
                    dropzone.classList.add('dropzone-error');
                    triggerShake(dropzone);
                    showError(errImage);
                    return;
                }

                clearError(errImage);
                dropzone.classList.remove('dropzone-error');
                placeholder.classList.add('hidden');

                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.src = e.target.result;
                    preview.style.display = '';
                };
                reader.readAsDataURL(file);
            });

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

            const inputNama   = document.getElementById('inputNama');
            const hintNama    = document.getElementById('hint-nama');
            const iconNamaOk  = document.getElementById('icon-nama-ok');
            const iconNamaErr = document.getElementById('icon-nama-err');
            const pbWrap      = document.getElementById('pb-nama-wrap');
            const pb          = document.getElementById('pb-nama');
            const rgxNama     = /^[a-zA-Z\s]+$/;

            inputNama.addEventListener('input', function () {
                const val = this.value;
                const len = val.trim().length;

                if (!val) {
                    clearField(this, hintNama, iconNamaOk, iconNamaErr);
                    pbWrap.classList.remove('show');
                    return;
                }

                pbWrap.classList.add('show');
                pb.style.width = Math.min(len / 60 * 100, 100) + '%';

                if (!rgxNama.test(val)) {
                    pb.style.backgroundColor = '#E24B4A';
                    setFieldState(this, 'error', hintNama, 'Hanya huruf dan spasi yang diperbolehkan.', 'hint-error', iconNamaOk, iconNamaErr);
                } else if (len < 2) {
                    pb.style.backgroundColor = '#EF9F27';
                    setFieldState(this, 'error', hintNama, 'Minimal 2 karakter.', 'hint-warning', iconNamaOk, iconNamaErr);
                } else {
                    pb.style.backgroundColor = '#97C459';
                    setFieldState(this, 'success', hintNama, 'Nama terlihat bagus!', 'hint-success', iconNamaOk, iconNamaErr);
                }
            });

            function validateNama() {
                const val = inputNama.value.trim();
                if (!val || !rgxNama.test(val) || val.length < 2) {
                    const msg = !val ? 'Nama wajib diisi.'
                              : !rgxNama.test(val) ? 'Nama hanya boleh berisi huruf dan spasi.'
                              : 'Nama minimal 2 karakter.';
                    setFieldState(inputNama, 'error', hintNama, msg, 'hint-error', iconNamaOk, iconNamaErr);
                    const len = inputNama.value.length;
                    pbWrap.classList.add('show');
                    pb.style.width = Math.min(len / 60 * 100, 100) + '%';
                    pb.style.backgroundColor = '#E24B4A';
                    return false;
                }
                setFieldState(inputNama, 'success', hintNama, 'Nama terlihat bagus!', 'hint-success', iconNamaOk, iconNamaErr);
                pbWrap.classList.add('show');
                pb.style.width = Math.min(inputNama.value.length / 60 * 100, 100) + '%';
                pb.style.backgroundColor = '#97C459';
                return true;
            }
            inputNama.addEventListener('blur', validateNama);
            validateNama();

            const inputDeskripsi = document.getElementById('inputDeskripsi');
            const errDeskripsi   = document.getElementById('errDeskripsi');
            const ccDesc         = document.getElementById('cc-desc');

            function updateCharCounter() {
                if (!ccDesc) return;
                const left = 200 - inputDeskripsi.value.length;
                ccDesc.textContent = left;
                ccDesc.className = 'v-char-counter' + (left < 10 ? ' danger' : left < 30 ? ' warn' : '');
            }

            /* ── JAM OPERASIONAL ── */
            const inputJam  = document.getElementById('inputJam');
            const errJam    = document.getElementById('errJam');
            const rgxJam    = /^\d{2}\.\d{2}\s*-\s*\d{2}\.\d{2}$/;

            function validateJam() {
                const val = inputJam.value.trim();
                if (!val) {
                    showError(errJam, inputJam);
                    inputJam.classList.remove('field-success');
                    return false;
                }
                if (!rgxJam.test(val)) {
                    showError(errJam, inputJam);
                    inputJam.classList.remove('field-success');
                    return false;
                }
                clearError(errJam, inputJam);
                inputJam.classList.add('field-success');
                return true;
            }
            inputJam.addEventListener('blur', validateJam);
            inputJam.addEventListener('input', function () {
                const val = this.value.trim();
                if (val && rgxJam.test(val)) {
                    clearError(errJam, this);
                    this.classList.add('field-success');
                } else if (!val) {
                    clearError(errJam, this);
                    this.classList.remove('field-success');
                } else {
                    this.classList.remove('field-success');
                }
            });

            function validateDeskripsi() {
                if (inputDeskripsi.value.trim() === '') {
                    showError(errDeskripsi, inputDeskripsi);
                    return false;
                }
                clearError(errDeskripsi, inputDeskripsi);
                return true;
            }
            inputDeskripsi.addEventListener('blur', validateDeskripsi);
            inputDeskripsi.addEventListener('input', function () {
                if (this.value.trim() !== '') clearError(errDeskripsi, inputDeskripsi);
                updateCharCounter();
            });
            updateCharCounter();

            const form = document.getElementById('mainForm');
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const namaOk     = validateNama();
                const deskOk     = validateDeskripsi();
                const jamOk      = validateJam();

                let galleryOk = true;
                [0, 1, 2].forEach(i => {
                    const inp = document.getElementById('galleryInput' + i);
                    if (inp && inp.files[0] && inp.files[0].size > MAX_SIZE) galleryOk = false;
                });

                const alpine = window.__alpineRoot;
                let priceOk = true;
                if (alpine && alpine.rooms) {
                    priceOk = !alpine.rooms.some(r =>
                        !r.harga_harian || Number(r.harga_harian) <= 0 ||
                        !r.harga_mingguan || Number(r.harga_mingguan) <= 0 ||
                        !r.harga_bulanan || Number(r.harga_bulanan) <= 0 ||
                        !r.harga_tahunan || Number(r.harga_tahunan) <= 0
                    );
                }
                let tipeOk = true;
                if (alpine && alpine.rooms && alpine.jumlahLapangan > 1 && !alpine.allSame) {
                    tipeOk = !alpine.rooms.some(r => {
                        if (Array.isArray(r.tipe)) return r.tipe.length === 0 || r.tipe.every(t => !t.trim());
                        return !r.tipe || r.tipe.trim() === '';
                    });
                }

                if (!namaOk || !deskOk || !jamOk || !galleryOk || !priceOk || !tipeOk) {
                    if (!priceOk) {
                        Swal.fire({
                            title: 'Harga Belum Lengkap',
                            text: 'Semua harga sewa (Jam/Harian, Mingguan, Bulanan, Tahunan) harus diisi dan lebih dari 0.',
                            icon: 'warning',
                            confirmButtonColor: '#1265A8',
                            confirmButtonText: 'OK',
                            customClass: { popup: 'rounded-[2.5rem] p-8' }
                        });
                    }
                    const firstErr = document.querySelector('.field-error, .dropzone-error');
                    if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Update',
                    text: "Apakah data yang Anda masukkan sudah benar?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#1265A8',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Cek Lagi',
                    reverseButtons: true,
                    customClass: { popup: 'rounded-[2.5rem] p-8' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (window.__alpineRoot && window.__alpineRoot.syncPaketHarian) {
                            window.__alpineRoot.syncPaketHarian();
                        }

                        const overlay = document.getElementById('loadingOverlay');
                        overlay.classList.remove('hidden');

                        const formData = new FormData(form);

                        fetch(form.action, {
                            method: 'POST',
                            body: formData,
                        })
                        .then(async res => {
                            const ct = res.headers.get('content-type') || '';
                            if (ct.includes('application/json')) {
                                const data = await res.json();
                                overlay.classList.add('hidden');
                                if (res.ok) {
                                    Swal.fire({ title: 'Berhasil!', text: 'Data berhasil diperbarui.', icon: 'success', confirmButtonColor: '#1265A8', customClass: { popup: 'rounded-[2.5rem] p-8' } })
                                        .then(() => { window.location.href = '/admin/dashboard/dataFasilitas'; });
                                } else {
                                    Swal.fire({ title: 'Gagal', text: data.message || 'Terjadi kesalahan.', icon: 'error', confirmButtonColor: '#1265A8', customClass: { popup: 'rounded-[2.5rem] p-8' } });
                                }
                            } else {
                                overlay.classList.add('hidden');
                                window.location.href = '/admin/dashboard/dataFasilitas';
                            }
                        })
                        .catch(err => {
                            overlay.classList.add('hidden');
                            Swal.fire({ title: 'Gagal', text: err.message || 'Terjadi kesalahan sistem.', icon: 'error', confirmButtonColor: '#1265A8', customClass: { popup: 'rounded-[2.5rem] p-8' } });
                        });
                    }
                });
            });

            document.getElementById('btn-batal').addEventListener('click', () => {
                Swal.fire({
                    title: 'Batalkan Perubahan?',
                    text: "Ketikan Anda tidak akan disimpan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Tetap di Sini',
                    reverseButtons: true,
                    customClass: { popup: 'rounded-[2.5rem] p-8' }
                }).then((result) => {
                    if (result.isConfirmed) window.location.href = urlAsal;
                });
            });
        });


    </script>
</body>
</html>
