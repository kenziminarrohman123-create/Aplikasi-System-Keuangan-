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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount_limit', 15, 2);
            $table->unsignedTinyInteger('month'); // 1 sampai 12
            $table->unsignedSmallInteger('year'); // Contoh: 2026
            $table->timestamps();

            // Mencegah user membuat 2 budget untuk kategori yang sama di bulan & tahun yang sama
            $table->unique(['user_id', 'category_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
