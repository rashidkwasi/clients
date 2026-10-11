<?php

namespace App\Http\Controllers;

use App\Models\invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\HttpResponses;
use App\Services\SmsService;
use App\Models\Customer;

class InvoicesController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = invoice::where('businessid', auth()->user()->businessid)->get();
        return $this->success($invoices);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                'invoiceno' => 'required|string|max:20|unique:invoices,invoiceno',
                'period_start' => 'required|date',
                'period_end' => 'required|date',
                'month' => 'required|string|max:2',
                'year' => 'required|string|max:4',
                'totalamount' => 'required|numeric',
                'paidamount' => 'required|numeric',
                'duedate' => 'required|date',
                'status' => 'required|integer',
                'businessid' => 'required|string|max:45',
                'createdby' => 'required|string|max:45',
                'created_at' => 'required|date',
            ]);

            $invoice = invoice::create([
                'invoiceno' => $request->invoiceno,
                'period_start' => $request->period_start,
                'period_end' => $request->period_end,
                'month' => $request->month,
                'year' => $request->year,
                'totalamount' => $request->totalamount,
                'paidamount' => $request->paidamount,
                'duedate' => $request->duedate,
                'status' => $request->status,
                'businessid' => auth()->user()->businessid,
                'createdby' => auth()->user()->userid ?? 'system',
                'created_at' => $request->created_at,
            ]);

            // Send SMS notification after invoice creation
            $this->sendInvoiceSms($invoice);

            DB::commit();
            return $this->success($invoice, 'Invoice created successfully', 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send SMS notification for invoice creation
     */
    private function sendInvoiceSms($invoice)
    {
        // You can implement the logic to find customer information from invoice data
        // For now, we'll just log that the method was called
        \Log::info("Invoice created - SMS notification would be sent here");
        
        // Example implementation if you have customer information in the invoice:
        // $customer = Customer::where('businessid', $invoice->businessid)->first();
        // if ($customer && $customer->telephone) {
        //     $smsService = new SmsService();
        //     $message = "Invoice {$invoice->invoiceno} created for amount GHS {$invoice->totalamount}. Due date: {$invoice->duedate}";
        //     $smsService->sendSms($customer->telephone, $message);
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show($invoice)
    {
        $invoice = invoice::find($invoice);

        if (!$invoice) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        // Ensure business ID filtering for data isolation
        if ($invoice->businessid !== auth()->user()->businessid) {
            return response()->json(['message' => 'Unauthorized access to this record'], 403);
        }

        return $this->success($invoice);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $invoiceId)
    {
        DB::beginTransaction();

        try {
            $invoice = invoice::find($invoiceId);

            if (!$invoice) {
                return response()->json(['message' => 'Record not found'], 404);
            }

            // Ensure business ID filtering for data isolation
            if ($invoice->businessid !== auth()->user()->businessid) {
                return response()->json(['message' => 'Unauthorized access to this record'], 403);
            }

            $validatedData = $request->validate([
                'invoiceno' => 'string|max:20|unique:invoices,invoiceno,' . $invoice->id,
                'period_start' => 'date',
                'period_end' => 'date',
                'month' => 'string|max:2',
                'year' => 'string|max:4',
                'totalamount' => 'numeric',
                'paidamount' => 'numeric',
                'duedate' => 'date',
                'status' => 'integer',
                'businessid' => 'string|max:45',
                'createdby' => 'string|max:45',
                'created_at' => 'date',
            ]);

            $invoice->update([
                'invoiceno' => $request->invoiceno ?? $invoice->invoiceno,
                'period_start' => $request->period_start ?? $invoice->period_start,
                'period_end' => $request->period_end ?? $invoice->period_end,
                'month' => $request->month ?? $invoice->month,
                'year' => $request->year ?? $invoice->year,
                'totalamount' => $request->totalamount ?? $invoice->totalamount,
                'paidamount' => $request->paidamount ?? $invoice->paidamount,
                'duedate' => $request->duedate ?? $invoice->duedate,
                'status' => $request->status ?? $invoice->status,
                'businessid' => $request->businessid ?? $invoice->businessid,
                'createdby' => $request->createdby ?? $invoice->createdby,
                'created_at' => $request->created_at ?? $invoice->created_at,
                'modifiedby' => auth()->user()->userid ?? 'system',
            ]);

            DB::commit();
            return $this->success($invoice, 'Invoice updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($invoiceId)
    {
        DB::beginTransaction();

        try {
            $invoice = invoice::find($invoiceId);

            if (!$invoice) {
                return response()->json(['message' => 'Record not found'], 404);
            }

            // Ensure business ID filtering for data isolation
            if ($invoice->businessid !== auth()->user()->businessid) {
                return response()->json(['message' => 'Unauthorized access to this record'], 403);
            }

            $invoice->delete();

            DB::commit();
            return $this->success(null, 'Invoice deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}