<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Perencanaan Anggaran (Budgeting)
            </h2>
            <form method="GET" action="{{ route('budgets.index') }}" class="flex items-center space-x-2">
                <select name="month" class="rounded-lg border-gray-300 text-sm">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
                <input type="number" name="year" value="{{ $year }}" class="w-24 rounded-lg border-gray-300 text-sm">
                <button type="submit" class="bg-gray-800 text-white px-3 py-2 rounded-lg text-sm">Filter</button>
            </form>
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
                <!-- Form Atur Budget -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-fit">
                    <h3 class="font-semibold text-gray-800 mb-4">Atur Batas Pengeluaran</h3>
                    <form action="{{ route('budgets.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kategori Pengeluaran</label>
                            <select name="category_id" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm">
                                @foreach ($expenseCategories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Batas Maksimal</label>
                            <x-currency-input name="amount_limit" placeholder="500,000" :required="true" />
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition">
                            Simpan Anggaran
                        </button>
                    </form>
                </div>

                <!-- Daftar Progress Budget -->
                <div class="md:col-span-2 space-y-4">
                    @forelse ($budgets as $budget)
                        @php
                            $barColor = $budget->percentage >= 100 ? 'bg-red-600' : ($budget->percentage >= 80 ? 'bg-amber-500' : 'bg-emerald-500');
                            $barWidth = min(100, $budget->percentage);
                        @endphp
                        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                            <div class="flex justify-between items-center mb-2">
                                <div>
                                    <span class="font-semibold text-gray-800">{{ $budget->category->name }}</span>
                                    <span class="text-xs ml-2 px-2 py-0.5 rounded-full {{ $budget->percentage >= 100 ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $budget->percentage }}% terpakai
                                    </span>
                                </div>
                                <form action="{{ route('budgets.destroy', $budget) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-gray-400 hover:text-red-600">Hapus</button>
                                </form>
                            </div>

                            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden mb-2">
                                <div class="{{ $barColor }} h-3 rounded-full transition-all duration-500" style="width: {{ $barWidth }}%"></div>
                            </div>

                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Terpakai: <strong class="text-gray-800">Rp {{ number_format($budget->spent, 0, ',', '.') }}</strong></span>
                                <span>Batas: <strong class="text-gray-800">Rp {{ number_format($budget->amount_limit, 0, ',', '.') }}</strong></span>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white p-8 rounded-xl border border-dashed border-gray-300 text-center text-gray-500 text-sm">
                            Belum ada target anggaran untuk bulan ini. Tambahkan di form sebelah kiri!
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>