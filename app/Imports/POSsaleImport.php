<?php
namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;

class POSsaleImport implements ToCollection
{
    public function collection(Collection $rows)
{
    foreach ($rows as $index => $row) 
    {
        // Skip headers (Rows 0-37 in your file)
        if ($index < 34 || empty(trim($row[0]))) continue;
        
        // Stop if we reach the end of the report
        if (str_contains($row[0], '---')) break;

        // 1. CLEAN THE NAME
      
        $excelName = trim(str_replace(['*', '`'], '', $row[0]));
        $qtySoldNow = (int)$row[4];

        // 2. FIND BY NAME TO GET THE ID
        $product = DB::table('products')
            ->where('product_name', 'LIKE', '%' . $excelName . '%')
            ->first();

        if ($product) {
            $pID = $product->product_ID; // This is your "Same ID" link

            // 3. TARGET THE INVENTORY TABLE USING THE ID
            $inventory = DB::table('inventory')->where('product_ID', $pID)->first();

            if ($inventory) {
                $newTotalSold = $inventory->invt_totalSold + $qtySoldNow;
                $newRemaining = $inventory->invt_quantity - $newTotalSold;
                 if($newRemaining < 0) $newRemaining = 0; 

                // Status logic
                $newStatus = ($newRemaining <= 0) ? 3 : (($newRemaining <= 5) ? 2 : 1);

                // 4. UPDATE
                DB::table('inventory')
                    ->where('product_ID', $pID)
                    ->update([
                        'invt_totalSold'      => $newTotalSold,
                        'invt_remainingStock' => $newRemaining,
                        'status_ID'           => $newStatus
                    ]);
            }
        }
    }
}
}