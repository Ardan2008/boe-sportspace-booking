<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <title>BOE-Sport Space | Manage Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-blue: #2563eb;
            --soft-slate: #f1f5f9;
            --text-main: #1e293b;
        }

        .my-swal-popup {
            border-radius: 24px !important;
            padding: 2.5rem !important;
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .my-swal-title {
            font-family: 'Inter', sans-serif;
            font-size: 1.5rem !important;
            font-weight: 700 !important;
            color: var(--text-main) !important;
        }

        /* Tombol Konfirmasi (YA) */
        .my-swal-confirm {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
            color: white !important;
            border-radius: 16px !important;
            padding: 14px 32px !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            letter-spacing: 0.5px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2) !important;
            border: none !important;
            order: 2;
        }

        .my-swal-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.3) !important;
            filter: brightness(1.1);
        }

        /* Tombol Batal (TIDAK) */
        .my-swal-cancel {
            background: var(--soft-slate) !important;
            color: #64748b !important;
            border-radius: 16px !important;
            padding: 14px 32px !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            transition: all 0.2s ease !important;
            border: 1px solid #e2e8f0 !important;
            margin-right: 12px !important;
            order: 1;
        }

        /* Efek Loading */
        .custom-loader {
            width: 48px;
            height: 48px;
            border: 5px solid var(--soft-slate);
            border-bottom-color: var(--primary-blue);
            border-radius: 50%;
            display: inline-block;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
        }

        @keyframes rotation {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .swal2-icon.swal2-success {
            border-color: #10b981 !important;
            color: #10b981 !important;
        }

        .swal2-icon.swal2-success [class^=swal2-success-line] {
            background-color: #10b981 !important;
        }

        .swal2-icon.swal2-success .swal2-success-ring {
            border: .25em solid rgba(16, 185, 129, 0.2) !important;
        }

        .swal2-icon.swal2-success .swal2-success-circular-line-left,
        .swal2-icon.swal2-success .swal2-success-circular-line-right,
        .swal2-icon.swal2-success .swal2-success-fix {
            background-color: transparent !important;
        }

        .my-swal-success-title {
            color: #065f46 !important;
            font-weight: 800 !important;
            font-family: 'Inter', sans-serif;
        }

        .my-swal-progress-bar {
            background: #10b981 !important;
        }
    </style>
</head>
<body>
    <div class="min-h-screen bg-slate-100 flex items-center justify-center p-6">
        <div class="w-full max-w-4xl bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col md:flex-row border border-slate-200">
            
            <div class="w-full md:w-1/3 bg-blue-600 p-8 text-white flex flex-col items-center justify-center text-center">
                <div class="relative mb-6">
                    <div class="w-32 h-32 bg-white/20 backdrop-blur-md p-2 rounded-full ring-4 ring-white/30">
                        <div class="w-full h-full bg-white rounded-full flex items-center justify-center overflow-hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="absolute bottom-2 right-2 bg-green-400 w-6 h-6 rounded-full border-4 border-blue-600 shadow-lg"></div>
                </div>
                
                <h2 class="text-xl font-bold tracking-tight uppercase">Manage Admin</h2>
                <p class="text-blue-100 text-sm mt-2 opacity-80 italic">Administrator Profile Settings</p>
                
                <div class="mt-8 w-full border-t border-white/20 pt-6">
                    <div class="text-xs uppercase tracking-widest text-blue-200 font-bold mb-2">Status</div>
                    <span class="px-4 py-1.5 bg-green-500/20 text-green-300 rounded-full text-xs font-bold border border-green-500/30">
                        Active Account
                    </span>
                </div>
            </div>

            <div class="w-full md:w-2/3 p-8 md:p-12 bg-white">
                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-slate-800">Account Details</h3>
                    <p class="text-slate-500 text-sm">Perbarui informasi kredensial dan hak akses admin di sini.</p>
                </div>

                <form action="{{ route('admin.update', $admin->id_log) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-2 ml-1">Email / Username</label>
                            <div class="relative">
                                <input name="username" type="text" value="{{ $admin->username }}"
                                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none text-slate-700 font-medium">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase mb-2 ml-1">Current Password</label>
                                <input type="password" value="********" 
                                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase mb-2 ml-1">New Password</label>
                                <input name="password" type="password" placeholder="Enter new password" 
                                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none italic text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-4 text-center md:text-left">Permission Role</label>
                        <div class="flex flex-wrap gap-4 justify-center md:justify-start">
                            <label class="flex items-center p-3 px-5 border-2 border-slate-100 rounded-2xl cursor-pointer hover:bg-blue-50 hover:border-blue-200 transition-all group">
                                <input type="radio" name="role" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-semibold text-slate-600 group-hover:text-blue-700">Read Only</span>
                            </label>
                            <label class="flex items-center p-3 px-5 border-2 border-blue-500 bg-blue-50 rounded-2xl cursor-pointer transition-all">
                                <input type="radio" name="role" checked class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-semibold text-blue-700">Full Edit Access</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 pt-6">
                        <button id="btn-kembali" type="button" class="sm:w-1/3 px-6 py-4 border-2 border-slate-200 text-slate-500 font-bold rounded-2xl hover:bg-slate-100 transition-all active:scale-95 text-sm uppercase flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali
                        </button>
                        <button id="btn-simpan" type="submit" class="flex-1 px-6 py-4 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all active:scale-95 text-sm uppercase flex items-center justify-center gap-2">
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('btn-kembali').addEventListener('click', function() {
        Swal.fire({
            title: 'Yakin ingin keluar?',
            text: "Data yang belum disimpan akan terhapus secara permanen.",
            icon: 'question', 
            iconColor: '#3b82f6',
            showCancelButton: true,
            confirmButtonText: 'Ya, Keluar Sekarang',
            cancelButtonText: 'Batal',
            buttonsStyling: false, 
            customClass: {
                popup: 'my-swal-popup',
                title: 'my-swal-title',
                confirmButton: 'my-swal-confirm',
                cancelButton: 'my-swal-cancel'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menyinkronkan...',
                    html: '<div class="mt-4"><span class="custom-loader"></span></div>',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    customClass: {
                        popup: 'my-swal-popup',
                        title: 'my-swal-title'
                    }
                });

                setTimeout(() => {
                    window.location.href = '/admin/dashboard/management/admin_active_control'; 
                }, 1800);
            }
        });
    });

    document.getElementById('btn-simpan').addEventListener('click', function(e) {
            e.preventDefault();

            const form = this.closest('form');
            const formData = new FormData(form);

            // 🔥 penting untuk Laravel PUT
            formData.append('_method', 'PUT');

            Swal.fire({
                title: 'Menyimpan Data',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(async (response) => {
                let data;

                try {
                    data = await response.json();
                } catch (e) {
                    data = null;
                }

                if (!response.ok || !data) {
                    throw { message: 'Server tidak mengembalikan JSON yang valid' };
                }

                return data;
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message
                    });
                } else {
                    throw { message: data.message || 'Gagal update' };
                }

                setTimeout(() => {
                    location.reload(); // 🔥 AUTO REFRESH
                }, 1600);
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: err.message || 'Terjadi kesalahan'
                });
            });
        });
    </script>
</body>
</html>