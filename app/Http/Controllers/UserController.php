<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Constructor to ensure authentication
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }
    
    /**
     * Display the user's profile.
     */
    public function profile()
    {
        $user = Auth::user();
        
        return view('profile.index', [
            'user' => $user,
        ]);
    }
    
    /**
     * Update the user's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
        
        return redirect()->route('profile')
            ->with('success', 'Profile updated successfully.');
    }
    
    /**
     * Show the KYC form.
     */
    public function showKycForm()
    {
        $user = Auth::user();
        
        // If KYC is already verified, redirect to profile
        if ($user->kyc_status === 'verified') {
            return redirect()->route('profile')
                ->with('info', 'Your KYC is already verified.');
        }
        
        return view('profile.kyc', [
            'user' => $user,
        ]);
    }
    
    /**
     * Submit KYC documents.
     */
    public function submitKyc(Request $request)
    {
        $user = Auth::user();
        
        // If KYC is already verified, redirect to profile
        if ($user->kyc_status === 'verified') {
            return redirect()->route('profile')
                ->with('info', 'Your KYC is already verified.');
        }
        
        $request->validate([
            'id_type' => 'required|in:passport,national_id,drivers_license',
            'id_number' => 'required|string|max:50',
            'id_front' => 'required|image|max:2048',
            'id_back' => 'required|image|max:2048',
            'selfie' => 'required|image|max:2048',
            'address_proof' => 'required|image|max:2048',
        ]);
        
        // Store the documents
        $idFrontPath = $request->file('id_front')->store('kyc/' . $user->id, 'public');
        $idBackPath = $request->file('id_back')->store('kyc/' . $user->id, 'public');
        $selfiePath = $request->file('selfie')->store('kyc/' . $user->id, 'public');
        $addressProofPath = $request->file('address_proof')->store('kyc/' . $user->id, 'public');
        
        // Update user KYC information
        $user->kyc_documents = [
            'id_type' => $request->id_type,
            'id_number' => $request->id_number,
            'id_front' => $idFrontPath,
            'id_back' => $idBackPath,
            'selfie' => $selfiePath,
            'address_proof' => $addressProofPath,
            'submitted_at' => now(),
        ];
        
        $user->kyc_status = 'pending';
        $user->save();
        
        return redirect()->route('profile')
            ->with('success', 'KYC documents submitted successfully. They will be reviewed shortly.');
    }
}