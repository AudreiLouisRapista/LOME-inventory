<?php
namespace App\Http\Controllers;
use Exception;
use DateTime;
use App\Models\Purchase;  
use App\Models\Supplier;  
use App\Models\Payment;   
use App\Models\ActivityLog;
use App\Imports\POSsaleImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // For direct database queries
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Session; // For session usage
use Illuminate\Support\Facades\File;


class MainController extends Controller
{
    /**
     * Show the main welcome page.
     */
    public function main(){
        return view('welcome');
    }

    /**
     * Show the registration form view.
     */


    /**
     * Handle an incoming registration request and save the new user.
     */

    /**
     * Handle user authentication (login).
     */
   
public function auth_user(Request $request) {
    $email = $request->email;
    $pass = $request->password;

    $user = DB::table('users')->where('email', $email)->first();
   
    // dd($user);
    
    if(!$user){
        return back()->with('errorMessage', 'User not found.');
    }

    if(!Hash::check($pass, $user->password)){
        return back()->with('errorMessage', 'Invalid password.');
    }

    if($user->role_id != '1'){
        
        return back()->with('errorMessage', 'Unauthorized access.');
    }

    $request->session()->regenerate();
   Session::put([
    'urs_id' => $user->urs_id,
    'email' => $user->email,
    // 'password' => $user->password,
    'name' => $user->name,
    'role_id' => $user->role_id,
    'user_role' => 'admin',
   

   ]);
   // ... inside your auth_user function ...

// if($user->role_id == '1'){
//     // This will tell us if the logic is correct before it tries to redirect
//     dd('User is admin, attempting to redirect to admin.dashboard'); 
//     return redirect()->route('admin.dashboard');
// }
  return redirect()->route('admin.dashboard');

}


private function logActivity($action, $description)
{
    ActivityLog::create([
        'admin_id' => Session::get('user_id'), // or Auth::id() if using Auth
        'action' => $action,
        'description' => $description,
    ]);
}

    public function admin_profile()
{
    $logs = ActivityLog::latest()->take(10)->get();

    $admins = DB::table('users')->get();

    return view('admin_profile', compact('admins','logs'));
}

 public function adminProfile(Request $request, $id) { 
    // 1. Get the current admin record to find the old image path
    $admin = DB::table('users')->where('urs_id', $id)->first();
    
    $updateData = [
        'email'  => $request->email,
        'name'   => $request->name,
        
    ];

    if ($request->filled('password')) {
        $updateData['password'] = Hash::make($request->password);
    }
    // 2. Handle Profile Image Update
  if ($request->hasFile('profile_image')) {
        $image = $request->file('profile_image');
        $filename = time() . '_' . $image->getClientOriginalName();
        
        // Save to public/images
        $image->move(public_path('images'), $filename);
        $new_path = 'images/' . $filename;

        // Delete old file if it's not the default avatar
        if ($admin->profile && $admin->profile !== 'dist/img/avatar.png') {
            $old_file_path = public_path($admin->profile);
            if (File::exists($old_file_path)) {
                File::delete($old_file_path);
            }
        }

        $updateData['profile'] = $new_path;
        
        // Update session immediately for UI refresh
        session(['profile' => $new_path]);
    }

    // 3. Update Database
    DB::table('users')->where('urs_id', $id)->update($updateData);

    session(['name' => $request->name]);
    $this->logActivity('updated', 'Updated Admin Profile: ' . $request->name);

    session()->flash('save', 'Admin Info updated successfully.');
    return redirect()->back();
}










   public function dashboard(Request $request)
{
    // dd(session()->all());

    $filter = $request->get('created_at', "all");


    $availableDates = DB::table('posimportdata')
        ->select(DB::raw('DATE(created_at) as date'))
        ->distinct()
        ->orderBy('date', 'desc')
        ->get();

    $logs = ActivityLog::latest()->take(10)->get();
   
            $logs = ActivityLog::whereIn('action', ['added','updated','deleted'])
                   ->latest()
                   ->take(10)
                   ->get();

        $totalProducts = DB::table('inventory')->count();  
        $totalQuantity = DB::table('inventory')->sum('invt_remainingStock');  
        $totalSold = DB::table('inventory')->sum('invt_totalSold');
        $instockProducts = DB::table('inventory')->where('status_ID', 1)->count();
        $lowStockProducts = DB::table('inventory')->where('status_ID', 2)->count();
        $outOfStock = DB::table('inventory')->where('status_ID', 3)->count();    
        
    $totalStockPossible = $totalQuantity + $totalSold;
        $quantityPercent = ($totalStockPossible > 0) 
        ? round(($totalQuantity / $totalStockPossible) * 100, 2) 
        : 0;

        $importedData = DB::table('posimportdata')
            ->join('products', 'posimportdata.product_ID', '=', 'products.product_ID')
            ->select('products.product_name', DB::raw('SUM(posimportdata.TotalSalesPerQty) as TotalSalesPerQty'))
            ->groupBy('products.product_name');
        if ($filter !== 'all' && !empty($filter)) {
            // If the user selects a specific date (e.g., 2026-03-10)
            $importedData->whereDate('posimportdata.created_at', $filter);
        }

        // 3. GET FULL DATASET FIRST (For Totals)
        $allFilteredSales = $importedData->get();

        $totalSales = $importedData->orderBy('TotalSalesPerQty', 'desc')
        ->limit(10)
        ->get()
        ->reverse();

     


    // Calculate accurate stats from the FULL filtered list
    $actualSum = $allFilteredSales->sum('TotalSalesPerQty');
    $totalSum = '₱' . number_format($actualSum, 2);
    $totalAverages = '₱' . number_format($actualSum / max(1, $allFilteredSales->count()), 2);
    
    // Get the absolute best seller from the filtered data
    $bestSellerRecord = $allFilteredSales->sortByDesc('TotalSalesPerQty')->first();
    $bestSellerName = $bestSellerRecord ? $bestSellerRecord->product_name : 'No Sales';

    // 4. GET TOP 10 FOR CHART ONLY
    $chartData = $allFilteredSales->sortByDesc('TotalSalesPerQty')->take(10)->reverse();
    $labels = $chartData->pluck('product_name');
    $values = $chartData->pluck('TotalSalesPerQty');
    
    // AJAX Check
    if ($request->ajax()) {
        return response()->json([
            'labels' => $labels,
            'values' => $values,
            'totalSum' => $totalSum,
            'totalAverages' => $totalAverages
        ]);
    }
    return view('dashboard', compact('logs', 'totalProducts', 'totalQuantity',
     'totalSold', 'instockProducts', 'lowStockProducts', 'outOfStock', 'quantityPercent' ,
      'totalSales', 'totalStockPossible', 'totalSum', 'labels', 'values', 'totalAverages',
       'filter', 'availableDates', 'importedData', 'allFilteredSales', 'bestSellerName','chartData','bestSellerRecord'));
    
}









public function pos_history() 
{
    // 1. Fetch logs and count how many records are in POSImportData for each log
    // We join with 'users' to get the actual name of the Admin
    $logs = DB::table('import_logs')
        ->leftJoin('users', 'import_logs.UploadedBy', '=', 'users.urs_id')
        ->select(
            'import_logs.*', 
            'users.name', // Assuming your column name is urs_username
            DB::raw('(SELECT COUNT(*) FROM POSImportData WHERE POSImportData.import_logs_ID = import_logs.Import_logs_ID) as row_count')
        )
        ->orderBy('import_logs.Uploaded_At', 'desc')
        ->paginate(10); // Matches your pagination design in the Blade

    // 2. Calculate Stats for the top cards
    $totalImports = DB::table('import_logs')->count();
    $successImports = DB::table('import_logs')->where('Status', 'Success')->count();

    return view('pos_history', compact('logs', 'totalImports', 'successImports'));
}

public function import_history() {
      $logs = DB::table('import_logs')
            ->orderBy('Uploaded_At', 'desc')
            ->get();
        return view('pos_history', compact('logs'));
}

    // Locate the file in storage and initiate a download for the Admin
public function download_importedFile($id) {
    // Note: Ensure the column name matches your Navicat (Import_logs_ID vs posImport_ID)
    $log = DB::table('import_logs')->where('Import_logs_ID', $id)->first();

    if ($log && Storage::disk('public')->exists($log->FilePath)) {
        return Storage::disk('public')->download($log->FilePath, $log->FileName);
    }

    return back()->with('error', 'File not found.');
}




public function inventory_report() {
   

    return view('inventory_report');
}




public function view_products(Request $request) {
    if ($request->ajax()) {
        $data = DB::table('products')
            ->join('category', 'products.category_ID', '=', 'category.category_ID')
            ->leftJoin('perishable', 'products.perishable_ID', '=', 'perishable.perishable_ID')
            ->whereNull('products.deleted_at')
            ->select([
                'products.product_ID', 
                'products.product_name',
                'products.tie_number',
                'products.tie_qty', 
                'products.category_ID',
                'category.category_name as name',
                'products.perishable_ID',
                'perishable.perishable_title'
            ]);

        return DataTables::of($data)
            ->addColumn('action', function($row){
                return '    <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-sm btn-outline-success edit-btn"
                                data-id="'.$row->product_ID.'" 
                                data-name="'.$row->product_name.'" 
                                data-category="'.$row->name.'"
                                data-tie_number="'.$row->tie_number.'"
                                data-tie_qty="'.$row->tie_qty.'"
                                data-category-ID="'.$row->category_ID.'"
                                data-perishable_title="'.$row->perishable_title.'"
                                data-perishable_ID="'.$row->perishable_ID.'">
                                <i class="bi bi-pen"></i>
                            </button>
                           <button class="btn btn-sm btn-outline-danger delete-btn"
                                data-id="'.$row->product_ID.'">
                                <i class="bi bi-trash"></i>
                            </button>
                            </div>';
                        
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    $categories = DB::table('category')->orderBy('category_name', 'ASC')->get();
    $products = DB::table('products')->orderBy('product_name', 'ASC')->get();
    $perishables = DB::table('perishable')->orderBy('perishable_title', 'ASC')->get();

    return view('products', compact('categories', 'products', 'perishables'));
}

    public function ProductsoftDelete($id)
{
    // Instead of deleting the row, we just mark it with the current time
    DB::table('products')
        ->where('product_ID', $id)
        ->update(['deleted_at' => now()]);

    return response()->json(['success' => 'Product moved to trash!']);
}



public function save_product(Request $request)
{

    // dd($request->all());
            //  // 1. Validate the input
                // $request->validate([
                //     'product_name'  => 'required|string',
                //     'category_ID'   => 'required|integer',
                //     'product_cost'  => 'required|numeric',

                //     'product_price' => 'required|numeric',
                //     'perishable_ID' => 'required|integer'
                // ]);

    $product = $request->product_name;
    $category = $request->category_ID;
    $perishable = $request->perishable_ID;
    $tie_number = $request->tie_number;
    $tie_qty = $request->tie_qty;
    $cost = $request->product_cost;
    $price = $request->product_price;


        $duplicate = DB::table('products')
        ->where('category_ID', $category)
        ->where('product_name', $product)
         ->whereNull('deleted_at')
        ->exists();
    if ($duplicate) {
        // NEED EDIT THE NAME 
        return back()->with('duplicate', 'The product ' . $request->product_name .'already exists in this category.');
    }
        else{
            DB::table('products')
            ->insert([
                'product_name' => $product,
                'category_ID' => $category,
                'perishable_ID' => $perishable,
                'tie_number' => $tie_number,
                'tie_qty' => $tie_qty,
                'product_cost' => $cost,
                'product_price' => $price,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 5. Create Activity Log
            $this->logActivity('added', 'Added Product for Name: ' . $request->product_name);
            // 6. Success Feedback
            session()->flash('save', 'Product added successfully!');
            return redirect()->back();
        }
}



public function update_product(Request $request) {
    DB::table('products')
        ->where('product_ID', $request->product_ID)
        ->update([
            'product_ID' => $request->product_ID,
            'product_name' => $request->product_name,
            'category_ID' => $request->category_ID,
            'product_price' => $request->price,
            'product_cost' => $request->cost,
            'deleted_at' => null,
            'updated_at' => now(),
            
      
        ]);
        $userName = session('name');
       $this->logActivity(
    'updated',
    "Updated Product ID: {$request->product_ID} | Name: {$request->product_name} | Responsible: {$userName} "
    );
   return response()->json(['save' => 'Product updated successfully.']);

}



// 


public function InventorysoftDelete($id)
{
    // Instead of deleting the row, we just mark it with the current time
    DB::table('inventory')
        ->where('inventory_ID', $id)
        ->update(['deleted_at' => now()]);

    return response()->json(['success' => 'Inventory moved to trash!']);
}

public function view_inventory(Request $request) {

    // 1. Handle DataTable AJAX (Refresh only the table)
    if ($request->ajax() && !$request->has('get_chart')) {
        $data = DB::table('inventory')
            ->leftJoin('products', 'inventory.product_ID', '=', 'products.product_ID')
            ->leftJoin('category', 'products.category_ID', '=', 'category.category_ID')
            ->whereNull('inventory.deleted_at');

        if ($request->category_id_table && $request->category_id_table != 'all') {
            $data->where('category.category_ID', $request->category_id_table);
        }
        if ($request->product_id_table && $request->product_id_table != 'all') {
            $data->where('products.product_ID', $request->product_id_table);
        }

        $data->select([
            'inventory.inventory_ID',
            'products.product_name as product_name',
            'inventory.invt_unitCost as unit_price', 
            'category.category_name as name',
            'inventory.invt_NewQuantity',
            'inventory.invt_StartingQuantity',
            'inventory.invt_remainingStock',
            'inventory.invt_totalSold',
            'inventory.invt_sellingPrice',
            'inventory.status_ID',
            'inventory.product_ID',
            'inventory.category_ID',
        ]);

        return DataTables::of($data)
            ->addColumn('action', function($row){
                return '<div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-sm btn-outline-success edit-btn"
                            data-inventory-id="'.$row->inventory_ID.'"
                            data-product-id="'.$row->product_ID.'"
                            data-unit-cost="' .$row->unit_price.'"
                            data-sellingPrice="'.$row->invt_sellingPrice.'"
                            data-product-name="'.$row->product_name.'"
                            data-category="'.$row->name.'"
                            data-category-ID="'.$row->category_ID.'"
                            data-cost="'.$row->unit_price.'"
                            data-update_NewQuantity="'.$row->invt_NewQuantity.'"
                            data-update_remainingstock="'.$row->invt_remainingStock.'">
                            <i class="bi bi-pen"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-btn"
                                    data-id="'.$row->inventory_ID.'">
                              <i class="bi bi-trash"></i>
                            </button>
                        </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    // 2. Handle Chart AJAX ONLY (Refresh only the chart)
    if ($request->ajax() && $request->has('get_chart')) {
        $query = DB::table('inventory')
            ->join('products', 'inventory.product_ID', '=', 'products.product_ID')
            ->select('products.product_name as name', 'inventory.invt_totalSold as sold', 'inventory.invt_remainingStock as remaining')
            ->where('inventory.invt_totalSold', '>', 0);

        if ($request->category_id != 'all') {
            $query->where('products.category_ID', $request->category_id);
        }

        return response()->json($query->orderBy('sold', 'desc')->limit(12)->get());
    }

    // 3. NORMAL PAGE LOAD (Initial data)
    $categories = DB::table('category')->orderBy('category_name', 'ASC')->get();

    // This query finds products that have at least one batch with is_added = 0
    $products = DB::table('batches')
        ->join('products', 'batches.product_id', '=', 'products.product_ID')
        ->select('products.product_ID', 'products.product_name')
        ->where('batches.is_added', 0) // Only show new batches
        ->groupBy('products.product_ID', 'products.product_name') 
        ->orderBy('products.product_name', 'ASC')
        ->get();
    $selectedCategory = $request->query('category_id', 'all');

    // Summary Stats for Cards
    $totalProducts = DB::table('inventory')->count();  
    $totalQuantity = DB::table('inventory')->sum('invt_remainingStock');  
    $totalSold = DB::table('inventory')->sum('invt_totalSold');
    $instockProducts = DB::table('inventory')->where('status_ID', 1)->count();
    $lowStockProducts = DB::table('inventory')->where('status_ID', 2)->count();
    $outOfStock = DB::table('inventory')->where('status_ID', 3)->count();

    return view('inventory', compact(
        'categories',
        'totalProducts',
        'instockProducts',
        'lowStockProducts',
        'outOfStock',
        'totalQuantity',
        'selectedCategory',
        'products',
        'totalSold'   
    ));
}

 
  
    
public function update_inventory(Request $request) {
    // 1. Get the current record from the database
    $inventory = DB::table('inventory')
        ->where('inventory_ID', $request->inventory_ID)
        ->first();

    if (!$inventory) {
        return response()->json(['error' => 'Record not found'], 404);
    }

    // 2. Treat the input as "New Stock Arriving Today"
    $incomingStock = (int)$request->update_NewQuantity; 

    // 3. Update the monthly "New Quantity" counter
    // We add today's arrival to whatever was already added this month
    $updatedMonthlyNew = $inventory->invt_NewQuantity + $incomingStock;

    // 4. THE CORE FORMULA:
    // (Starting Stock from Rollover + Total New Stock this month) - Total Sold this month
    $totalRemaining = ($inventory->invt_StartingQuantity + $updatedMonthlyNew) - $inventory->invt_totalSold;

    // 5. Determine Status based on the result
    $status_ID = 1; // In Stock
    if ($totalRemaining <= 0) {
        $status_ID = 3; // Out of Stock
        $totalRemaining = 0; 
    } elseif ($totalRemaining <= 5) {
        $status_ID = 2; // Low Stock
    }

    // 6. Update the Database
    $affected = DB::table('inventory')
        ->where('inventory_ID', $request->inventory_ID)
        ->update([
            'invt_NewQuantity'    => $updatedMonthlyNew,
            'invt_remainingStock' => $totalRemaining,
            'status_ID'           => $status_ID,
            'deleted_at'          => null,
            'updated_at'          => now(),
        ]);

    return response()->json([
        'save' => 'New Quantity Added',
        'debug' => [
            'new_starting' => $inventory->invt_StartingQuantity,
            'monthly_additions' => $updatedMonthlyNew,
            'total_sold' => $inventory->invt_totalSold,
            'final_remaining' => $totalRemaining
        ]
    ]);
}

  
public function import_pos_sales(Request $request) 
{
    $request->validate(['pos_import'], [
        'pos_import' => 'required|mimes:xls,xlsx,csv']);

    $file = $request->file('pos_import');
    $fileHash = md5_file($file->getRealPath());

    // Check for duplicates
    $exists = DB::table('import_logs')->where('FileHash', $fileHash)->exists();
    if ($exists) {
        return response()->json(['error' => 'This file has already been imported.'], 422);
    }

    $fileName = time() . '_' . $file->getClientOriginalName();
    $filePath = $file->storeAs('pos_import', $fileName, 'public');

   
    $importLogID = DB::table('import_logs')->insertGetId([
        'FileName'    => $fileName,
        'FilePath'    => $filePath,
        'FileHash'    => $fileHash,
        'Status'      => 'Success', // Ensure this matches your Varchar/Enum
        'UploadedBy'  => session('urs_id') ?? 1, // Fallback to 1 for testing
        'Uploaded_At' => now()
    ]);
    // Now pass that ID to the Import class
    Excel::import(new POSsaleImport($importLogID), storage_path('app/public/' . $filePath));
    return response()->json(['save' => 'Import completed and inventory updated!']);
}


public function getProductsByCategory($id) {
    $products = DB::table('products')
        ->where('products.category_ID', $id)
        ->select([
            'products.product_ID', 
            'products.product_name',
            // Subquery for Quantity: Sum only unprocessed batches
            DB::raw("(SELECT IFNULL(SUM(quantity), 0) FROM batches 
                      WHERE product_ID = products.product_ID 
                      AND is_added = 0) as batch_quantity"),
            
            // Subquery for Cost: Get unit_price from the latest unprocessed batch
            DB::raw("(SELECT IFNULL(pi.unit_price, 0) 
                      FROM batches b
                      JOIN purchase_items pi ON b.purchase_item_id = pi.purchase_item_id
                      WHERE b.product_ID = products.product_ID 
                      AND b.is_added = 0 
                      ORDER BY b.batch_ID DESC LIMIT 1) as unit_cost")
        ])
        ->get();

    return response()->json($products);
}

public function add_new_inventory(Request $request)
{
    DB::beginTransaction();
    try {
        $product_ID = $request->product_ID;
        
        // Sum all batches for this product that haven't been processed yet (is_added = 0)
        $incomingQty = DB::table('batches')
            ->where('product_id', $product_ID)
            ->where('is_added', 0)
            ->sum('quantity');

        if ($incomingQty <= 0) {
            return back()->with('errorMessage', 'No new batch quantity found for this product.');
        }

        $inventory = DB::table('inventory')->where('product_ID', $product_ID)->first();

        if (!$inventory) {
            // Create new record
            DB::table('inventory')->insert([
                'product_ID'            => $product_ID,
                'category_ID'           => $request->category_ID,
                'invt_unitCost'         => $request->product_cost,
                'invt_sellingPrice'     => $request->product_price, // Fixed variable name
                'invt_StartingQuantity' => $incomingQty,
                'invt_remainingStock'   => $incomingQty,
                'status_ID'             => 1, // Set to In Stock
                'created_at'            => now(),
                'updated_at'            => now()
            ]);
        } else {
            // Update existing record
            DB::table('inventory')->where('product_ID', $product_ID)->update([
                'invt_unitCost'       => $request->product_cost,
                'invt_sellingPrice'   => $request->product_price, // Fixed variable name
                'invt_NewQuantity'    => $incomingQty, 
                'invt_remainingStock' => $inventory->invt_remainingStock + $incomingQty,
                'status_ID'           => 1, // Ensure it is marked as In Stock (1)
                'updated_at'          => now(),
                'deleted_at'          => null
            ]);
        }

        // Mark batches as processed
        DB::table('batches')
            ->where('product_id', $product_ID)
            ->where('is_added', 0)
            ->update([
                'is_added' => 1,
                'updated_at' => now()
            ]);

        DB::commit();
        return back()->with('save', 'Inventory updated successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('errorMessage', 'Error: ' . $e->getMessage());
    }
}

public function inventory_rollover(Request $request) {
    if (session('user_role') !== 'admin') {
        return response()->json(['error' => 'Permission denied.'], 403);
    }

    DB::beginTransaction();
    try {
        $items = DB::table('inventory')->get();
        $timestamp = now();

        foreach ($items as $item) {
            // 1. ARCHIVE: Save current data to history table
            DB::table('inventory_history')->insert([
                'product_ID'    => $item->product_ID,
                'starting_qty'  => $item->invt_StartingQuantity ?? 0,
                'added_qty'     => $item->invt_NewQuantity ?? 0,
                'sold_qty'      => $item->invt_totalSold ?? 0,
                'closing_stock' => $item->invt_remainingStock ?? 0,
                'snapshot_date' => $timestamp,
                'created_at'    => $timestamp,
            ]);

            // 2. RESET: Update the main inventory table for the new month
            DB::table('inventory')
                ->where('inventory_ID', $item->inventory_ID)
                ->update([
                    'invt_StartingQuantity' => $item->invt_remainingStock ?? 0,
                    'invt_NewQuantity'      => null,
                    'invt_totalSold'        => null,
                    'invt_remainingStock'   => null,
                    'deleted_at'            => null,
                    'updated_at'            => $timestamp
                ]);
        }

        DB::commit();
        return response()->json(['save' => 'Month closed! History saved and balances reset.']);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['errorMessage' => 'System error: ' . $e->getMessage()], 500);
    }
}

public function purchase_invoice(Request $request)
{
    // 1. Fetch Purchases with Supplier info
    $query = DB::table('purchases')
        ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.supplier_id')
        ->select([
            'purchases.*',
            'suppliers.supplier_name'
        ]);

    // Apply filter if selected
    if ($request->supplier_id) {
        $query->where('purchases.supplier_id', $request->supplier_id);
    }

    $purchases = $query->latest('purchases.created_at')->get();

  
   // 2. Fetch all Items and group them by invoice_id
        $purchase_items = DB::table('purchase_items')
            ->join('products', 'purchase_items.product_id', '=', 'products.product_ID')
            ->select([
                'purchase_items.*',
                'products.product_name',
               'products.tie_number',
                'products.tie_qty',

                DB::raw('products.tie_number * products.tie_qty as tie_total'),
                
            ])
            ->get()
            ->groupBy('purchase_id'); // Grouping by invoice_id as you mentioned

    // 3. Dropdown data
    $suppliers = DB::table('suppliers')->orderBy('supplier_name', 'ASC')->get();
    $products  = DB::table('products')->orderBy('product_name', 'ASC')->get();

    return view('purchase_invoice', compact('suppliers', 'purchases', 'products', 'purchase_items'));
}

public function getPaymentHistory($id)
{
    // Fetch payments related to this purchase
    $payments = DB::table('payments') // Or whatever your payment table is called
        ->where('purchase_id', $id)
        ->orderBy('payment_date', 'desc')
        ->get();

    return response()->json($payments);
}

public function storePayment(Request $request) 
{
    // 1. Validate the request and check for duplicate reference numbers
    // This is the most professional way to catch duplicates before they hit the DB
    $request->validate([
        
        'purchase_id'      => 'required',
        'amount_paid'      => 'required|numeric|min:0',
        'payment_date'     => 'required|date',
        'payment_method'   => 'required'
    ]);

    try {
        DB::transaction(function () use ($request) {
            // 2. Log payment
            DB::table('payments')->insert([
                'purchase_id'           => $request->purchase_id,
                'amount_paid'           => $request->amount_paid,
                'payment_date'          => $request->payment_date,
                'payment_method'        => $request->payment_method,
                'old_remaining_balance' => $request->old_remaining_balance,
                'reference_number'      => $request->reference_number,
                'created_at'            => now()
            ]);

            // 3. Update purchase
            $p = DB::table('purchases')->where('purchase_id', $request->purchase_id)->first();
            $totalPaid = ($p->total_paid ?? 0) + $request->amount_paid;
            $status = ($totalPaid >= $p->net_amount) ? 'Paid' : 'Partial';

            DB::table('purchases')->where('purchase_id', $request->purchase_id)->update([
                'total_paid' => $totalPaid,
                'status'     => $status
            ]);
        });

        return redirect()->back()->with('save', 'Payment recorded successfully!');

    } catch (\Illuminate\Database\QueryException $e) {
        // Catch-all for database integrity issues
        if ($e->errorInfo[1] == 1062) {
            return redirect()->back()->with('errorMessage', 'Duplicate Entry: This reference number already exists.');
        }

        return redirect()->back()->with('errorMessage', 'An unexpected database error occurred.');
    }
}




public function saveInvoiceAndItem(Request $request)
{   
    DB::beginTransaction();

    try {
        // STEP 1: Insert the main invoice record
        $invoiceId = DB::table('purchases')->insertGetId([
            'supplier_id'    => $request->supplier_id,
            'invoice_number' => $request->invoice_number,
            'invoice_date'   => $request->invoice_date,
            'gross_amount'   => $request->gross_total_raw,
            'vat_amount'     => $request->vat_amount_raw,
            'net_amount'     => $request->grand_total_raw,
            'due_date'       => $request->due_date,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        foreach ($request->product_name as $key => $name) {
            
            // STEP 2: Product Check/Creation
            $product = DB::table('products')->where('product_name', $name)->first();

            if (!$product) {
                $productId = DB::table('products')->insertGetId([
                    'product_name' => $name,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            } else {
                $productId = $product->product_ID; 
            }

            // STEP 3: Save Purchase Item
            $purchaseItemID = DB::table('purchase_items')->insertGetId([
                'purchase_id' => $invoiceId,
                'product_id'  => $productId,
                'unit_price'  => $request->unit_price[$key],
                'total_price' => $request->quantity[$key] * ($request->tie_qty[$key] * $request->tie_number[$key] * $request->unit_price[$key]),
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // STEP 4: Save Batch (This is your "Loading Dock")
            $batchQty = (int)($request->tie_qty[$key] * $request->tie_number[$key]);
            $batchId = DB::table('batches')->insertGetId([
                'purchase_item_id' => $purchaseItemID,
                'product_id'       => $productId,
                'expiration_date'  => ($request->perishable_type[$key] === 'perishable') ? $request->exp_date[$key] : null,
                'quantity'         => $batchQty,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            // STEP 5: Record Movement
            DB::table('stock_movements')->insert([
                'product_ID'       => $productId,
                'purchase_item_id' => $purchaseItemID,
                'purchase_id'      => $invoiceId,
                'batch_ID'         => $batchId,
                'MovementType'     => 'IN',
                'quantity'         => $request->quantity[$key],
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            // STEP 6: REMOVED
            // We no longer sync to inventory here. 
            // The admin will do this manually in the Inventory module.
        }

        DB::commit(); 
        return redirect()->route('add_invoice')->with('save', 'Invoice saved! Go to Inventory to receive items.');

    } catch (\Illuminate\Database\QueryException $e) {
        DB::rollback();
        // Exception handling for unique constraints (Invoice Number) remains...
        if ($e->errorInfo[1] == 1062) {
             return back()->withInput()->with('errorMessage', 'Duplicate Entry: ' . $e->getMessage());
        }
        return back()->withInput()->with('errorMessage', 'Database Error: ' . $e->getMessage());
    } catch (\Exception $e) {
        DB::rollback();
        return back()->withInput()->with('errorMessage', 'General Error: ' . $e->getMessage());
    }
}




public function add_invoice(Request $request)
{
    // 1. Fetch data using Eloquent (latest first)
    $query = Purchase::with('supplier')->withSum('payments as total_paid_sum', 'amount_paid')->latest();

    // 2. Apply existing filters
    if ($request->supplier_id) {
        $query->where('supplier_id', $request->supplier_id);
    }
    
    $purchases_data = $query->get();

    // AJAX Response for the main table
    if ($request->ajax()) {
        return response()->json(['data' => $purchases_data]);
    }

    // 3. Normal Load Variables
    $suppliers = DB::table('suppliers')->orderBy('supplier_name', 'ASC')->get();
    
    // UPDATED: Join with the perishable table to get the 'perishable_title' string
    $products = DB::table('products')
        ->leftJoin('perishable', 'products.perishable_ID', '=', 'perishable.perishable_ID') // Change 'perishable_ID' if your FK name differs
        ->select(
            'products.product_ID', 
            'products.product_name', 
            'products.tie_qty', 
            'products.tie_number',
            'perishable.perishable_title' // This provides the "Perishable" or "Non-Perishable" string
        ) 
        ->orderBy('products.product_name', 'ASC')
        ->get();

    return view('add_invoice', compact('suppliers', 'products'));
}


public function stockMovement(Request $request)
{
    // 1. Get Inbound (Stock Adjustments/Purchases)
    $inbound = DB::table('stock_movements')
        ->join('products', 'stock_movements.product_ID', '=', 'products.product_ID')
        ->leftJoin('purchases', 'stock_movements.purchase_id', '=', 'purchases.purchase_id')
        ->leftJoin('batches', 'stock_movements.batch_ID', '=', 'batches.batch_ID')
        ->select(
            'stock_movements.created_at',
            'products.product_name',
            'batches.quantity as batch_quantity', // This is the batch capacity
            'purchases.invoice_number'
        )
        ->get()
        ->map(function ($item) {
            $item->type = 'Inbound';
            $item->reference = $item->invoice_number ?? 'MANUAL';
            $item->move_qty = $item->batch_quantity ?? 0; // Standardized name
            return $item;
        });

    // 2. Get Outbound (POS Imports)
    $outbound = DB::table('posimportdata')
        ->join('products', 'posimportdata.product_ID', '=', 'products.product_ID')
        ->select(
            'posimportdata.created_at',
            'products.product_name',
            'posimportdata.QuantitySold',
            'posimportdata.pos_import_ID'
        )
        ->get()
        ->map(function ($item) {
            $item->type = 'Outbound';
            $item->reference = 'IMPORT-' . $item->pos_import_ID;
            $item->move_qty = $item->QuantitySold; // Standardized name
            return $item;
        });

  
        // --- NEW: Calculate Totals ---
    $recentIn = $inbound->sum('move_qty');
    $recentOut = $outbound->sum('move_qty');

    $movements = $inbound->concat($outbound)->sortByDesc('created_at');

    return view('stockMovement', compact('movements', 'recentIn', 'recentOut'));
}

        // LOG OUT

public function logout(Request $request)
{
    // 1. Tell Laravel's Auth system to log the current user out.
    Auth::logout();

    // 2. Invalidate the current session and remove all session data.
    // This is the core action that destroys the 'user_role' key and all other data.
    $request->session()->invalidate(); 

    // 3. Regenerate the session's CSRF token for security.
    $request->session()->regenerateToken();

    // 4. Redirect the user.
    return redirect('/');
}

}
