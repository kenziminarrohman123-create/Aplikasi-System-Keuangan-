<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Filter bulan & tahun (default bulan & tahun saat ini)
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        // 1. Ringkasan Saldo & Arus Kas Bulan Ini
        $totalBalance = $user->wallets()->sum('balance');

        $monthlyIncome = $user->transactions()
            ->where('type', 'income')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('amount');

        $monthlyExpense = $user->transactions()
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('amount');

        $netCashFlow = $monthlyIncome - $monthlyExpense;

        // 2. Indikator Analitik: Savings Rate (%) & Rata-rata Pengeluaran Harian (Burn Rate)
        $savingsRate = $monthlyIncome > 0
            ? round((($monthlyIncome - $monthlyExpense) / $monthlyIncome) * 100, 1)
            : 0;

        $daysPassed = ($month === now()->month && $year === now()->year)
            ? max(1, now()->day)
            : Carbon::create($year, $month)->daysInMonth;

        $dailyBurnRate = $monthlyExpense / $daysPassed;

        // 3. Data Donut Chart: Pengeluaran per Kategori Bulan Ini
        $expenseByCategory = $user->transactions()
            ->where('transactions.type', 'expense')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select(
                'categories.name',
                'categories.color',
                DB::raw('SUM(transactions.amount) as total')
            )
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderByDesc('total')
            ->get();

        // 4. Data Bar Chart: Tren Pemasukan vs Pengeluaran 6 Bulan Terakhir
        $trendLabels = [];
        $trendIncome = [];
        $trendExpense = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::create($year, $month, 1)->subMonths($i);
            $trendLabels[] = $date->translatedFormat('M Y');

            $trendIncome[] = (float) $user->transactions()
                ->where('type', 'income')
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('amount');

            $trendExpense[] = (float) $user->transactions()
                ->where('type', 'expense')
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('amount');
        }

        // 5. Status Budgeting Bulan Ini
        $budgets = $user->budgets()
            ->with('category')
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->map(function ($budget) use ($user, $month, $year) {
                $spent = $user->transactions()
                    ->where('type', 'expense')
                    ->where('category_id', $budget->category_id)
                    ->whereMonth('transaction_date', $month)
                    ->whereYear('transaction_date', $year)
                    ->sum('amount');

                $percentage = $budget->amount_limit > 0
                    ? round(($spent / $budget->amount_limit) * 100, 1)
                    : 0;

                $budget->spent = $spent;
                $budget->percentage = $percentage;
                return $budget;
            });

        // 6. 5 Transaksi Terakhir
        $recentTransactions = $user->transactions()
            ->with(['wallet', 'toWallet', 'category'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'month',
            'year',
            'totalBalance',
            'monthlyIncome',
            'monthlyExpense',
            'netCashFlow',
            'savingsRate',
            'dailyBurnRate',
            'expenseByCategory',
            'trendLabels',
            'trendIncome',
            'trendExpense',
            'budgets',
            'recentTransactions'
        ));
    }
}
