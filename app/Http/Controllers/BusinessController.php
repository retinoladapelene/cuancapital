<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessController extends Controller
{
    public function index(Request $request)
    {
        $settings = \App\Models\SystemSetting::all()->pluck('value', 'key');
        
        $latestSession = null;
        $latestSimulation = null;
        $latestBlueprint = null;

        if (Auth::check()) {
            $latestSession = \App\Models\ReverseGoalSession::where('user_id', Auth::id())
                ->latest()
                ->first();

            if ($latestSession) {
                $latestSimulation = \App\Models\ProfitSimulation::where('reverse_goal_session_id', $latestSession->id)
                    ->latest()
                    ->first();
            }

            $latestBlueprint = \App\Models\Blueprint::where('user_id', Auth::id())->first();
        }

        return view('index', [
            'settings' => $settings,
            'latestSession' => $latestSession,
            'latestSimulation' => $latestSimulation,
            'latestBlueprint' => $latestBlueprint
        ]);
    }

    public function update(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('ENTERED BusinessController@update', $request->all());

        $rules = [
            'selling_price' => 'nullable|numeric',
            'variable_costs' => 'nullable|numeric',
            'fixed_costs' => 'nullable|numeric',
            'traffic' => 'nullable|numeric', // allow anything numeric, old was integer
            'conversion_rate' => 'nullable|numeric',
            'ad_spend' => 'nullable|numeric',
            'target_revenue' => 'nullable|numeric',
            'available_cash' => 'nullable|numeric',
            'max_capacity' => 'nullable|numeric', // old was integer
            'business_name' => 'nullable|string',
            'currency' => 'nullable|string',
        ];

        // Replace empty strings with null so validation passes
        $data = $request->all();
        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }

        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules);

        if ($validator->fails()) {
            \Illuminate\Support\Facades\Log::error('Validation failed in BusinessController', [
                'errors' => $validator->errors()->toArray(),
                'data_received' => $data
            ]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        \Illuminate\Support\Facades\Log::info('Business Update Request:', $request->all());

        $profile = $request->user()->businessProfile;
        
        if (!$profile) {
            $profile = $request->user()->businessProfile()->create();
        }

        $profile->update($data);

        return response()->json($profile);
    }
}
