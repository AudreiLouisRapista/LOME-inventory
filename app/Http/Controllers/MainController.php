<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // For direct database queries
use Illuminate\Validation\Rule;
use App\Models\ActivityLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Session; // For session usage
use Exception;
use DateTime;
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

public function Update_teacherProfile(Request $request, $id) {
    // 1. Fetch current teacher data
    $teacher = DB::table('teacher')->where('teachers_id', $id)->first();

    if (!$teacher) {
        return redirect()->back()->with('errorMessage', 'Teacher not found.');
    }

    // 2. Prepare update data
    $updateData = [
        'email'  => $request->email,
        'name'   => $request->name,
        'gender' => $request->gender,
        'phone'  => $request->phone,
    ];

    if ($request->filled('password')) {
        $updateData['password'] = Hash::make($request->password);
    }

    // 3. Handle Image Upload
    if ($request->hasFile('profile_image')) {
        $image = $request->file('profile_image');
        $filename = time() . '_' . $image->getClientOriginalName();
        
        // Save to public/images
        $image->move(public_path('images'), $filename);
        $new_path = 'images/' . $filename;

        // Delete old file if it's not the default avatar
        if ($teacher->profile && $teacher->profile !== 'dist/img/avatar.png') {
            $old_file_path = public_path($teacher->profile);
            if (File::exists($old_file_path)) {
                File::delete($old_file_path);
            }
        }

        $updateData['profile'] = $new_path;
        
        // Update session immediately for UI refresh
        session(['profile' => $new_path]);
    }

    // 4. Update Database
    DB::table('teacher')->where('teachers_id', $id)->update($updateData);

    // 5. Update session name & Log activity
    session(['name' => $request->name]);
    session()->save(); // Force session to save changes

    $logMessage = 'Updated Teacher Profile: ' . $request->name;
    if ($request->filled('password')) {
        $logMessage .= ' (Password was also changed)';
    }

    $this->logActivity('updated', $logMessage);

    return redirect()->back()->with('save', 'Information updated successfully.');
}



public function TeacherUI(Request $request) {
    // 1. Get the logged-in teacher's ID from the session
    $teacherId = session('id'); 
    
    if (!$teacherId) {
        return redirect()->route('login')->with('error', 'Session expired.');
    }

    // 2. Data for the dropdown
    $school_years_map = DB::table('school_year')->pluck('schoolyear_name', 'schoolyear_id');
    $selected_year_id = $request->get('schoolyear_id');

    // 3. Fetch specific teacher profile info
    $teachers = DB::table('teacher')
        ->where('teachers_id', $teacherId)
        ->select(
            'teachers_id', 
            'password as teacher_password',
            'email as teacher_email', 
            'name as teacher_name', 
            'phone as teacher_phone', 
            'gender as teacher_gender', 
            'profile as teacher_profile'
        )
        ->get();

    // 4. Fetch the specific teacher's schedule/loads
    $query = DB::table('schedules')
        ->join('subject', 'schedules.subject_id', '=', 'subject.subject_id')
        ->join('grade_level', 'schedules.grade_id', '=', 'grade_level.grade_id')
        ->join('section', 'schedules.section_id', '=', 'section.section_id')
        ->where('schedules.teachers_id', $teacherId) // Only this teacher
        ->select(
            'schedules.*', 
            'schedules.create_at',
            'subject.subject_name as sub_name',
            'grade_level.grade_title as grade_name',
            'section.section_name as sec_name'
        );

    // Filter by year if selected
    if ($selected_year_id) {
        $query->where('schedules.schoolyear_id', $selected_year_id);
    }

    $teacher_ui = $query->get();

    return view('TeacherUI', compact('teachers', 'teacher_ui', 'school_years_map', 'selected_year_id'));
}






    /**s
     * Show the dashboard view.
     */
   public function dashboard()
{
    // dd(session()->all());
    $logs = ActivityLog::latest()->take(10)->get();
    // Count data
    $totalWorkers = DB::table('users')->where('role_id','2')->count();
   



            $logs = ActivityLog::whereIn('action', ['added','updated','deleted'])
                   ->latest()
                   ->take(10)
                   ->get();

    

    return view('dashboard', compact('logs'  ));
    
}



public function save_section(Request $request){


       // 1. Validate input
    // $request->validate([
    //     'subject_code' => 'required|string|max:255',
    //     'subject_name' => 'required|string|max:255',
    //     'subject_gradelevel' => 'required|string|max:255',
    //     // 'subject_status' => 'required|exists:teacher,teachers_id',
    // ]);



    // 2. Save the new subject
   $save_section = DB::table('section')->insert([
        'section_name' => $request->section_name,
        'grade_id' => $request->grade_id,
        'section_capacity' => $request-> section_capacity,
        'section_strand' => $request-> section_strand,

    ]);


 session()->flash('save', 'Section ADDED successfully.');
    return redirect()->back();

}

public function view_section() {


       $view_section = DB::table('section')
            ->leftJoin('grade_level', 'grade_level.grade_id', '=', 'section.grade_id')
             ->select(
            'section.section_id',
            'section.section_name',
            'section.section_capacity',
            'grade_level.grade_title',
            'section.grade_id',
            'section.section_strand'
        )
            ->get();





 return view('section', compact('view_section'));


}

public function update_section(Request $request) {
    $section_id = $request->section_id;


    DB::table('section')
        ->where('section_id', $section_id)
        ->update([

            'section_name'    => $request->section_name,
            'section_capacity'     => $request->section_capacity,
            'grade_id'   => $request->grade_id,  
            'section_strand'     => $request->section_strand,
            
        ]);

         $this->logActivity(
        'updated',
        'Updated Section ID ' . $section_id . ': ' . $request->section_name
    );
    session()->flash('save', 'Section updated successfully.');
    return redirect()->back();
}






    // SUBJECTS

public function save_subjects(Request $request){


       // 1. Validate input
    // $request->validate([
    //     'subject_code' => 'required|string|max:255',
    //     'subject_name' => 'required|string|max:255',
    //     'subject_gradelevel' => 'required|string|max:255',
    //     // 'subject_status' => 'required|exists:teacher,teachers_id',
    // ]);



    // 2. Save the new subject
   $save_subjects = DB::table('subject')->insert([
        'subject_name' => $request->sub_name,
        'grade_id' => $request->grade_id,
        'subject_status' => $request->t_status,
        'sub_strand' => $request->sub_strand ?? 'N/A',
        'sub_yearlevel' => $request->sub_yearlevel,

    ]);

    $this->logActivity(
    'added',
    'Added subject: ' . $request->sub_name . ' for Grade ' . $request->grade_id
);

    return redirect()->back()->with('success', 'Subject added and teacher status updated!');


    }

     public function view_subject() {


       $view_subject = DB::table('subject')
            ->leftJoin('status', 'status.status_id', '=', 'subject.subject_status')
            ->leftJoin('grade_level', 'grade_level.grade_id', '=', 'subject.grade_id')
             ->select(
            'subject.subject_name',
            'subject.sub_strand',
            'subject.sub_yearlevel',
            'grade_level.grade_title',
            'status.status_name'
        )
            ->get();





    return view('subject', compact('view_subject'));


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


public function update_teacher(Request $request) {
    $teachers_id = $request->teachers_id;

    // 1. Start with the basic data everyone has
    $updateData = [
        'email'         => $request->email,
        'name'          => $request->name,
        'teacher_major' => $request->major,  
        'gender'        => $request->gender,
        'age'           => $request->age,
        'phone'         => $request->phone,
    ];

    // 2. Only add password to the update list if a new one was typed
    // This prevents overwriting the existing password with an empty string or a double-hash
    if ($request->filled('password')) {
        $updateData['password'] = Hash::make($request->password);
    }

    // 3. Perform the update using the merged array
    DB::table('teacher')
        ->where('teachers_id', $teachers_id)
        ->update($updateData);

    // 4. Log the activity with the teacher's name
    $this->logActivity(
        'updated',
        'Updated teacher info for: ' . $request->name . ' (ID: ' . $teachers_id . ')'
    );

    session()->flash('save', 'Teacher updated successfully.');
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
                'category.category_name as name'
            ]);
             return DataTables::of($data)
            ->addColumn('action', function($row){
                // We write the HTML for the button here
                return '<button class="btn btn-sm btn-outline-success edit-btn" 
                        data-id="'.$row->product_ID.'" 
                        data-name="'.$row->product_name.'" 
                        data-category="'.$row->name.'">
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
    $product_unit_amount = $request->input('product_unit_amount');
    $units = $request->input('unit_ID');
    

     

    // 2. Check if Product already exists
    $check_exist = DB::table('products')->where('product_name', $product_name)->exists();
    if ($check_exist) {
        return redirect()->back()->with('errorMessage', 'Product already exists');
    }

  
    // 4. Save to Database
    DB::table('products')->insert([
        'product_name'  => $product_name,
        'category_ID'      => $category,
        'product_exp'     => $product_expired,
        'product_price'   => $product_price,
        'product_cost'    => $product_cost,
        'product_unit_amount' => $product_unit_amount,
        'unit_ID' => $units,
       
    ]);

    // 5. Create Activity Log
    $this->logActivity('added', 'Added Product for Name: ' . $product_name);
    // 6. Success Feedback
    session()->flash('save', 'Product added successfully!');
    return redirect()->back();
}






        // SAVE SUBJECTS


public function save_inventory(Request $request){

    $product_ID = $request->product_ID; 
    $category_ID = $request->category_ID;
    $quantity = $request->quantity;
  

    // Step 2: HIGHEST PRIORITY - Check for an EXACT duplicate schedule (Same time, section, day)
    $duplicate = DB::table('inventory')
        ->where('category_ID', $category_ID)
          ->where('product_ID', $product_ID)
        ->where('quantity', $quantity)
        ->exists();
    if ($duplicate) {
        // NEED EDIT THE NAME 
        return back()->with('error', 'Conflict! Product with the same category and quantity already exists.');
    }

    // 3. Insert the Schedule
    DB::table('inventory')->insert([
        'product_ID'    => $product_ID,
        'category_ID'    => $category_ID,
        'status_ID'      => 1, // Assuming new inventory is always "In Stock"
        'quantity'      => $quantity,
       
    ]);

    if($quantity < 5){
        DB::table('inventory')
        ->where('product_ID', $product_ID)
        ->update(['status_ID' => $quantity == 0 ? 3 : 2]); // Set to Out of Stock if quantity is 0, otherwise Low Stock
    }

  
    $products = DB::table('products')->where('product_ID', $product_ID)->first();
    $categories = DB::table('category')->where('category_ID', $category_ID)->first();
   

    $this->logActivity(
        'added',
        'Added inventory for product ID ' . $product_ID . ', category name ' . $categories->category_name
    );

    session()->flash('save', 'Inventory saved successfully!');
    return redirect()->back();
}




   


// public function set_system_schoolyear(Request $request)
// {
//     $selectedSchoolYear = $request->input('filter_schoolyear_name'); // string like "2025-2026"

//     if ($selectedSchoolYear) {
//         session(['system_schoolyear' => $selectedSchoolYear]);
//     }

//     return redirect()->back(); // redirect to dashboard or current page
// }







    public function updateTeacherStatus($teachers_id) {

        $count = DB::table('schedules')
        ->where('teachers_id', $teachers_id)
        ->count();

        DB::table('teacher')
        ->where('teachers_id', $teachers_id)
        ->update([
            't_status' => ($count > 0) ? 1 : 0 // 1 = Assigned, 0 = Unassigned
        ]);

    }


public function teacher_loads(Request $request)
{
    // 1. Get the selected school year from the request
    $selected_year_id = $request->get('schoolyear_id'); 
    // dd($selected_year_id);
    // Fetch available school years for the dropdown
    $school_years_map = DB::table('school_year')->pluck('schoolyear_name', 'schoolyear_id');
    
    // Initialize empty collection and array
    $teachers = collect(); 
    $teacher_loads = [];   

    // 2. Logic: Show ALL teachers ONLY if a year is selected
    if ($selected_year_id) {
    
    // Fetch only the sections that have schedules in the selected year.
    $teachers = DB::table('teacher')
        // CORRECTED JOIN: Match section.section_id with schedules.section_id
        ->join('schedules', 'teacher.teachers_id', '=', 'schedules.teachers_id') 
        ->where('schedules.schoolyear_id', $selected_year_id)
        ->distinct() // Ensure each teacher only appears once
        ->select('teacher.*') // Select all columns from the section table
        ->get();
        
        // 3. Fetch specific schedules for each teacher for the chosen year
        foreach ($teachers as $teacher) {
            $teacher_loads[$teacher->teachers_id] = DB::table('schedules')
                ->join('subject', 'schedules.subject_id', '=', 'subject.subject_id')
                ->join('grade_level', 'schedules.grade_id', '=', 'grade_level.grade_id')
                ->join('section', 'schedules.section_id', '=', 'section.section_id')
                ->select(
                    'schedules.*', 
                    'subject.subject_name as sub_name',
                    'grade_level.grade_title as grade_name',
                    'section.section_name as sec_name',
                ) 
                ->where('schedules.teachers_id', $teacher->teachers_id)
                ->where('schedules.schoolyear_id', $selected_year_id) // Filter loads by chosen year
                ->get();
        }
    }
    
    return view('teacher_loadView', compact('teachers', 'teacher_loads', 'school_years_map', 'selected_year_id'));
}


public function section_loads(Request $request)
{
    // 1. Get the selected school year from the request
    $selected_year_id = $request->get('schoolyear_id'); 
    // dd($selected_year_id);
    // Fetch available school years for the dropdown
    $school_years_map = DB::table('school_year')->pluck('schoolyear_name', 'schoolyear_id');
    
    // Initialize empty collection and array
    $sections = collect(); 
    $section_loads = [];   

    // 2. Logic: Show ALL teachers ONLY if a year is selected
    if ($selected_year_id) {
    
    // Fetch only the sections that have schedules in the selected year.
    $sections = DB::table('section')
        // CORRECTED JOIN: Match section.section_id with schedules.section_id
        ->join('schedules', 'section.section_id', '=', 'schedules.section_id') 
        ->where('schedules.schoolyear_id', $selected_year_id)
        ->distinct() // Ensure each teacher only appears once
        ->select('section.*') // Select all columns from the section table
        ->get();
        
        // 3. Fetch specific schedules for each teacher for the chosen year
        foreach ($sections as $section) {
            $section_loads[$section->section_id] = DB::table('section')
                ->join('schedules', 'section.section_id', '=', 'schedules.section_id') 
                ->join('subject', 'schedules.subject_id', '=', 'subject.subject_id')
                ->join('grade_level', 'schedules.grade_id', '=', 'grade_level.grade_id')
                ->select(
                    'schedules.*', 
                    'subject.subject_name as sub_name',
                    'grade_level.grade_title as grade_name',
                ) 
                ->where('schedules.section_id', $section->section_id)
                ->where('schedules.schoolyear_id', $selected_year_id) // Filter loads by chosen year
                ->get();
        }
    }
    
    return view('section_loadView', compact('sections', 'section_loads', 'school_years_map', 'selected_year_id'));
}





public function print_section_load($id, $year)
{
    $section = DB::table('section')->where('section_id', $id)->first();
    $schoolyear = DB::table('school_year')->where('schoolyear_id', $year)->first();

    $loads = DB::table('schedules')
        ->join('subject', 'schedules.subject_id', '=', 'subject.subject_id')
        ->join('teacher', 'schedules.teachers_id', '=', 'teacher.teachers_id')
        ->join('grade_level', 'schedules.grade_id', '=', 'grade_level.grade_id')
        ->where('schedules.section_id', $id)
        ->where('schedules.schoolyear_id', $year)
        ->select(
            'schedules.*', 
            'subject.subject_name', 
            'teacher.name as teacher_name', 
            'grade_level.grade_title'
        )
        ->orderBy('schedules.sub_Stime', 'asc')
        ->get();

    $pdf = Pdf::loadView('pdf.section_load_print', compact('section', 'loads', 'schoolyear'));
    
    // Set paper to A4 Portrait
    return $pdf->setPaper('a4', 'portrait')->stream('Section_'.$section->section_name.'.pdf');
}

public function print_teacher_load($id, $year)
{
    // Fetch Teacher and School Year
    $teacher = DB::table('teacher')->where('teachers_id', $id)->first();
    $schoolyear = DB::table('school_year')->where('schoolyear_id', $year)->first();

    // Fetch the schedules and JOIN the section table to get names
    $loads = DB::table('schedules')
        ->join('subject', 'schedules.subject_id', '=', 'subject.subject_id')
        ->join('section', 'schedules.section_id', '=', 'section.section_id')
        ->join('grade_level', 'schedules.grade_id', '=', 'grade_level.grade_id')
        ->where('schedules.teachers_id', $id)
        ->where('schedules.schoolyear_id', $year)
        ->select(
            'subject.subject_name',
            'section.section_name', // This is what you need for the table
            'grade_level.grade_title',
            'schedules.sub_date',
            'schedules.sub_Stime',
            'schedules.sub_Etime'
        )
        ->get();

    // Pass ONLY teacher, loads, and schoolyear to the view
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.teacher_load_print', compact('teacher', 'loads', 'schoolyear'));
    
    return $pdf->setPaper( 'a4', 'portrait')->stream('Teacher_Load_'.$teacher->name.'.pdf');
}

   
        // VIEW INVENTORY

public function view_inventory(Request $request) {
  
 if ($request->ajax()) {
        $data = DB::table('inventory')
            ->join('products', 'inventory.product_ID', '=', 'products.product_ID')
            ->join('category', 'products.category_ID', '=', 'category.category_ID')
            ->join('product_status', 'inventory.status_ID', '=', 'product_status.status_ID')
            ->select([
                'inventory.*', 
                'products.product_name', 
                'category.category_name as name',
                'product_status.status_title'
            ]);
             return DataTables::of($data)
            ->addColumn('action', function($row){
                // We write the HTML for the button here
                return '<button class="btn btn-sm btn-outline-success edit-btn" 
                        data-id="'.$row->product_ID.'" 
                        data-name="'.$row->product_name.'" 
                        data-category="'.$row->name.'">
                        <i class="bi bi-pen"></i></button>';
            })
            ->rawColumns(['action']) // Tells Yajra to render HTML, not just text
            ->make(true);
    }
  
     $categories = DB::table('category')->orderBy('category_name', 'ASC')->get();      

    return view('inventory', compact( 'categories'));


}

public function getProductsByCategory($id) {

        $products = DB::table('products')
            ->where('category_ID', $id)
            ->get(['product_ID', 'product_name']);
            
        return response()->json($products);
}







public function update_subject(Request $request) {

    $update_subject = DB::table('schedules')
        ->where('teacher-id', $request->teacher_id)
        ->where('sub_id', '!=', $request->sub_id)
        ->count();


        if($update_subject >= 5) {
            return redirect()->back()->with('error', 'This teacher is already assigned to another subject.');
        }

    DB::table('schedules')
        ->where('sub_id', $request->sub_id)
        ->update([
            'sub_code' => $request->sub_code,
            'sub_name' => $request->sub_name,
            'teachers_id' => $request->teachers_id,
            'sub_date' => $request->sub_date,
            'sub_Stime' => $request->sub_Stime,
            'sub_Etime' => $request->sub_Etime,
        ]);
        $this->logActivity(
    'updated',
    'Updated subject ID ' . $request->sub_id . ' to ' . $request->sub_name
);
    return redirect()->back()->with('success', 'Subject updated successfully.');

}



        // TEACHERS


     public function teachers() {
        $teachers = DB::table('teacher')->get();
        return view('schedule', compact('schedule', 'teachers'));

     }

     public function subject() {
        $subject = DB::table('subject')->get();
        return view('schedule', compact('schedule', 'subject'));

     }




        // TEACHERS STATUS

     public function teacher_status() {
        $teacher_status = DB::table('teacher')
            ->join('status', 'status.status_id', '=', 'teacher.t_status')
             ->select(
            'teacher.name',
            'teacher.lastname',
            'teacher.age',
            'teacher.phone',
            'status.status_name'
        )
            // ->where('t_status', 1) // Assuming 1 indicates 'active' status
            ->get();

        return view('teachers', compact('teacher_status'));
    }





        
   public function save_student(Request $request){





    // 2. Save the new subject
   $save_student = DB::table('students')->insert([
        'student_firstname' => $request->student_firstname,
        'student_lastname' => $request-> student_lastname,

    ]);



    return redirect()->back()->with('success', 'Student added successfully!');


    }

     public function view_student() {


       $view_student = DB::table('students') ->get();





    return view('student', compact('view_student'));


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
