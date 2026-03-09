<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';
    protected $primaryKey = 'inventory_ID';

    protected $fillable = [
        'product_ID',
        'category_ID',
        'invt_StartingQuantity',
        'invt_NewQuantity',
        'invt_totalSold',
        'invt_remainingStock',
        'status_ID',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_ID', 'product_ID');
    }
}
