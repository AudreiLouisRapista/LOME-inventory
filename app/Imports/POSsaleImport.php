<?php
namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;

class POSsaleImport implements ToCollection
{
    // 1. You must declare this variable so the class can use it
    protected $importLogID;

    // 2. You must have this "constructor" to catch the ID sent from the Controller
    public function __construct($importLogID)
    {
        $this->importLogID = $importLogID;
    }

   public function collection(Collection $rows)
{
    foreach ($rows as $index => $row) 
    {
        // Skip exactly 34 rows (0 to 33) to start at Row 35
        if ($index < 34) continue;

        // Stop if the row is empty or we reach the summary
        if (empty($row[0]) || str_contains($row[0], '---')) break;

        // Clean names: remove * and `
        $excelName = trim(str_replace(['*', '`'], '', $row[0]));
        
        // Clean numbers: remove commas so (int)"1,000" doesn't become 1
        $qtySoldNow = (int)str_replace(',', '', $row[4] ?? 0); 
        $salesValue = (float)str_replace(',', '', $row[5] ?? 0);

        // Use a looser match for product names
        $product = DB::table('products')
            ->where('product_name', 'LIKE', '%' . $excelName . '%')
            ->first();

        if ($product) {
            DB::table('POSImportData')->insert([
                'import_logs_ID' => $this->importLogID, 
                'product_ID'     => $product->product_ID,
                'QuantitySold'   => $qtySoldNow,
                'TotalSales'     => $salesValue
            ]);

            // Update Inventory logic
            $inventory = DB::table('inventory')->where('product_ID', $product->product_ID)->first();
            if ($inventory) {
                $newTotalSold = $inventory->invt_totalSold + $qtySoldNow;
                $newRemaining = max(0, ($inventory->invt_StartingQuantity + $inventory->invt_NewQuantity) - $newTotalSold);

                DB::table('inventory')->where('product_ID', $product->product_ID)->update([
                    'invt_totalSold'      => $newTotalSold,
                    'invt_remainingStock' => $newRemaining,
                    'status_ID'           => ($newRemaining <= 0) ? 3 : (($newRemaining <= 5) ? 2 : 1)
                ]);
            }
        }
    }
}
}
?>
