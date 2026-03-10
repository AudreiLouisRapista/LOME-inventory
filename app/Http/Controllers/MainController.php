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










   public function dashboard()
{
    // dd(session()->all());
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

     $totalSales = DB::table('posimportdata')
    ->join('products', 'posimportdata.product_ID', '=', 'products.product_ID')
    ->select('products.product_name', DB::raw('SUM(posimportdata.TotalSalesPerQty) as TotalSalesPerQty'))
    ->groupBy('products.product_name')
    ->orderBy('TotalSalesPerQty', 'desc')
    ->limit(10)
    ->get();

    $labels = $totalSales->pluck('product_name');
    $values  = $totalSales->pluck('TotalSalesPerQty');
    $totalSum = number_format($totalSales->sum('TotalSalesPerQty')) . 'k';
    $totalAverages = number_format($totalSales->sum('TotalSalesPerQty') / 1000, 1) . 'k';
    

    return view('dashboard', compact('logs', 'totalProducts', 'totalQuantity',
     'totalSold', 'instockProducts', 'lowStockProducts', 'outOfStock', 'quantityPercent' ,
      'totalSales', 'totalStockPossible', 'totalSum', 'labels', 'values', 'totalAverages'));
    
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


public function product_report() {
    // $pos_sales = DB::table('pos_sales')
    //     ->join('products', 'pos_sales.product_ID', '=', 'products.product_ID')
    //     ->join('category', 'products.category_ID', '=', 'category.category_ID')
    //     ->select(
    //         'pos_sales.*',
    //         'products.product_name',
    //         'category.category_name'
    //     )
    //     ->orderBy('pos_sales.sale_date', 'desc')
    //     ->get();

    return view('product_report');
}

public function inventory_report() {
   

    return view('inventory_report');
}





public function view_products(Request $request) {
 if ($request->ajax()) {
        $data = DB::table('products')
            ->join('category', 'products.category_ID', '=', 'category.category_ID')
            ->select([
                'products.product_ID', 
                'products.product_name', 
                'products.product_price',
                'products.product_cost',
                'products.category_ID',
                'category.category_name as name'
            ]);
             return DataTables::of($data)
            ->addColumn('action', function($row){
                // We write the HTML for the button here
                return '<button class="btn btn-sm btn-outline-success edit-btn" 
                        data-id="'.$row->product_ID.'" 
                        data-name="'.$row->product_name.'" 
                        data-category="'.$row->name.'"
                        data-category-ID="'.$row->category_ID.'"
                        data-cost="'.$row->product_cost.'"
                        data-price="'.$row->product_price.'">
                      <i class="bi bi-pen"></i></button>';
            })
            ->rawColumns(['action']) // Tells Yajra to render HTML, not just text
            ->make(true);
    }
  
     $categories = DB::table('category')->orderBy('category_name', 'ASC')->get();
    $products = DB::table('products')->orderBy('product_name', 'ASC')->get();
     

    return view('products', compact('categories', 'products'));
}





public function save_product(Request $request)
{
 // 1. Validate the input
    $request->validate([
        'product_name'  => 'required|string',
        'category_ID'   => 'required|integer',
        'product_cost'  => 'required|numeric',
        'product_price' => 'required|numeric',
    ]);

    // 2. Link the data. 
    // This finds the product by name and updates the category and prices.
    // If the name doesn't exist, it creates a new row.
    DB::table('products')->updateOrInsert(
        ['product_name' => $request->product_name], // Search by name
        [
            'category_ID'   => $request->category_ID,
            'product_cost'  => $request->product_cost,
            'product_price' => $request->product_price,
            'created_at'    => now(),
            'updated_at'    => now()
        ]
    );
    // 5. Create Activity Log
    $this->logActivity('added', 'Added Product for Name: ' . $request->product_name);
    // 6. Success Feedback
    session()->flash('save', 'Product added successfully!');
    return redirect()->back();
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
      
        ]);
        $this->logActivity(
    'updated',
    'Updated product ID ' . $request->product_ID . ' to ' . $request->product_name
 );
   return response()->json(['success' => 'Product updated successfully.']);

}



public function save_inventory(Request $request){
    $inventory_ID = $request->inventory_ID;
    $product_ID = $request->product_ID; 
    $category_ID = $request->category_ID;
    $StartingQuantity = $request->product_StartingQuantity;
   
  

    // Step 2: HIGHEST PRIORITY - Check for an EXACT duplicate schedule (Same time, section, day)
    $duplicate = DB::table('inventory')
        ->where('category_ID', $category_ID)
        ->where('product_ID', $product_ID)
        ->where('invt_StartingQuantity', $StartingQuantity)
        ->exists();
    if ($duplicate) {
        // NEED EDIT THE NAME 
        return back()->with('error', 'Product with the same category already exists.');
    }

    $status_ID = 1; // Assuming new inventory is always "In Stock"
    if($StartingQuantity <= 5){
        $status_ID = 2; // "Low Stock"
    } elseif($StartingQuantity == 0){
        $status_ID = 3; // "Out of Stock"
    } elseif($StartingQuantity > 5){
        $status_ID = 1; // "In Stock"
    }


    DB::table('inventory')
    ->insert([
        'product_ID' => $product_ID,
        'category_ID' => $category_ID,
        'invt_StartingQuantity' => $StartingQuantity,
        'status_ID' => $status_ID
    ]);

  

  
    $products = DB::table('products')->where('product_ID', $product_ID)->first();
    $categories = DB::table('category')->where('category_ID', $category_ID)->first();
   

    $this->logActivity(
        'added',
        'Added inventory for product ID ' . $product_ID . ', category name ' . $categories->category_name
    );
  
    session()->flash('save', 'Inventory saved successfully!');
    return redirect()->back();
}



   
        // VIEW INVENTORY

public function view_inventory(Request $request) {
    // 1. Handle DataTable AJAX (Refresh only the table)
    if ($request->ajax() && !$request->has('get_chart')) {
        $data = DB::table('inventory')
            ->Join('products', 'inventory.product_ID', '=', 'products.product_ID')
            ->Join('category', 'products.category_ID', '=', 'category.category_ID');
                                // FILTERS:
                    if ($request->category_id_table && $request->category_id_table != 'all') {
                        $data->where('category.category_ID', $request->category_id_table);
                    }

                    if ($request->product_id_table && $request->product_id_table != 'all') {
                        $data->where('products.product_ID', $request->product_id_table);
                    }
            $data->select([
                'inventory.inventory_ID',
                'products.product_name as product_name', 
                'category.category_name as name',
                'products.product_price',
                'products.product_cost',
                'inventory.invt_NewQuantity',
                'inventory.invt_StartingQuantity',
                'inventory.invt_remainingStock',
                'inventory.invt_totalSold',
                'inventory.status_ID',
                'inventory.product_ID',
                'inventory.category_ID',


            ]);
        return DataTables::of($data)
            ->addColumn('action', function($row){
                return '<button class="btn btn-sm btn-outline-success edit-btn" 
                        data-inventory-id="'.$row->inventory_ID.'"
                        data-product-id="'.$row->product_ID.'"
                        data-product-name="'.$row->product_name.'"
                        data-category="'.$row->name.'"
                        data-category-ID="'.$row->category_ID.'"
                        data-cost="'.$row->product_cost.'"
                        data-price="'.$row->product_price.'"
                        data-update_NewQuantity="'.$row->invt_NewQuantity.'"
                        data-update_remainingstock="'.$row->invt_remainingStock. '">
                        <i class="bi bi-pen"></i></button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    // Handle Chart AJAX ONLY (Refresh only the chart)
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
    $products = DB::table('products')->orderBy('product_name', 'ASC')->get(); 
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
            'updated_at'          => now(),
        ]);

    return response()->json([
        'success' => 'Stock added! Formula applied: (Starting + New) - Sold',
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


   

    
    

    return response()->json(['success' => 'Import completed and inventory updated!']);
}



public function getProductsByCategory($id) {

        $products = DB::table('products')
        ->leftJoin('inventory', 'products.product_ID', '=', 'inventory.product_ID')
            ->leftJoin('batches', 'products.product_ID', '=', 'batches.product_ID')
            ->where('products.category_ID', $id)
            ->select([
                'products.product_ID', 
                'products.product_name', 
                'products.product_price',
                'products.product_cost',
                'batches.quantity as batch_quantity',
                DB::raw('IFNULL(inventory.invt_StartingQuantity, 0) as current_stock'),
            ])
            
            ->get();

            
        return response()->json($products);
}

public function add_new_inventory(Request $request)
        {
            $validated = $request->validate([
                'product_ID' => ['required', 'integer'],
             
                'batch_quantity' => ['required', 'integer', 'min:1'],
             
            ]);

            $product_ID = (int) $validated['product_ID'];
           
            $incomingQty = (int) $validated['batch_quantity'];

            DB::beginTransaction();

            try {
                $product = DB::table('products')->where('product_ID', $product_ID)->lockForUpdate()->first();
                if (!$product) {
                    DB::rollBack();
                    return back()->with('error', 'Product not found.');
                }

                // 2) INVENTORY UPDATE
                $inventory = DB::table('inventory')->where('product_ID', $product_ID)->lockForUpdate()->first();
                
              

                if (!$inventory) {
                    DB::table('inventory')->insert([
                        'product_ID'            => $product_ID,
                        'category_ID'           => $product->category_ID,
                        'invt_StartingQuantity' => $incomingQty, 
                        'invt_NewQuantity'      => 0,
                        'invt_totalSold'        => 0,
                        'invt_remainingStock'   => 0,
                        'status_ID'             => 1,
                        'created_at'            => now(),
                        'updated_at'            => now(),
                    ]);
                } elseif ($incomingQty > 0){
                    DB::table('inventory')->where('product_ID', $product_ID)->update([
                        'invt_NewQuantity' => $incomingQty,
                        'updated_at' => now(),
                    ]);
                } else {
                    $currentSold = (int) ($inventory->invt_totalSold ?? 0);
                    $currentNew  = (int) ($inventory->invt_NewQuantity ?? 0);
                    
                    $totalRemaining = ($incomingQty + $currentNew) - $currentSold;

                    DB::table('inventory')
                        ->where('product_ID', $product_ID)
                        ->update([
                            'invt_StartingQuantity' => $incomingQty, 
                            'invt_remainingStock'   => $totalRemaining,
                            'updated_at'            => now(),
                        ]);
                }

                DB::commit();
                return back()->with('save', 'Inventory updated successfully.');

            } catch (\Throwable $e) {
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
                    'invt_NewQuantity'      => 0,
                    'invt_totalSold'        => 0,
                    'invt_remainingStock'   => 0,
                    'updated_at'            => $timestamp
                ]);
        }

        DB::commit();
        return response()->json(['success' => 'Month closed! History saved and balances reset.']);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'System error: ' . $e->getMessage()], 500);
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

    // 2. Fetch all Items and group them by invoice_id for the modals
    $purchase_items = DB::table('purchase_items')
        ->join('products', 'purchase_items.product_id', '=', 'products.product_ID')
        ->join('uom', 'purchase_items.uom_ID', '=', 'uom.uom_ID')
        ->select([
            'purchase_items.*',
            'products.product_name',
            'uom.uom_title'
        ])
        ->get()
        ->groupBy('purchase_id'); // Ensure this matches your FK column

    // 3. Dropdown data
    $suppliers = DB::table('suppliers')->orderBy('supplier_name', 'ASC')->get();
    $products  = DB::table('products')->orderBy('product_name', 'ASC')->get();
    $uoms      = DB::table('uom')->get();

    return view('purchase_invoice', compact('suppliers', 'purchases', 'products', 'uoms', 'purchase_items'));
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
    // $request->validate([
    //     'purchase_id' => 'required',
    //     'amount_paid' => 'required|numeric|min:0',
    //     'payment_date' => 'required|date',
    //     'payment_method' => 'required'
    // ]);

    // Use a transaction to ensure both tables update or neither does
    DB::transaction(function () use ($request) {
        // 1. Log payment
        DB::table('payments')->insert([
            'purchase_id' => $request->purchase_id,
            'amount_paid' => $request->amount_paid,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'old_remaining_balance'=> $request->old_remaining_balance,
            'reference_number' => $request->reference_number,
            'created_at' => now()
        ]);

        // 2. Update purchase
        $p = DB::table('purchases')->where('purchase_id', $request->purchase_id)->first();
        $totalPaid = ($p->total_paid_sum ?? 0) + $request->amount_paid;
        $status = ($totalPaid >= $p->net_amount) ? 'Paid' : 'Partial';

        DB::table('purchases')->where('purchase_id', $request->purchase_id)->update([
            'total_paid' => $totalPaid,
            'status' => $status
        ]);
    });

    return redirect()->back()->with('save', 'Payment recorded!');
}

public function saveInvoiceAndItem(Request $request)
{   
    // dd($request->all());
        //     // Validate the incoming data
        //    $request->validate([
        //     'supplier_id' => 'required',
        //     'invoice_number' => 'required',
        //     'product_name.*' => 'required',
        //     'quantity.*' => 'required|numeric|min:1', // Add this
        //     'unit_price.*' => 'required|numeric',    // Add this
        // ]);

    DB::beginTransaction();

   try {

   
    

    // 1. Save the Main Invoice
    $invoiceId = DB::table('purchases')->insertGetId([
        'supplier_id'    => $request->supplier_id,
        'invoice_number'     => $request->invoice_number,
        'invoice_date'   => $request->invoice_date,
        'gross_amount'    => $request->gross_total_raw,
        'vat_amount'     => $request->vat_amount_raw,
        'net_amount'    => $request->grand_total_raw,
        'due_date'      => $request->due_date,
        'invoice_date' => $request->invoice_date,
        'created_at'     => now(),
        'updated_at'        => now(),
    ]);



        foreach ($request->product_name as $key => $name) {
            $product = DB::table('products')->where('product_name', $name)->first();

            if (!$product) {
                $productId = DB::table('products')->insertGetId([
                    'product_name' => $name,
                    // 'uom_ID'       => $request->uom[$key],
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            } else {
            
                $productId = $product->product_ID; 
            }

      $purchaseItemID = DB::table('purchase_items')->insertGetId([
            'purchase_id'        => $invoiceId,
            'product_id'        => $productId,
            'uom_quantity'  => $request->quantity[$key],
            'uom_ID'            => $request->uom[$key],
            'quantity_per_uom' => $request->quantity_per_unit[$key],
            'unit_tie'          => $request->tie_number[$key],
            'unit_price'        => $request->unit_price[$key],
            'total_price'       => $request->quantity[$key] * ($request->quantity_per_unit[$key] * $request->tie_number[$key] * $request->unit_price[$key]),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

         $batchId = DB::table('batches')->insertGetId([
        'purchase_item_id' => $purchaseItemID,
        'product_id' => $productId,
        'batch_code' => $request->batch_number,
        'mfg_date' => $request->mfg_date,
        'expiration_date' => $request->exp_date,
        'quantity' => $request->quantity_per_unit[$key] * ($request-> tie_number[$key]),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

        $stockMovement = DB::table('stock_movements')->insert([
            'product_ID' => $productId,
            'purchase_item_id' => $purchaseItemID,
            'purchase_id' => $invoiceId,
            'batch_ID' => $batchId,
            'MovementType' => 'IN',
            'quantity' => $request->quantity[$key],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    DB::commit();
    // Use the session key 'save' to match your 'with' call
    return redirect()->route('add_invoice')->with('save', 'Invoice saved successfully!');

    } catch (\Exception $e) {
        DB::rollback();
        // Return with the error message so you can see what went wrong
        return back()->with('errorMessage', 'Error: ' . $e->getMessage());
    }
}





public function add_invoice(Request $request)
{
    //  1. Fetch data using Eloquent (latest first)
    $query = Purchase::with('supplier')->withSum('payments as total_paid_sum', 'amount_paid')->latest();

    // 2. Apply your existing filters
    if ($request->supplier_id) {
        $query->where('supplier_id', $request->supplier_id);
    }
    
    $purchases_data = $query->get();

    // 2. AJAX Response
    if ($request->ajax()) {
        return response()->json(['data' => $purchases_data]);
    }

    // 3. Normal Load Variables (Ensure lowercase names!)
    $suppliers = DB::table('suppliers')->orderBy('supplier_name', 'ASC')->get();
    $products  = DB::table('products')->orderBy('product_name', 'ASC')->get();
    $uoms      = DB::table('uom')->get();
    $purchases = $purchases_data;

 return view('add_invoice', compact('suppliers', 'purchases', 'products', 'uoms'));
}

public function stockMovement(Request $request)
{
    // 1. Fetch all movements with their related product and category data
    // We order by ID desc so the newest "Activity" is at the top
    $movements = DB::table('stock_movements')
        ->join('products', 'stock_movements.product_ID', '=', 'products.product_ID')
        ->join('purchases', 'stock_movements.purchase_id', '=', 'purchases.purchase_id')
        ->join('batches', 'stock_movements.batch_ID', '=', 'batches.batch_ID')
        ->join('purchase_items', 'stock_movements.purchase_item_id', '=', 'purchase_items.purchase_item_id')
        ->select(
            'stock_movements.*', 
            'products.product_name', 
            'purchases.invoice_number',
            'batches.quantity as batch_quantity',

            
        )
        ->orderBy('stock_movements.created_at', 'desc')
        ->get();

    // 2. Calculate Quick Stats for the Top Cards (Last 30 days)
    $recentIn = DB::table('stock_movements')
        ->where('MovementType', 'IN')
        ->where('created_at', '>=', now()->subDays(30))
        ->sum('quantity');

    $recentOut = DB::table('stock_movements')
        ->where('MovementType', 'OUT')
        ->where('created_at', '>=', now()->subDays(30))
        ->sum('quantity');

    // 3. Pass everything to the view
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
