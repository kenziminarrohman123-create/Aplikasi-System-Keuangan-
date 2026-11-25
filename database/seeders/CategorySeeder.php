<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Pengeluaran (Expense)
            ['name' => 'Makan & Minum', 'type' => 'expense', 'icon' => 'utensils', 'color' => '#EF4444'],
            ['name' => 'Transportasi', 'type' => 'expense', 'icon' => 'car', 'color' => '#F59E0B'],
            ['name' => 'Tagihan & Utilitas', 'type' => 'expense', 'icon' => 'zap', 'color' => '#8B5CF6'],
            ['name' => 'Belanja', 'type' => 'expense', 'icon' => 'shopping-bag', 'color' => '#EC4899'],
            ['name' => 'Hiburan', 'type' => 'expense', 'icon' => 'film', 'color' => '#6366F1'],
            ['name' => 'Kesehatan', 'type' => 'expense', 'icon' => 'heart', 'color' => '#14B8A6'],
            ['name' => 'Lainnya (Keluar)', 'type' => 'expense', 'icon' => 'more-horizontal', 'color' => '#64748B'],

            // Pemasukan (Income)
            ['name' => 'Gaji / Uang Saku', 'type' => 'income', 'icon' => 'briefcase', 'color' => '#10B981'],
            ['name' => 'Freelance / Proyek', 'type' => 'income', 'icon' => 'laptop', 'color' => '#059669'],
            ['name' => 'Bonus / Hadiah', 'type' => 'income', 'icon' => 'gift', 'color' => '#3B82F6'],
            ['name' => 'Investasi', 'type' => 'income', 'icon' => 'trending-up', 'color' => '#0D9488'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'user_id' => null, // Berlaku global untuk semua user
                'name' => $category['name'],
                'type' => $category['type'],
                'icon' => $category['icon'],
                'color' => $category['color'],
            ]);
        }
    }
}
