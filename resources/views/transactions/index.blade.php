<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Catatan Transaksi Keuangan
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Form Input Transaksi (Dinamis dengan Alpine.js) -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-fit" x-data="{ type: '{{ old('type', 'expense') }}' }">
                    <h3 class="font-semibold text-gray-800 mb-4">Catat Transaksi Baru</h3>

                    @if ($wallets->isEmpty())
                        <div class="p-4 bg-amber-50 text-amber-800 rounded-lg text-sm">
                            Kamu belum punya dompet. <a href="{{ route('wallets.index') }}" class="underline font-semibold">Buat dompet dulu di sini</a> sebelum mencatat transaksi.
                        </div>
                    @else
                        <form action="{{ route('transactions.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <!-- Pilihan Tipe Transaksi -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Transaksi</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button type="button" @click="type = 'expense'"
                                        :class="type === 'expense' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700'"
                                        class="py-2 rounded-lg text-xs font-semibold transition">
                                        Pengeluaran
                                    </button>
                                    <button type="button" @click="type = 'income'"
                                        :class="type === 'income' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700'"
                                        class="py-2 rounded-lg text-xs font-semibold transition">
                                        Pemasukan
                                    </button>
                                    <button type="button" @click="type = 'transfer'"
                                        :class="type === 'transfer' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                                        class="py-2 rounded-lg text-xs font-semibold transition">
                                        Transfer
                                    </button>
                                </div>
                                <input type="hidden" name="type" x-model="type">
                            </div>

                            <!-- Dompet Asal -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700" x-text="type === 'transfer' ? 'Dari Dompet' : 'Pilih Dompet'"></label>
                                <select name="wallet_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                                    @foreach ($wallets as $wallet)
                                        <option value="{{ $wallet->id }}">{{ $wallet->name }} (Rp {{ number_format($wallet->balance, 0, ',', '.') }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Dompet Tujuan (Hanya muncul jika tipe = transfer) -->
                            <div x-show="type === 'transfer'" x-transition>
                                <label class="block text-sm font-medium text-gray-700">Ke Dompet Tujuan</label>
                                <select name="to_wallet_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                                    <option value="">-- Pilih Dompet Tujuan --</option>
                                    @foreach ($wallets as $wallet)
                                        <option value="{{ $wallet->id }}">{{ $wallet->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Kategori (Sembunyikan jika tipe = transfer) -->
                            <div x-show="type !== 'transfer'" x-transition>
                                <label class="block text-sm font-medium text-gray-700">Kategori</label>
                                <select name="category_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">
                                            [{{ $category->type === 'income' ? 'Masuk' : 'Keluar' }}] {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Nominal -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nominal (Rp)</label>
                                <input type="number" name="amount" min="1" placeholder="50000" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                            </div>

                            <!-- Tanggal -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                                <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                                <input type="text" name="note" placeholder="Beli makan siang, bayar kos, dll."
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                            </div>

                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition">
                                Simpan Transaksi
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Riwayat Transaksi -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden h-fit">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800">Riwayat Transaksi Terbaru</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse ($transactions as $trx)
                            <div class="p-4 sm:px-6 flex items-center justify-between hover:bg-gray-50">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-xs"
                                         style="background-color: {{ $trx->category->color ?? '#3B82F6' }}">
                                        {{ strtoupper(substr($trx->type, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">
                                            @if ($trx->type === 'transfer')
                                                Transfer: {{ $trx->wallet->name }} → {{ $trx->toWallet->name ?? 'Dompet Dihapus' }}
                                            @else
                                                {{ $trx->category->name ?? 'Tanpa Kategori' }}
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $trx->transaction_date->format('d M Y') }} • {{ $trx->wallet->name }}
                                            @if($trx->note) • "{{ $trx->note }}" @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4">
                                    <span class="text-sm font-bold
                                        {{ $trx->type === 'income' ? 'text-emerald-600' : ($trx->type === 'expense' ? 'text-red-600' : 'text-blue-600') }}">
                                        {{ $trx->type === 'income' ? '+' : ($trx->type === 'expense' ? '-' : '') }}
                                        Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                    </span>

                                    <form action="{{ route('transactions.destroy', $trx) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini? Saldo dompet akan otomatis dikembalikan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 text-xs">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500 text-sm">
                                Belum ada transaksi yang dicatat.
                            </div>
                        @endforelse
                    </div>
                    <div class="p-4 border-t border-gray-100">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>