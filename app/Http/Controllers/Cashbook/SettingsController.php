<?php

namespace App\Http\Controllers\Cashbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function show(Request $request)
    {
        $settings = \App\Models\CashbookUserSettings::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['saving_rate_target' => 20.00, 'timezone' => 'Asia/Jakarta']
        );
        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'saving_rate_target' => 'numeric|min:0|max:100',
            'timezone' => 'string',
        ]);

        $settings = \App\Models\CashbookUserSettings::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['saving_rate_target' => 20.00, 'timezone' => 'Asia/Jakarta']
        );

        $settings->update($validated);
        return response()->json($settings);
    }
}
