<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'product_ID';

    protected $fillable = [
        'product_name',
        'category_ID',
        'product_exp',
        'product_price',
        'product_cost',
    ];

    public function batches()
    {
        return $this->hasMany(Batch::class, 'product_ID', 'product_ID');
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'product_ID', 'product_ID');
    }
}
