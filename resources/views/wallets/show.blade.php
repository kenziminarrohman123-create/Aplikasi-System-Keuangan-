<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('wallets.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 font-medium">
                    ← Kembali ke Dompet
                </a>
                <span class="text-gray-300">/</span>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Riwayat {{ $wallet->name }}
                </h2>
            </div>
            <a href="{{ route('transactions.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition">
                + Catat Transaksi
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Banner Kartu Dompet & Ringkasan Masuk/Keluar -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Kartu Utama -->
                <div class="rounded-xl p-6 text-white shadow-md flex flex-col justify-between"
                     style="background-color: {{ $wallet->color }};">
                    <div>
                        <span class="text-xs uppercase tracking-wider opacity-80">{{ $wallet->type }}</span>
                        <h3 class="text-2xl font-bold mt-0.5">{{ $wallet->name }}</h3>
                    </div>
                    <div class="mt-6">
                        <span class="text-xs opacity-80 block">Saldo Saat Ini</span>
                        <span class="text-2xl font-extrabold">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Total Pemasukan & Transfer Masuk -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-center">
                    <span class="text-xs font-medium text-gray-500 uppercase">Total Dana Masuk ({{ $wallet->name }})</span>
                    <p class="text-2xl font-bold text-emerald-600 mt-2">+Rp {{ number_format($totalIn, 0, ',', '.') }}</p>
                    <span class="text-xs text-gray-400 mt-1">Termasuk pemasukan & transfer masuk</span>
                </div>

                <!-- Total Pengeluaran & Transfer Keluar -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-center">
                    <span class="text-xs font-medium text-gray-500 uppercase">Total Dana Keluar ({{ $wallet->name }})</span>
                    <p class="text-2xl font-bold text-red-600 mt-2">-Rp {{ number_format($totalOut, 0, ',', '.') }}</p>
                    <span class="text-xs text-gray-400 mt-1">Termasuk pengeluaran & transfer keluar</span>
                </div>
            </div>

            <!-- Daftar Riwayat Transaksi Khusus Dompet Ini -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800">Mutasi & Riwayat Transaksi {{ $wallet->name }}</h3>
                    <span class="text-xs text-gray-400">Total {{ $transactions->total() }} transaksi</span>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($transactions as $trx)
                        @php
                            $isIncomingTransfer = ($trx->type === 'transfer' && $trx->to_wallet_id === $wallet->id);
                            $isPositive = ($trx->type === 'income' || $isIncomingTransfer);
                        @endphp
                        <div class="p-4 sm:px-6 flex items-center justify-between hover:bg-gray-50">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-xs"
                                     style="background-color: {{ $trx->category->color ?? ($isIncomingTransfer ? '#10B981' : '#3B82F6') }}">
                                    {{ $trx->type === 'transfer' ? 'TR' : ($trx->type === 'income' ? 'IN' : 'OUT') }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">
                                        @if ($trx->type === 'transfer')
                                            @if ($isIncomingTransfer)
                                                Transfer Masuk dari {{ $trx->wallet->name }}
                                            @else
                                                Transfer Keluar ke {{ $trx->toWallet->name ?? 'Dompet Dihapus' }}
                                            @endif
                                        @else
                                            {{ $trx->category->name ?? 'Tanpa Kategori' }}
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $trx->transaction_date->format('d M Y') }}
                                        @if($trx->note) • "{{ $trx->note }}" @endif
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4">
                                <span class="text-sm font-bold {{ $isPositive ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $isPositive ? '+' : '-' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                </span>

                                <form action="{{ route('transactions.destroy', $trx) }}" method="POST"
                                      onsubmit="return confirm('Hapus transaksi ini? Saldo dompet akan otomatis dikembalikan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 text-xs">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-10 text-center text-gray-500 text-sm">
                            Belum ada riwayat pemasukan maupun pengeluaran pada dompet <strong>{{ $wallet->name }}</strong>.
                        </div>
                    @endforelse
                </div>

                @if ($transactions->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>