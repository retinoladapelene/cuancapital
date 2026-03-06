<?php

namespace App\Http\Controllers;

use App\Models\AdArsenal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdArsenalController extends Controller
{
    // Public API — returns active cards only with image_url
    public function index()
    {
        $ads = AdArsenal::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return $ads->map(fn($ad) => $this->appendImageUrl($ad));
    }

    // Admin API — returns ALL cards (active + inactive)
    public function adminIndex()
    {
        $ads = AdArsenal::orderBy('sort_order')->get();
        return $ads->map(fn($ad) => $this->appendImageUrl($ad));
    }

    // Admin API — Create
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'link' => 'required|url',
            'tag' => 'in:HOT,NEW,FOUNDATION,PREMIUM',
            'image' => 'nullable|image|max:200', // Max 200KB
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'link' => $request->link,
            'tag' => $request->tag ?? 'NEW',
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
            'created_by' => $request->user()->id,
        ];

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('arsenal', 'public');
        }

        $ad = AdArsenal::create($data);

        return response()->json($this->appendImageUrl($ad), 201);
    }

    // Admin API — Update
    public function update(Request $request, AdArsenal $adArsenal)
    {
        $request->validate([
            'image' => 'nullable|image|max:200', // Max 200KB
        ]);

        $data = $request->except(['image', '_method']);

        // Cast is_active from string to boolean (FormData sends strings)
        if (isset($data['is_active'])) {
            $data['is_active'] = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;
        }

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($adArsenal->image_path && Storage::disk('public')->exists($adArsenal->image_path)) {
                Storage::disk('public')->delete($adArsenal->image_path);
            }
            $data['image_path'] = $request->file('image')->store('arsenal', 'public');
        }

        $adArsenal->update($data);

        return response()->json($this->appendImageUrl($adArsenal->fresh()));
    }

    // Admin API — Delete
    public function destroy(AdArsenal $adArsenal)
    {
        // Delete image file
        if ($adArsenal->image_path && Storage::disk('public')->exists($adArsenal->image_path)) {
            Storage::disk('public')->delete($adArsenal->image_path);
        }

        $adArsenal->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    // Helper: Append full image URL to response
    private function appendImageUrl(AdArsenal $ad): array
    {
        $data = $ad->toArray();
        $data['image_url'] = $ad->image_path
            ? asset('storage/' . $ad->image_path)
            : null;
        return $data;
    }
}
