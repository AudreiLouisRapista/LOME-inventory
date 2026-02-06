<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $categories = ['LAUNDRY PRODUCTS', 'CONDIMENTS', 'BEVERAGES', 'SNACKS & BISCUITS',
        'HOUSEHOLD SUPPLIES', 'CANNED GOODS', 'VEGETABLES & SPICES',
        'PLASTIC WARE & MERCHANDISE', 'LIQUID MILK', 'MILK', 'POWDERED MILK',
        'MEDICINE', 'MEDICARE', 'LIQOUR & WINES', 'DRY GOODS', 
        'SCHOOL & OFFICE SUPPLIES', 'CANDIES', 'CANDIES & CHOCOLATES',
        'PASTA', 'FROZEN GOODS', 'PERFUME', 'HAIR CARE', 'SANITARY',
        'MEAT PRODUCTS', 'POWDERED JUICE', 'BODY CARE', 'FRAGRANCE',
        'CONFECTIONERS', 'SPREAD & CHEESE', 'FEMININE CARE', 'BISCUIT',
        'SOAP', 'COFFEE MIXES', 'VEGETABLES & CONDIMENTS', 'TOYS',
        'ICE CREAM', 'CIGARRETES', 'DIAPERS', 'ALCOHOL', 'FRUITS',
        'BABY FOODS', 'CHEESES', 'ORAL CARE', 'JUICES', 'SAUCE',
        'FACIAL CARE', 'BAKING INGREDIENTS', 'OTHERS', 'SKINCARE',
        'BAKING', 'CHOCOLATE', 'NAIL CARE', 'CANDY', 'JUICE', 'TOILET SOAP'];

        foreach($categories as $cat){
            \DB::table('categories')->insert([
                'name' => $cat,
                'slug' => Str::slug($cat),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
