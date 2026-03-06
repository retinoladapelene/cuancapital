<?php

namespace App\Http\Controllers\Cashbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CashbookCategory;

class CategoryController extends Controller
{
    /**
     * Default categories per pilar — seeded on first access.
     */
    private const DEFAULTS = [
        // Wajib (kebutuhan pokok)
        ['name' => 'Makan & Minuman',   'pillar' => 'wajib'],
        ['name' => 'Transportasi',       'pillar' => 'wajib'],
        ['name' => 'Listrik & Air',      'pillar' => 'wajib'],
        ['name' => 'Sewa / KPR',         'pillar' => 'wajib'],
        ['name' => 'Kesehatan',          'pillar' => 'wajib'],
        // Growth (investasi diri & bisnis)
        ['name' => 'Tabungan / Investasi','pillar' => 'growth'],
        ['name' => 'Pendidikan',          'pillar' => 'growth'],
        ['name' => 'Bisnis / Modal',      'pillar' => 'growth'],
        // Lifestyle (gaya hidup)
        ['name' => 'Hiburan',            'pillar' => 'lifestyle'],
        ['name' => 'Belanja Pribadi',    'pillar' => 'lifestyle'],
        ['name' => 'Langganan Digital',  'pillar' => 'lifestyle'],
        ['name' => 'Liburan',            'pillar' => 'lifestyle'],
        // Bocor (pengeluaran tidak terencana)
        ['name' => 'Jajan / Impulsif',   'pillar' => 'bocor'],
        ['name' => 'Denda & Charge',     'pillar' => 'bocor'],
        ['name' => 'Tak Terduga',        'pillar' => 'bocor'],
        // Income source (pillar = growth for pemasukan)
        ['name' => 'Gaji / Penghasilan', 'pillar' => 'growth'],
    ];

    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $categories = CashbookCategory::where('user_id', $userId)->get();

        // Auto-seed default categories on first access
        if ($categories->isEmpty()) {
            $now = now();
            $inserts = array_map(fn($d) => [
                'user_id'    => $userId,
                'name'       => $d['name'],
                'pillar'     => $d['pillar'],
                'is_default' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ], self::DEFAULTS);

            CashbookCategory::insert($inserts);
            $categories = CashbookCategory::where('user_id', $userId)->get();
        }

        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'pillar' => 'required|in:wajib,growth,lifestyle,bocor',
        ]);

        $category = CashbookCategory::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'pillar' => $validated['pillar'],
            'is_default' => false,
        ]);

        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'pillar' => 'in:wajib,growth,lifestyle,bocor',
        ]);

        $category = CashbookCategory::where('id', $id)->where('user_id', $request->user()->id)->firstOrFail();
        $category->update($validated);

        return response()->json($category);
    }

    public function destroy(Request $request, $id)
    {
        $category = CashbookCategory::where('id', $id)->where('user_id', $request->user()->id)->firstOrFail();
        
        if ($category->transactions()->exists()) {
            return response()->json(['error' => 'Cannot delete category with existing transactions.'], 400);
        }

        $category->delete();
        return response()->json(['message' => 'Category deleted.']);
    }
}
