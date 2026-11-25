<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Contoh: BCA, GoPay, Dompet Tunai
            $table->enum('type', ['cash', 'bank', 'ewallet', 'investment'])->default('cash');
            $table->decimal('balance', 15, 2)->default(0); // Presisi 15 digit, 2 angka di belakang koma
            $table->string('color')->default('#3B82F6'); // Warna kartu dompet di UI
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
