<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Analitik Keuangan Pribadi
                </h2>
                <p class="text-xs text-gray-500">Ringkasan arus kas & kesehatan finansialmu</p>
            </div>

            <!-- Filter Bulan & Tahun -->
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center space-x-2">
                <select name="month" class="rounded-lg border-gray-300 text-sm">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
                <input type="number" name="year" value="{{ $year }}" class="w-24 rounded-lg border-gray-300 text-sm">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    Tampilkan
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- 4 Kartu Metrik Utama -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <span class="text-xs font-medium text-gray-500 uppercase">Total Saldo Dompet</span>
                    <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($totalBalance, 0, ',', '.') }}</p>
                    <a href="{{ route('wallets.index') }}" class="text-xs text-indigo-600 hover:underline mt-2 inline-block">Kelola dompet →</a>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <span class="text-xs font-medium text-gray-500 uppercase">Pemasukan Bulan Ini</span>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">+Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</p>
                    <span class="text-xs text-gray-400 mt-2 inline-block">Savings Rate: <strong>{{ $savingsRate }}%</strong></span>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <span class="text-xs font-medium text-gray-500 uppercase">Pengeluaran Bulan Ini</span>
                    <p class="text-2xl font-bold text-red-600 mt-1">-Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</p>
                    <span class="text-xs text-gray-400 mt-2 inline-block">Rata-rata: <strong>Rp {{ number_format($dailyBurnRate, 0, ',', '.') }}/hari</strong></span>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <span class="text-xs font-medium text-gray-500 uppercase">Arus Kas Bersih (Net)</span>
                    <p class="text-2xl font-bold {{ $netCashFlow >= 0 ? 'text-indigo-600' : 'text-red-600' }} mt-1">
                        {{ $netCashFlow >= 0 ? '+' : '' }}Rp {{ number_format($netCashFlow, 0, ',', '.') }}
                    </p>
                    <a href="{{ route('transactions.index') }}" class="text-xs text-indigo-600 hover:underline mt-2 inline-block">+ Catat transaksi →</a>
                </div>
            </div>

            <!-- Baris Grafik Analitik (Chart.js) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Bar Chart: Tren 6 Bulan -->
                <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="font-semibold text-gray-800 mb-4">Tren Pemasukan vs Pengeluaran (6 Bulan)</h3>
                    <div class="relative h-72">
                        <canvas id="cashFlowChart"></canvas>
                    </div>
                </div>

                <!-- Donut Chart: Komposisi Pengeluaran -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="font-semibold text-gray-800 mb-4">Distribusi Pengeluaran</h3>
                    @if ($expenseByCategory->isEmpty())
                        <div class="h-72 flex items-center justify-center text-sm text-gray-400 text-center">
                            Belum ada data pengeluaran pada bulan ini.
                        </div>
                    @else
                        <div class="relative h-72">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Baris Bawah: Status Anggaran & Transaksi Terakhir -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Ringkasan Budget -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-gray-800">Monitoring Anggaran Bulan Ini</h3>
                        <a href="{{ route('budgets.index') }}" class="text-xs text-indigo-600 hover:underline">Atur Anggaran</a>
                    </div>
                    <div class="space-y-4">
                        @forelse ($budgets as $budget)
                            @php
                                $barColor = $budget->percentage >= 100 ? 'bg-red-600' : ($budget->percentage >= 80 ? 'bg-amber-500' : 'bg-emerald-500');
                            @endphp
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="font-medium text-gray-700">{{ $budget->category->name }}</span>
                                    <span class="text-gray-500">
                                        Rp {{ number_format($budget->spent, 0, ',', '.') }} / Rp {{ number_format($budget->amount_limit, 0, ',', '.') }} ({{ $budget->percentage }}%)
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="{{ $barColor }} h-2 rounded-full" style="width: {{ min(100, $budget->percentage) }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400">Belum ada anggaran yang diatur untuk bulan ini.</p>
                        @endforelse
                    </div>
                </div>

                <!-- 5 Transaksi Terakhir -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-gray-800">5 Transaksi Terakhir</h3>
                        <a href="{{ route('transactions.index') }}" class="text-xs text-indigo-600 hover:underline">Lihat Semua</a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse ($recentTransactions as $trx)
                            <div class="py-2.5 flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">
                                        {{ $trx->type === 'transfer' ? 'Transfer Antar Dompet' : ($trx->category->name ?? 'Transaksi') }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $trx->transaction_date->format('d M Y') }} • {{ $trx->wallet->name }}</p>
                                </div>
                                <span class="text-sm font-bold {{ $trx->type === 'income' ? 'text-emerald-600' : ($trx->type === 'expense' ? 'text-red-600' : 'text-blue-600') }}">
                                    {{ $trx->type === 'income' ? '+' : ($trx->type === 'expense' ? '-' : '') }}
                                    Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400">Belum ada transaksi tercatat.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Script Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // 1. Bar Chart Arus Kas
        const ctxCashFlow = document.getElementById('cashFlowChart').getContext('2d');
        new Chart(ctxCashFlow, {
            type: 'bar',
            data: {
                labels: @json($trendLabels),
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: @json($trendIncome),
                        backgroundColor: '#10B981',
                        borderRadius: 6,
                    },
                    {
                        label: 'Pengeluaran',
                        data: @json($trendExpense),
                        backgroundColor: '#EF4444',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        // 2. Donut Chart Kategori Pengeluaran
        @if ($expenseByCategory->isNotEmpty())
        const ctxCategory = document.getElementById('categoryChart').getContext('2d');
        new Chart(ctxCategory, {
            type: 'doughnut',
            data: {
                labels: @json($expenseByCategory->pluck('name')),
                datasets: [{
                    data: @json($expenseByCategory->pluck('total')),
                    backgroundColor: @json($expenseByCategory->pluck('color')),
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
        @endif
    </script>
</x-app-layout>