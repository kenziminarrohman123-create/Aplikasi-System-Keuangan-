<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manajemen Dompet & Rekening
            </h2>
            <div class="text-right">
                <span class="text-xs text-gray-500 block">Total Seluruh Saldo</span>
                <span class="text-lg font-bold text-emerald-600">Rp {{ number_format($totalBalance, 0, ',', '.') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Form Tambah Dompet -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-fit">
                    <h3 class="font-semibold text-gray-800 mb-4">Tambah Dompet Baru</h3>
                    <form action="{{ route('wallets.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Dompet / Bank</label>
                            <input type="text" name="name" placeholder="Contoh: BCA, GoPay, Tunai" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipe Akun</label>
                            <select name="type" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="cash">Uang Tunai (Cash)</option>
                                <option value="bank">Rekening Bank</option>
                                <option value="ewallet">E-Wallet (GoPay, OVO, DANA)</option>
                                <option value="investment">Tabungan / Investasi</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Saldo Awal (Rp)</label>
                            <input type="number" name="balance" value="0" min="0" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Warna Kartu</label>
                            <input type="color" name="color" value="#3B82F6" class="mt-1 h-10 w-full rounded-lg border-gray-300 cursor-pointer">
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition">
                            + Simpan Dompet
                        </button>
                    </form>
                </div>

                <!-- Daftar Kartu Dompet -->
                <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4 h-fit">
                    @forelse ($wallets as $wallet)
                        <div class="rounded-xl p-5 text-white shadow-md relative flex flex-col justify-between min-h-[140px]"
                             style="background-color: {{ $wallet->color }};">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-xs uppercase tracking-wider opacity-80">{{ $wallet->type }}</span>
                                    <h4 class="text-lg font-bold">{{ $wallet->name }}</h4>
                                </div>
                                <form action="{{ route('wallets.destroy', $wallet) }}" method="POST" onsubmit="return confirm('Hapus dompet ini? Semua transaksi di dalamnya juga akan terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-white/70 hover:text-white text-xs bg-black/20 px-2 py-1 rounded">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                            <div class="mt-6">
                                <span class="text-xs opacity-80 block">Saldo Saat Ini</span>
                                <span class="text-2xl font-bold">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 bg-white p-8 rounded-xl border border-dashed border-gray-300 text-center text-gray-500">
                            Belum ada dompet. Silakan buat dompet pertamamu di formulir sebelah kiri!
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>