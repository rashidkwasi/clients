<?php

namespace App\Http\Controllers;

use App\Models\Allocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\HttpResponses;

class AllocationController extends Controller
{
    use HttpResponses;

    /**
     * Display all allocation records
     */
    public function allallocations()
    {
        $allocations = Allocation::where('businessid', auth()->user()->businessid)->get();
        return $this->success($allocations);
    }

    /**
     * Create a new allocation record
     */
    public function createallocation(Request $request)
    {
        DB::beginTransaction();

        try {
            $allocation = Allocation::create([
                'invoiceno' => $request->invoiceno,
                'paymentno' => $request->paymentno,
                'amount' => $request->amount,
                'tdate' => $request->tdate,
                'businessid' => $request->businessid,
                'createdby' => auth()->user()->userid ?? 'system',
            ]);

            DB::commit();
            return $this->success($allocation);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a single allocation record
     */
    public function show($id)
    {
        $allocation = Allocation::find($id);

        if (!$allocation) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return response()->json($allocation);
    }

    /**
     * Update an allocation record
     */
    public function updateallocation(Request $request)
    {
        $allocation = Allocation::find($request->id);

        if (!$allocation) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $request->validate([
            'invoiceno' => 'sometimes|string|max:20',
            'paymentno' => 'sometimes|string|max:20',
            'amount' => 'sometimes|numeric',
            'tdate' => 'sometimes|date',
            'businessid' => 'sometimes|string|max:45',
        ]);

        $allocation->update([
            'invoiceno' => $request->invoiceno ?? $allocation->invoiceno,
            'paymentno' => $request->paymentno ?? $allocation->paymentno,
            'amount' => $request->amount ?? $allocation->amount,
            'tdate' => $request->tdate ?? $allocation->tdate,
            'businessid' => $request->businessid ?? $allocation->businessid,
            'createdby' => auth()->user()->userid ?? 'system',
        ]);

        return $this->success($allocation);
    }

    /**
     * Delete an allocation record
     */
    public function destroy($id)
    {
        $allocation = Allocation::find($id);

        if (!$allocation) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $allocation->delete();

        return response()->json(['message' => 'Record deleted successfully']);
    }
}