<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <title>BOE-Space Reserve | Edit Fasilitas</title>
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
                    Edit <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#1265A8] to-blue-400" x-text="tipe === 'asrama' ? 'Asrama' : 'Aula'">Facility</span> Data
                </h2>
                <div class="h-1.5 w-12 bg-gradient-to-r from-[#1265A8] to-blue-400 mx-auto mt-4 rounded-full"></div>

                <div class="flex justify-center gap-4 mt-8">
                    <button type="button" @click="confirmTypeChange('asrama')" :class="tipe === 'asrama' ? 'bg-[#1265A8] text-white shadow-lg' : 'bg-slate-100 text-slate-400 hover:bg-slate-200'" class="px-8 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all duration-300">Asrama</button>
                    <button type="button" @click="confirmTypeChange('aula')" :class="tipe === 'aula' ? 'bg-[#1265A8] text-white shadow-lg' : 'bg-slate-100 text-slate-400 hover:bg-slate-200'" class="px-8 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all duration-300">Aula</button>
                    <input type="hidden" name="tipe" :value="tipe">
                </div>
            </div>

            <form id="mainForm" action="{{ route('fasilitas.update', $fasilitas->id) }}" method="POST" enctype="multipart/form-data" class="p-8 lg:p-12 pt-6" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" name="tipe" :value="tipe">
                <input type="hidden" name="paket_harian" id="paketHarianInput" value="">
                <input type="hidden" name="rooms_data" id="roomsDataInput" value="">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

                    <div class="space-y-6">

                        <div class="group">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Fasilitas</label>
                            <input type="text" id="inputNama" name="nama" value="{{ old('nama', $fasilitas->nama) }}"
                                class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all duration-300 shadow-sm font-semibold" required>
                            <span class="error-msg" id="errNama">⚠ Nama minimal 2 huruf dan tidak boleh mengandung angka.</span>
                        </div>

                        <div class="group">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Deskripsi Singkat</label>
                            <textarea id="inputDeskripsi" name="deskripsi" rows="3"
                                class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all duration-300 shadow-sm resize-none font-medium leading-relaxed" required>{{ old('deskripsi', $fasilitas->deskripsi) }}</textarea>
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
                                <input type="text" name="jam_operasional" value="{{ old('jam_operasional', $fasilitas->jam_operasional) }}" placeholder="08.00 - 22.00"
                                    class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all duration-300 shadow-sm font-semibold">
                            </div>
                            <div x-show="tipe === 'aula'">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Kapasitas (Orang)</label>
                                <input type="number" name="max_dewasa" value="{{ old('max_dewasa', $fasilitas->max_dewasa) }}" placeholder="Total"
                                    class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all duration-300 shadow-sm font-semibold">
                            </div>
                        </div>

                        {{-- Max Durasi Sewa — full-width 4-column grid, asrama only --}}
                        <div x-show="tipe === 'asrama'">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Max Durasi Sewa</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">

                                <div>
                                    <label class="block text-[10px] font-black text-[#1265A8] uppercase tracking-widest mb-2 ml-1">Hari</label>
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

                        <div x-show="tipe === 'asrama'">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">
                                Jumlah Kamar Tersedia
                            </label>
                            <div class="kamar-stepper-wrap" id="kamarStepperWrap">
                                <button type="button" class="kamar-stepper-btn"
                                    @click="adjustKamar(-1)"
                                    :disabled="jumlahKamar <= 1"
                                    aria-label="Kurangi kamar">−</button>
                                <input type="number"
                                    name="jumlah_kamar"
                                    x-model.number="jumlahKamar"
                                    min="1" max="999"
                                    class="kamar-stepper-input"
                                    aria-label="Jumlah kamar">
                                <button type="button" class="kamar-stepper-btn"
                                    @click="adjustKamar(1)"
                                    aria-label="Tambah kamar">+</button>
                            </div>
                            <div class="kamar-badge-edit">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span x-text="jumlahKamar + ' kamar tersedia'"></span>
                            </div>
                        </div>

                        <div class="group">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Labels / Fitur</label>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <template x-for="label in labels[tipe]" :key="label">
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="labels[]" :value="label" x-model="selectedLabels" class="hidden">
                                        <span :class="selectedLabels.includes(label) ? 'bg-[#1265A8] text-white border-[#1265A8]' : 'bg-white text-slate-400 border-slate-200'"
                                            class="px-4 py-2 rounded-xl border text-[10px] font-black uppercase tracking-widest transition-all duration-300 block"
                                            x-text="label"></span>
                                    </label>
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

                        <div x-show="tipe === 'asrama'" x-cloak class="w-full bg-gradient-to-br from-blue-50/40 to-indigo-50/30 rounded-3xl border border-blue-100/60 p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#1265A8]/10 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-[#1265A8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xs font-black uppercase tracking-widest text-slate-500">Room Specifications</h3>
                                        <p class="text-[10px] font-semibold text-slate-400" x-text="'Room ' + (currentRoomIndex + 1) + ' of ' + rooms.length"></p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" class="room-slider-btn" @click="prevRoom" :disabled="currentRoomIndex <= 0">‹</button>
                                    <button type="button" class="room-slider-btn" @click="nextRoom" :disabled="currentRoomIndex >= rooms.length - 1">›</button>
                                </div>
                            </div>

                            <div class="flex justify-center gap-2">
                                <template x-for="(r, ri) in rooms" :key="ri">
                                    <div @click="currentRoomIndex = ri"
                                        :class="ri === currentRoomIndex ? 'room-dot active' : 'room-dot'"
                                        :title="'Room ' + (ri + 1)"></div>
                                </template>
                            </div>

                            <div class="relative">
                                <template x-for="(room, ri) in rooms" :key="ri">
                                    <div x-show="ri === currentRoomIndex"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 translate-x-8"
                                        x-transition:enter-end="opacity-100 translate-x-0"
                                        :data-room-index="ri"
                                        class="bg-white rounded-2xl border border-slate-200/80 p-5 space-y-4">

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-0.5">Tipe Kamar</label>
                                                @include('admin.dashboard.partials._room_type_dropdown', ['roomIndex' => 'ri', 'roomsVar' => 'rooms'])
                                            </div>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-0.5">Jumlah</label>
                                                    <input type="number" x-model.number="rooms[ri].jumlah" min="1"
                                                        class="w-full px-4 py-3.5 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all font-semibold text-sm">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Keunggulan Tipe Kamar --}}
                                        <div>
                                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-0.5">Keunggulan Tipe Kamar</label>
                                            <textarea x-model="rooms[ri].keunggulan" rows="2"
                                                placeholder="Deskripsi singkat keunggulan tipe kamar ini..."
                                                class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none font-medium text-sm resize-none"></textarea>
                                        </div>

                                        {{-- Ukuran (Panjang × Lebar) & Konfigurasi Ranjang --}}
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-0.5">Ukuran Kamar</label>
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
                                            <div>
                                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-0.5">Konfigurasi Ranjang</label>
                                                <input type="text" x-model="rooms[ri].ranjang"
                                                    placeholder="Contoh: 1 King Bed"
                                                    class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none font-bold text-sm">
                                            </div>
                                        </div>

                                        {{-- Nomor Kamar Tagging --}}
                                        <div>
                                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-0.5">Nomor Kamar</label>
                                            <div class="flex gap-2">
                                                <input type="text"
                                                    x-model="rooms[ri].temp_input"
                                                    @keydown.enter.prevent="addNomorKamar(ri)"
                                                    :disabled="rooms[ri].nomor_kamar.length >= rooms[ri].jumlah"
                                                    :class="rooms[ri].nomor_kamar.length >= rooms[ri].jumlah ? 'bg-gray-100 cursor-not-allowed text-gray-400' : 'bg-white'"
                                                    placeholder="Ketik nomor lalu Enter..."
                                                    class="flex-1 px-4 py-3 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none font-semibold text-sm transition-all">
                                                <button type="button"
                                                    @click="addNomorKamar(ri)"
                                                    :disabled="rooms[ri].nomor_kamar.length >= rooms[ri].jumlah"
                                                    :class="rooms[ri].nomor_kamar.length >= rooms[ri].jumlah ? 'bg-gray-100 cursor-not-allowed text-gray-400' : 'bg-[#1265A8] text-white hover:bg-slate-800'"
                                                    class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center transition-all font-black text-lg">
                                                    +
                                                </button>
                                            </div>
                                            <div class="mt-1.5 text-[10px] font-bold">
                                                <span x-show="rooms[ri].nomor_kamar.length < rooms[ri].jumlah" class="text-red-500">
                                                    🔴 Input Belum Selesai (<span x-text="rooms[ri].nomor_kamar.length"></span> dari <span x-text="rooms[ri].jumlah"></span>)
                                                </span>
                                                <span x-show="rooms[ri].nomor_kamar.length >= rooms[ri].jumlah && rooms[ri].jumlah > 0" class="text-green-600">
                                                    🟢 Semua Nomor Kamar Telah Di-input (<span x-text="rooms[ri].jumlah"></span> dari <span x-text="rooms[ri].jumlah"></span>)
                                                </span>
                                            </div>
                                            <div class="flex flex-wrap gap-1.5 mt-2">
                                                <template x-for="(tag, tagIdx) in rooms[ri].nomor_kamar" :key="tagIdx">
                                                    <span @click="removeNomorKamar(ri, tagIdx)"
                                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-[#1265A8] text-[10px] font-black cursor-pointer hover:bg-red-50 hover:border-red-200 hover:text-red-500 transition-all select-none">
                                                        <span x-text="tag"></span>
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </span>
                                                </template>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-0.5">Cap. Dewasa (Kamar)</label>
                                                <input type="number" x-model.number="rooms[ri].max_dewasa" min="1"
                                                    :name="'rooms[' + ri + '][max_dewasa]'"
                                                    class="w-full px-4 py-3.5 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all font-semibold text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-0.5">Cap. Anak (Kamar)</label>
                                                <input type="number" x-model.number="rooms[ri].max_anak" min="0"
                                                    :name="'rooms[' + ri + '][max_anak]'"
                                                    class="w-full px-4 py-3.5 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all font-semibold text-sm">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-0.5">Foto Kamar (3 foto)</label>
                                            <div class="grid grid-cols-3 gap-3">
                                                <template x-for="fi in [0, 1, 2]" :key="fi">
                                                    <div class="relative overflow-hidden rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/50 hover:border-[#1265A8] transition-all duration-300 h-20 flex items-center justify-center group/foto cursor-pointer">
                                                        <img :src="rooms[ri].fotoPreviews[fi]" class="absolute inset-0 w-full h-full object-cover z-10" x-show="rooms[ri].fotoPreviews[fi]">
                                                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover/foto:opacity-100 transition-opacity z-20 flex items-center justify-center" x-show="rooms[ri].fotoPreviews[fi]">
                                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                        </div>
                                                        <div class="relative z-20 flex flex-col items-center" x-show="!rooms[ri].fotoPreviews[fi]">
                                                            <svg class="w-4 h-4 text-slate-300 group-hover/foto:text-[#1265A8] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                                        </div>
                                                        <input type="file" accept="image/*"
                                                            :name="'room_fotos[' + ri + '][' + fi + ']'"
                                                            class="absolute inset-0 opacity-0 cursor-pointer z-30"
                                                            @change="handleRoomFoto($event, ri, fi)">
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- Fasilitas Kamar — 10 icon cards with micro-inputs --}}
                                        <div>
                                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-0.5">Fasilitas Kamar</label>
                                            <div class="grid grid-cols-5 sm:grid-cols-5 gap-1.5">
                                                <div class="flex flex-col items-center bg-white border border-slate-200 rounded-xl px-1.5 py-2.5 transition-all duration-200 hover:border-[#1265A8] hover:shadow-sm">
                                                    <svg class="w-4 h-4 text-[#1265A8] mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3-3m0 0l3 3m-3-3v8M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span class="text-[7px] font-black text-slate-500 uppercase tracking-wider mb-0.5 leading-tight text-center">AC</span>
                                                    <input type="number" x-model.number="rooms[ri].fasilitas.ac" min="0" placeholder="0"
                                                        class="w-11 h-6 text-center bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-[#1265A8] outline-none font-bold text-xs">
                                                </div>
                                                <div class="flex flex-col items-center bg-white border border-slate-200 rounded-xl px-1.5 py-2.5 transition-all duration-200 hover:border-[#1265A8] hover:shadow-sm">
                                                    <svg class="w-4 h-4 text-[#1265A8] mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5m0-5l-2 1m2-1l-2-1m2 1v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5m0 5l-2-1m2 1l2-1"/></svg>
                                                    <span class="text-[7px] font-black text-slate-500 uppercase tracking-wider mb-0.5 leading-tight text-center">Kipas Angin</span>
                                                    <input type="number" x-model.number="rooms[ri].fasilitas.kipas_angin" min="0" placeholder="0"
                                                        class="w-11 h-6 text-center bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-[#1265A8] outline-none font-bold text-xs">
                                                </div>
                                                <div class="flex flex-col items-center bg-white border border-slate-200 rounded-xl px-1.5 py-2.5 transition-all duration-200 hover:border-[#1265A8] hover:shadow-sm">
                                                    <svg class="w-4 h-4 text-[#1265A8] mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a1 1 0 001 1h16a1 1 0 001-1V7a1 1 0 00-1-1H4a1 1 0 00-1 1zm0 0l8 5 8-5M12 12l-8 5m8-5l8 5"/></svg>
                                                    <span class="text-[7px] font-black text-slate-500 uppercase tracking-wider mb-0.5 leading-tight text-center">Meja & Kursi</span>
                                                    <input type="number" x-model.number="rooms[ri].fasilitas.meja_kursi" min="0" placeholder="0"
                                                        class="w-11 h-6 text-center bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-[#1265A8] outline-none font-bold text-xs">
                                                </div>
                                                <div class="flex flex-col items-center bg-white border border-slate-200 rounded-xl px-1.5 py-2.5 transition-all duration-200 hover:border-[#1265A8] hover:shadow-sm">
                                                    <svg class="w-4 h-4 text-[#1265A8] mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                                    <span class="text-[7px] font-black text-slate-500 uppercase tracking-wider mb-0.5 leading-tight text-center">Lemari / Locker</span>
                                                    <input type="number" x-model.number="rooms[ri].fasilitas.lemari_locker" min="0" placeholder="0"
                                                        class="w-11 h-6 text-center bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-[#1265A8] outline-none font-bold text-xs">
                                                </div>
                                                <div class="flex flex-col items-center bg-white border border-slate-200 rounded-xl px-1.5 py-2.5 transition-all duration-200 hover:border-[#1265A8] hover:shadow-sm">
                                                    <svg class="w-4 h-4 text-[#1265A8] mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                    <span class="text-[7px] font-black text-slate-500 uppercase tracking-wider mb-0.5 leading-tight text-center">Stopkontak</span>
                                                    <input type="number" x-model.number="rooms[ri].fasilitas.stopkontak" min="0" placeholder="0"
                                                        class="w-11 h-6 text-center bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-[#1265A8] outline-none font-bold text-xs">
                                                </div>
                                                <div class="flex flex-col items-center bg-white border border-slate-200 rounded-xl px-1.5 py-2.5 transition-all duration-200 hover:border-[#1265A8] hover:shadow-sm">
                                                    <svg class="w-4 h-4 text-[#1265A8] mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 20h16M4 20V4a2 2 0 012-2h12a2 2 0 012 2v16M4 20h16M8 12h8M8 8h8M8 16h4"/></svg>
                                                    <span class="text-[7px] font-black text-slate-500 uppercase tracking-wider mb-0.5 leading-tight text-center">K. Mandi Dalam</span>
                                                    <input type="number" x-model.number="rooms[ri].fasilitas.kamar_mandi_dalam" min="0" placeholder="0"
                                                        class="w-11 h-6 text-center bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-[#1265A8] outline-none font-bold text-xs">
                                                </div>
                                                <div class="flex flex-col items-center bg-white border border-slate-200 rounded-xl px-1.5 py-2.5 transition-all duration-200 hover:border-[#1265A8] hover:shadow-sm">
                                                    <svg class="w-4 h-4 text-[#1265A8] mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                                    <span class="text-[7px] font-black text-slate-500 uppercase tracking-wider mb-0.5 leading-tight text-center">Water Heater</span>
                                                    <input type="number" x-model.number="rooms[ri].fasilitas.water_heater" min="0" placeholder="0"
                                                        class="w-11 h-6 text-center bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-[#1265A8] outline-none font-bold text-xs">
                                                </div>
                                                <div class="flex flex-col items-center bg-white border border-slate-200 rounded-xl px-1.5 py-2.5 transition-all duration-200 hover:border-[#1265A8] hover:shadow-sm">
                                                    <svg class="w-4 h-4 text-[#1265A8] mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                                                    <span class="text-[7px] font-black text-slate-500 uppercase tracking-wider mb-0.5 leading-tight text-center">Bantal & Sprei</span>
                                                    <input type="number" x-model.number="rooms[ri].fasilitas.bantal_set_sprei" min="0" placeholder="0"
                                                        class="w-11 h-6 text-center bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-[#1265A8] outline-none font-bold text-xs">
                                                </div>
                                                <div class="flex flex-col items-center bg-white border border-slate-200 rounded-xl px-1.5 py-2.5 transition-all duration-200 hover:border-[#1265A8] hover:shadow-sm">
                                                    <svg class="w-4 h-4 text-[#1265A8] mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 4v1m-6-7a2 2 0 00-2 2m0 4v1M6 21h6m-3-3v3"/></svg>
                                                    <span class="text-[7px] font-black text-slate-500 uppercase tracking-wider mb-0.5 leading-tight text-center">Gantungan Baju</span>
                                                    <input type="number" x-model.number="rooms[ri].fasilitas.gantungan_baju" min="0" placeholder="0"
                                                        class="w-11 h-6 text-center bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-[#1265A8] outline-none font-bold text-xs">
                                                </div>
                                                <div class="flex flex-col items-center bg-white border border-slate-200 rounded-xl px-1.5 py-2.5 transition-all duration-200 hover:border-[#1265A8] hover:shadow-sm">
                                                    <svg class="w-4 h-4 text-[#1265A8] mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                                                    <span class="text-[7px] font-black text-slate-500 uppercase tracking-wider mb-0.5 leading-tight text-center">Kaca Rias</span>
                                                    <input type="number" x-model.number="rooms[ri].fasilitas.kaca_rias" min="0" placeholder="0"
                                                        class="w-11 h-6 text-center bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-[#1265A8] outline-none font-bold text-xs">
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-0.5">Harga Sewa</label>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Harian <span class="text-red-400">*</span></label>
                                                    <div class="relative">
                                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 font-black text-[#1265A8] text-xs">Rp</span>
                                                        <input type="text"
                                                            data-price-field="harga_harian"
                                                            :value="(room.harga_harian !== '' && room.harga_harian != null) ? new Intl.NumberFormat('id-ID').format(room.harga_harian) : ''"
                                                            @input="room.harga_harian = $event.target.value.replace(/\D/g, '') !== '' ? Number($event.target.value.replace(/\D/g, '')) : ''; $event.target.value = (room.harga_harian !== '' && room.harga_harian != null) ? new Intl.NumberFormat('id-ID').format(room.harga_harian) : ''; syncPaketHarian()"
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
                                                            @input="room.harga_mingguan = $event.target.value.replace(/\D/g, '') !== '' ? Number($event.target.value.replace(/\D/g, '')) : ''; $event.target.value = (room.harga_mingguan !== '' && room.harga_mingguan != null) ? new Intl.NumberFormat('id-ID').format(room.harga_mingguan) : ''; syncPaketHarian()"
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
                                                            @input="room.harga_bulanan = $event.target.value.replace(/\D/g, '') !== '' ? Number($event.target.value.replace(/\D/g, '')) : ''; $event.target.value = (room.harga_bulanan !== '' && room.harga_bulanan != null) ? new Intl.NumberFormat('id-ID').format(room.harga_bulanan) : ''; syncPaketHarian()"
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
                                                            @input="room.harga_tahunan = $event.target.value.replace(/\D/g, '') !== '' ? Number($event.target.value.replace(/\D/g, '')) : ''; $event.target.value = (room.harga_tahunan !== '' && room.harga_tahunan != null) ? new Intl.NumberFormat('id-ID').format(room.harga_tahunan) : ''; syncPaketHarian()"
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
                tipe: '{{ $fasilitas->tipe ?? 'asrama' }}',
                jumlahKamar: {{ $fasilitas->jumlah_kamar ?? 1 }},
                rooms: @json($rooms),
                currentRoomIndex: 0,
                roomTypes: @json($roomTypes->toArray()),
                customRoomType: '',
                showCustomRoomInput: false,

                labels: {
                    asrama: [...new Set(['Shower', 'AC', 'Wifi', 'Parkir', 'TV', 'Lemari', ...@json($fasilitas->labels ?? [])])],
                    aula:   [...new Set(['Wifi', 'Sound System', 'AC', 'Kursi', 'Meja', 'Panggung', 'Proyektor', ...@json($fasilitas->labels ?? [])])]
                },
                selectedLabels: @json($fasilitas->labels ?? []),
                customLabel: '',
                galleryPreviews: [
                    @if(isset($fasilitas->gallery[0])) '{{ asset('storage/fasilitas/gallery/' . $fasilitas->gallery[0]) }}' @else null @endif,
                    @if(isset($fasilitas->gallery[1])) '{{ asset('storage/fasilitas/gallery/' . $fasilitas->gallery[1]) }}' @else null @endif,
                    @if(isset($fasilitas->gallery[2])) '{{ asset('storage/fasilitas/gallery/' . $fasilitas->gallery[2]) }}' @else null @endif
                ],
                galleryErrors: [false, false, false],

                init() {
                    // Expose this component to window so roomTypeDropdown instances
                    // can access the shared roomTypes array and per-room tipe values.
                    // Requirements: 6.4, 11.2
                    window.__alpineRoot = this;

                    if (!this.rooms || this.rooms.length === 0) {
                        this.rooms = this.generateDefaultRooms();
                    }

                    this.rooms.forEach(r => {
                        // Hydrate fotoPreviews from saved DB foto paths
                        if (!r.fotoPreviews) {
                            r.fotoPreviews = [null, null, null];
                        }
                        if (Array.isArray(r.foto) && r.foto.length > 0) {
                            r.foto.forEach((filename, fi) => {
                                if (filename && !r.fotoPreviews[fi]) {
                                    r.fotoPreviews[fi] = '/storage/fasilitas/rooms/' + filename;
                                }
                            });
                        }
                        if (!r.foto) {
                            r.foto = [];
                        }
                        if (!r.nomor_kamar) {
                            r.nomor_kamar = [];
                        }
                        if (r.temp_input === undefined) {
                            r.temp_input = '';
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
                        if (r.ranjang === undefined) {
                            r.ranjang = '';
                        }
                        if (!r.fasilitas) {
                            r.fasilitas = { ac: 0, kipas_angin: 0, meja_kursi: 0, lemari_locker: 0, stopkontak: 0, kamar_mandi_dalam: 0, water_heater: 0, bantal_set_sprei: 0, gantungan_baju: 0, kaca_rias: 0 };
                        }
                        // Ensure numeric prices are stored as numbers (not strings) to avoid 0 display bug
                        r.harga_harian   = r.harga_harian   !== '' && r.harga_harian   != null ? Number(r.harga_harian)   : '';
                        r.harga_mingguan = r.harga_mingguan !== '' && r.harga_mingguan != null ? Number(r.harga_mingguan) : '';
                        r.harga_bulanan  = r.harga_bulanan  !== '' && r.harga_bulanan  != null ? Number(r.harga_bulanan)  : '';
                        r.harga_tahunan  = r.harga_tahunan  !== '' && r.harga_tahunan  != null ? Number(r.harga_tahunan)  : '';
                    });

                    this.$watch('jumlahKamar', (newVal) => {
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
                        this.syncPaketHarian();
                    });

                    this.$watch('rooms', () => {
                        // Auto-trim nomor_kamar tags if jumlah was decreased
                        this.rooms.forEach(r => {
                            if (r.nomor_kamar.length > r.jumlah) {
                                r.nomor_kamar.splice(r.jumlah);
                            }
                        });
                        this.syncPaketHarian();
                    }, { deep: true });

                    if (this.rooms.length > 0) {
                        this.syncPaketHarian();
                    }
                },

                createEmptyRoom(index) {
                    return {
                        tipe: '',
                        jumlah: 1,
                        nomor_kamar: [],
                        temp_input: '',
                        kode_blok: '',
                        max_dewasa: {{ $fasilitas->max_dewasa ?? 1 }},
                        max_anak: {{ $fasilitas->max_anak ?? 0 }},
                        foto: [],
                        fotoPreviews: [null, null, null],
                        harga_harian: index === 0 ? '{{ $fasilitas->harga }}' : '',
                        harga_mingguan: '',
                        harga_bulanan: index === 0 ? '{{ $fasilitas->harga_bulanan }}' : '',
                        harga_tahunan: '',
                        keunggulan: '',
                        panjang: '',
                        lebar: '',
                        ranjang: '',
                        fasilitas: {
                            ac: 0,
                            kipas_angin: 0,
                            meja_kursi: 0,
                            lemari_locker: 0,
                            stopkontak: 0,
                            kamar_mandi_dalam: 0,
                            water_heater: 0,
                            bantal_set_sprei: 0,
                            gantungan_baju: 0,
                            kaca_rias: 0,
                        },
                    };
                },

                generateDefaultRooms() {
                    const rooms = [];
                    const count = this.jumlahKamar;
                    for (let i = 0; i < count; i++) {
                        rooms.push(this.createEmptyRoom(i));
                    }
                    return rooms;
                },

                syncPaketHarian() {
                    const payload = this.rooms.map(r => {
                        const { fotoPreviews, ...rest } = r;
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

                    // ── Strict submit guard (asrama only) ──────────────────────
                    const btnSimpan = document.querySelector('#mainForm button[type="submit"]');
                    if (btnSimpan && this.tipe === 'asrama') {
                        const reasons = [];

                        const hasMismatch = this.rooms.some(r => r.nomor_kamar.length < r.jumlah);
                        if (hasMismatch) reasons.push('Nomor kamar belum lengkap.');

                        const missingTipe = this.rooms.some(r => !r.tipe || r.tipe.trim() === '');
                        if (missingTipe) reasons.push('Tipe kamar belum dipilih.');

                        const missingPrice = this.rooms.some(r =>
                            !r.harga_harian   || Number(r.harga_harian)   <= 0 ||
                            !r.harga_mingguan || Number(r.harga_mingguan) <= 0 ||
                            !r.harga_bulanan  || Number(r.harga_bulanan)  <= 0 ||
                            !r.harga_tahunan  || Number(r.harga_tahunan)  <= 0
                        );
                        if (missingPrice) reasons.push('Semua harga sewa harus diisi dan lebih dari 0.');

                        btnSimpan.disabled = reasons.length > 0;
                        btnSimpan.title    = reasons.join(' ');

                        // Visual: red border on empty price inputs
                        this.rooms.forEach((r, ri) => {
                            const card = document.querySelector(`[data-room-index="${ri}"]`);
                            if (!card) return;
                            ['harga_harian','harga_mingguan','harga_bulanan','harga_tahunan'].forEach(field => {
                                const inp = card.querySelector(`[data-price-field="${field}"]`);
                                if (!inp) return;
                                const val = Number(r[field]);
                                if (!val || val <= 0) {
                                    inp.classList.add('field-error');
                                } else {
                                    inp.classList.remove('field-error');
                                }
                            });
                        });
                    }
                },

                adjustKamar(delta) {
                    const next = Math.min(999, Math.max(1, this.jumlahKamar + delta));
                    this.jumlahKamar = next;
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

                addNomorKamar(ri) {
                    const room = this.rooms[ri];
                    if (!room) return;
                    const val = (room.temp_input || '').trim();
                    if (!val) return;
                    if (room.nomor_kamar.includes(val)) return;
                    if (room.nomor_kamar.length >= room.jumlah) return;
                    room.nomor_kamar.push(val);
                    room.temp_input = '';
                    this.syncPaketHarian();
                },

                removeNomorKamar(ri, idx) {
                    const room = this.rooms[ri];
                    if (!room) return;
                    const removed = room.nomor_kamar.splice(idx, 1);
                    if (removed.length > 0) {
                        room.temp_input = removed[0];
                    }
                    this.syncPaketHarian();
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

                handleRoomFoto(event, roomIndex, fotoIndex) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const MAX = 2 * 1024 * 1024;
                    if (file.size > MAX) {
                        Swal.fire({
                            title: 'File Terlalu Besar',
                            text: 'Foto kamar maksimal 2 MB.',
                            icon: 'warning',
                            confirmButtonColor: '#1265A8',
                            confirmButtonText: 'OK',
                            customClass: { popup: 'rounded-[2.5rem] p-8' }
                        });
                        event.target.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        if (!this.rooms[roomIndex].fotoPreviews) {
                            this.rooms[roomIndex].fotoPreviews = [null, null, null];
                        }
                        this.rooms[roomIndex].fotoPreviews[fotoIndex] = e.target.result;
                    };
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

            const inputNama = document.getElementById('inputNama');
            const errNama   = document.getElementById('errNama');

            function validateNama() {
                const val = inputNama.value.trim();
                if (val.length < 2 || /\d/.test(val)) {
                    showError(errNama, inputNama);
                    return false;
                }
                clearError(errNama, inputNama);
                return true;
            }
            inputNama.addEventListener('blur', validateNama);
            inputNama.addEventListener('input', function () {
                const val = this.value.trim();
                if (val.length >= 2 && !/\d/.test(val)) clearError(errNama, inputNama);
            });

            const inputDeskripsi = document.getElementById('inputDeskripsi');
            const errDeskripsi   = document.getElementById('errDeskripsi');

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
            });

            const form = document.getElementById('mainForm');
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const namaOk     = validateNama();
                const deskOk     = validateDeskripsi();

                let galleryOk = true;
                [0, 1, 2].forEach(i => {
                    const inp = document.getElementById('galleryInput' + i);
                    if (inp && inp.files[0] && inp.files[0].size > MAX_SIZE) galleryOk = false;
                });

                // Room nomor kamar completeness guard (asrama only)
                let roomsOk = true;
                const alpineRoot = window.__alpineRoot;
                if (alpineRoot && alpineRoot.tipe === 'asrama' && alpineRoot.rooms) {
                    const incomplete = alpineRoot.rooms.filter(r => r.nomor_kamar.length < r.jumlah);
                    if (incomplete.length > 0) {
                        roomsOk = false;
                        Swal.fire({
                            title: 'Nomor Kamar Belum Lengkap',
                            text: `${incomplete.length} tipe kamar masih memiliki nomor kamar yang belum di-input. Lengkapi sebelum menyimpan.`,
                            icon: 'warning',
                            confirmButtonColor: '#1265A8',
                            confirmButtonText: 'OK',
                            customClass: { popup: 'rounded-[2.5rem] p-8' }
                        });
                        return;
                    }
                }

                if (!namaOk || !deskOk || !galleryOk) {
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
                        document.getElementById('loadingOverlay').classList.remove('hidden');
                        form.submit();
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

        // ── roomTypeDropdown Alpine component ─────────────────────────────────
        // Registered here so it is available on this page in addition to the
        // Create Fasilitas page. Both pages use the same component definition.
        document.addEventListener('alpine:init', () => {
            Alpine.data('roomTypeDropdown', function () {
                const el   = this.$el;
                const csrf = '{{ csrf_token() }}';

                return {
                    open:         false,
                    addMode:      false,
                    editingId:    null,
                    editingName:  '',
                    newTypeName:  '',
                    saving:       false,
                    deleting:     null,
                    errorMessage: '',
                    // Local reactive copy keeps the button label in sync
                    // without depending on cross-component window.__alpineRoot.
                    selectedTipe: '',

                    get rVar()  { return el.dataset.roomsVar; },
                    get rIdx()  { return parseInt(el.dataset.roomIndex); },
                    get hiddenName() { return `${this.rVar}[${this.rIdx}][tipe]`; },

                    init() {
                        const arr = window.__alpineRoot?.[this.rVar];
                        this.selectedTipe = arr?.[this.rIdx]?.tipe ?? '';

                        // When the room slider changes, data-room-index is updated
                        // reactively by Alpine; observe it to re-seed selectedTipe.
                        const obs = new MutationObserver(() => {
                            const a2 = window.__alpineRoot?.[this.rVar];
                            this.selectedTipe = a2?.[this.rIdx]?.tipe ?? '';
                        });
                        obs.observe(el, { attributes: true, attributeFilter: ['data-room-index'] });
                    },

                    allTypes() {
                        return window.__alpineRoot?.roomTypes ?? [];
                    },

                    currentTipe() {
                        return this.selectedTipe;
                    },

                    setTipe(val) {
                        this.selectedTipe = val;
                        const arr = window.__alpineRoot?.[this.rVar];
                        if (arr?.[this.rIdx] !== undefined) {
                            arr[this.rIdx].tipe = val;
                            if (window.__alpineRoot?.syncPaketHarian) {
                                window.__alpineRoot.syncPaketHarian();
                            }
                        }
                    },

                    toggle() {
                        this.open = !this.open;
                        if (!this.open) { this.addMode = false; this.editingId = null; this.errorMessage = ''; }
                    },

                    close() {
                        this.open = false; this.addMode = false; this.editingId = null; this.errorMessage = '';
                    },

                    selectType(name) { this.setTipe(name); this.close(); },

                    handleEscape() { this.close(); },

                    startAdd() {
                        this.addMode = true; this.newTypeName = ''; this.errorMessage = '';
                        this.$nextTick(() => this.$refs.newInput?.focus());
                    },

                    async saveNew() {
                        const name = this.newTypeName.trim();
                        if (!name || this.saving) return;
                        this.saving = true; this.errorMessage = '';
                        try {
                            const res  = await fetch('/admin/room-types', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                                body: JSON.stringify({ name }),
                            });
                            const data = await res.json();
                            if (res.ok) {
                                window.__alpineRoot?.roomTypes.push({ id: data.id, name: data.name });
                                this.selectType(data.name); this.newTypeName = ''; this.addMode = false;
                            } else if (res.status === 422) {
                                const errors = data.errors?.name ?? [];
                                this.errorMessage = errors.some(m => String(m).toLowerCase().includes('unique'))
                                    ? 'Tipe kamar ini sudah ada.' : (data.message || 'Validasi gagal.');
                            } else { this.errorMessage = data.message || 'Gagal menyimpan tipe kamar.'; }
                        } catch { this.errorMessage = 'Terjadi kesalahan jaringan.'; }
                        finally  { this.saving = false; }
                    },

                    startEdit(id, name) { this.editingId = id; this.editingName = name; this.errorMessage = ''; },

                    async saveEdit(id) {
                        const name = this.editingName.trim();
                        if (!name || this.saving) return;
                        this.saving = true; this.errorMessage = '';
                        try {
                            const res  = await fetch(`/admin/room-types/${id}`, {
                                method: 'PUT',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                                body: JSON.stringify({ name }),
                            });
                            const data = await res.json();
                            if (res.ok) {
                                const types = window.__alpineRoot?.roomTypes ?? [];
                                const idx   = types.findIndex(t => t.id === id);
                                if (idx !== -1) {
                                    const old = types[idx].name; types[idx].name = data.name;
                                    if (this.currentTipe() === old) this.setTipe(data.name);
                                }
                                this.editingId = null;
                            } else if (res.status === 422) {
                                this.errorMessage = String(data.message || '').toLowerCase().includes('unique')
                                    ? 'Nama sudah digunakan.' : (data.message || 'Validasi gagal.');
                            } else { this.errorMessage = data.message || 'Gagal mengubah tipe kamar.'; }
                        } catch { this.errorMessage = 'Terjadi kesalahan jaringan.'; }
                        finally  { this.saving = false; }
                    },

                    async deleteType(index) {
                        const types = window.__alpineRoot?.roomTypes ?? [];
                        const t     = types[index];
                        if (!t) return;
                        const result = await Swal.fire({
                            title: `Hapus "${t.name}"?`,
                            text: `Tipe kamar "${t.name}" akan dihapus permanen.`,
                            icon: 'warning', showCancelButton: true,
                            confirmButtonColor: '#E24B4A', cancelButtonColor: '#94a3b8',
                            confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal',
                            reverseButtons: true, customClass: { popup: 'rounded-[2.5rem] p-8' },
                        });
                        if (!result.isConfirmed) return;
                        this.deleting = index;
                        this.errorMessage = '';
                        try {
                            if (t.id) {
                                await window.axios.delete(`/admin/room-types/${t.id}`, {
                                    headers: { 'X-CSRF-TOKEN': csrf },
                                });
                            }
                            if (this.currentTipe() === t.name) this.setTipe('');
                            types.splice(index, 1);
                        } catch {
                            this.errorMessage = 'Gagal menghapus tipe kamar.';
                        } finally {
                            this.deleting = null;
                        }
                    },
                };
            });
        });
    </script>
</body>
</html>
