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
                <!-- Form Tambah Dompet dengan Auto-Color Brand -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-fit"
                     x-data="{
                        type: 'cash',
                        preset: '',
                        name: '',
                        color: '#10B981',
                        brandColors: {
                            'BRI': '#00529C',
                            'BCA': '#0066AE',
                            'BSA': '#006838',
                            'Mandiri': '#003D79',
                            'BSI': '#00A39D',
                            'BNI': '#F15A23',
                            'Jago': '#D97706',
                            'ShopeePay': '#EE4D2D',
                            'DANA': '#118EEA',
                            'GoPay': '#00AA13',
                            'OVO': '#4C3494',
                            'LinkAja': '#E82529'
                        },
                        onTypeChange() {
                            this.preset = '';
                            if (this.type === 'cash') {
                                this.name = 'Dompet Tunai';
                                this.color = '#10B981';
                            } else if (this.type === 'investment') {
                                this.name = '';
                                this.color = '#6366F1';
                            } else {
                                this.name = '';
                                this.color = '#3B82F6';
                            }
                        },
                        onPresetChange() {
                            if (this.preset && this.preset !== 'custom') {
                                this.name = this.preset;
                                this.color = this.brandColors[this.preset] || '#3B82F6';
                            } else if (this.preset === 'custom') {
                                this.name = '';
                            }
                        },
                        detectBrandFromText() {
                            const query = this.name.trim().toLowerCase();
                            for (const [brand, hex] of Object.entries(this.brandColors)) {
                                if (query === brand.toLowerCase()) {
                                    this.color = hex;
                                    break;
                                }
                            }
                        }
                     }">
                    <h3 class="font-semibold text-gray-800 mb-4">Tambah Dompet Baru</h3>
                    <form action="{{ route('wallets.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- 1. Pilih Tipe Akun -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipe Akun</label>
                            <select name="type" x-model="type" @change="onTypeChange()"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="cash">Uang Tunai (Cash)</option>
                                <option value="bank">Rekening Bank</option>
                                <option value="ewallet">E-Wallet</option>
                                <option value="investment">Tabungan / Investasi</option>
                            </select>
                        </div>

                        <!-- 2A. Pilihan Cepat Rekening Bank -->
                        <div x-show="type === 'bank'" x-transition>
                            <label class="block text-sm font-medium text-gray-700">Pilih Bank</label>
                            <select x-model="preset" @change="onPresetChange()"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">-- Pilih Rekening Bank --</option>
                                <option value="BRI">BRI</option>
                                <option value="BCA">BCA</option>
                                <option value="BSA">BSA</option>
                                <option value="Mandiri">Mandiri</option>
                                <option value="BSI">BSI</option>
                                <option value="BNI">BNI</option>
                                <option value="Jago">Jago</option>
                                <option value="custom">Lainnya (Ketik Manual)</option>
                            </select>
                        </div>

                        <!-- 2B. Pilihan Cepat E-Wallet -->
                        <div x-show="type === 'ewallet'" x-transition>
                            <label class="block text-sm font-medium text-gray-700">Pilih E-Wallet</label>
                            <select x-model="preset" @change="onPresetChange()"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">-- Pilih E-Wallet --</option>
                                <option value="ShopeePay">ShopeePay</option>
                                <option value="DANA">DANA</option>
                                <option value="GoPay">GoPay</option>
                                <option value="OVO">OVO</option>
                                <option value="LinkAja">LinkAja</option>
                                <option value="custom">Lainnya (Ketik Manual)</option>
                            </select>
                        </div>

                        <!-- 3. Nama Dompet (Terisi otomatis dari pilihan di atas, tapi tetap bisa diedit misal: "BCA Utama") -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Dompet / Rekening</label>
                            <input type="text" name="name" x-model="name" @input="detectBrandFromText()"
                                placeholder="Contoh: BCA Utama, GoPay, Tunai" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>

                        <!-- 4. Saldo Awal (Dengan format koma otomatis) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Saldo Awal</label>
                            <x-currency-input name="balance" :allow-zero="true" placeholder="0" :required="true" />
                        </div>

                        <!-- 5. Warna Kartu (Otomatis berubah sesuai brand, tetap bisa di-custom bebas) -->
                        <div>
                            <div class="flex justify-between items-center">
                                <label class="block text-sm font-medium text-gray-700">Warna Kartu</label>
                                <span class="text-xs font-mono text-gray-400 uppercase" x-text="color"></span>
                            </div>
                            <div class="mt-1 flex items-center space-x-3">
                                <input type="color" name="color" x-model="color"
                                    class="h-10 w-16 rounded-lg border-gray-300 cursor-pointer p-0.5 bg-white">
                                <div class="flex-1 h-10 rounded-lg flex items-center px-3 text-white text-xs font-semibold shadow-inner transition-colors duration-300"
                                     :style="'background-color: ' + color">
                                    <span x-text="name || 'Pratinjau Warna Kartu'"></span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition">
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