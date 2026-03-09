<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;

class Supplier extends Model
{
    use HasFactory;

    protected $primaryKey = 'supplier_id'; // your actual PK
    public $incrementing = true;           // if PK is auto-increment
    protected $keyType = 'int';

    protected $fillable = [
        'supplier_name',
        'address',
        'contact_no'
    ];
    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'supplier_id');
    }


}
