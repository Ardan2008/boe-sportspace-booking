<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <title>BOE-Sport Space | Detail Booking</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body.swal2-shown { overflow: unset !important; padding-right: 0 !important; }
    </style>
</head>
<body>
    <div class="min-h-screen bg-[#f8fafc] flex items-center justify-center p-4 font-sans text-slate-900">
        <div class="w-full max-w-xl bg-white/80 backdrop-blur-xl rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-white p-2">
            
            <div class="bg-white rounded-[2.3rem] p-8 sm:p-10 border border-slate-100">
                
                <div class="mb-10 text-center">
                    <div class="inline-flex items-center gap-2 bg-blue-50 text-blue-600 text-[10px] font-bold uppercase tracking-[0.2em] px-3 py-1.5 rounded-full mb-3">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                        </span>
                        Administrator
                    </div>
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight">Detail Booking</h2>
                    <p class="text-slate-400 text-sm mt-1 font-medium">Informasi lengkap booking lapangan olahraga</p>
                </div>

                <div class="grid grid-cols-1 gap-6">
                                        
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6"> 
                        <div class="relative group mt-2"> 
                            <label class="absolute -top-2.5 left-4 bg-white px-2 text-[11px] font-bold text-blue-600 uppercase tracking-widest z-10 transition-all">
                                Nama Pemesan
                            </label>
                            <input type="text" value="Yanto Sholeh" readonly 
                                class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none font-semibold text-slate-700">
                        </div>

                        <div class="relative group mt-2">
                            <label class="absolute -top-2.5 left-4 bg-white px-2 text-[11px] font-bold text-blue-600 uppercase tracking-widest z-10 transition-all">
                                WhatsApp
                            </label>
                            <input type="tel" value="08934678010" readonly
                                class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none font-semibold text-slate-700">
                        </div>
                    </div>

                    <div class="relative group">
                        <label class="absolute -top-2.5 left-4 bg-white px-2 text-[11px] font-bold text-blue-600 uppercase tracking-wider z-10">Fasilitas</label>
                        <div class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl font-semibold text-slate-700">
                            Lapangan Tunggul Ametung - BOE-Sport Space
                        </div>
                    </div>

                    <div class="relative group">
                        <label class="absolute -top-2.5 left-4 bg-white px-2 text-[11px] font-bold text-emerald-600 uppercase tracking-wider z-10">Total Pembayaran</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-5 text-emerald-600 font-bold">Rp</span>
                            <input type="text" value="200.000" readonly
                                class="w-full pl-12 pr-5 py-4 bg-emerald-50/30 border-2 border-emerald-100/50 rounded-2xl font-bold text-xl text-emerald-700">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute -top-2.5 left-4 bg-white px-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider z-10">Tanggal</label>
                            <div class="w-full px-4 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl font-bold text-slate-600 text-sm">
                                24 Februari 2026
                            </div>
                        </div>
                        <div class="relative">
                            <label class="absolute -top-2.5 left-4 bg-white px-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider z-10">Sesi Waktu</label>
                            <div class="w-full px-4 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl font-bold text-slate-600 text-sm">
                                Pagi (08:00 - 12:00)
                            </div>
                        </div>
                    </div>

                    <div class="pt-8">
                        <button type="button" onclick="kembali()" 
                            class="w-full py-4 rounded-2xl text-slate-500 font-black tracking-[0.2em] border-2 border-slate-100 hover:border-slate-200 hover:bg-slate-50 hover:text-slate-800 transition-all duration-300 active:scale-[0.95] flex items-center justify-center group shadow-sm cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            KEMBALI
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>  

    <script>
        function kembali() {
            Swal.fire({
                title: '<span class="text-xl font-black text-slate-800 uppercase tracking-[0.2em]">Kembali ke Riwayat?</span>',
                html: '<p class="text-slate-500 font-medium text-sm px-4 leading-relaxed tracking-wide">Anda akan diarahkan kembali ke daftar riwayat booking.</p>',
                icon: 'info',
                iconColor: '#2563eb',
                showCancelButton: true,
                confirmButtonColor: '#2563eb', 
                cancelButtonColor: '#1e293b', 
                confirmButtonText: 'YA, KEMBALI',
                cancelButtonText: 'TETAP DI SINI',
                reverseButtons: true,
                padding: '3rem 2rem',
                borderRadius: '2.5rem',
                customClass: {
                    confirmButton: 'rounded-2xl px-8 py-4 font-black tracking-widest text-[10px]',
                    cancelButton: 'rounded-2xl px-8 py-4 font-black tracking-widest text-[10px] text-white'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "/admin/dashboard/historyBooking";
                }
            });
        }
    </script>
</body>
</html>