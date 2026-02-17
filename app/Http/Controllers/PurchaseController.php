<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $purchases = Purchase::with('supplier')
            ->latest()
            ->get()
            ->map(function ($purchase) {

                $remaining = $purchase->net_amount - $purchase->total_paid;
                $today = now()->toDateString();

                if ($remaining <= 0) {
                    $status = 'PAID';
                } elseif ($remaining < $purchase->net_amount) {
                    $status = 'PARTIAL';
                } elseif ($purchase->due_date < $today) {
                    $status = 'OVERDUE';
                } else {
                    $status = 'UNPAID';
                }

                return [
                    'id' => $purchase->purchase_id,
                    'invoiceNumber' => $purchase->invoice_number,
                    'supplier' => optional($purchase->supplier)->supplier_name,
                    'invoiceDate' => $purchase->invoice_date,
                    'dueDate' => $purchase->due_date,
                    'netAmount' => (float) $purchase->net_amount,
                    'totalPaid' => (float) $purchase->total_paid,
                    'status' => $status
                ];
            });

        return view('purchases.index', compact('purchases'));
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
