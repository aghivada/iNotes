<?php
require_once 'config.php';
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iNotes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // AKTIFKAN OPSI DARK MODE BERBASIS CLASS PADA TAILWIND CDN
        tailwind.config = {
            darkMode: 'class'
        }

        // Pengecekan tema awal saat halaman dimuat
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <style>
        /* Pola Grid Geometris yang Dipertajam */
        .bg-grid-pattern {
            background-size: 50px 50px;
            background-image: linear-gradient(to right, rgba(99, 102, 241, 0.15) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(99, 102, 241, 0.15) 1px, transparent 1px);
            
            /* Efek Masking: Grid hanya muncul tajam di atas dan memudar total di setengah halaman awal */
            -webkit-mask-image: linear-gradient(to bottom, white 30%, transparent 80%);
            mask-image: linear-gradient(to bottom, white 30%, transparent 80%);
        }
        
        .dark .bg-grid-pattern {
            background-image: linear-gradient(to right, rgba(99, 102, 241, 0.10) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(99, 102, 241, 0.10) 1px, transparent 1px);
        }
    </style>
</head>
<body class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 antialiased min-h-screen flex flex-col transition-colors duration-300 relative overflow-x-hidden">

    <!-- ORNAMEN 1: BACKGROUND GRID MESH -->
    <div class="absolute top-0 left-0 right-0 h-[600px] bg-grid-pattern pointer-events-none -z-20"></div>

    <!-- ORNAMEN 2: PENDARAN CAHAYA LEMBUT (GLOW EFFECT) -->
    <div class="absolute top-[-5%] left-[50%] -translate-x-1/2 w-[700px] h-[350px] bg-indigo-500/15 dark:bg-indigo-500/10 rounded-full blur-[130px] pointer-events-none -z-10"></div>

    <!-- WRAPPER UNTUK KONTEN UTAMA -->
    <div class="flex-grow">
        <!-- NAVBAR -->
        <nav class="max-w-6xl mx-auto px-6 py-6 flex justify-between items-center relative z-10">
            <div class="flex items-center gap-2">
                <span class="font-black text-2xl tracking-wider text-indigo-600 dark:text-indigo-400">I</span>
                <span class="font-bold text-lg tracking-wide text-slate-900 dark:text-white uppercase">INOTE</span>
            </div>
            
            <button id="theme-toggle" class="p-2 rounded-xl bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all shadow-sm">
                <svg id="theme-toggle-light-icon" class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M16.243 17.657l.707.707M6.343 6.343l.707-.707M14 12a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <svg id="theme-toggle-dark-icon" class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>
        </nav>

        <!-- HERO SECTION -->
        <header class="max-w-4xl mx-auto text-center px-6 py-28 sm:py-36 relative z-10">        
            <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-slate-900 dark:text-white leading-tight">
                Your effective <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-500 dark:from-indigo-400 dark:to-purple-400">Note app</span>
            </h1>
            <p class="mt-6 text-base sm:text-lg text-slate-500 dark:text-slate-400 max-w-xl mx-auto leading-relaxed">
                Manage your notes, tasks, and brilliant ideas effectively with a beautifully minimalist layout designed for speed and simplicity.
            </p>
            <div class="mt-10">
                <a href="login.php" class="inline-block bg-slate-900 hover:bg-black dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white font-semibold px-8 py-3.5 rounded-xl shadow-md transition-all transform hover:-translate-y-0.5 active:translate-y-0">
                    Coba Sekarang
                </a>
            </div>
        </header>

        <!-- SEKTOR FITUR -->
        <section class="max-w-5xl mx-auto px-6 py-24 border-t border-slate-100 dark:border-slate-900 relative z-20">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Fitur 1 -->
                <div class="p-8 rounded-2xl bg-slate-50/50 dark:bg-slate-900/20 border border-slate-200/60 dark:border-slate-900/80 shadow-sm hover:border-indigo-500/40 dark:hover:border-indigo-400/40 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-lg mb-5 shadow-sm border border-indigo-100 dark:border-none">✨</div>
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Minimalist UI</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">Fokus penuh pada tulisan Anda tanpa gangguan visual yang tidak penting.</p>
                </div>

                <!-- Fitur 2 -->
                <div class="p-8 rounded-2xl bg-slate-50/50 dark:bg-slate-900/20 border border-slate-200/60 dark:border-slate-900/80 shadow-sm hover:border-indigo-500/40 dark:hover:border-indigo-400/40 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-lg mb-5 shadow-sm border border-indigo-100 dark:border-none">🎨</div>
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Smart Contrast</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">Warna teks otomatis menyesuaikan tingkat kecerahan latar belakang yang Anda pilih.</p>
                </div>

                <!-- Fitur 3 -->
                <div class="p-8 rounded-2xl bg-slate-50/50 dark:bg-slate-900/20 border border-slate-200/60 dark:border-slate-900/80 shadow-sm hover:border-indigo-500/40 dark:hover:border-indigo-400/40 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-lg mb-5 shadow-sm border border-indigo-100 dark:border-none">🔒</div>
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Google Auth</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">Masuk secara instan dan aman dengan satu klik menggunakan akun Google Anda.</p>
                </div>

            </div>
        </section>
    </div>

    <!-- FOOTER -->
    <footer class="text-center py-10 text-xs text-slate-400 dark:text-slate-600 relative z-10 border-t border-slate-50 dark:border-slate-900/40 w-full">
        &copy; 2024 iNotes App. Made With 🦾
    </footer>

    <script>
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');

        function updateIcons() {
            if (document.documentElement.classList.contains('dark')) {
                themeToggleLightIcon.classList.remove('hidden');
                themeToggleDarkIcon.classList.add('hidden');
            } else {
                themeToggleLightIcon.classList.add('hidden');
                themeToggleDarkIcon.classList.remove('hidden');
            }
        }

        updateIcons();

        themeToggleBtn.addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
            if (document.documentElement.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
            } else {
                localStorage.setItem('theme', 'light');
            }
            updateIcons();
        });
    </script>
</body>
</html>