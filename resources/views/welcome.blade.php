<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FinAnalytics — SaaS Expense Tracker & Analitik Keuangan Pribadi</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 selection:bg-indigo-600 selection:text-white">

    <!-- Navbar -->
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="/" class="flex items-center space-x-2.5">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-extrabold text-lg shadow-sm">
                    F
                </div>
                <span class="text-xl font-bold tracking-tight text-slate-900">Fin<span class="text-indigo-600">Analytics</span></span>
            </a>

            <!-- Navigation Actions -->
            @if (Route::has('login'))
                <nav class="flex items-center space-x-3">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                            Buka Dashboard →
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="px-4 py-2 text-sm font-semibold text-slate-700 hover:text-indigo-600 transition">
                            Masuk
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                                Daftar Gratis
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </div>
    </header>

    <!-- Hero Section -->
    <main>
        <section class="relative overflow-hidden pt-16 pb-24 sm:pt-24 sm:pb-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

                    <!-- Kolom Kiri: Teks & CTA -->
                    <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                        <div class="inline-flex items-center space-x-2 bg-indigo-50 border border-indigo-100 text-indigo-700 px-3.5 py-1.5 rounded-full text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                            <span>100% Gratis • Multi-Wallet • Real-Time Analytics</span>
                        </div>

                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 leading-tight">
                            Kendalikan Arus Kas & <span class="text-indigo-600">Analitik Keuangan</span> dalam Satu Tempat.
                        </h1>

                        <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                            Catat pemasukan, pengeluaran, dan transfer antar-dompet secara instan. Pantau batas anggaran bulanan dan pahami pola belanjamu lewat grafik interaktif tanpa ribet.
                        </p>

                        <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3 pt-2">
                            @auth
                                <a href="{{ url('/dashboard') }}"
                                   class="w-full sm:w-auto px-7 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition text-center">
                                    Masuk ke Dashboard Saya
                                </a>
                            @else
                                <a href="{{ route('register') }}"
                                   class="w-full sm:w-auto px-7 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition text-center">
                                    Mulai Sekarang — Gratis
                                </a>
                                <a href="#fitur"
                                   class="w-full sm:w-auto px-7 py-3.5 bg-white hover:bg-slate-100 text-slate-700 font-semibold rounded-xl border border-slate-200 transition text-center">
                                    Lihat Fitur Utama
                                </a>
                            @endauth
                        </div>
                    </div>

                    <!-- Kolom Kanan: Ilustrasi UI Mockup Dashboard -->
                    <div class="lg:col-span-5">
                        <div class="bg-white rounded-2xl shadow-xl border border-slate-200/80 p-6 space-y-5">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                                <div>
                                    <span class="text-xs font-medium text-slate-400 uppercase">Total Saldo Gabungan</span>
                                    <p class="text-2xl font-extrabold text-slate-900">Rp 14.850.000</p>
                                </div>
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg">
                                    +24.5% Savings Rate
                                </span>
                            </div>

                            <!-- Simulasi Kartu Dompet -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-3.5 rounded-xl bg-blue-600 text-white">
                                    <span class="text-[10px] uppercase opacity-80 block">Bank Utama</span>
                                    <span class="font-bold text-sm block">BCA</span>
                                    <span class="font-semibold text-base mt-2 block">Rp 11.200.000</span>
                                </div>
                                <div class="p-3.5 rounded-xl bg-emerald-600 text-white">
                                    <span class="text-[10px] uppercase opacity-80 block">E-Wallet</span>
                                    <span class="font-bold text-sm block">GoPay / DANA</span>
                                    <span class="font-semibold text-base mt-2 block">Rp 3.650.000</span>
                                </div>
                            </div>

                            <!-- Simulasi Progress Bar Budget -->
                            <div class="space-y-3 pt-1">
                                <div class="flex justify-between text-xs">
                                    <span class="font-semibold text-slate-700">Anggaran Makan & Minum</span>
                                    <span class="text-slate-500">68% terpakai</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2.5">
                                    <div class="bg-indigo-600 h-2.5 rounded-full" style="width: 68%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Section Fitur Unggulan -->
        <section id="fitur" class="py-20 bg-white border-t border-slate-200/70">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <h2 class="text-3xl font-bold text-slate-900">Dirancang untuk Kejelasan Finansial</h2>
                    <p class="text-slate-600 mt-3 text-sm sm:text-base">
                        Semua yang kamu butuhkan untuk memisahkan pos keuangan, mencegah pemborosan, dan melihat tren arus kas secara akurat.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Fitur 1 -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-200 transition">
                        <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg mb-4">
                            01
                        </div>
                        <h3 class="font-bold text-slate-900 text-lg mb-2">Multi-Wallet</h3>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Pisahkan saldo rekening bank, e-wallet, uang tunai, hingga tabungan dengan pembaruan saldo otomatis.
                        </p>
                    </div>

                    <!-- Fitur 2 -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-200 transition">
                        <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-lg mb-4">
                            02
                        </div>
                        <h3 class="font-bold text-slate-900 text-lg mb-2">Smart Transfer</h3>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Pindahkan saldo antar-dompet (misal tarik tunai ATM) tanpa merusak laporan statistik pengeluaran bulanan.
                        </p>
                    </div>

                    <!-- Fitur 3 -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-200 transition">
                        <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-lg mb-4">
                            03
                        </div>
                        <h3 class="font-bold text-slate-900 text-lg mb-2">Budgeting Alert</h3>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Tetapkan batas maksimal belanja per kategori dan pantau indikator warna otomatis sebelum melewati batas.
                        </p>
                    </div>

                    <!-- Fitur 4 -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-200 transition">
                        <div class="w-11 h-11 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-lg mb-4">
                            04
                        </div>
                        <h3 class="font-bold text-slate-900 text-lg mb-2">Visual Analytics</h3>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Hitung <em>daily burn rate</em>, <em>savings rate</em>, dan distribusi pengeluaran bulanan melalui grafik interaktif.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm">
            <div class="flex items-center space-x-2">
                <span class="font-bold text-white">FinAnalytics</span>
                <span>• Personal Finance SaaS</span>
            </div>
            <p>
                Dibangun dengan Laravel & Tailwind CSS oleh <span class="text-white font-medium">Kenzi Minarrohman</span>
            </p>
        </div>
    </footer>

</body>
</html>