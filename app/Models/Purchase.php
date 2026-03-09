<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $primaryKey = 'purchase_id';

    protected $fillable = [
        'supplier_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'gross_amount',
        'vat_amount',
        'net_amount',
        'total_paid',
        'status'
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'total_paid' => 'decimal:2',  // Add this
        'invoice_date' => 'date',
        'due_date' => 'date'
    ];


    /**
     * Purchase belongs to Supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }
    /**
     * Purchase has many items
     */
    public function items()
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_id');
    }

    /**
     * Purchase has many payments
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'purchase_id');
    }

    /**
     * Helper: total paid
     */
    public function totalPaid()
    {
        return $this->payments()->sum('amount_paid');
    }

    /**
     * Helper: remaining balance
     */
    public function remainingBalance()
    {
        return $this->net_amount - $this->totalPaid();
    }


}
