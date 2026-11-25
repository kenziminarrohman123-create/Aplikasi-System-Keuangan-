<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ambil transaksi milik user beserta relasi dompet & kategorinya
        $transactions = $user->transactions()
            ->with(['wallet', 'toWallet', 'category'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(15);

        $wallets = $user->wallets;

        // Ambil kategori bawaan sistem (user_id null) + kategori buatan user sendiri
        $categories = Category::whereNull('user_id')
            ->orWhere('user_id', $user->id)
            ->get();

        return view('transactions.index', compact('transactions', 'wallets', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense,transfer',
            'wallet_id' => 'required|exists:wallets,id',
            'to_wallet_id' => 'required_if:type,transfer|nullable|exists:wallets,id|different:wallet_id',
            'category_id' => 'required_unless:type,transfer|nullable|exists:categories,id',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
            'note' => 'nullable|string|max:255',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $wallet = Wallet::where('id', $validated['wallet_id'])->where('user_id', $user->id)->firstOrFail();

        // Validasi saldo cukup untuk pengeluaran atau transfer
        if (in_array($validated['type'], ['expense', 'transfer']) && $wallet->balance < $validated['amount']) {
            return back()->withErrors(['amount' => 'Saldo dompet tidak mencukupi untuk transaksi ini.'])->withInput();
        }

        DB::transaction(function () use ($validated, $user, $wallet) {
            // 1. Simpan catatan transaksi
            $user->transactions()->create([
                'wallet_id' => $wallet->id,
                'to_wallet_id' => $validated['type'] === 'transfer' ? $validated['to_wallet_id'] : null,
                'category_id' => $validated['type'] !== 'transfer' ? $validated['category_id'] : null,
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'transaction_date' => $validated['transaction_date'],
                'note' => $validated['note'],
            ]);

            // 2. Perbarui saldo dompet otomatis
            if ($validated['type'] === 'income') {
                $wallet->increment('balance', $validated['amount']);
            } elseif ($validated['type'] === 'expense') {
                $wallet->decrement('balance', $validated['amount']);
            } elseif ($validated['type'] === 'transfer') {
                $toWallet = Wallet::where('id', $validated['to_wallet_id'])->where('user_id', $user->id)->firstOrFail();
                $wallet->decrement('balance', $validated['amount']);
                $toWallet->increment('balance', $validated['amount']);
            }
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dicatat!');
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        DB::transaction(function () use ($transaction) {
            $wallet = $transaction->wallet;

            // Kembalikan (rollback) saldo dompet saat transaksi dihapus
            if ($transaction->type === 'income') {
                $wallet->decrement('balance', $transaction->amount);
            } elseif ($transaction->type === 'expense') {
                $wallet->increment('balance', $transaction->amount);
            } elseif ($transaction->type === 'transfer' && $transaction->toWallet) {
                $wallet->increment('balance', $transaction->amount);
                $transaction->toWallet->decrement('balance', $transaction->amount);
            }

            $transaction->delete();
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi dihapus dan saldo telah disesuaikan.');
    }
}
