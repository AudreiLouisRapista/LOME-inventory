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
        'admin_id' => Session::get('id'), // or Auth::id() if using Auth
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

    return view('dashboard', compact('logs', 'totalProducts', 'totalQuantity',
     'totalSold', 'instockProducts', 'lowStockProducts', 'outOfStock', 'quantityPercent' ,));
    
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
public function download_import($id) {
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

    return view('products', compact('categories'));
}





public function save_product(Request $request)
{
 //   dd($request->all(), $request->file());
    // 1. Capture Input Data
    $product_name = $request->input('product_name');
    $category = $request->input('category_ID');
    $product_expired = $request->input('product_exp');
    $product_price = $request->input('product_price');
    $product_cost = $request->input('product_cost');
    

     

    // 2. Check if Product already exists
    $check_exist = DB::table('products')->where('product_name', $product_name)->exists();
    if ($check_exist) {
        return back()->with('duplicate', 'Product already exists.');
    }

  
    // 4. Save to Database
    DB::table('products')->insert([
        'product_name'  => $product_name,
        'category_ID'      => $category,
        'product_exp'     => $product_expired,
        'product_price'   => $product_price,
        'product_cost'    => $product_cost,
       
       
    ]);

    // 5. Create Activity Log
    $this->logActivity('added', 'Added Product for Name: ' . $product_name);
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

    // !!! CRITICAL: You must use insertGetId so the DB actually saves the row !!!
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
            ->where('products.category_ID', $id)
            ->select([
                'products.product_ID', 
                'products.product_name', 
                'products.product_price',
                'products.product_cost',
                DB::raw('IFNULL(inventory.invt_StartingQuantity, 0) as current_stock'),
            ])
            
            ->get();

            
        return response()->json($products);
}

public function store_batch_supply(Request $request)
{
    $validated = $request->validate([
        'product_ID' => ['required', 'integer'],
        'expiration_date' => ['required', 'date'],
        'quantity' => ['required', 'integer', 'min:1'],
        'batch_code' => ['nullable', 'string', 'max:255'],
        'mfg_date' => ['nullable', 'date'],
    ]);

    $product_ID = (int) $validated['product_ID'];
    $expirationDate = $validated['expiration_date'];
    $incomingQty = (int) $validated['quantity'];

    DB::beginTransaction();
    try {
        // Ensure product exists (we also need category_ID for inventory creation)
        $product = DB::table('products')->where('product_ID', $product_ID)->lockForUpdate()->first();
        if (!$product) {
            DB::rollBack();
            return back()->with('error', 'Product not found.');
        }

        // 1) BATCH UPSERT: same product_ID + same expiration_date
        $existingBatch = DB::table('batches')
            ->where('product_ID', $product_ID)
            ->where('expiration_date', $expirationDate)
            ->lockForUpdate()
            ->first();

        if ($existingBatch) {
            DB::table('batches')
                ->where('batch_ID', $existingBatch->batch_ID)
                ->update([
                    'quantity' => (int) $existingBatch->quantity + $incomingQty,
                    'updated_at' => now(),
                ]);
        } else {
            $batchCode = $validated['batch_code'] ?? null;
            if (!$batchCode) {
                $batchCode = 'B-' . $product_ID . '-' . str_replace('-', '', $expirationDate);
            }

            try {
                DB::table('batches')->insert([
                    'product_ID' => $product_ID,
                    'batch_code' => $batchCode,
                    'mfg_date' => $validated['mfg_date'] ?? null,
                    'expiration_date' => $expirationDate,
                    'quantity' => $incomingQty,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                // If another request created the batch first, just increment it.
                DB::table('batches')
                    ->where('product_ID', $product_ID)
                    ->where('expiration_date', $expirationDate)
                    ->increment('quantity', $incomingQty, ['updated_at' => now()]);
            }
        }

        // 2) INVENTORY UPDATE: treat incoming batch quantity as new stock arriving
        $inventory = DB::table('inventory')->where('product_ID', $product_ID)->lockForUpdate()->first();

        if (!$inventory) {
            $starting = 0;
            $monthlyNew = $incomingQty;
            $sold = 0;
            $remaining = $incomingQty;

            $status_ID = 1;
            if ($remaining <= 0) {
                $status_ID = 3;
                $remaining = 0;
            } elseif ($remaining <= 5) {
                $status_ID = 2;
            }

            DB::table('inventory')->insert([
                'product_ID' => $product_ID,
                'category_ID' => $product->category_ID,
                'invt_StartingQuantity' => $starting,
                'invt_NewQuantity' => $monthlyNew,
                'invt_totalSold' => $sold,
                'invt_remainingStock' => $remaining,
                'status_ID' => $status_ID,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $currentStarting = (int) ($inventory->invt_StartingQuantity ?? 0);
            $currentMonthlyNew = (int) ($inventory->invt_NewQuantity ?? 0);
            $currentSold = (int) ($inventory->invt_totalSold ?? 0);

            $updatedMonthlyNew = $currentMonthlyNew + $incomingQty;
            $totalRemaining = ($currentStarting + $updatedMonthlyNew) - $currentSold;

            $status_ID = 1;
            if ($totalRemaining <= 0) {
                $status_ID = 3;
                $totalRemaining = 0;
            } elseif ($totalRemaining <= 5) {
                $status_ID = 2;
            }

            DB::table('inventory')
                ->where('inventory_ID', $inventory->inventory_ID)
                ->update([
                    'invt_NewQuantity' => $updatedMonthlyNew,
                    'invt_remainingStock' => $totalRemaining,
                    'status_ID' => $status_ID,
                    'updated_at' => now(),
                ]);
        }

        DB::commit();

        $this->logActivity('added', 'Added batch supply for product ID ' . $product_ID . ' exp ' . $expirationDate . ' qty ' . $incomingQty);
        return back()->with('save', 'Batch supply saved and inventory updated.');
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error', 'System error: ' . $e->getMessage());
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
    // 1. Fetch data using Eloquent (latest first)
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

    return view('purchase_invoice', compact('suppliers', 'purchases', 'products', 'uoms'));
}
public function storePayment(Request $request) 
{
    // 1. Validate the incoming AJAX data
    $request->validate([
        'purchase_id'     => 'required|exists:Purchases,purchase_id',
        'amount_paid'     => 'required|numeric|min:0.01',
        'payment_date'    => 'required|date',
        'payment_method'  => 'required',
        'reference_number'=> 'nullable|string'
    ]);

    // 2. Fetch the Purchase Invoice to calculate the current balance
    $invoice = Purchase::findOrFail($request->purchase_id);
    
    // 3. Calculate 'old_remaining_balance'
    // This is (Total Net Amount) minus (Sum of all previous payments)
    $totalPaidSoFar = $invoice->payments()->sum('amount_paid');
    $oldBalance = $invoice->net_amount - $totalPaidSoFar;

    // 4. Save the payment with the missing field
    $payment = Payment::create([
        'purchase_id'           => $request->purchase_id,
        'amount_paid'           => $request->amount_paid,
        'payment_date'          => $request->payment_date,
        'payment_method'        => $request->payment_method,
        'reference_number'      => $request->reference_number,
        'old_remaining_balance' => $oldBalance, // This satisfies the SQL requirement
    ]);

    // 5. Optional: Update the Invoice Status if fully paid
    $newBalance = $oldBalance - $request->amount_paid;
    if ($newBalance <= 0) {
        $invoice->update(['status' => 'paid']);
    } elseif ($newBalance < $invoice->net_amount) {
        $invoice->update(['status' => 'partial']);
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Payment of ₱' . number_format($request->amount_paid, 2) . ' recorded.'
    ]);
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
    ]);

    foreach ($request->product_name as $key => $name) {
        $product = DB::table('products')->where('product_name', $name)->first();

        if (!$product) {
            $productId = DB::table('products')->insertGetId([
                'product_name' => $name,
                'uom_id'       => $request->uom[$key],
                'created_at'   => now(),
            ]);
        } else {
           
            $productId = $product->product_ID; 
        }

        DB::table('purchase_items')->insert([
            'purchase_id'        => $invoiceId,
            'product_id'        => $productId,
            'uom_ID'            => $request->uom[$key],
            'quantity_per_uom' => $request->quantity_per_unit[$key],
            'unit_tie'          => $request->tie_number[$key],
            'unit_price'        => $request->unit_price[$key],
            'total_price'       => $request->quantity[$key] * ($request->quantity_per_unit[$key] * $request->tie_number[$key] * $request->unit_price[$key]),
            'created_at'        => now(),
            'updated_at'        => now(),
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
