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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete(); // Nullable agar bisa dipakai sebagai kategori default sistem
            $table->string('name'); // Contoh: Makan & Minum, Gaji, Transportasi
            $table->enum('type', ['income', 'expense']);
            $table->string('icon')->default('tag'); // Nama ikon
            $table->string('color')->default('#10B981'); // Warna badge kategori
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
