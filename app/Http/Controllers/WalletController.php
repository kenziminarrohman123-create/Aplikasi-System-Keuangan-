<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Wallet;
use App\Models\Transaction;

/** @var \App\Models\User $user */
$user = Auth::user();

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
}
