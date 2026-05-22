<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/image/logo/tutwuri-logo.svg">
    <title>Boe-Sport Space | Detail Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 to-gray-100 flex items-center justify-center p-6">

    <div class="w-full max-w-2xl">

        <!-- Card -->
        <div class="bg-white rounded-3xl shadow-2xl p-10 relative overflow-hidden">

            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">
                        Detail Admin
                    </h1>
                    <p class="text-sm text-gray-400">
                        Informasi lengkap akun administrator
                    </p>
                </div>

                <a href="{{ route('admin.active.control') }}"
                class="group inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-600 px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm hover:border-blue-600 hover:text-blue-600 transition-all duration-300 active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Back</span>
                </a>
            </div>

            <!-- Profile Section -->
            <div class="flex items-center gap-6 mb-10">

                <div class="w-24 h-24 rounded-full bg-blue-600 text-white flex items-center justify-center text-4xl font-bold shadow-lg">
                    {{ strtoupper(substr($admin->username, 0, 1)) }}
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-gray-800">
                        {{ $admin->nama }}
                    </h2>
                    <p class="text-gray-500">
                        Username: <span class="font-semibold text-gray-700">{{ $admin->username }}</span>
                    </p>
                </div>

            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200 mb-8"></div>

            <!-- Detail Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="bg-gray-50 p-5 rounded-2xl hover:shadow-md transition">
                    <p class="text-xs uppercase text-gray-400 tracking-wide">
                        ID Admin
                    </p>
                    <p class="text-lg font-bold text-gray-800 mt-1">
                        {{ $admin->id_log }}
                    </p>
                </div>

                <div class="bg-gray-50 p-5 rounded-2xl hover:shadow-md transition">
                    <p class="text-xs uppercase text-gray-400 tracking-wide">
                        Nama Lengkap
                    </p>
                    <p class="text-lg font-bold text-gray-800 mt-1">
                        {{ $admin->nama }}
                    </p>
                </div>

                <div class="bg-gray-50 p-5 rounded-2xl hover:shadow-md transition md:col-span-2">
                    <p class="text-xs uppercase text-gray-400 tracking-wide">
                        Username
                    </p>
                    <p class="text-lg font-bold text-gray-800 mt-1">
                        {{ $admin->username }}
                    </p>
                </div>

            </div>

        </div>
    </div>

</body>
</html>