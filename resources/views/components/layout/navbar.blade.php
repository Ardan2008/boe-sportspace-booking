@vite(['resources/css/app.css', 'resources/js/app.js'])

<style>
    /* Mencegah scroll saat menu terbuka */
    .no-scroll {
        overflow: hidden;
        height: 100vh;
    }

    /* Animasi Hover Link Mobile */
    .mobile-link {
        position: relative;
        transition: all 0.3s ease;
    }

    .mobile-link::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 50%;
        width: 0;
        height: 3px;
        background-color: #276AD7;
        transition: all 0.3s ease;
        transform: translateX(-50%);
        border-radius: 2px;
    }

    .mobile-link:hover {
        color: #276AD7;
        transform: scale(1.1);
    }

    .mobile-link:hover::after {
        width: 100%;
    }
</style>

<nav id="navbar" class="fixed top-0 left-0 w-full z-50 flex items-center justify-between px-6 md:px-12 bg-transparent text-white border-b border-white/30 transition-all duration-500 py-4">
    
    <div class="flex items-center gap-4 transition-all duration-500 z-50" id="logo-container">
        <img src="/image/logo/tutwuri-logo.svg" alt="Logo" id="nav-logo" class="h-16 md:h-20 w-auto transition-all duration-500">
        <div class="flex flex-col tracking-tight">
            <h1 id="nav-title" class="text-[18px] md:text-[25px] font-semibold leading-none transition-all duration-500">
                BBPPMPV BOE Malang
            </h1>
            <p id="nav-subtitle" class="block text-[10px] md:text-[14px] text-white/80 font-normal tracking-wider leading-tight mt-1 transition-all duration-500">
                Balai Besar Pengembangan Penjaminan Mutu Pendidikan Vokasi<br>
                Bidang Otomotif dan Elektronika
            </p>
        </div>
    </div>

    <ul id="nav-links" class="hidden md:flex text-[18px] items-center gap-8 font-semibold transition-all duration-500 mr-10">
        <li><a href="/" class="hover:text-[#276AD7] transition">Home</a></li>
        <li><a href="#about" class="hover:text-[#276AD7] transition">About</a></li>
        <li><a href="#booking" class="hover:text-[#276AD7] transition">Booking</a></li>
        <li><a href="#contact" class="hover:text-[#276AD7] transition">Contact</a></li>
        <li>
            <button id="dev-btn-desktop" class="px-4 py-1.5 border border-white/50 rounded-xl bg-white/10 backdrop-blur-sm text-white hover:bg-[#276AD7] hover:border-[#276AD7] hover:text-white transition duration-300 cursor-pointer text-[16px]">
                Developers
            </button>
        </li>
    </ul>

    <button id="menu-btn" class="md:hidden flex flex-col gap-1.5 z-[60] p-2 transition-colors duration-300">
        <span class="w-7 h-1 bg-current transition-all duration-300"></span>
        <span class="w-7 h-1 bg-current transition-all duration-300"></span>
        <span class="w-7 h-1 bg-current transition-all duration-300"></span>
    </button>

    <div id="mobile-menu" class="fixed inset-0 bg-white text-black translate-x-full transition-transform duration-500 flex flex-col items-center justify-center gap-10 text-3xl font-black z-[55]">
        <a href="/" class="mobile-link">Home</a>
        <a href="#about" class="mobile-link">About</a>
        <a href="#booking" class="mobile-link">Booking</a>
        <a href="#contact" class="mobile-link">Contact</a>
        <button id="dev-btn-mobile" class="mobile-link text-3xl font-black cursor-pointer">Developer</button>
    </div>
</nav>

<div id="dev-modal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/60 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    
    <div class="bg-[#F8FAFC] rounded-2xl shadow-2xl max-w-4xl w-full m-4 relative transform scale-95 transition-transform duration-300 text-black flex flex-col max-h-[90vh]" id="modal-content">
        
        <button id="close-modal-btn" class="absolute top-4 right-4 md:top-6 md:right-6 w-10 h-10 flex items-center justify-center bg-gray-100 text-gray-500 rounded-full shadow-md transition-all duration-300 cursor-pointer z-[80] overflow-hidden group">
            <span class="absolute inset-0 w-full h-full bg-red-500 rounded-full scale-0 transition-transform duration-300 ease-out origin-center group-hover:scale-100"></span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 relative z-10 transition-all duration-300 group-hover:text-white group-hover:rotate-90 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        <div class="p-6 md:p-10 flex flex-col flex-1 overflow-hidden w-full">
            
            <div class="text-center mb-6 flex-shrink-0 pr-8 pl-8"> 
                <h3 class="text-2xl md:text-3xl font-black text-[#276AD7] tracking-tight">Our Developer Team</h3>
                <p class="text-gray-500 text-sm mt-1">Tim pengembangan website BBPPMPV BOE Malang</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 overflow-y-auto flex-1 pr-2 pb-2">
                
                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm flex items-start gap-4">
                    <div class="p-3 bg-blue-50 text-[#276AD7] rounded-xl flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold tracking-wider text-[#276AD7] uppercase block border-b border-gray-100 pb-1 mb-1">Backend Developer</span>
                        <h4 class="font-bold text-gray-800 text-sm leading-snug uppercase">Mohammad Dirgo Marchellino</h4>
                        <p class="text-[11px] text-gray-400 font-semibold mt-0.5">SMKN 1 KRAKSAAN</p>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm flex items-start gap-4">
                    <div class="p-3 bg-blue-50 text-[#276AD7] rounded-xl flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold tracking-wider text-[#276AD7] uppercase block border-b border-gray-100 pb-1 mb-1">UI/UX Designer</span>
                        <h4 class="font-bold text-gray-800 text-sm leading-snug uppercase">Moh. Romsi Ramadani</h4>
                        <p class="text-[11px] text-gray-400 font-semibold mt-0.5">SMKN 1 KRAKSAAN</p>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm flex items-start gap-4">
                    <div class="p-3 bg-blue-50 text-[#276AD7] rounded-xl flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold tracking-wider text-[#276AD7] uppercase block border-b border-gray-100 pb-1 mb-1">Frontend Developer</span>
                        <h4 class="font-bold text-gray-800 text-sm leading-snug uppercase">Ardan Ramadhan P.H</h4>
                        <p class="text-[11px] text-gray-400 font-semibold mt-0.5">SMKN 1 PURWOSARI</p>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm flex items-start gap-4">
                    <div class="p-3 bg-blue-50 text-[#276AD7] rounded-xl flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold tracking-wider text-[#276AD7] uppercase block border-b border-gray-100 pb-1 mb-1">UI/UX Designer</span>
                        <h4 class="font-bold text-gray-800 text-sm leading-snug uppercase">Syafiq Labib</h4>
                        <p class="text-[11px] text-gray-400 font-semibold mt-0.5">SMKN 1 PURWOSARI</p>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm flex items-start gap-4">
                    <div class="p-3 bg-blue-50 text-[#276AD7] rounded-xl flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold tracking-wider text-[#276AD7] uppercase block border-b border-gray-100 pb-1 mb-1">Backend Developer</span>
                        <h4 class="font-bold text-gray-800 text-sm leading-snug uppercase">Muhammad Farchan</h4>
                        <p class="text-[11px] text-gray-400 font-semibold mt-0.5">SMKN 8 MALANG</p>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm flex items-start gap-4">
                    <div class="p-3 bg-blue-50 text-[#276AD7] rounded-xl flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold tracking-wider text-[#276AD7] uppercase block border-b border-gray-100 pb-1 mb-1">UI/UX Designer</span>
                        <h4 class="font-bold text-gray-800 text-sm leading-snug uppercase">Feriska Agustina Fitria</h4>
                        <p class="text-[11px] text-gray-400 font-semibold mt-0.5">SMKN 8 MALANG</p>
                    </div>
                </div>

            </div>
        </div>
        
    </div>
</div>

<script>
    const navbar = document.getElementById('navbar');
    const navLogo = document.getElementById('nav-logo');
    const navTitle = document.getElementById('nav-title');
    const subtitle = document.getElementById('nav-subtitle');
    const menuBtn = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const spans = menuBtn.querySelectorAll('span');
    const mobileLinks = document.querySelectorAll('.mobile-link');
    const body = document.body;

    // Elemen Modal Baru
    const devModal = document.getElementById('dev-modal');
    const modalContent = document.getElementById('modal-content');
    const devBtnDesktop = document.getElementById('dev-btn-desktop');
    const devBtnMobile = document.getElementById('dev-btn-mobile');
    const closeModalBtn = document.getElementById('close-modal-btn');

    window.addEventListener('scroll', function() {
        const isScrolled = window.scrollY > 50;
        const isMenuOpen = !mobileMenu.classList.contains('translate-x-full');
        const devBtnDesktop = document.getElementById('dev-btn-desktop'); // Tambahkan ini jika belum didefinisikan di atas

        if (isScrolled) {
            // Scrolled Down
            navbar.classList.replace('py-4', 'py-2');
            navbar.classList.add('bg-white', 'shadow-md', 'text-black', 'border-gray-200');
            navbar.classList.remove('bg-transparent', 'text-white', 'border-white/30');
            
            // Perubahan Box Developer saat navbar Putih
            if (devBtnDesktop) {
                devBtnDesktop.classList.replace('border-white/50', 'border-[#276AD7]/30');
                devBtnDesktop.classList.replace('bg-white/10', 'bg-[#276AD7]/5');
                devBtnDesktop.classList.replace('text-white', 'text-[#276AD7]');
            }

            // Resize Logo & Title
            navLogo.classList.add('h-12', 'md:h-16'); 
            navLogo.classList.remove('h-16', 'md:h-20');
            navTitle.classList.add('md:text-[20px]');
            navTitle.classList.remove('md:text-[25px]');
            if(subtitle) subtitle.classList.replace('text-white/80', 'text-black/60');
            
        } else {
            // Scrolled Up (Top of page)
            navbar.classList.replace('py-2', 'py-4');
            navbar.classList.remove('bg-white', 'shadow-md', 'text-black', 'border-gray-200');
            navbar.classList.add('bg-transparent', 'text-white', 'border-white/30');
            
            // Kembalikan Box Developer ke mode transparan/putih
            if (devBtnDesktop) {
                devBtnDesktop.classList.replace('border-[#276AD7]/30', 'border-white/50');
                devBtnDesktop.classList.replace('bg-[#276AD7]/5', 'bg-white/10');
                devBtnDesktop.classList.replace('text-[#276AD7]', 'text-white');
            }

            // Restore Logo & Title
            navLogo.classList.remove('h-12', 'md:h-16');
            navLogo.classList.add('h-16', 'md:h-20');
            navTitle.classList.remove('md:text-[20px]');
            navTitle.classList.add('md:text-[25px]');
            if(subtitle) subtitle.classList.replace('text-black/60', 'text-white/80');
        }

        // Hamburger color logic 
        if (isMenuOpen || isScrolled) {
            menuBtn.classList.add('text-black');
            menuBtn.classList.remove('text-white');
        } else {
            menuBtn.classList.add('text-white');
            menuBtn.classList.remove('text-black');
        }
    });

    // Toggle Menu
    menuBtn.addEventListener('click', () => {
        const isMenuOpen = mobileMenu.classList.contains('translate-x-0');
        if (!isMenuOpen) {
            openMenu();
        } else {
            closeMenu();
        }
    });

    function openMenu() {
        mobileMenu.classList.replace('translate-x-full', 'translate-x-0');
        
        // Animasi Hamburger ke X
        spans[0].classList.add('rotate-45', 'translate-y-[10px]');
        spans[1].classList.add('opacity-0');
        spans[2].classList.add('-rotate-45', '-translate-y-[10px]');
        
        // Kunci Scroll
        body.classList.add('no-scroll');
        
        // Warna text saat menu putih terbuka
        menuBtn.classList.add('text-black');
        menuBtn.classList.remove('text-white');
        navTitle.classList.add('text-black');
        if(subtitle) subtitle.classList.add('text-black/60');
    }

    function closeMenu() {
        mobileMenu.classList.replace('translate-x-0', 'translate-x-full');
        
        // Animasi X kembali ke Hamburger
        spans[0].classList.remove('rotate-45', 'translate-y-[10px]');
        spans[1].classList.remove('opacity-0');
        spans[2].classList.remove('-rotate-45', '-translate-y-[10px]');
        
        // Lepas Kunci Scroll
        body.classList.remove('no-scroll');
        
        if (window.scrollY <= 50) {
            menuBtn.classList.replace('text-black', 'text-white');
            navTitle.classList.remove('text-black');
            if(subtitle) subtitle.classList.remove('text-black/60');
        }
    }

    mobileLinks.forEach(link => { 
        link.addEventListener('click', closeMenu); 
    });


    // ================= LOGIK MODAL DEVELOPER =================

    function openModal() {
        devModal.classList.remove('hidden');
        devModal.classList.add('flex');
        body.classList.add('no-scroll');
        
        // Animasi Fade In
        setTimeout(() => {
            devModal.classList.add('opacity-100');
            modalContent.classList.replace('scale-95', 'scale-100');
        }, 10);
    }

    function closeModal() {
        devModal.classList.remove('opacity-100');
        modalContent.classList.replace('scale-100', 'scale-95');
        
        // Sembunyikan setelah animasi selesai
        setTimeout(() => {
            devModal.classList.replace('flex', 'hidden');
            // Hanya lepas no-scroll jika menu mobile juga sedang tertutup
            if (mobileMenu.classList.contains('translate-x-full')) {
                body.classList.remove('no-scroll');
            }
        }, 300);
    }

    // Event Listeners untuk Modal
    devBtnDesktop.addEventListener('click', openModal);
    devBtnMobile.addEventListener('click', () => {
        closeMenu(); // Tutup menu mobile terlebih dahulu
        setTimeout(openModal, 400); // Buka modal setelah menu mobile bergeser
    });
    closeModalBtn.addEventListener('click', closeModal);

    // Close modal jika klik di luar area card putih
    devModal.addEventListener('click', (e) => {
        if (e.target === devModal) closeModal();
    });
</script>