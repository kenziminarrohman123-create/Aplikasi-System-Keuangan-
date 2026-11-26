<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $wallets = $user->wallets()->latest()->get();
        $totalBalance = $wallets->sum('balance');

        return view('wallets.index', compact('wallets', 'totalBalance'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:cash,bank,ewallet,investment',
            'balance' => 'required|numeric|min:0',
            'color' => 'nullable|string|max:20',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->wallets()->create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'balance' => $validated['balance'],
            'color' => $validated['color'] ?? '#3B82F6',
        ]);

        return redirect()->route('wallets.index')->with('success', 'Dompet berhasil ditambahkan!');
    }

    public function show(Wallet $wallet)
    {
        if ($wallet->user_id !== Auth::id()) {
            abort(403);
        }

        // Ambil seluruh transaksi yang melibatkan dompet ini (keluar, masuk, maupun transfer masuk)
        $transactions = Transaction::with(['wallet', 'toWallet', 'category'])
            ->where('user_id', Auth::id())
            ->where(function ($query) use ($wallet) {
                $query->where('wallet_id', $wallet->id)
                    ->orWhere('to_wallet_id', $wallet->id);
            })
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(15);

        // Hitung total uang masuk ke dompet ini (income + transfer masuk)
        $totalIn = Transaction::where('user_id', Auth::id())
            ->where(function ($query) use ($wallet) {
                $query->where(function ($q) use ($wallet) {
                    $q->where('wallet_id', $wallet->id)->where('type', 'income');
                })->orWhere(function ($q) use ($wallet) {
                    $q->where('to_wallet_id', $wallet->id)->where('type', 'transfer');
                });
            })
            ->sum('amount');

        // Hitung total uang keluar dari dompet ini (expense + transfer keluar)
        $totalOut = Transaction::where('user_id', Auth::id())
            ->where('wallet_id', $wallet->id)
            ->whereIn('type', ['expense', 'transfer'])
            ->sum('amount');

        return view('wallets.show', compact('wallet', 'transactions', 'totalIn', 'totalOut'));
    }

    public function destroy(Wallet $wallet)
    {
        if ($wallet->user_id !== Auth::id()) {
            abort(403);
        }

        $wallet->delete();

        return redirect()->route('wallets.index')->with('success', 'Dompet berhasil dihapus.');
    }
}
