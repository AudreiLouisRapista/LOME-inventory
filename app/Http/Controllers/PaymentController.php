<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_id' => 'required|exists:purchases,purchase_id',
            'payment_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Get the purchase
            $purchase = Purchase::findOrFail($validated['purchase_id']);
            
            // Calculate remaining balance BEFORE payment
            $oldRemainingBalance = $purchase->net_amount - $purchase->total_paid;

            // Validate amount doesn't exceed remaining balance
            if ($validated['amount_paid'] > $oldRemainingBalance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount exceeds remaining balance!'
                ], 422);
            }

            // Create payment record with old remaining balance
            $payment = Payment::create([
                'purchase_id' => $validated['purchase_id'],
                'payment_date' => $validated['payment_date'],
                'amount_paid' => $validated['amount_paid'],
                'old_remaining_balance' => $oldRemainingBalance,  // Store balance before payment
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null
            ]);

            // Update purchase total_paid and status
            $purchase->total_paid += $validated['amount_paid'];
            $newRemainingBalance = $purchase->net_amount - $purchase->total_paid;

            // Update status based on remaining balance
            if ($newRemainingBalance <= 0) {
                $purchase->status = 'PAID';
            } elseif ($purchase->total_paid > 0 && $newRemainingBalance > 0) {
                $purchase->status = 'PARTIAL';
            }
            
            $purchase->save();

            DB::commit();

            // Return updated purchase data
            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully!',
                'data' => [
                    'payment' => $payment,
                    'purchase' => [
                        'id' => $purchase->purchase_id,
                        'invoiceNumber' => $purchase->invoice_number,
                        'supplier' => $purchase->supplier->supplier_name,
                        'invoiceDate' => $purchase->invoice_date,
                        'dueDate' => $purchase->due_date,
                        'netAmount' => (float) $purchase->net_amount,
                        'totalPaid' => (float) $purchase->total_paid,
                        'oldRemainingBalance' => (float) $oldRemainingBalance,
                        'newRemainingBalance' => (float) $newRemainingBalance,
                        'status' => $purchase->status
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error recording payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}