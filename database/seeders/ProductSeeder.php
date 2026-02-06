<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')
        ->insert([
           [
            'name'=>'iphone 17 pro max full paid',
            'price' => 100000,
            'stock' =>100,

           ],
           [
    'name'=>'iphone 16 pro max',
            'price' => 90000,
            'stock' =>100,
           ],
           [
 'name'=>'iphone 16 pro ',
            'price' => 80000,
            'stock' =>100,
           ]
        ]);

        

    }
}