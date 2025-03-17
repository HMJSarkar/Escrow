<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    /**
     * Display a listing of the assets.
     */
    public function index(Request $request)
    {
        $query = Asset::query();
        
        // Filter by category if provided
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Filter by type if provided
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Only show approved assets to regular users
        if (!Auth::user()->hasRole('admin')) {
            $query->where('approval_status', 'approved');
        }
        
        $assets = $query->with(['user', 'category'])->paginate(15);
        
        return view('assets.index', [
            'assets' => $assets,
            'categories' => Category::getActive(),
        ]);
    }

    /**
     * Show the form for creating a new asset.
     */
    public function create()
    {
        return view('assets.create', [
            'categories' => Category::getActive(),
        ]);
    }

    /**
     * Store a newly created asset in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:money,crypto,physical,digital,service,other',
            'value' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'category_id' => 'required|exists:categories,id',
            'condition' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'images.*' => 'nullable|image|max:2048', // Max 2MB per image
        ]);
        
        $asset = new Asset($request->except('images'));
        $asset->user_id = Auth::id();
        $asset->status = 'pending';
        $asset->approval_status = 'pending';
        
        // Handle image uploads
        if ($request->hasFile('images')) {
            $imagesPaths = [];
            
            // Check if total number of images exceeds the limit of 5
            if (count($request->file('images')) > 5) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['images' => 'You can upload a maximum of 5 images per asset.']);
            }
            
            foreach ($request->file('images') as $image) {
                $path = $image->store('assets', 'public');
                $imagesPaths[] = $path;
            }
            $asset->images = $imagesPaths;
        }
        
        $asset->save();
        
        return redirect()->route('assets.show', $asset)
            ->with('success', 'Asset created successfully and pending approval.');
    }

    /**
     * Display the specified asset.
     */
    public function show(Asset $asset)
    {
        // Check if user can view this asset
        if (!Auth::user()->hasRole('admin') && 
            $asset->user_id !== Auth::id() && 
            $asset->approval_status !== 'approved') {
            abort(403, 'Unauthorized action.');
        }
        
        return view('assets.show', [
            'asset' => $asset->load(['user', 'category']),
        ]);
    }

    /**
     * Show the form for editing the specified asset.
     */
    public function edit(Asset $asset)
    {
        // Check if user can edit this asset
        if (!Auth::user()->hasRole('admin') && $asset->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Don't allow editing if asset is already in an exchange
        if ($asset->status === 'exchanged') {
            return redirect()->route('assets.show', $asset)
                ->with('error', 'Cannot edit an asset that is already in an exchange.');
        }
        
        return view('assets.edit', [
            'asset' => $asset,
            'categories' => Category::getActive(),
        ]);
    }

    /**
     * Update the specified asset in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        // Check if user can update this asset
        if (!Auth::user()->hasRole('admin') && $asset->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Don't allow updating if asset is already in an exchange
        if ($asset->status === 'exchanged') {
            return redirect()->route('assets.show', $asset)
                ->with('error', 'Cannot update an asset that is already in an exchange.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:money,crypto,physical,digital,service,other',
            'value' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'category_id' => 'required|exists:categories,id',
            'condition' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'images.*' => 'nullable|image|max:2048', // Max 2MB per image
        ]);
        
        $asset->fill($request->except('images'));
        
        // If not admin, set approval status back to pending after update
        if (!Auth::user()->hasRole('admin')) {
            $asset->approval_status = 'pending';
        }
        
        // Handle image removals
        if ($request->has('remove_images') && $asset->images) {
            // Get the raw image paths without the storage URL prefix
            $rawImages = json_decode($asset->getRawOriginal('images'), true) ?: [];
            $currentImages = $asset->images;
            $toRemove = $request->input('remove_images');
            
            foreach ($toRemove as $index) {
                if (isset($rawImages[$index])) {
                    // Delete using the raw path
                    Storage::disk('public')->delete($rawImages[$index]);
                    unset($rawImages[$index]);
                    unset($currentImages[$index]);
                }
            }
            
            // Update the raw image paths in the database
            $asset->images = array_values($rawImages);
        }
        
        // Handle image uploads
        if ($request->hasFile('images')) {
            $imagesPaths = $asset->images ?? [];
            
            // Check if total images would exceed the limit of 5
            $currentCount = count($imagesPaths);
            $newCount = count($request->file('images'));
            
            if ($currentCount + $newCount > 5) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['images' => 'You can upload a maximum of 5 images per asset.']);
            }
            
            foreach ($request->file('images') as $image) {
                $path = $image->store('assets', 'public');
                $imagesPaths[] = $path;
            }
            
            $asset->images = $imagesPaths;
        }
        
        $asset->save();
        
        return redirect()->route('assets.show', $asset)
            ->with('success', 'Asset updated successfully.');
    }

    /**
     * Remove the specified asset from storage.
     */
    public function destroy(Asset $asset)
    {
        // Check if user can delete this asset
        if (!Auth::user()->hasRole('admin') && $asset->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Don't allow deleting if asset is already in an exchange
        if ($asset->status === 'exchanged') {
            return redirect()->route('assets.show', $asset)
                ->with('error', 'Cannot delete an asset that is already in an exchange.');
        }
        
        // Delete associated images
        if ($asset->images) {
            // Get the raw image paths without the storage URL prefix
            $rawImages = json_decode($asset->getRawOriginal('images'), true) ?: [];
            
            foreach ($rawImages as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        
        $asset->delete();
        
        return redirect()->route('assets.index')
            ->with('success', 'Asset deleted successfully.');
    }

    /**
     * Admin approval for assets.
     */
    public function approve(Asset $asset)
    {
        // Check if user is admin
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        
        $asset->approval_status = 'approved';
        $asset->status = 'approved';
        $asset->save();
        
        return redirect()->route('admin.assets.pending')
            ->with('success', 'Asset approved successfully.');
    }

    /**
     * Admin rejection for assets.
     */
    public function reject(Request $request, Asset $asset)
    {
        // Check if user is admin
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'approval_notes' => 'required|string',
        ]);
        
        $asset->approval_status = 'rejected';
        $asset->status = 'rejected';
        $asset->approval_notes = $request->approval_notes;
        $asset->save();
        
        return redirect()->route('admin.assets.pending')
            ->with('success', 'Asset rejected successfully.');
    }

    /**
     * Display user's assets.
     */
    public function myAssets()
    {
        $assets = Asset::where('user_id', Auth::id())
            ->with('category')
            ->paginate(15);
        
        return view('assets.my-assets', [
            'assets' => $assets,
        ]);
    }
}