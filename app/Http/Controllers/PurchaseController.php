<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:purchases,invoice_number|max:255',
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'gross_amount' => 'required|numeric|min:0',
            'vat_amount' => 'required|numeric|min:0',
            'net_amount' => 'required|numeric|min:0',
        ]);

        // Set initial status
        $validated['status'] = 'UNPAID';

        // Create the purchase record
        $purchase = Purchase::create($validated);

        // Return success response
        return response()->json([
            'success' => true,
            'message' => "✓ Invoice {$purchase->invoice_number} added successfully!",
            'purchase' => $purchase
        ], 201);
    }

    /**
     * Display items for a specific purchase (UI only).
     */
 
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
