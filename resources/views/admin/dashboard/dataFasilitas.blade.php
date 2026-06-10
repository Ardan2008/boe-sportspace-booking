<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <title>BOE-Sport Space | Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f8fafc; 
            overflow-x: hidden; 
        }

        .ripple {
            position: absolute;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        @keyframes shimmer {
            100% { transform: translateX(250%); }
        }
    </style>
</head>
<body class="flex min-h-screen">
    @include('admin.dashboard.layouts.sidebar')

    <main class="flex-1 md:ml-64 p-6 md:p-10">
        @include('admin.dashboard.layouts.header', [
            'headerTitle' => 'Data Fasilitas',
            'headerSubtitle' => 'Selamat datang di manajemen data fasilitas.'
        ])
        
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <script>
            window.__facilityRooms = @json($facilities->mapWithKeys(fn($f) => [$f->id => $f->paket_harian ?? []])->toArray());
        </script>

        <section x-data="{ 
            openPreview: false, 
            previewImg: '', 
            previewTitle: '',
            previewDesc: '',
            maintenanceModal: false,
            maintData: { id: null, name: '', start_date: '', end_date: '', reason: '' },
            selectedRooms: [],
            selectAllRooms: false,
            reasonLen: 0,
            reasonMax: 255,
            dateStartErr: '',
            dateEndErr: '',
            reasonErr: '',
            roomErr: '',
            durationDays: 0,
            get facilityRoomList() {
                const data = window.__facilityRooms?.[this.maintData.id];
                if (!data || !Array.isArray(data)) return [];
                const list = [];
                data.forEach(rt => {
                    const rooms = Array.isArray(rt.nomor_kamar) ? rt.nomor_kamar : [];
                    rooms.forEach(nr => {
                        const tipeLabel = Array.isArray(rt.tipe) ? rt.tipe.join(', ') : (rt.tipe || 'Tipe');
                        list.push({ room: nr, type: tipeLabel, blok: rt.kode_blok || '' });
                    });
                });
                return list;
            },
            get reasonLeft()   { return this.reasonMax - this.reasonLen; },
            get reasonPct()    { return Math.min(this.reasonLen / this.reasonMax * 100, 100); },
            get reasonColor()  {
                if (this.reasonLen === 0)              return '#e2e8f0';
                if (this.reasonLen < 20)               return '#f97316';
                if (this.reasonLeft < 30)              return '#ef4444';
                return '#22c55e';
            },
            toggleAllRooms() {
                this.selectAllRooms = !this.selectAllRooms;
                this.roomErr = '';
                if (this.selectAllRooms) {
                    this.selectedRooms = [];
                } else {
                    this.selectedRooms = this.facilityRoomList.map(r => r.room);
                }
            },
            closeMaintModal() {
                window.dispatchEvent(new CustomEvent('reset-maint-toggle', { detail: this.maintData.id }));
                this.maintenanceModal = false;
                this.dateStartErr = '';
                this.dateEndErr = '';
                this.reasonErr = '';
                this.roomErr = '';
                this.durationDays = 0;
                this.reasonLen = 0;
            },
            openMaintenanceModal(id, name) {
                this.maintData = { id: id, name: name, start_date: new Date().toISOString().split('T')[0], end_date: '', reason: '' };
                this.selectedRooms = [];
                this.selectAllRooms = false;
                this.reasonLen = 0;
                this.dateStartErr = '';
                this.dateEndErr = '';
                this.reasonErr = '';
                this.roomErr = '';
                this.durationDays = 0;
                this.maintenanceModal = true;
            },
            submitMaintenance() {
                const url = `/admin/fasilitas/${this.maintData.id}/maintenance`;
                const body = {
                    tgl_mulai: this.maintData.start_date,
                    tgl_selesai: this.maintData.end_date,
                    tujuan: this.maintData.reason
                };
                if (!this.selectAllRooms && this.selectedRooms.length > 0) {
                    body.nomor_kamar = this.selectedRooms;
                }
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    willOpen: () => Swal.showLoading()
                });
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(body)
                })
                .then(res => res.json())
                .then(data => {
                    Swal.close();
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#ef4444',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.message,
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        }).then(() => location.reload());
                    }
                })
                .catch(err => {
                    Swal.close();
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan sistem.',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    }).then(() => location.reload());
                });
            },
            handleMaintenanceToggle(id, name, isMaintenance) {
                if (!isMaintenance) {
                    this.openMaintenanceModal(id, name);
                } else {
                    this.cancelMaintenance(id, name);
                }
            },
            cancelMaintenance(id, name) {
                Swal.fire({
                    title: 'Selesai Perbaikan?',
                    text: `Apakah fasilitas ${name} sudah siap digunakan kembali?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#1265A8',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Ya, Batalkan!',
                    cancelButtonText: 'Tutup',
                    reverseButtons: true,
                    customClass: { popup: 'rounded-[2rem] p-8' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const url = `/admin/fasilitas/${id}/cancel-maintenance`;
                        Swal.fire({
                            title: 'Membatalkan...',
                            text: 'Mohon tunggu sebentar.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            willOpen: () => Swal.showLoading()
                        });
                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            Swal.close();
                            if (data.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonColor: '#1265A8',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => location.reload());
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: data.message,
                                    icon: 'error',
                                    confirmButtonColor: '#ef4444'
                                });
                            }
                        })
                        .catch(err => {
                            Swal.close();
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan sistem.',
                                icon: 'error',
                                confirmButtonColor: '#ef4444'
                            });
                        });
                    }
                });
            },
            validateStart() {
                const today = new Date(); today.setHours(0,0,0,0);
                const val   = new Date(this.maintData.start_date);
                if (!this.maintData.start_date) {
                    this.dateStartErr = 'Tanggal mulai wajib diisi.'; return false;
                }
                if (val < today) {
                    this.dateStartErr = 'Tanggal mulai tidak boleh di masa lalu.'; return false;
                }
                this.dateStartErr = '';
                this.calcDuration();
                return true;
            },
            validateEnd() {
                if (!this.maintData.end_date) {
                    this.dateEndErr = 'Tanggal selesai wajib diisi.'; return false;
                }
                const start = new Date(this.maintData.start_date);
                const end   = new Date(this.maintData.end_date);
                if (end < start) {
                    this.dateEndErr = 'Tanggal selesai tidak boleh sebelum tanggal mulai.'; return false;
                }
                this.dateEndErr = '';
                this.calcDuration();
                return true;
            },
            validateReason() {
                if (this.maintData.reason.trim().length < 10) {
                    this.reasonErr = 'Alasan minimal 10 karakter.'; return false;
                }
                this.reasonErr = '';
                return true;
            },
            calcDuration() {
                if (this.maintData.start_date && this.maintData.end_date) {
                    const s = new Date(this.maintData.start_date);
                    const e = new Date(this.maintData.end_date);
                    const d = Math.ceil((e - s) / (1000 * 60 * 60 * 24)) + 1;
                    this.durationDays = d > 0 ? d : 0;
                }
            },
            validateRooms() {
                if (this.facilityRoomList.length > 0 && !this.selectAllRooms && this.selectedRooms.length === 0) {
                    this.roomErr = 'Pilih minimal satu kamar atau gunakan &quot;Semua Kamar&quot;.';
                    return false;
                }
                this.roomErr = '';
                return true;
            },
            submitWithValidation() {
                const s = this.validateStart();
                const e = this.validateEnd();
                const r = this.validateReason();
                const rm = this.validateRooms();
                if (s && e && r && rm) this.submitMaintenance();
            },
        }">
            <div class="flex items-center justify-between mb-8">
                <div class="flex flex-col gap-1.5 p-2">
                    <h3 class="text-2xl font-extrabold tracking-tight text-slate-800 leading-none">
                        Daftar Fasilitas
                    </h3>
                    
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#1265A8] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#1265A8]"></span>
                        </span>
                        <p class="text-[13px] font-medium text-slate-500 uppercase tracking-wider">
                            Total <span class="text-slate-900 font-bold">{{ count($facilities) }}</span> Fasilitas <span class="lowercase">tersedia</span>
                        </p>
                    </div>
                </div>

                @if(session('role') === 'owner' || filter_var(session('can_edit'), FILTER_VALIDATE_BOOLEAN))
                <a href="/admin/dashboard/create/createFasilitas" id="btnTambah" onclick="handleLoading(event, this)" class="group relative inline-flex items-center gap-2 px-8 py-3.5 bg-[#1265A8] text-white rounded-2xl font-bold text-sm transition-all duration-300 hover:bg-[#0d4d82] hover:shadow-[0_10px_20px_-10px_rgba(18,101,168,0.5)] active:scale-95 overflow-hidden">
                    <div class="absolute inset-0 w-1/2 h-full bg-white/10 skew-x-[-25deg] -translate-x-full group-hover:animate-[shimmer_0.75s_infinite]"></div>
                    
                    <div class="relative flex items-center gap-2">
                        <svg id="iconPlus" class="w-5 h-5 transition-all duration-500 group-hover:rotate-180" 
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>

                        <svg id="iconLoading" class="hidden w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        
                        <span id="btnText">Tambah Fasilitas</span>
                    </div>
                </a>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($facilities as $item)
                <div class="group bg-white rounded-[2rem] overflow-hidden border border-slate-100 shadow-sm hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                    
                    {{-- Bagian Gambar dengan Hover Zoom & Eye Icon --}}
                    <div class="relative h-52 overflow-hidden cursor-pointer" 
                        @click="openPreview = true; previewImg = '{{ asset('storage/fasilitas/' . $item->image) }}'; previewTitle = '{{ $item->nama }}'; previewDesc = '{{ $item->deskripsi }}'">
                        
                        <img src="{{ asset('storage/fasilitas/' . $item->image) }}" 
                            alt="{{ $item['nama'] }}" 
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-125 {{ $item->is_maintenance ? 'grayscale brightness-75' : '' }}">
                        
                        @if($item->is_maintenance)
                        <div class="absolute top-4 left-4 z-10 flex flex-col gap-2">
                            <span class="flex items-center gap-1.5 px-3 py-1.5 bg-red-600 text-white text-[10px] font-black rounded-lg shadow-lg uppercase tracking-widest border border-red-400/30 backdrop-blur-md">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                                </span>
                                Mode Perbaikan
                            </span>
                        </div>
                        @endif

                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300 flex items-center justify-center">
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-white/20 backdrop-blur-md p-3 rounded-full border border-white/50">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="mb-6">
                            <h4 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-[#1265A8] transition-colors">
                                {{ $item['nama'] }}
                            </h4>
                            <p class="text-slate-500 text-sm line-clamp-2 mb-4">
                                {{ $item->deskripsi }}
                            </p>

                            <div class="flex items-center justify-between p-3 bg-slate-50/50 rounded-2xl border border-slate-100">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-tight">Maintenance Mode</span>
                                    <span class="text-[11px] font-bold {{ $item->is_maintenance ? 'text-red-600' : 'text-emerald-600' }}">
                                        {{ $item->is_maintenance ? 'Sedang Perbaikan' : 'Siap Digunakan' }}
                                    </span>
                                </div>
                                
                                <div x-data="{ active: {{ $item->is_maintenance ? 'true' : 'false' }}, fid: {{ $item->id }} }"
                                    class="relative inline-flex items-center cursor-pointer"
                                    @click="handleMaintenanceToggle(fid, @js($item->nama), active)"
                                    @reset-maint-toggle.window="if ($event.detail === fid) active = false">
                                    
                                    {{-- Track --}}
                                    <div class="w-10 h-5 rounded-full transition-all duration-300 relative"
                                        :class="active ? 'bg-red-500' : 'bg-slate-200'">
                                        {{-- Thumb --}}
                                        <div class="absolute top-[2px] left-[2px] w-4 h-4 bg-white rounded-full shadow transition-all duration-300"
                                            :class="active ? 'translate-x-5' : 'translate-x-0'"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-3 pt-4 border-t border-slate-50">
                            <h4 class="text-sm uppercase tracking-[0.15em] text-[#1265A8] font-black">
                                Rp {{ number_format($item['harga'] ?? 0, 0, ',', '.') }}
                            </h4>

                            <div class="flex items-center gap-3">
                                @if(session('role') === 'owner' || filter_var(session('can_edit'), FILTER_VALIDATE_BOOLEAN))
                                <form id="delete-form-{{ $item->id }}" action="{{ route('fasilitas.destroy', $item->id) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>

                                <button type="button" 
                                    onclick="confirmDelete('{{ $item->id }}')"
                                    class="p-3 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                                
                                <a href="{{ route('fasilitas.edit', $item->id) }}" class="btn-edit inline-flex items-center gap-2 px-5 py-3 rounded-xl border border-slate-200 text-slate-600 hover:border-[#1265A8] hover:text-[#1265A8] transition-all font-medium text-sm">
                                    <div class="button-content flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Edit
                                    </div>
                                    <div class="loading-spinner hidden">
                                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </a>
                                @else
                                <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-xs font-semibold">View Only</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- MODAL PREVIEW GAMBAR --}}
            <div x-show="openPreview" x-cloak
                class="fixed inset-0 z-[200] flex items-center justify-center p-4"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">

                <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm"></div>

                <div class="relative bg-white rounded-[2.5rem] shadow-2xl max-w-2xl w-full overflow-hidden border border-slate-100"
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="scale-90 opacity-0"
                    x-transition:enter-end="scale-100 opacity-100">

                    <button @click="openPreview = false"
                        class="absolute top-4 right-4 z-10 p-2 bg-white/90 backdrop-blur rounded-full shadow-lg hover:bg-red-500 hover:text-white transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <img :src="previewImg" :alt="previewTitle"
                        class="w-full max-h-[65vh] object-contain bg-slate-900">

                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-800" x-text="previewTitle"></h3>
                        <p class="text-sm text-slate-500 mt-1" x-text="previewDesc"></p>
                    </div>
                </div>
            </div>

            <style>
                [x-cloak] { display: none !important; }

                /* ── Progress bar alasan ── */
                .reason-progress {
                    height: 3px;
                    border-radius: 99px;
                    transition: width .3s ease, background-color .3s ease;
                }

                /* ── Date field valid/error ── */
                .date-valid {
                    border-color: #22c55e !important;
                    background-color: #f0fdf4 !important;
                    box-shadow: 0 0 0 3px rgba(34,197,94,.12) !important;
                }
                .date-error {
                    border-color: #ef4444 !important;
                    background-color: #fff5f5 !important;
                    box-shadow: 0 0 0 3px rgba(239,68,68,.12) !important;
                    animation: maintShake .35s ease;
                }
                @keyframes maintShake {
                    0%,100% { transform: translateX(0); }
                    20%      { transform: translateX(-5px); }
                    40%      { transform: translateX(5px); }
                    60%      { transform: translateX(-4px); }
                    80%      { transform: translateX(4px); }
                }

                /* ── Duration pill ── */
                .duration-pill {
                    display: inline-flex;
                    align-items: center;
                    gap: 5px;
                    padding: 4px 12px;
                    border-radius: 99px;
                    font-size: 10px;
                    font-weight: 800;
                    letter-spacing: .05em;
                    transition: all .2s;
                }
            </style>

            {{-- MODAL MAINTENANCE --}}
            <div x-show="maintenanceModal" x-cloak
                class="fixed inset-0 z-[100] overflow-y-auto"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                >

                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeMaintModal()"></div>

                    <div class="relative bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100"
                        x-transition:enter="transition ease-out duration-300 transform"
                        x-transition:enter-start="scale-90 opacity-0"
                        x-transition:enter-end="scale-100 opacity-100">

                        {{-- Header --}}
                        <div class="bg-red-600 p-8 text-white relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-8 opacity-10">
                                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L1 21h22L12 2zm0 3.45L19.55 19H4.45L12 5.45zM11 16h2v2h-2v-2zm0-7h2v5h-2V9z"/>
                                </svg>
                            </div>
                            <div class="relative z-10">
                                <h3 class="text-2xl font-black mb-1">Mode Perbaikan</h3>
                                <p class="text-red-100 text-sm font-medium">
                                    Fasilitas: <span x-text="maintData.name" class="font-bold underline text-white"></span>
                                </p>

                                {{-- Duration pill — muncul setelah kedua tanggal diisi --}}
                                <div class="mt-3" x-show="durationDays > 0">
                                    <span class="duration-pill bg-white/20 text-white border border-white/30">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span x-text="durationDays + ' hari diblokir'"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Form --}}
                        <form class="p-8 space-y-5" @submit.prevent="submitWithValidation">

                            {{-- Tanggal --}}
                            <div class="grid grid-cols-2 gap-4">

                                {{-- Mulai --}}
                                <div class="space-y-1.5">
                                    <label class="text-[10px] uppercase font-black text-slate-400 tracking-widest px-1">Mulai Dari</label>
                                    <input type="date"
                                        x-model="maintData.start_date"
                                        @change="validateStart(); validateEnd()"
                                        name="tgl_mulai"
                                        :class="dateStartErr ? 'date-error' : (maintData.start_date && !dateStartErr ? 'date-valid' : '')"
                                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none transition-all">

                                    {{-- Error hint --}}
                                    <p class="text-[10px] font-bold text-red-500 px-1 min-h-[14px]" x-text="dateStartErr"></p>
                                </div>

                                {{-- Selesai --}}
                                <div class="space-y-1.5">
                                    <label class="text-[10px] uppercase font-black text-slate-400 tracking-widest px-1">Sampai Dengan</label>
                                    <input type="date"
                                        x-model="maintData.end_date"
                                        @change="validateEnd()"
                                        name="tgl_selesai"
                                        :class="dateEndErr ? 'date-error' : (maintData.end_date && !dateEndErr ? 'date-valid' : '')"
                                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none transition-all">

                                    <p class="text-[10px] font-bold text-red-500 px-1 min-h-[14px]" x-text="dateEndErr"></p>
                                </div>
                            </div>

                            {{-- Info durasi detail --}}
                            <div x-show="durationDays > 0"
                                class="flex items-center gap-3 p-3 bg-red-50 border border-red-100 rounded-2xl">
                                <svg class="w-4 h-4 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <p class="text-[11px] font-bold text-red-600">
                                    Fasilitas akan diblokir selama <span class="underline" x-text="durationDays + ' hari'"></span>.
                                    Semua booking yang bertabrakan akan ditolak otomatis.
                                </p>
                            </div>

                            {{-- Pilih Kamar — muncul jika fasilitas memiliki data kamar --}}
                            <div x-show="facilityRoomList.length > 0"
                                class="space-y-2 p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                                <div class="flex items-center justify-between">
                                    <label class="text-[10px] uppercase font-black text-slate-400 tracking-widest">Pilih Kamar</label>
                                    <button type="button" @click="toggleAllRooms()"
                                        class="text-[9px] font-black uppercase tracking-widest px-3 py-1 rounded-full transition-all"
                                        :class="selectAllRooms ? 'bg-blue-100 text-blue-600 border border-blue-200' : 'bg-slate-200 text-slate-500 border border-slate-300'"
                                        x-text="selectAllRooms ? 'Semua Kamar' : 'Pilih Individual'"></button>
                                </div>
                                <div x-show="!selectAllRooms" x-transition
                                    class="max-h-40 overflow-y-auto space-y-1.5 pr-1">
                                    <template x-for="(r, i) in facilityRoomList" :key="i">
                                        <label class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-xl border border-slate-100 cursor-pointer hover:border-blue-200 transition-all"
                                            :class="selectedRooms.includes(r.room) ? 'border-blue-400 bg-blue-50' : ''">
                                            <input type="checkbox"
                                                :value="r.room"
                                                x-model="selectedRooms"
                                                @change="roomErr = ''"
                                                class="w-3.5 h-3.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                            <span class="text-[11px] font-bold text-slate-700">
                                                <span x-text="r.type"></span>
                                                <span x-show="r.blok" x-text="' (Blok ' + r.blok + ')'"></span>
                                                — Kamar <span x-text="r.room"></span>
                                            </span>
                                        </label>
                                    </template>
                                </div>
                                <p x-show="selectAllRooms" class="text-[10px] font-medium text-slate-400">Semua kamar akan diblokir untuk perbaikan.</p>
                                <p class="text-[10px] font-bold text-red-500 px-1 min-h-[14px]" x-text="roomErr"></p>
                            </div>

                            {{-- Alasan --}}
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between px-1">
                                    <label class="text-[10px] uppercase font-black text-slate-400 tracking-widest">Alasan Perbaikan</label>
                                    {{-- Counter --}}
                                    <span class="text-[10px] font-black transition-colors"
                                        :class="reasonLeft < 30 ? 'text-red-500' : reasonLeft < 60 ? 'text-orange-400' : 'text-slate-400'"
                                        x-text="reasonLen + ' / ' + reasonMax"></span>
                                </div>

                                <textarea
                                    x-model="maintData.reason"
                                    @input="reasonLen = maintData.reason.length; validateReason()"
                                    @blur="validateReason()"
                                    name="tujuan"
                                    rows="3"
                                    :maxlength="reasonMax"
                                    placeholder="Contoh: Renovasi lantai, Perbaikan AC, Pengecatan ulang..."
                                    :class="reasonErr ? 'border-red-400 bg-red-50/50 focus:ring-red-500/20 focus:border-red-400' : (maintData.reason.trim().length >= 10 ? 'border-green-400 bg-green-50/30 focus:ring-green-500/20 focus:border-green-400' : '')"
                                    class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 transition-all resize-none"></textarea>

                                {{-- Progress bar --}}
                                <div class="h-[3px] bg-slate-100 rounded-full overflow-hidden">
                                    <div class="reason-progress"
                                        :style="'width:' + reasonPct + '%; background-color:' + reasonColor"></div>
                                </div>

                                {{-- Error + tips --}}
                                <div class="flex items-center justify-between px-1">
                                    <p class="text-[10px] font-bold text-red-500 min-h-[14px]" x-text="reasonErr"></p>
                                    <p class="text-[10px] text-slate-300 font-medium" x-show="!reasonErr && reasonLen < 10">
                                        Min. 10 karakter
                                    </p>
                                </div>
                            </div>

                            {{-- Summary validasi — muncul jika ada error saat submit --}}
                            <div x-show="dateStartErr || dateEndErr || reasonErr || roomErr"
                                class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-2xl">
                                <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <div>
                                    <p class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-1">Periksa kembali</p>
                                    <ul class="space-y-0.5">
                                        <li x-show="dateStartErr" class="text-[11px] text-red-500 font-semibold" x-text="'• ' + dateStartErr"></li>
                                        <li x-show="dateEndErr"   class="text-[11px] text-red-500 font-semibold" x-text="'• ' + dateEndErr"></li>
                                        <li x-show="reasonErr"    class="text-[11px] text-red-500 font-semibold" x-text="'• ' + reasonErr"></li>
                                        <li x-show="roomErr"     class="text-[11px] text-red-500 font-semibold" x-text="'• ' + roomErr"></li>
                                    </ul>
                                </div>
                            </div>

                            {{-- Action buttons --}}
                            <div class="pt-2 flex items-center gap-3">
                                <button type="button" @click="closeMaintModal()"
                                    class="flex-1 px-6 py-4 bg-slate-100 text-slate-500 rounded-2xl border border-slate-200 font-bold text-sm hover:bg-slate-200 transition-all">
                                    Batal
                                </button>
                                <button type="submit"
                                    :disabled="!!(dateStartErr || dateEndErr || reasonErr || roomErr)"
                                    :class="(dateStartErr || dateEndErr || reasonErr || roomErr) ? 'opacity-40 cursor-not-allowed' : 'hover:bg-red-700 hover:shadow-lg hover:shadow-red-600/30'"
                                    class="flex-[2] px-6 py-4 bg-red-600 text-white rounded-2xl font-black text-sm transition-all flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    Blokir Jadwal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- Back to Top Button --}}
    <button id="backToTop" 
        class="fixed bottom-8 right-8 z-50 p-4 rounded-2xl bg-white/80 backdrop-blur-lg border border-slate-200 text-[#1265A8] shadow-2xl transition-all duration-500 translate-y-20 opacity-0 hover:bg-[#1265A8] hover:text-white hover:-translate-y-1 active:scale-90 group"
        aria-label="Back to Top">
        
        <div class="relative">
            <div class="absolute inset-0 bg-blue-400 blur-lg opacity-0 group-hover:opacity-40 transition-opacity"></div>
            
            <svg class="w-6 h-6 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"></path>
            </svg>
        </div>
    </button>

    <script>
        function handleLoading(event, element) {
            // Mencegah redirect instan
            event.preventDefault();
            const url = element.getAttribute('href');
            
            const iconPlus = element.querySelector('#iconPlus');
            const iconLoading = element.querySelector('#iconLoading');
            const btnText = element.querySelector('#btnText');

            // Ubah State Tombol
            iconPlus.classList.add('hidden');
            iconLoading.classList.remove('hidden');
            btnText.innerText = 'Memuat...';
            element.classList.add('opacity-90', 'cursor-not-allowed');
            element.style.pointerEvents = 'none'; // Mencegah klik ganda

            setTimeout(() => {
                window.location.href = url;
            }, 600); 
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Fasilitas?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-[2rem] p-8'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form berdasarkan ID yang dikirim
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }

        document.getElementById('btnTambah').addEventListener('click', function(e) {
            const btn = this;
            const icon = document.getElementById('iconPlus');
            const spinner = document.getElementById('spinner');
            const text = document.getElementById('btnText');

            // efek ripple
            const ripple = document.createElement('span');
            const rect = btn.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = `${size}px`;
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            ripple.classList.add('ripple');
            
            btn.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);

            // efek loading
            e.preventDefault(); 
            const targetUrl = btn.getAttribute('href');

            icon.classList.add('hidden');
            spinner.classList.remove('hidden');
            text.innerText = 'Memuat...';
            btn.classList.add('opacity-80', 'cursor-wait');

            setTimeout(() => {
                window.location.href = targetUrl;
            }, 500); 
        });

        // Ambil semua elemen dengan class btn-edit
        const editButtons = document.querySelectorAll('.btn-edit');

        editButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault(); // Stop pindah halaman instan
                
                const targetUrl = this.getAttribute('href');
                const content = this.querySelector('.button-content');
                const spinner = this.querySelector('.loading-spinner');

                // Tampilkan loading
                content.classList.add('hidden');
                spinner.classList.remove('hidden');
                this.classList.add('opacity-70', 'cursor-wait');

                setTimeout(() => {
                    window.location.href = targetUrl;
                }, 600);
            });
        });

        // Logika Back to Top
        const backToTopBtn = document.getElementById('backToTop');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 400) {
                // Tampilkan tombol saat scroll lebih dari 400px
                backToTopBtn.classList.remove('translate-y-20', 'opacity-0');
                backToTopBtn.classList.add('translate-y-0', 'opacity-100');
            } else {
                // Sembunyikan tombol saat di atas
                backToTopBtn.classList.add('translate-y-20', 'opacity-0');
                backToTopBtn.classList.remove('translate-y-0', 'opacity-100');
            }
        });

        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
</body>
</html>