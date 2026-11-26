@props([
    'name',
    'value' => '',
    'placeholder' => '0',
    'required' => false,
    'allowZero' => false, // True khusus untuk Saldo Awal dompet yang boleh 0
])

<div x-data="{
        raw: '{{ preg_replace('/[^0-9]/', '', (string) old($name, $value)) }}',
        display: '',
        allowZero: {{ $allowZero ? 'true' : 'false' }},
        init() {
            this.formatValue(this.raw);
        },
        formatValue(val) {
            // 1. Hapus semua karakter selain angka
            let digits = String(val).replace(/\D/g, '');

            // 2. Cegah angka 0 di awal
            if (digits.length > 1) {
                digits = digits.replace(/^0+/, '');
            } else if (!this.allowZero && digits === '0') {
                digits = '';
            }

            this.raw = digits;
            // 3. Tambahkan koma pemisah ribuan otomatis (contoh: 1,500,000)
            this.display = digits ? Number(digits).toLocaleString('en-US') : '';
        }
     }"
     class="relative mt-1">

    <!-- Prefix Rp -->
    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
        <span class="text-gray-500 sm:text-sm font-medium">Rp</span>
    </div>

    <!-- Input yang dilihat user (dengan koma otomatis) -->
    <input type="text"
           inputmode="numeric"
           x-model="display"
           @input="formatValue($event.target.value)"
           placeholder="{{ $placeholder }}"
           {{ $required ? 'required' : '' }}
           class="block w-full rounded-lg border-gray-300 pl-9 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-semibold text-gray-800">

    <!-- Input tersembunyi yang dikirim ke Laravel (angka murni tanpa koma) -->
    <input type="hidden" name="{{ $name }}" :value="raw">
</div>