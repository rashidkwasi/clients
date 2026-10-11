<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\HttpResponses;

class PlansController extends Controller
{
    use HttpResponses;

    /**
     * Display all plan records
     */
    public function allplans()
    {
        $plans = Plan::where('businessid', auth()->user()->businessid)->get();
        return $this->success($plans);
    }

    /**
     * Create a new plan record
     */
    public function createplan(Request $request)
    {
        DB::beginTransaction();

        try {
            $plan = Plan::create([
                'planid' => $request->planid,
                'name' => $request->name,
                'setup_fee' => $request->setup_fee,
                'monthly_fee' => $request->monthly_fee,
                'description' => $request->description,
                'status' => $request->status,
                'createdby' => auth()->user()->userid ?? 'system',
                'created_at' => $request->created_at,
                'modifiedby' => auth()->user()->userid ?? 'system',
                'modified_at' => $request->modified_at,
                'businessid' => $request->businessid,
            ]);

            DB::commit();
            return $this->success($plan);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a single plan record
     */
    public function show($id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return response()->json($plan);
    }

    /**
     * Update a plan record
     */
    public function updateplan(Request $request)
    {
        $plan = Plan::find($request->id);

        if (!$plan) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $request->validate([
            'planid' => 'sometimes|string|max:20',
            'name' => 'sometimes|string|max:45',
            'setup_fee' => 'sometimes|numeric',
            'monthly_fee' => 'sometimes|numeric',
            'description' => 'sometimes|string|max:128',
            'status' => 'sometimes|integer',
            'createdby' => 'sometimes|string|max:45',
            'created_at' => 'sometimes|date',
            'modifiedby' => 'sometimes|string|max:45',
            'modified_at' => 'sometimes|date',
            'businessid' => 'sometimes|string|max:20',
        ]);

        $plan->update([
            'planid' => $request->planid ?? $plan->planid,
            'name' => $request->name ?? $plan->name,
            'setup_fee' => $request->setup_fee ?? $plan->setup_fee,
            'monthly_fee' => $request->monthly_fee ?? $plan->monthly_fee,
            'description' => $request->description ?? $plan->description,
            'status' => $request->status ?? $plan->status,
            'createdby' => auth()->user()->userid ?? 'system',
            'created_at' => $request->created_at ?? $plan->created_at,
            'modifiedby' => auth()->user()->userid ?? 'system',
            'modified_at' => $request->modified_at ?? $plan->modified_at,
            'businessid' => $request->businessid ?? $plan->businessid,
        ]);

        return $this->success($plan);
    }

    /**
     * Delete a plan record
     */
    public function destroy($id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $plan->delete();

        return response()->json(['message' => 'Record deleted successfully']);
    }
}