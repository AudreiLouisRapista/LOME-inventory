<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $table = 'batches';
    protected $primaryKey = 'batch_ID';

    protected $fillable = [
        'product_ID',
        'batch_code',
        'mfg_date',
        'expiration_date',
        'quantity',
    ];

    protected $casts = [
        'mfg_date' => 'date',
        'expiration_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_ID', 'product_ID');
    }
}
