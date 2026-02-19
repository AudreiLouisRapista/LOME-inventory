<?php
namespace App\Http\Controllers;
use Exception;
use DateTime;
use App\Models\ActivityLog;
use App\Imports\POSsaleImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
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
    
    $quantityPercent = $totalProducts > 0 ? round(($totalQuantity / ($totalQuantity + $totalSold)) * 100, 2) : 0;

    return view('dashboard', compact('logs', 'totalProducts', 'totalQuantity',
     'totalSold', 'instockProducts', 'lowStockProducts', 'outOfStock', 'quantityPercent'));
    
}










public function pos_history() {
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

    return view('pos_history');
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









    





public function deact_teacher(Request $request) {
    $teacherId = $request->teachers_id;
    $scheduleId = $request->schedule_id; // The specific row to clear
    $teacher_name = $request->name;

    // 1. UNASSIGN: Clear the teacher from this specific schedule row
    DB::table('schedules')
        ->where('schedule_id', $scheduleId)
        ->update(['teachers_id' => 0]); // Set to 0 to unassign

    // 2. CHECK REMAINING: Count how many schedules this teacher still has
    $remainingSchedules = DB::table('schedules')
        ->where('teachers_id', $teacherId)
        ->count();

    // 3. UPDATE STATUS: If 0 left, update teacher status to 0
    if ($remainingSchedules == 0) {
        DB::table('teacher')
            ->where('teachers_id', $teacherId)
            ->update(['t_status' => 0]);

        $this->logActivity('updated', 'Unassigned ' . $teacher_name . '. No schedules left, status updated to Inactive.');
        session()->flash('warning', 'Unassigned successfully. Teacher now has 0 schedules and is hidden.');
    } else {
        // They still have other classes, so keep t_status at 1
        $this->logActivity('updated', 'Unassigned ' . $teacher_name . ' from one schedule. ' . $remainingSchedules . ' left.');
        session()->flash('success', 'Schedule removed. Teacher still has ' . $remainingSchedules . ' classes.');
    }

    return redirect()->back();
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
    $newQuantity = $request->update_NewQuantity;
    $remaining = $request->update_remainingstock;

    $totalRemaining = $newQuantity + $remaining;

    // Default Status (In Stock)
    $status_ID = 1; 

    if($totalRemaining === "0" || $totalRemaining === 0) {
        $status_ID = 3; // Out of Stock
    } elseif($totalRemaining <= 5) {
        $status_ID = 2; // Low Stock
    }

    $affected = DB::table('inventory')
        ->where('inventory_ID', $request->inventory_ID)
        ->update([
            'inventory_ID'       => $request->inventory_ID,
            'product_ID'          => $request->product_ID,
            'category_ID'         => $request->category_ID,
            'invt_NewQuantity'       => $newQuantity,
            'invt_remainingStock'       => $totalRemaining,
            'status_ID'           => $status_ID,
            'updated_at'          => now(),
        ]);

    return response()->json([
    'success' => $affected > 0 ? 'Updated!' : 'No rows changed',
    'debug_info' => [
        'received_id' => $request->inventory_ID,
        'rows_affected' => $affected,
        'data_sent' => $request->all()
    ]
    ]);
}

  

public function import_pos_sales(Request $request) 
{
    $request->validate([
        'inventory_file' => 'required|mimes:xlsx,xls,csv'
    ]);

    try {
        Excel::import(new POSsaleImport, $request->file('inventory_file'));
        return back()->with('success', 'Inventory updated successfully!');
    } catch (\Exception $e) {
        return back()->with('error', 'Import failed: ' . $e->getMessage());
    }
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
