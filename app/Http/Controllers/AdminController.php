<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Asset;
use App\Models\Exchange;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Constructor to ensure admin access
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }
    
    /**
     * Display admin dashboard.
     */
    public function dashboard()
    {
        $stats = [
            'users' => User::count(),
            'assets' => Asset::count(),
            'exchanges' => Exchange::count(),
            'transactions' => Transaction::count(),
            'pending_kyc' => User::where('kyc_status', 'pending')->count(),
            'pending_assets' => Asset::where('approval_status', 'pending')->count(),
            'pending_transactions' => Transaction::where('status', 'pending')->count(),
        ];
        
        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();
        $recentExchanges = Exchange::with(['initiator', 'responder'])->orderBy('created_at', 'desc')->take(5)->get();
        $recentTransactions = Transaction::with('user')->orderBy('created_at', 'desc')->take(5)->get();
        
        return view('admin.dashboard', [
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'recentExchanges' => $recentExchanges,
            'recentTransactions' => $recentTransactions,
        ]);
    }
    
    /**
     * Display list of users.
     */
    public function users()
    {
        $users = User::paginate(15);
        
        return view('admin.users.index', [
            'users' => $users,
        ]);
    }
    
    /**
     * Display user details.
     */
    public function showUser(User $user)
    {
        $user->load(['assets', 'transactions']);
        
        return view('admin.users.show', [
            'user' => $user,
        ]);
    }
    
    /**
     * Update user status.
     */
    public function updateUserStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,banned',
        ]);
        
        $user->status = $request->status;
        $user->save();
        
        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User status updated successfully.');
    }
    
    /**
     * Show user KYC details.
     */
    public function showUserKyc(User $user)
    {
        return view('admin.users.kyc', [
            'user' => $user,
        ]);
    }
    
    /**
     * Approve user KYC.
     */
    public function approveKyc(User $user)
    {
        $user->kyc_status = 'verified';
        $user->save();
        
        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User KYC approved successfully.');
    }
    
    /**
     * Reject user KYC.
     */
    public function rejectKyc(Request $request, User $user)
    {
        $request->validate([
            'notes' => 'required|string',
        ]);
        
        $user->kyc_status = 'rejected';
        $user->save();
        
        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User KYC rejected successfully.');
    }
    
    /**
     * Display list of assets.
     */
    public function assets()
    {
        $assets = Asset::with(['user', 'category'])->paginate(15);
        
        return view('admin.assets.index', [
            'assets' => $assets,
        ]);
    }
    
    /**
     * Display list of pending assets.
     */
    public function pendingAssets()
    {
        $assets = Asset::where('approval_status', 'pending')
            ->with(['user', 'category'])
            ->paginate(15);
        
        return view('admin.assets.pending', [
            'assets' => $assets,
        ]);
    }
    
    /**
     * Display list of exchanges.
     */
    public function exchanges()
    {
        $exchanges = Exchange::with(['initiator', 'responder', 'offeredAsset', 'requestedAsset'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.exchanges.index', [
            'exchanges' => $exchanges,
        ]);
    }
    
    /**
     * Display list of transactions.
     */
    public function transactions()
    {
        $transactions = Transaction::with(['user', 'exchange'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.transactions.index', [
            'transactions' => $transactions,
        ]);
    }
    
    /**
     * Display admin settings.
     */
    public function settings()
    {
        return view('admin.settings', [
            'fee_monetary' => config('escrow.fee_monetary', 10),
            'fee_default' => config('escrow.fee_default', 5),
        ]);
    }
    
    /**
     * Update admin settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'fee_monetary' => 'required|numeric|min:0|max:100',
            'fee_default' => 'required|numeric|min:0|max:100',
        ]);
        
        // In a real application, this would update the configuration
        // For now, we'll just redirect with a success message
        
        return redirect()->route('admin.settings')
            ->with('success', 'Settings updated successfully.');
    }
}