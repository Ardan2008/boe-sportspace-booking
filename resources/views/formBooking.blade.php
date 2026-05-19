<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <title>BOE-Space Reserve | Form Reservasi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style> 
        body { font-family: 'Poppins', sans-serif; }
        [x-cloak] { display: none !important; }
        .step-transition { transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); }

        /* ── Status Colors (Mirrored from Admin) ── */
        .status-ready       { background-color: #d1fae5; color: #065f46; }
        .status-pending     { background-color: #fef9c3; color: #854d0e; }
        .status-booked      { background-color: #dbeafe; color: #1e40af; }
        .status-blocked     { background-color: #1e293b; color: #f1f5f9; }
        .status-maintenance { background-color: #fee2e2; color: #991b1b; }
        .status-past        { background-color: #f1f5f9; color: #94a3b8; }
        .status-closed      { background-color: #e2e8f0; color: #94a3b8; opacity: 0.5; cursor: not-allowed; }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-4px); }
            40%, 80% { transform: translateX(4px); }
        }
        .animate-shake {
            animation: shake 0.4s ease-in-out;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-gray-100 min-h-screen font-['Poppins']">

<main class="flex flex-col items-center justify-start pt-32 pb-20 px-4" 
    x-cloak
    x-data="bookingForm({
        facilities: {{ $facilities->toJson() }},
        selectedFacilityId: '{{ $selectedId ?? '' }}'
    })">

    <div class="w-full max-w-2xl bg-white/80 backdrop-blur-xl p-8 md:p-12 rounded-[3.5rem] shadow-2xl border border-white/60 relative overflow-hidden">
        
        {{-- Progress Bar --}}
        <div class="absolute top-0 left-0 w-full h-2 bg-gray-100">
            <div class="h-full bg-blue-600 transition-all duration-700" :style="'width: ' + (step * 25) + '%'"></div>
        </div>

        {{-- Step 1: Initial Choice --}}
        <div x-show="step === 1" x-transition class="step-transition">
            <div class="text-center mb-10">
                <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] bg-blue-50 px-4 py-1.5 rounded-full border border-blue-100">Langkah 1/4</span>
                <h2 class="text-3xl font-black text-gray-900 mt-6 uppercase leading-tight">Pilih Tipe Pilihan</h2>
                <p class="text-sm text-gray-400 font-medium mt-2">Tentukan durasi pemesanan Anda di BOE Malang.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <button @click="packageType = 'harian'; nextStep()" 
                    class="group relative p-8 bg-white border-2 border-gray-100 rounded-[2.5rem] hover:border-blue-600 transition-all duration-500 hover:shadow-2xl hover:-translate-y-2">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500">
                        <svg class="w-8 h-8 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-800 uppercase tracking-tighter">Booking-Harian</h3>
                    <p class="text-xs text-gray-400 mt-2 font-medium">Cocok untuk kebutuhan jangka pendek atau harian.</p>
                </button>

                <button @click="packageType = 'bulanan'; nextStep()" 
                    class="group relative p-8 bg-white border-2 border-gray-100 rounded-[2.5rem] hover:border-blue-600 transition-all duration-500 hover:shadow-2xl hover:-translate-y-2">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-800 uppercase tracking-tighter">Booking-Bulanan</h3>
                    <p class="text-xs text-gray-400 mt-2 font-medium">Lebih hemat untuk kebutuhan jangka panjang (Bulanan).</p>
                </button>
            </div>
            
            <div class="mt-12 flex justify-center">
                <button @click="confirmCancel()" class="text-gray-400 hover:text-red-500 font-bold uppercase tracking-widest text-xs transition-colors">Batal Booking</button>
            </div>
        </div>

        {{-- Step 2: Configuration --}}
        <div x-show="step === 2" x-transition class="step-transition">
            <div class="text-center mb-10">
                <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] bg-blue-50 px-4 py-1.5 rounded-full border border-blue-100">Langkah 2/4</span>
                <h2 class="text-3xl font-black text-gray-900 mt-6 uppercase leading-tight">Konfigurasi Paket</h2>
                <p class="text-sm text-gray-400 font-medium mt-2" x-text="'Tipe: ' + packageType.toUpperCase()"></p>
            </div>

            <div class="space-y-6">
                {{-- Duration --}}
                <div class="flex items-center justify-between p-6 bg-gray-50 rounded-3xl border border-gray-100">
                    <div>
                        <h4 class="font-black text-gray-800 uppercase tracking-tighter">Durasi Booking</h4>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest" x-text="packageType === 'harian' ? 'Satuan: Hari' : 'Satuan: Bulan'"></p>
                    </div>
                    <div class="flex items-center gap-6">
                        <button @click="dec('duration', 1)" class="w-12 h-12 bg-white shadow-sm rounded-2xl flex items-center justify-center font-black text-xl text-blue-600 hover:bg-blue-600 hover:text-white transition-all">-</button>
                        <span class="text-2xl font-black text-gray-800" x-text="duration"></span>
                        <button @click="inc('duration')" class="w-12 h-12 bg-white shadow-sm rounded-2xl flex items-center justify-center font-black text-xl text-blue-600 hover:bg-blue-600 hover:text-white transition-all">+</button>
                    </div>
                </div>

                {{-- Guests --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-6 bg-gray-50 rounded-3xl border border-gray-100">
                        <h4 class="font-black text-gray-800 uppercase tracking-tighter mb-4" x-text="currentFacility?.tipe === 'aula' ? 'Total Kapasitas' : 'Dewasa'">Dewasa</h4>
                        <div class="flex items-center justify-between">
                            <button @click="dec('adults', 1)" class="w-10 h-10 bg-white rounded-xl flex items-center justify-center font-black text-blue-600 shadow-sm">-</button>
                            <span class="text-xl font-black text-gray-800" x-text="adults"></span>
                            <button @click="inc('adults')" class="w-10 h-10 bg-white rounded-xl flex items-center justify-center font-black text-blue-600 shadow-sm">+</button>
                        </div>
                    </div>
                    {{-- Hide children for Aula --}}
                    <div x-show="currentFacility?.tipe === 'asrama'" class="p-6 bg-gray-50 rounded-3xl border border-gray-100">
                        <h4 class="font-black text-gray-800 uppercase tracking-tighter mb-4">Anak</h4>
                        <div class="flex items-center justify-between">
                            <button @click="dec('children', 0)" class="w-10 h-10 bg-white rounded-xl flex items-center justify-center font-black text-blue-600 shadow-sm">-</button>
                            <span class="text-xl font-black text-gray-800" x-text="children"></span>
                            <button @click="inc('children')" class="w-10 h-10 bg-white rounded-xl flex items-center justify-center font-black text-blue-600 shadow-sm">+</button>
                        </div>
                    </div>
                </div>

                {{-- Child Ages --}}
                <div x-show="currentFacility?.tipe === 'asrama' && children > 0" x-transition class="p-6 bg-blue-50/20 rounded-3xl border border-blue-100">
                    <h4 class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-4">Umur Anak (Tahun)</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <template x-for="(age, idx) in childAges" :key="idx">
                            <input type="number" x-model="childAges[idx]" placeholder="0" class="w-full p-3 bg-white border border-gray-200 rounded-xl text-center font-bold text-sm outline-none focus:border-blue-400">
                        </template>
                    </div>
                </div>

                {{-- Rooms --}}
                <div x-show="currentFacility?.tipe === 'asrama'" class="p-6 bg-gray-50 rounded-3xl border border-gray-100 flex items-center justify-between">
                    <div>
                        <h4 class="font-black text-gray-800 uppercase tracking-tighter">Jumlah Kamar</h4>
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest italic">* 1 Kamar Max 1 Dewasa</p>
                    </div>
                    <div class="flex items-center gap-6">
                        <button @click="dec('rooms', 1)" class="w-12 h-12 bg-white shadow-sm rounded-2xl flex items-center justify-center font-black text-xl text-blue-600">-</button>
                        <span class="text-2xl font-black text-gray-800" x-text="rooms"></span>
                        <button @click="inc('rooms')" class="w-12 h-12 bg-white shadow-sm rounded-2xl flex items-center justify-center font-black text-xl text-blue-600 transition-all"
                            :class="rooms >= adults ? 'opacity-30 cursor-not-allowed' : 'hover:bg-blue-600 hover:text-white'">+</button>
                    </div>
                </div>
            </div>

            <div class="mt-12 flex justify-between gap-4">
                <button @click="prevStep()" class="flex-1 py-4 px-6 bg-slate-100 text-slate-400 font-bold rounded-2xl uppercase tracking-widest text-xs">Kembali</button>
                <button @click="nextStep()" class="flex-[2] py-4 px-6 bg-blue-600 text-white font-black rounded-2xl uppercase tracking-widest text-xs shadow-lg shadow-blue-200">Lanjut ke Kalender</button>
            </div>
        </div>

        {{-- Step 3: Calendar Selection --}}
        <div x-show="step === 3" x-transition class="step-transition">
            <div class="text-center mb-10">
                <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] bg-blue-50 px-4 py-1.5 rounded-full border border-blue-100">Langkah 3/4</span>
                <h2 class="text-3xl font-black text-gray-900 mt-6 uppercase leading-tight">Pilih Tanggal</h2>
                <p class="text-sm text-gray-400 font-medium mt-2">Kalender Ketersediaan Unit</p>
            </div>

            <div class="bg-white rounded-[2.5rem] overflow-hidden border-2 border-black/10 shadow-xl relative">
                {{-- Loading Overlay --}}
                <div x-show="isLoadingCalendar" class="absolute inset-0 z-50 bg-white/60 backdrop-blur-sm flex flex-col items-center justify-center gap-4 transition-opacity">
                    <div class="w-10 h-10 border-4 border-slate-100 border-t-blue-600 rounded-full animate-spin"></div>
                    <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Sinkronisasi Jadwal...</p>
                </div>

                {{-- Header --}}
                <div class="p-6 md:p-8 flex items-center justify-between bg-white border-b border-gray-100">
                    <div>
                        <h3 class="text-xl md:text-2xl font-black uppercase tracking-tighter text-gray-900" x-text="monthName"></h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em]" x-text="currentYear"></p>
                    </div>
                    <div class="flex gap-2">
                        <button @click="prevMonth()" class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-100 transition-all text-gray-900">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <button @click="nextMonth()" class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-100 transition-all text-gray-900">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>

                {{-- Status Legend --}}
                <div class="px-6 md:px-8 py-3 flex flex-wrap gap-x-4 gap-y-2 bg-gray-50/50 border-b border-gray-100">
                    <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full status-ready"></div><span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Ready</span></div>
                    <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full status-pending"></div><span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Pending</span></div>
                    <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full status-booked"></div><span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Booked</span></div>
                    <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full status-blocked"></div><span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Blocked</span></div>
                    <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full status-maintenance"></div><span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Repair</span></div>
                </div>

                <div class="grid grid-cols-7 gap-px bg-gray-100">
                    <template x-for="d in ['MIN', 'SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB']">
                        <div class="bg-gray-50 py-3 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest" x-text="d"></div>
                    </template>
                    <template x-for="(item, idx) in daysInMonth" :key="idx">
                        <div class="h-16 sm:h-20 md:h-24 relative group transition-all flex items-center justify-center cursor-pointer"
                            :class="item.day ? 'status-' + getDateStatus(item.date) : 'bg-white'"
                            @click="selectDate(item.date)">
                            
                            {{-- Date Number --}}
                            <div x-show="item.day" class="relative z-10 text-sm md:text-base font-black transition-all duration-300"
                                :class="{
                                    'ring-4 ring-black/10 rounded-full w-8 h-8 md:w-10 md:h-10 flex items-center justify-center bg-gray-900 text-white shadow-lg scale-110': selectedDate && item.date && item.date.getTime() === selectedDate.getTime()
                                }"
                                x-text="item.day">
                            </div>

                            {{-- Range Indication Overlay --}}
                            <div x-show="item.day && isInRange(item.date)" 
                                x-transition
                                class="absolute inset-0 bg-blue-600/10 border-2 border-blue-600/30 z-0">
                            </div>

                            {{-- Tooltip info --}}
                            <template x-if="item.day && getDateStatus(item.date) !== 'ready' && getDateStatus(item.date) !== 'closed'">
                                <div class="absolute bottom-1 left-0 right-0 text-center opacity-0 group-hover:opacity-100 transition-opacity z-20">
                                    <span class="bg-black/80 text-white text-[7px] font-black uppercase px-2 py-0.5 rounded shadow-lg whitespace-nowrap" x-text="getDayInfo(item.date)"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <div class="mt-12 flex justify-between gap-4">
                <button @click="prevStep()" class="flex-1 py-4 px-6 bg-slate-100 text-slate-400 font-bold rounded-2xl uppercase tracking-widest text-xs">Kembali</button>
                <button x-show="selectedDate" @click="nextStep()" class="flex-[2] py-4 px-6 bg-blue-600 text-white font-black rounded-2xl uppercase tracking-widest text-xs shadow-lg shadow-blue-200">Konfirmasi Data Diri</button>
            </div>
        </div>

        {{-- Step 4: Personal Data & Confirmation --}}
        <div x-show="step === 4" x-transition class="step-transition">
            <div class="text-center mb-10">
                <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] bg-blue-50 px-4 py-1.5 rounded-full border border-blue-100">Langkah Akhir</span>
                <h2 class="text-3xl font-black text-gray-900 mt-6 uppercase leading-tight">Konfirmasi Data</h2>
                <p class="text-sm text-gray-400 font-medium mt-2">Detail Pemohon</p>
            </div>

            <div class="space-y-6">
                <div x-data="{ 
                    name: '',
                    provinsi: '',
                    provinsiName: '',
                    kabupaten: '',
                    kabupatenName: '',
                    whatsapp: '',
                    email: '',
                    fotoPreview: null,
                    
                    searchProvinsi: '',
                    searchKabupaten: '',
                    
                    provinces: [],
                    regencies: [],
                    regenciesCache: {},
                    loadingProvinsi: true,  
                    loadingKabupaten: false, 
                    
                    openProvinsi: false,
                    openKabupaten: false,
                    
                    errors:  { name: false, provinsi: false, kabupaten: false, whatsapp: false, email: false, foto: false },
                    success: { name: false, provinsi: false, kabupaten: false, whatsapp: false, email: false, foto: false },
                    shake:   { name: false, provinsi: false, kabupaten: false, whatsapp: false, email: false, foto: false },
                    fotoErrorMsg: '',
                    fotoFileName: '',

                    async init() {
                        this.loadingProvinsi = true;
                        try {
                            let response = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
                            this.provinces = await response.json();
                        } catch (error) {
                            console.error('Gagal mengambil data provinsi:', error);
                        } finally {
                            this.loadingProvinsi = false;
                        }
                    },

                    async fetchKabupaten(provinsiId) {
                        this.loadingKabupaten = true;
                        this.regencies = []; 
                        this.kabupaten = '';
                        this.kabupatenName = '';
                        this.success.kabupaten = false;
                        this.searchKabupaten = '';
                        
                        if (this.regenciesCache[provinsiId]) {
                            this.regencies = this.regenciesCache[provinsiId];
                            this.loadingKabupaten = false;
                            return;
                        }

                        try {
                            let response = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinsiId}.json`);
                            if (!response.ok) throw new Error('Gagal memuat data kabupaten dari server');
                            this.regencies = await response.json();
                            this.regenciesCache[provinsiId] = this.regencies;
                        } catch (error) {
                            console.error('Gagal mengambil data kabupaten:', error);
                            this.regencies = [];
                        } finally {
                            this.loadingKabupaten = false;
                        }
                    },

                    get filteredProvinces() {
                        if (!this.searchProvinsi.trim()) return this.provinces;
                        return this.provinces.filter(p => 
                            p.name.toLowerCase().includes(this.searchProvinsi.toLowerCase().trim())
                        );
                    },

                    get filteredRegencies() {
                        if (!this.searchKabupaten.trim()) return this.regencies;
                        return this.regencies.filter(k => 
                            k.name.toLowerCase().includes(this.searchKabupaten.toLowerCase().trim())
                        );
                    },

                    triggerError(field) {
                        this.errors[field] = true;
                        this.success[field] = false;
                        this.shake[field] = true;
                        setTimeout(() => { this.shake[field] = false; }, 400);
                    },

                    triggerSuccess(field) {
                        this.errors[field] = false;
                        this.success[field] = true;
                    },

                    validateField(field) {
                        if (field === 'name') {
                            if (!this.name.trim()) { this.errors.name = false; this.success.name = false; return; }
                            let alphaRegex = /^[a-zA-Z\s]+$/;
                            let isValid = this.name.trim().length >= 3 && alphaRegex.test(this.name);
                            isValid ? this.triggerSuccess('name') : this.triggerError('name');
                        }
                        if (field === 'whatsapp') {
                            if (!this.whatsapp) { this.errors.whatsapp = false; this.success.whatsapp = false; return; }
                            let numericRegex = /^[0-9]+$/;
                            let isValid = numericRegex.test(this.whatsapp) && this.whatsapp.length >= 9 && this.whatsapp.length <= 14;
                            isValid ? this.triggerSuccess('whatsapp') : this.triggerError('whatsapp');
                        }
                        if (field === 'email') {
                            if (!this.email) { this.errors.email = false; this.success.email = false; return; }
                            let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                            let isValid = emailRegex.test(this.email);
                            isValid ? this.triggerSuccess('email') : this.triggerError('email');
                        }
                    },

                    selectProvinsi(p) {
                        this.provinsi = p.id;
                        this.provinsiName = p.name;
                        this.openProvinsi = false;
                        this.searchProvinsi = '';
                        this.triggerSuccess('provinsi');
                        this.fetchKabupaten(p.id);
                    },

                    selectKabupaten(k) {
                        this.kabupaten = k.id;
                        this.kabupatenName = k.name;
                        this.openKabupaten = false;
                        this.searchKabupaten = '';
                        this.triggerSuccess('kabupaten');
                    },

                    handleFileChange(e) {
                        let file = e.target.files[0];
                        if (!file) return;

                        let allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                        if (!allowedTypes.includes(file.type)) {
                            this.fotoErrorMsg = 'Format file harus JPG, JPEG, atau PNG!';
                            this.triggerError('foto');
                            this.fotoPreview = null;
                            this.fotoFileName = '';
                            e.target.value = '';
                            return;
                        }

                        if (file.size > 2 * 1024 * 1024) {
                            this.fotoErrorMsg = 'Ukuran file terlalu besar! Maksimal 2MB.';
                            this.triggerError('foto');
                            this.fotoPreview = null;
                            this.fotoFileName = '';
                            e.target.value = '';
                            return;
                        }

                        this.fotoFileName = file.name.length > 24 ? file.name.substring(0, 24) + '...' : file.name;
                        let reader = new FileReader();
                        reader.onload = (event) => { 
                            this.fotoPreview = event.target.result;
                            this.triggerSuccess('foto');
                        };
                        reader.readAsDataURL(file);
                    }
                }" class="space-y-4">

                    {{-- NAMA LENGKAP --}}
                    <div class="relative" :class="{ 'animate-shake': shake.name }">
                        <label class="text-[9px] font-black uppercase tracking-widest ml-1 mb-2 flex items-center gap-1 transition-colors duration-200"
                            :class="errors.name ? 'text-red-500' : success.name ? 'text-emerald-500' : 'text-gray-400'">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Nama Lengkap <span class="text-red-500 text-xs">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" x-model="name" @input="validateField('name')" placeholder="Masukkan nama lengkap Anda" 
                                class="w-full px-5 py-3.5 bg-gray-50 border-2 rounded-2xl outline-none font-medium text-sm transition-all duration-200 pr-10"
                                :class="errors.name ? 'border-red-400 bg-red-50/40 focus:border-red-500' : success.name ? 'border-emerald-400 bg-emerald-50/30 focus:border-emerald-500' : 'border-gray-200 focus:border-blue-500 focus:bg-white'">
                            {{-- Ikon Status --}}
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 transition-all duration-300" x-show="errors.name">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 transition-all duration-300" x-show="success.name" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                        </div>
                        {{-- Pesan Error --}}
                        <div x-show="errors.name" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="flex items-center gap-1.5 mt-1.5 ml-1">
                            <svg class="w-3 h-3 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span class="text-[10px] text-red-500 font-semibold">Nama minimal 3 karakter & hanya huruf abjad</span>
                        </div>
                        {{-- Pesan Sukses --}}
                        <div x-show="success.name" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="flex items-center gap-1.5 mt-1.5 ml-1">
                            <svg class="w-3 h-3 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="text-[10px] text-emerald-600 font-semibold">Nama valid</span>
                        </div>
                    </div>

                    {{-- PROVINSI & KABUPATEN --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 relative" style="z-index: 50;">
                        
                        {{-- Provinsi --}}
                        <div class="relative" :class="{ 'animate-shake': shake.provinsi }" style="z-index: 40;">
                            <label class="text-[9px] font-black uppercase tracking-widest ml-1 mb-2 flex items-center gap-1 transition-colors duration-200"
                                :class="errors.provinsi ? 'text-red-500' : success.provinsi ? 'text-emerald-500' : 'text-gray-400'">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Provinsi Asal <span class="text-red-500 text-xs">*</span>
                            </label>
                            <div @click.stop="openProvinsi = !openProvinsi; openKabupaten = false" @click.away="openProvinsi = false" 
                                class="w-full px-5 py-3.5 bg-gray-50 border-2 rounded-2xl outline-none flex justify-between items-center cursor-pointer font-medium text-sm transition-all duration-200"
                                :class="errors.provinsi ? 'border-red-400 bg-red-50/40' : success.provinsi ? 'border-emerald-400 bg-emerald-50/30' : 'border-gray-200 hover:border-gray-300'">
                                <span :class="provinsiName ? 'text-gray-800' : 'text-gray-400'" x-text="provinsiName || 'Pilih Provinsi...'"></span>
                                <div class="flex items-center gap-2">
                                    <span x-show="success.provinsi" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </span>
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="openProvinsi ? 'text-blue-500 rotate-180' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                            <div x-show="openProvinsi" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0 -translate-y-1 scale-95" @click.stop class="absolute left-0 w-full mt-2 bg-white border border-gray-100 shadow-xl shadow-gray-200/80 rounded-2xl overflow-hidden" style="z-index: 100;">
                                <div class="p-3 border-b border-gray-50">
                                    <input x-model="searchProvinsi" type="text" placeholder="Cari Provinsi..." class="w-full bg-gray-50 text-xs px-4 py-2.5 rounded-xl outline-none border border-gray-200 focus:border-blue-400 transition-colors">
                                </div>
                                <div class="max-h-48 overflow-y-auto">
                                    <div x-show="loadingProvinsi" class="px-5 py-4 text-xs font-semibold text-gray-400 text-center flex items-center justify-center gap-2">
                                        <svg class="w-3.5 h-3.5 animate-spin text-blue-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        Memuat provinsi...
                                    </div>
                                    <template x-for="p in filteredProvinces" :key="p.id">
                                        <div @click="selectProvinsi(p)" 
                                            class="px-5 py-2.5 cursor-pointer text-xs font-semibold text-gray-700 transition-colors"
                                            :class="provinsi === p.id ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50'"
                                            x-text="p.name"></div>
                                    </template>
                                    <div x-show="!loadingProvinsi && filteredProvinces.length === 0" class="px-5 py-4 text-xs font-semibold text-gray-400 text-center">Tidak ditemukan</div>
                                </div>
                            </div>
                            <div x-show="errors.provinsi" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="flex items-center gap-1.5 mt-1.5 ml-1">
                                <svg class="w-3 h-3 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                <span class="text-[10px] text-red-500 font-semibold">Pilih provinsi terlebih dahulu</span>
                            </div>
                        </div>

                        {{-- Kabupaten --}}
                        <div class="relative" :class="{ 'animate-shake': shake.kabupaten }" style="z-index: 30;">
                            <label class="text-[9px] font-black uppercase tracking-widest ml-1 mb-2 flex items-center gap-1 transition-colors duration-200"
                                :class="errors.kabupaten ? 'text-red-500' : success.kabupaten ? 'text-emerald-500' : 'text-gray-400'">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                Kabupaten / Kota <span class="text-red-500 text-xs">*</span>
                            </label>
                            <div @click.stop="if(!provinsi) { triggerError('provinsi') } else { openKabupaten = !openKabupaten; openProvinsi = false }" @click.away="openKabupaten = false" 
                                class="w-full px-5 py-3.5 bg-gray-50 border-2 rounded-2xl outline-none flex justify-between items-center font-medium text-sm transition-all duration-200"
                                :class="!provinsi ? 'opacity-50 cursor-not-allowed border-gray-200' : errors.kabupaten ? 'border-red-400 bg-red-50/40 cursor-pointer' : success.kabupaten ? 'border-emerald-400 bg-emerald-50/30 cursor-pointer' : 'border-gray-200 hover:border-gray-300 cursor-pointer'">
                                <span :class="kabupatenName ? 'text-gray-800' : 'text-gray-400'" x-text="loadingKabupaten ? 'Memuat data kota...' : kabupatenName || 'Pilih Kota/Kabupaten...'"></span>
                                <div class="flex items-center gap-2">
                                    <svg x-show="loadingKabupaten" class="w-3.5 h-3.5 animate-spin text-blue-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                    <span x-show="success.kabupaten && !loadingKabupaten" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </span>
                                    <svg x-show="!loadingKabupaten" class="w-4 h-4 transition-transform duration-200" :class="openKabupaten ? 'text-blue-500 rotate-180' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                            <div x-show="openKabupaten && provinsi" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0 -translate-y-1 scale-95" @click.stop class="absolute left-0 w-full mt-2 bg-white border border-gray-100 shadow-xl shadow-gray-200/80 rounded-2xl overflow-hidden" style="z-index: 100;">
                                <div class="p-3 border-b border-gray-50">
                                    <input x-model="searchKabupaten" type="text" placeholder="Cari Kota..." class="w-full bg-gray-50 text-xs px-4 py-2.5 rounded-xl outline-none border border-gray-200 focus:border-blue-400 transition-colors">
                                </div>
                                <div class="max-h-48 overflow-y-auto">
                                    <div x-show="loadingKabupaten" class="px-5 py-4 text-xs font-semibold text-gray-400 text-center flex items-center justify-center gap-2">
                                        <svg class="w-3.5 h-3.5 animate-spin text-blue-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        Memuat data kabupaten...
                                    </div>
                                    <template x-for="k in filteredRegencies" :key="k.id">
                                        <div @click="selectKabupaten(k)" 
                                            class="px-5 py-2.5 cursor-pointer text-xs font-semibold text-gray-700 transition-colors"
                                            :class="kabupaten === k.id ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50'"
                                            x-text="k.name"></div>
                                    </template>
                                    <div x-show="!loadingKabupaten && filteredRegencies.length === 0" class="px-5 py-4 text-xs font-semibold text-gray-400 text-center">Tidak ditemukan</div>
                                </div>
                            </div>
                            <div x-show="errors.kabupaten" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="flex items-center gap-1.5 mt-1.5 ml-1">
                                <svg class="w-3 h-3 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                <span class="text-[10px] text-red-500 font-semibold">Pilih kota/kabupaten</span>
                            </div>
                        </div>
                    </div>

                    {{-- WHATSAPP & EMAIL --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 relative z-0">

                        {{-- WhatsApp --}}
                        <div class="relative" :class="{ 'animate-shake': shake.whatsapp }">
                            <label class="text-[9px] font-black uppercase tracking-widest ml-1 mb-2 flex items-center gap-1 transition-colors duration-200"
                                :class="errors.whatsapp ? 'text-red-500' : success.whatsapp ? 'text-emerald-500' : 'text-gray-400'">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                Nomor WhatsApp <span class="text-red-500 text-xs">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" x-model="whatsapp" @input="validateField('whatsapp')" placeholder="08xxxxxxxxx" 
                                    class="w-full px-5 py-3.5 bg-gray-50 border-2 rounded-2xl outline-none font-medium text-sm transition-all duration-200 pr-10"
                                    :class="errors.whatsapp ? 'border-red-400 bg-red-50/40 focus:border-red-500' : success.whatsapp ? 'border-emerald-400 bg-emerald-50/30 focus:border-emerald-500' : 'border-gray-200 focus:border-blue-500 focus:bg-white'">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2" x-show="errors.whatsapp">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                <span class="absolute right-4 top-1/2 -translate-y-1/2" x-show="success.whatsapp" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                            </div>
                            <div x-show="errors.whatsapp" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="flex items-center gap-1.5 mt-1.5 ml-1">
                                <svg class="w-3 h-3 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                <span class="text-[10px] text-red-500 font-semibold">Nomor valid 9–14 digit angka</span>
                            </div>
                            <div x-show="success.whatsapp" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="flex items-center gap-1.5 mt-1.5 ml-1">
                                <svg class="w-3 h-3 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span class="text-[10px] text-emerald-600 font-semibold">Nomor valid</span>
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="relative" :class="{ 'animate-shake': shake.email }">
                            <label class="text-[9px] font-black uppercase tracking-widest ml-1 mb-2 flex items-center gap-1 transition-colors duration-200"
                                :class="errors.email ? 'text-red-500' : success.email ? 'text-emerald-500' : 'text-gray-400'">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Email Aktif <span class="text-red-500 text-xs">*</span>
                            </label>
                            <div class="relative">
                                <input type="email" x-model="email" @input="validateField('email')" placeholder="nama@email.com" 
                                    class="w-full px-5 py-3.5 bg-gray-50 border-2 rounded-2xl outline-none font-medium text-sm transition-all duration-200 pr-10"
                                    :class="errors.email ? 'border-red-400 bg-red-50/40 focus:border-red-500' : success.email ? 'border-emerald-400 bg-emerald-50/30 focus:border-emerald-500' : 'border-gray-200 focus:border-blue-500 focus:bg-white'">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2" x-show="errors.email">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                <span class="absolute right-4 top-1/2 -translate-y-1/2" x-show="success.email" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                            </div>
                            <div x-show="errors.email" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="flex items-center gap-1.5 mt-1.5 ml-1">
                                <svg class="w-3 h-3 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                <span class="text-[10px] text-red-500 font-semibold">Format email tidak sesuai</span>
                            </div>
                            <div x-show="success.email" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="flex items-center gap-1.5 mt-1.5 ml-1">
                                <svg class="w-3 h-3 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span class="text-[10px] text-emerald-600 font-semibold">Email valid</span>
                            </div>
                        </div>
                    </div>

                    {{-- UPLOAD FOTO --}}
                    <div class="relative z-0 mt-2 p-5 bg-white border-2 rounded-3xl transition-all duration-200" 
                        :class="{ 'animate-shake': shake.foto }"
                        :style="errors.foto ? 'border-color: rgb(248 113 113)' : success.foto ? 'border-color: rgb(52 211 153)' : 'border-color: rgb(229 231 235)'">

                        <label class="text-[9px] font-black uppercase tracking-widest flex items-center gap-1 mb-4 transition-colors duration-200"
                            :class="errors.foto ? 'text-red-500' : success.foto ? 'text-emerald-500' : 'text-gray-400'">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2"/></svg>
                            Upload Foto Identitas <span class="text-red-500 text-xs">*</span>
                        </label>

                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1 space-y-3">
                                <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed rounded-2xl cursor-pointer transition-all duration-200"
                                    :class="errors.foto ? 'border-red-400 bg-red-50/30 hover:bg-red-50/50' : success.foto ? 'border-emerald-400 bg-emerald-50/30 hover:bg-emerald-50/50' : 'border-gray-300 bg-gray-50 hover:bg-gray-100 hover:border-blue-400'">
                                    <div class="flex flex-col items-center text-center px-4">
                                        {{-- Ikon berubah sesuai state --}}
                                        <svg x-show="!success.foto && !errors.foto" class="w-7 h-7 mb-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        <svg x-show="success.foto" class="w-7 h-7 mb-1.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <svg x-show="errors.foto" class="w-7 h-7 mb-1.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>

                                        <p class="text-xs font-semibold" 
                                            :class="errors.foto ? 'text-red-500' : success.foto ? 'text-emerald-600' : 'text-gray-500'"
                                            x-text="success.foto ? fotoFileName : 'Klik untuk unggah file'"></p>
                                        <p class="text-[10px] mt-0.5"
                                            :class="errors.foto ? 'text-red-400' : success.foto ? 'text-emerald-500' : 'text-gray-400'"
                                            x-text="success.foto ? 'Foto siap digunakan ✓' : 'JPG, JPEG, PNG (Maks. 2MB)'"></p>
                                    </div>
                                    <input type="file" class="hidden" accept="image/jpeg,image/png,image/jpg" @change="handleFileChange" />
                                </label>

                                {{-- Error/Sukses pesan foto --}}
                                <div x-show="errors.foto" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="flex items-center gap-1.5 ml-1">
                                    <svg class="w-3 h-3 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    <span class="text-[10px] text-red-500 font-semibold" x-text="fotoErrorMsg"></span>
                                </div>

                                <div class="p-3 bg-blue-50 rounded-xl flex items-start gap-2.5">
                                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <p class="text-[10px] text-blue-700 font-medium leading-relaxed">Dokumen identitas hanya digunakan untuk validasi reservasi dan dihapus otomatis setelah masa sewa berakhir.</p>
                                </div>
                            </div>

                            {{-- Preview --}}
                            <div class="w-full md:w-44 flex flex-col items-center justify-center border-2 rounded-2xl overflow-hidden relative min-h-[7rem] transition-all duration-200"
                                :class="errors.foto ? 'border-red-200 bg-red-50/20' : success.foto ? 'border-emerald-300 bg-emerald-50/20' : 'border-gray-200 bg-gray-50'">
                                <template x-if="fotoPreview">
                                    <div class="w-full h-full absolute inset-0">
                                        <img :src="fotoPreview" class="object-cover w-full h-full" alt="Preview Identitas">
                                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-2">
                                            <p class="text-[9px] text-white font-black text-center tracking-widest">PREVIEW</p>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!fotoPreview">
                                    <div class="text-center p-4">
                                        <svg class="w-7 h-7 mx-auto mb-1.5" :class="errors.foto ? 'text-red-300' : 'text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <p class="text-[10px] font-bold" :class="errors.foto ? 'text-red-400' : 'text-gray-400'">Belum ada foto</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Summary Card --}}
                <div class="bg-[#0f172a] rounded-[1.75rem] overflow-hidden">

                    {{-- Header --}}
                    <div class="px-6 pt-6">
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 bg-[#1e3a5f] rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                </div>
                                <span class="text-[10px] font-bold tracking-[.12em] uppercase text-blue-400">Ringkasan Reservasi</span>
                            </div>
                            <span class="text-[10px] font-semibold bg-[#1e3a5f] text-blue-300 px-3 py-1 rounded-full tracking-wide">Draft</span>
                        </div>

                        {{-- Nama Fasilitas --}}
                        <div class="mb-5">
                            <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Fasilitas</p>
                            <p class="text-[17px] font-bold text-slate-100 leading-snug truncate" x-text="currentFacility?.nama"></p>
                        </div>

                        {{-- Grid 3 Info --}}
                        <div class="grid grid-cols-3 gap-2.5 mb-5">
                            <div class="bg-[#1e293b] rounded-xl p-3">
                                <div class="flex items-center gap-1.5 mb-1.5">
                                    <svg class="w-3 h-3 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    <p class="text-[9px] font-semibold text-slate-500 uppercase tracking-wider">Paket</p>
                                </div>
                                <p class="text-[13px] font-bold text-slate-100" x-text="packageType.charAt(0).toUpperCase() + packageType.slice(1)"></p>
                            </div>
                            <div class="bg-[#1e293b] rounded-xl p-3">
                                <div class="flex items-center gap-1.5 mb-1.5">
                                    <svg class="w-3 h-3 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="text-[9px] font-semibold text-slate-500 uppercase tracking-wider">Check-In</p>
                                </div>
                                <p class="text-[13px] font-bold text-slate-100" x-text="selectedDate ? new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short' }).format(selectedDate) : '-'"></p>
                            </div>
                            <div class="bg-[#1e293b] rounded-xl p-3">
                                <div class="flex items-center gap-1.5 mb-1.5">
                                    <svg class="w-3 h-3 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-[9px] font-semibold text-slate-500 uppercase tracking-wider">Durasi</p>
                                </div>
                                <p class="text-[13px] font-bold text-slate-100" x-text="duration + (packageType === 'harian' ? ' Hari' : ' Bln')"></p>
                            </div>
                        </div>

                        {{-- Kamar & Tamu --}}
                        <div class="flex items-center gap-1.5 bg-[#1e293b] rounded-xl px-3.5 py-2.5 mb-5">
                            <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span class="text-[11px] text-slate-400 font-medium" x-text="rooms + ' Kamar'"></span>
                            <span class="text-slate-600 mx-1">·</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-[11px] text-slate-400 font-medium" x-text="adults + ' Tamu Dewasa'"></span>
                        </div>
                    </div>

                    {{-- Footer Total --}}
                    <div class="bg-[#0a1628] px-6 py-4 flex items-center justify-between border-t border-slate-800">
                        <div>
                            <p class="text-[9px] font-semibold text-slate-500 uppercase tracking-widest mb-1">Total Estimasi</p>
                            <p class="text-[26px] font-extrabold text-blue-400 leading-none tracking-tight" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalPrice)"></p>
                            <p class="text-[10px] text-slate-600 font-medium mt-1">Sudah termasuk pajak & layanan</p>
                        </div>
                        <div class="w-11 h-11 bg-[#1e3a5f] rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                        </div>
                    </div>

                </div>
            </div>

            <div class="mt-12 space-y-4">
                <div class="flex justify-between gap-4">
                    <button @click="prevStep()" class="flex-1 py-4 px-6 bg-slate-100 text-slate-400 font-bold rounded-2xl uppercase tracking-widest text-xs">Kembali</button>
                    <button @click="submitBooking()" class="flex-[2] py-4 px-6 bg-blue-600 text-white font-black rounded-2xl uppercase tracking-widest text-xs shadow-lg shadow-blue-200 hover:bg-black transition-all">Submit Reservasi</button>
                </div>
                <button @click="confirmCancel()" class="w-full py-4 text-red-500 font-bold uppercase tracking-widest text-[10px] bg-red-50 rounded-2xl border border-red-100 transition-colors">Batal Booking</button>
            </div>
        </div>

    </div>

    {{-- Footer Info --}}
    <div class="mt-12 text-center">
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.4em]">© 2026 BBPPMPV BOE MALANG</p>
    </div>
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
            selectedDate: null,
            name: '',
            email: '',
            whatsapp: '',
            provinsi: '',
            provinsiName: '',
            kabupaten: '',
            kabupatenName: '',
            provinces: [],
            regencies: [],
            fotoIdentitas: null,
            fotoPreview: null,
            facilities: config.facilities || [],
            selectedFacilityId: config.selectedFacilityId || '',

            // Calendar state
            currentMonth: new Date().getMonth(),
            currentYear: new Date().getFullYear(),
            daysInMonth: [],
            calendarEvents: [],
            isLoadingCalendar: false,

            init() {
                this.updateDaysInMonth();
                this.$watch('children', val => {
                    const count = parseInt(val) || 0;
                    if (count > this.childAges.length) {
                        for (let i = this.childAges.length; i < count; i++) this.childAges.push('');
                    } else {
                        this.childAges = this.childAges.slice(0, count);
                    }
                });
                this.$watch('adults', val => { 
                    if (this.currentFacility?.tipe === 'asrama' && this.rooms > val) this.rooms = val; 
                });

                // Watch for month/year changes to refetch calendar
                this.$watch('currentMonth', () => { this.updateDaysInMonth(); this.fetchCalendarData(); });
                this.$watch('currentYear', () => { this.updateDaysInMonth(); this.fetchCalendarData(); });
                this.$watch('selectedFacilityId', () => { this.fetchCalendarData(); });

                // Fetch Provinces from Emsifa API
                fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
                    .then(res => res.json())
                    .then(data => this.provinces = data);
                
                // Watch province to fetch regencies
                this.$watch('provinsi', val => {
                    this.kabupaten = '';
                    this.kabupatenName = '';
                    this.regencies = [];
                    if(val) {
                        const prov = this.provinces.find(p => p.id == val);
                        this.provinsiName = prov ? prov.name : '';
                        fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${val}.json`)
                            .then(res => res.json())
                            .then(data => this.regencies = data);
                    }
                });

                // Watch kabupaten to get Name
                this.$watch('kabupaten', val => {
                    if(val) {
                        const kab = this.regencies.find(k => k.id == val);
                        this.kabupatenName = kab ? kab.name : '';
                    } else {
                        this.kabupatenName = '';
                    }
                });
            },

            get currentFacility() {
                return this.facilities.find(f => f.id == this.selectedFacilityId) || null;
            },

            get totalPrice() {
                const f = this.currentFacility;
                if (!f) return 0;
                if (this.packageType === 'harian') {
                    return (parseInt(this.duration) || 0) * (parseFloat(f.harga) || 0);
                } else {
                    if (!f.harga_bulanan) return 0;
                    return (parseInt(this.duration) || 0) * (parseFloat(f.harga_bulanan) || 0);
                }
            },

            handleFileChange(event) {
                const file = event.target.files[0];
                if (!file) {
                    this.fotoIdentitas = null;
                    this.fotoPreview = null;
                    return;
                }
                if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
                    Swal.fire('Format Tidak Valid', 'Mohon unggah file format JPG, JPEG, atau PNG.', 'error');
                    event.target.value = '';
                    this.fotoIdentitas = null;
                    this.fotoPreview = null;
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire('Ukuran Terlalu Besar', 'Maksimal ukuran file foto identitas adalah 2MB.', 'error');
                    event.target.value = '';
                    this.fotoIdentitas = null;
                    this.fotoPreview = null;
                    return;
                }
                this.fotoIdentitas = file;
                this.fotoPreview = URL.createObjectURL(file);
            },

            nextStep() { 
                if (this.step === 2) {
                    if (this.currentFacility?.tipe === 'asrama') {
                        if (this.packageType === 'harian' && this.duration > (this.currentFacility.max_durasi_harian || 999)) {
                            Swal.fire('Peringatan', `Maksimal durasi harian untuk asrama ini adalah ${this.currentFacility.max_durasi_harian} hari.`, 'warning');
                            return;
                        }
                    }
                    this.fetchCalendarData();
                }
                if (this.step < 4) this.step++; 
            },

            prevStep() { if (this.step > 1) this.step--; },

            confirmCancel() {
                Swal.fire({
                    title: 'Batal Booking?',
                    text: 'Semua progres pengisian akan hilang.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Tidak'
                }).then(result => { if (result.isConfirmed) window.location.href = '/'; });
            },

            inc(field, max = null) {
                const f = this.currentFacility;
                if (field === 'duration') {
                    if (this.packageType === 'harian') {
                        const limit = f?.max_durasi_harian || 999;
                        if (this.duration >= limit) {
                            Swal.fire({
                                title: 'Peringatan',
                                text: `Maksimal durasi harian untuk ${f?.nama || 'fasilitas ini'} adalah ${limit} hari.`,
                                icon: 'warning',
                                confirmButtonColor: '#276AD7'
                            });
                            return;
                        }
                    }
                }
                if (field === 'rooms') {
                    if (f?.tipe === 'aula') return;
                    if (this.rooms >= this.adults) {
                        Swal.fire({
                            title: 'Peringatan',
                            text: 'Maksimal 1 orang 1 kamar, mohon tambah jumlah orang/dewasa untuk menambah 1 kamar lagi',
                            icon: 'warning',
                            confirmButtonColor: '#276AD7'
                        });
                        return;
                    }
                }
                if (field === 'adults') {
                    const limit = f?.max_dewasa || 999;
                    if (this.adults >= limit) {
                        Swal.fire('Peringatan', `Maksimal kapasitas dewasa adalah ${limit}`, 'warning');
                        return;
                    }
                }
                if (field === 'children') {
                    const limit = f?.max_anak || 0;
                    if (this.children >= limit) {
                        Swal.fire('Peringatan', `Maksimal kapasitas anak adalah ${limit}`, 'warning');
                        return;
                    }
                }
                if (max !== null && this[field] >= max) return;
                this[field]++;
            },

            dec(field, min = 0) { if (this[field] > min) this[field]--; },

            updateDaysInMonth() {
                const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                const startDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
                this.daysInMonth = [];
                for (let i = 0; i < startDay; i++) this.daysInMonth.push({ day: null, date: null });
                for (let i = 1; i <= lastDay; i++) {
                    this.daysInMonth.push({ day: i, date: new Date(this.currentYear, this.currentMonth, i) });
                }
            },

            async fetchCalendarData() {
                if (!this.selectedFacilityId) return;
                this.isLoadingCalendar = true;
                try {
                    const res = await fetch(`/schedule_booking/data?fasilitas_id=${this.selectedFacilityId}&year=${this.currentYear}&month=${this.currentMonth + 1}&t=${Date.now()}`);
                    this.calendarEvents = await res.json();
                } catch (e) {
                    this.calendarEvents = [];
                } finally {
                    this.isLoadingCalendar = false;
                }
            },

            formatDateLocal(date) {
                if (!date) return '';
                const d = new Date(date);
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            },

            getDateStatus(date) {
                if (!date) return 'closed';
                const today = new Date(); today.setHours(0,0,0,0);
                if (date < today) return 'closed';
                
                for (const ev of this.calendarEvents) {
                    const start = new Date(ev.tgl_mulai); start.setHours(0,0,0,0);
                    const end   = new Date(ev.tgl_selesai); end.setHours(23,59,59,999);
                    if (date >= start && date <= end) {
                        if (ev.color === "yellow") return "pending";
                        if (ev.color === "blue")   return "booked";
                        if (ev.color === "black")  return "blocked";
                        if (ev.color === "red")    return "maintenance";
                    }
                }
                return 'ready';
            },

            getDayInfo(date) {
                if (!date) return "";
                for (const ev of this.calendarEvents) {
                    const start = new Date(ev.tgl_mulai); start.setHours(0,0,0,0);
                    const end   = new Date(ev.tgl_selesai); end.setHours(23,59,59,999);
                    if (date >= start && date <= end) {
                        if (ev.status === "maintenance") return "Perbaikan: " + (ev.reason || "Maintenance");
                        return ev.status.toUpperCase();
                    }
                }
                return "";
            },

            isInRange(date) {
                if (!this.selectedDate || !date) return false;
                const start = new Date(this.selectedDate);
                start.setHours(0,0,0,0);
                const end = new Date(start);
                
                if (this.packageType === 'harian') {
                    end.setDate(start.getDate() + (parseInt(this.duration) - 1));
                } else {
                    end.setMonth(start.getMonth() + parseInt(this.duration));
                    end.setDate(end.getDate() - 1);
                }
                
                date.setHours(0,0,0,0);
                return date >= start && date <= end;
            },

            selectDate(date) {
                const status = this.getDateStatus(date);
                if (!date || status !== 'ready') return;
                this.selectedDate = date;
            },

            prevMonth() {
                if (this.currentMonth === 0) {
                    this.currentMonth = 11;
                    this.currentYear--;
                } else {
                    this.currentMonth--;
                }
            },

            nextMonth() {
                if (this.currentMonth === 11) {
                    this.currentMonth = 0;
                    this.currentYear++;
                } else {
                    this.currentMonth++;
                }
            },

            get monthName() {
                return new Intl.DateTimeFormat('id-ID', { month: 'long' }).format(new Date(this.currentYear, this.currentMonth));
            },

            submitBooking() {
                if (!this.name || !this.whatsapp || !this.email || !this.provinsiName || !this.kabupatenName || !this.fotoIdentitas) {
                    Swal.fire({
                        title: 'Data Tidak Lengkap',
                        text: 'Mohon lengkapi seluruh data pemohon termasuk Asal Wilayah dan Foto Identitas Anda.',
                        icon: 'warning',
                        confirmButtonColor: '#1265A8'
                    });
                    return;
                }

                Swal.fire({ title: 'Mengirim Reservasi...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                const formData = new FormData();
                formData.append('name', this.name);
                formData.append('whatsapp', this.whatsapp);
                formData.append('email', this.email);
                formData.append('provinsi', this.provinsiName);
                formData.append('kabupaten', this.kabupatenName);
                formData.append('fasilitas_id', this.selectedFacilityId);
                formData.append('package_type', this.packageType);
                if (this.fotoIdentitas) {
                    formData.append('foto_identitas', this.fotoIdentitas);
                }
                formData.append('duration', this.duration);
                formData.append('adults', this.adults);
                formData.append('children_count', this.children);
                formData.append('rooms_count', this.rooms);
                this.childAges.forEach(age => formData.append('child_age[]', age));
                formData.append('tgl_mulai', this.formatDateLocal(this.selectedDate));
                if (this.packageType === 'bulanan') {
                    const end = new Date(this.selectedDate);
                    end.setMonth(end.getMonth() + parseInt(this.duration));
                    end.setDate(end.getDate() - 1);
                    formData.append('tgl_selesai', this.formatDateLocal(end));
                }
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route('bookings.store') }}', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        sessionStorage.setItem('booking_success', 'true');
                        window.location.href = '/';
                    } else {
                        Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        }));
    });
</script>

</body>
</html>
