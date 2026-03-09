<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $table = 'stock_movements';
    protected $primaryKey = 'StockMovementID';
    
    // Allow these fields to be filled by your controller
    protected $fillable = [
        'Product_ID', 'Batch_ID', 'Purchase_ID', 
        'MovementType', 'Quantity', 'Balance_After', 'Remarks'
    ];
}