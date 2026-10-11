<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\HttpResponses;
use App\Services\SmsService;
use App\Models\Customer;
use App\Models\Invoice;

class PaymentsController extends Controller
{
    use HttpResponses;

    /**
     * Display all payment records
     */
    public function allpayments()
    {
        $payments = Payment::where('businessid', auth()->user()->businessid)->get();
        return $this->success($payments);
    }

    /**
     * Create a new payment record
     */
    public function createpayment(Request $request)
    {
        DB::beginTransaction();

        try {
            $payment = Payment::create([
                'clientid' => $request->clientid,
                'item' => $request->item,
                'description' => $request->description,
                'amount' => $request->amount,
                'createdby' => auth()->user()->userid ?? 'system',
                'created_at' => $request->created_at,
                'status' => $request->status,
                'businessid' => $request->businessid,
                'paymentno' => $request->paymentno,
                'invoiceno' => $request->invoiceno,
            ]);

            // If invoice number is provided, update the invoice's paid amount
            // If invoice number is provided, update the invoice's paid amount
            if ($request->has('invoiceno') && !empty($request->invoiceno)) {
                $this->updateInvoicePaidAmount($request->invoiceno, $request->amount);
            } else {
                // No invoice number provided, use clientid to find outstanding invoices
                $this->applyPaymentToOutstandingInvoices($payment);
            }

            // Send SMS notification after payment creation
            $this->sendPaymentSms($payment);

            DB::commit();
            return $this->success($payment);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update invoice paid amount
     */
    private function updateInvoicePaidAmount($invoiceNo, $amount)
    {
        try {
            $invoice = Invoice::where('invoiceno', $invoiceNo)->first();

            if ($invoice) {
                // Add the payment amount to the existing paid amount
                $newPaidAmount = $invoice->paidamount + $amount;

                // Update the invoice
                $invoice->update([
                    'paidamount' => $newPaidAmount
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("Error updating invoice paid amount: " . $e->getMessage());
        }
    }

    /**
     * Send SMS notification for payment creation
     */
    private function sendPaymentSms($payment)
    {
        // You can implement the logic to find customer information from payment data
        // For now, we'll just log that the method was called
        \Log::info("Payment created - SMS notification would be sent here");

        // Example implementation if you have customer information in the payment:
        // $customer = Customer::where('id', $payment->clientid)->first();
        // if ($customer && $customer->telephone) {
        //     $smsService = new SmsService();
        //     $message = "Payment of GHS {$payment->amount} received for {$payment->item}. Payment no: {$payment->paymentno}";
        //     $smsService->sendSms($customer->telephone, $message);
        // }
    }

    private function applyPaymentToOutstandingInvoices($payment)
    {
        try {
            // Get all outstanding invoices for the client
            $outstandingInvoices = Invoice::where('clientid', $payment->clientid)
                ->where('paidamount', '<', 'totalamount')
                ->orderBy('created_at') // Optional: prioritize older invoices first
                ->get();

            $remainingAmount = $payment->amount;

            foreach ($outstandingInvoices as $invoice) {
                if ($remainingAmount <= 0) {
                    break; // No more remaining amount to apply
                }

                // Calculate the new paid amount for this invoice
                $newPaidAmount = min($invoice->paidamount + $remainingAmount, $invoice->totalamount);
                $amountApplied = $newPaidAmount - $invoice->paidamount;

                // Update the invoice's paid amount
                $invoice->update([
                    'paidamount' => $newPaidAmount,
                ]);

                // Decrease the remaining payment amount
                $remainingAmount -= $amountApplied;
            }

        } catch (\Exception $e) {
            \Log::error("Error applying payment to outstanding invoices: " . $e->getMessage());
        }
    }

    /**
     * Show a single payment record
     */
    public function show($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return response()->json($payment);
    }

    /**
     * Update a payment record
     */
    public function updatepayment(Request $request)
    {
        $payment = Payment::find($request->id);

        if (!$payment) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $request->validate([
            'clientid' => 'sometimes|string|max:45',
            'item' => 'sometimes|string|max:30',
            'description' => 'sometimes|string|max:45',
            'amount' => 'sometimes|numeric',
            'createdby' => 'sometimes|string|max:45',
            'created_at' => 'sometimes|date',
            'status' => 'sometimes|integer',
            'businessid' => 'sometimes|string|max:45',
            'paymentno' => 'sometimes|string|max:20',
            'invoiceno' => 'sometimes|string|max:20',
        ]);

        $payment->update([
            'clientid' => $request->clientid ?? $payment->clientid,
            'item' => $request->item ?? $payment->item,
            'description' => $request->description ?? $payment->description,
            'amount' => $request->amount ?? $payment->amount,
            'createdby' => auth()->user()->userid ?? 'system',
            'created_at' => $request->created_at ?? $payment->created_at,
            'status' => $request->status ?? $payment->status,
            'businessid' => $request->businessid ?? $payment->businessid,
            'paymentno' => $request->paymentno ?? $payment->paymentno,
            'invoiceno' => $request->invoiceno ?? $payment->invoiceno,
        ]);

        return $this->success($payment);
    }

    /**
     * Delete a payment record
     */
    public function destroy($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $payment->delete();

        return response()->json(['message' => 'Record deleted successfully']);
    }
}
