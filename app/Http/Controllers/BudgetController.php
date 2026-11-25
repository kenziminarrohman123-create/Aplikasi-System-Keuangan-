<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

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

                $budget->spent = $spent;
                $budget->percentage = $budget->amount_limit > 0
                    ? round(($spent / $budget->amount_limit) * 100, 1)
                    : 0;
                return $budget;
            });

        $expenseCategories = Category::where('type', 'expense')
            ->where(function ($q) use ($user) {
                $q->whereNull('user_id')->orWhere('user_id', $user->id);
            })
            ->get();

        return view('budgets.index', compact('budgets', 'expenseCategories', 'month', 'year'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount_limit' => 'required|numeric|min:1000',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:2100',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->budgets()->updateOrCreate(
            [
                'category_id' => $validated['category_id'],
                'month' => $validated['month'],
                'year' => $validated['year'],
            ],
            [
                'amount_limit' => $validated['amount_limit'],
            ]
        );

        return redirect()->route('budgets.index', [
            'month' => $validated['month'],
            'year' => $validated['year'],
        ])->with('success', 'Target anggaran berhasil disimpan!');
    }

    public function destroy(Budget $budget)
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        $budget->delete();

        return back()->with('success', 'Anggaran berhasil dihapus.');
    }
}
