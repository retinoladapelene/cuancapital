<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blueprint;
use Illuminate\Support\Facades\Auth;

class BlueprintStorageController extends Controller
{
    /**
     * Get all blueprints for the authenticated user, grouped by type.
     * Returns minimal data for sidebar storage menu.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user && Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();
        }

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Fetch minimal fields to avoid memory bloat
        $blueprints = Blueprint::where('user_id', $user->id)
            ->select('id', 'type', 'title', 'created_at', 'updated_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($blueprint) {
                // Map title to name for frontend compatibility
                $blueprint->name = $blueprint->title;
                return $blueprint;
            })
            ->groupBy('type');

        return response()->json([
            'success' => true,
            'data' => $blueprints
        ]);
    }

    /**
     * Get a specific blueprint by ID
     */
    public function show($id)
    {
        $blueprint = Blueprint::where('user_id', auth()->id())->findOrFail($id);
        
        // Map title to name for frontend compatibility
        $blueprint->name = $blueprint->title ?? $blueprint->name;

        return response()->json([
            'success' => true,
            'data' => $blueprint
        ]);
    }

    /**
     * Rename any blueprint
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $blueprint = Blueprint::where('user_id', auth()->id())->findOrFail($id);
        $blueprint->update(['title' => $validated['name']]); // Ensure 'title' is updated since DB uses title

        return response()->json([
            'status' => 'success',
            'message' => 'Blueprint renamed',
            'data' => $blueprint
        ]);
    }

    /**
     * Delete any blueprint
     */
    public function destroy($id)
    {
        $blueprint = Blueprint::where('user_id', auth()->id())->findOrFail($id);
        $blueprint->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Blueprint deleted'
        ]);
    }
}
