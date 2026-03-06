<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SimulationEngine;
use App\Support\ApiResponse;
use Exception;

class SimulationController extends Controller
{
    protected $engine;

    public function __construct(SimulationEngine $engine)
    {
        $this->engine = $engine;
    }

    /**
     * POST /api/simulation/start
     */
    public function start(Request $request)
    {
        $request->validate([
            'simulation_id' => 'required|exists:simulations,id'
        ]);

        try {
            $data = $this->engine->startSimulation($request->user()->id, $request->simulation_id);
            return ApiResponse::success($data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), 400);
        }
    }

    /**
     * POST /api/simulation/answer
     */
    public function answer(Request $request)
    {
        $request->validate([
            'simulation_id' => 'required|exists:simulations,id',
            'step_id' => 'required|exists:simulation_steps,id',
            'option_id' => 'required|exists:simulation_options,id'
        ]);

        try {
            $data = $this->engine->processAnswer(
                $request->user()->id,
                $request->simulation_id,
                $request->step_id,
                $request->option_id
            );
            return ApiResponse::success($data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), 400);
        }
    }

    /**
     * GET /api/simulation/result
     */
    public function result(Request $request)
    {
        $request->validate([
            'simulation_id' => 'required|exists:simulations,id'
        ]);

        try {
            $data = $this->engine->finishSimulation($request->user()->id, $request->simulation_id);
            return ApiResponse::success($data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), 400);
        }
    }
}
