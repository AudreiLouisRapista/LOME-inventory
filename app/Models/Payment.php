<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'payment_id';
    
    protected $fillable = [
        'purchase_id',
        'payment_date',
        'amount_paid',
        'old_remaining_balance',  // Add this
        'payment_method',
        'reference_number'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount_paid' => 'decimal:2',
        'old_remaining_balance' => 'decimal:2'  // Add this
    ];

    /**
     * Payment belongs to Purchase
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'purchase_id');
    }

}
