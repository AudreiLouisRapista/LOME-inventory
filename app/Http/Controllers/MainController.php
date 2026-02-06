<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

    $admins = DB::table('admin')->get();

    return view('admin_profile', compact('admins','logs'));
}

 public function adminProfile(Request $request, $id) { 
    // 1. Get the current admin record to find the old image path
    $admin = DB::table('admin')->where('id', $id)->first();
    
    $updateData = [
        'email'  => $request->email,
        'name'   => $request->name,
        'gender' => $request->gender,
        'phone'  => $request->phone,
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
    DB::table('admin')->where('id', $id)->update($updateData);

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
    $view_schedule = DB::table('schedules')->count();
    // $view_subject = DB::table('subject')->count();

    // Count subjects per grade level
    $grade1Count = DB::table('schedules')->where('grade_id', '7')->count();
    $grade2Count = DB::table('schedules')->where('grade_id', '8')->count();
    $grade3Count = DB::table('schedules')->where('grade_id', '9')->count();
    $grade4Count = DB::table('schedules')->where('grade_id', '10')->count();
    $grade5Count = DB::table('schedules')->where('grade_id', '11')->count();
    $grade6Count = DB::table('schedules')->where('grade_id', '12')->count();

  //  $schoolYears = DB::table('school_year')->orderBy('schoolyear_name', 'desc')->get();



            $logs = ActivityLog::whereIn('action', ['added','updated','deleted'])
                   ->latest()
                   ->take(10)
                   ->get();

    

    return view('dashboard', compact(
        'totalWorkers',
        'grade1Count',
        'grade2Count',
        'grade3Count',
        'grade4Count',
        'grade5Count',
        'grade6Count',
       
        'logs',
        'view_schedule',
        // 'schoolYears'
        
    ));
    
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







    public function view_products() {
    // Get all records from 'teacher' table



    $view_products = DB::table('products')
        ->join('categories', 'products.category_id', '=', 'categories.category_id')
        ->select('products.*', 'categories.name as name')
        ->get();
    // 2. Fetch all categories so your dropdown doesn't throw "Undefined variable"
    $categories = DB::table('categories')->orderBy('name', 'ASC')->get();
           

    return view('products', compact('view_products', 'categories'));
}

public function save_product(Request $request)
{
//   dd($request->all(), $request->file());
    // 1. Capture Input Data
    $product_name = $request->input('product_name');
    $category = $request->input('category_id');
    $product_unit = $request->input('product_unit');
    $product_unit_type = $request->input('product_unit_type');
    $product_qts = $request->input('product_qts');

    

    // 2. Check if Product already exists
    $check_exist = DB::table('products')->where('product_name', $product_name)->exists();
    if ($check_exist) {
        return redirect()->back()->with('errorMessage', 'Product already exists');
    }

  
    // 4. Save to Database
    DB::table('products')->insert([
        'product_name'  => $product_name,
        'category_id'      => $category,
        'product_unit'     => $product_unit,
        'product_qts' => $product_qts,
        'product_unit_type' => $product_unit_type,
        
       
    ]);

    // 5. Create Activity Log
    $this->logActivity('added', 'Added Product for Name: ' . $product_name);
    // 6. Success Feedback
    session()->flash('save', 'Product added successfully!');
    return redirect()->back();
}






        // SAVE SUBJECTS


public function save_schedule(Request $request){
    // ... (Your request variable setup is here) ...
    $teacher_id = $request->teachers_id; 
    $subject_id = $request->subject_id;
    $section_id = $request->section_id;
    $newday = $request->days;
    $dayString = implode('-', $newday);
    $start = $request->sub_Stime;
    $end = $request->sub_Etime;
    $schoolyear_name = $request->schoolyear_id; 

    // Time validation and parsing
    try {
        $startTime = DateTime::createFromFormat('H:i', $start);
        $endTime = DateTime::createFromFormat('H:i', $end);
        if (!$startTime || !$endTime) {
            throw new Exception('Invalid format');
        }
    } catch (Exception $e) {
        return back()->with('error', 'Invalid time format. Please use HH:MM (24-hour).');
    }
    
    // Check if start is before end
    if ($startTime >= $endTime) {
        return back()->with('error', 'Start time must be before end time.');
    }

    // Step 1: Get the grade from the subject (Needed for Step 3)
    $grade_id = DB::table('subject')
        ->where('subject_id', $subject_id)
        ->value('grade_id');
    if (!$grade_id) {
        return back()->with('error', 'Subject not found. Please check your selection.');
    }


    // ----------------------------------------------------------------------
    // 💥 REORDERED CONFLICT CHECKS START HERE 💥
    // ----------------------------------------------------------------------


    // Step 2: HIGHEST PRIORITY - Check for an EXACT duplicate schedule (Same time, section, day)
    $duplicate = DB::table('schedules')
        ->where('section_id', $section_id)
        ->where('sub_date', $dayString)
        ->where('sub_Stime', $start) // Exact match on start time
        ->where('sub_Etime', $end)   // Exact match on end time
        ->exists();
    if ($duplicate) {
        // NEED EDIT THE NAME 
        return back()->with('error', 'Conflict! This exact schedule (Time, Section, and Days) already exists.');
    }


    // Step 3: Check for conflicts within the SAME SECTION (Time Overlap)
    // This is the correct logic for preventing students from being double-booked.
    $existingSectionSchedules = DB::table('schedules')
        ->where('section_id', $section_id) // Filter by the specific section
        ->get();
        
    foreach ($existingSectionSchedules as $sched) {
        $existingDay = explode('-', $sched->sub_date);
        $dayConflict = array_intersect($newday, $existingDay);
        
        if (!empty($dayConflict)) {
            try {
                // Robust Time Parsing (H:i:s is common in DB, H:i is from input)
                $existingStartTime = DateTime::createFromFormat('H:i:s', $sched->sub_Stime)
                                     ?: DateTime::createFromFormat('H:i', $sched->sub_Stime);
                $existingEndTime = DateTime::createFromFormat('H:i:s', $sched->sub_Etime)
                                   ?: DateTime::createFromFormat('H:i', $sched->sub_Etime);

                if (!$existingStartTime || !$existingEndTime) {
                    continue; // Skip invalid existing times
                }
            } catch (Exception $e) {
                continue; 
            }
            
            // Check for overlap: new start < existing end AND new end > existing start
            if ($startTime < $existingEndTime && $endTime > $existingStartTime) {
                return back()->with(
                    'error',
                    'Conflict! Section ' . $section_id . ' is already booked on ' . implode(', ', $dayConflict) . ' at this time.'
                );
            }
        }
    }
    

    // Step 4: Check for teacher conflicts (Time Overlap, only if assigned)
    if ($teacher_id && $teacher_id != "0") {
        $teacherSchedules = DB::table('schedules')
            ->where('teachers_id', $teacher_id)
            ->get();
            
        foreach ($teacherSchedules as $sched) {
            $existingDay = explode('-', $sched->sub_date);
            $dayConflict = array_intersect($newday, $existingDay);
            
            if (!empty($dayConflict)) {
                try {
                    // Robust Time Parsing
                    $existingStartTime = DateTime::createFromFormat('H:i:s', $sched->sub_Stime)
                                         ?: DateTime::createFromFormat('H:i', $sched->sub_Stime);
                    $existingEndTime = DateTime::createFromFormat('H:i:s', $sched->sub_Etime)
                                       ?: DateTime::createFromFormat('H:i', $sched->sub_Etime);
                                       
                    if (!$existingStartTime || !$existingEndTime) {
                        continue; 
                    }
                } catch (Exception $e) {
                    continue; 
                }
                
                if ($startTime < $existingEndTime && $endTime > $existingStartTime) {
                    return back()->with(
                        'error',
                        'Conflict! Teacher is busy on ' . implode(', ', $dayConflict) . ' at this time.'
                    );
                }
            }
        }
    }
    
    // ----------------------------------------------------------------------
    // 💥 CONFLICT CHECKS END HERE 💥
    // ----------------------------------------------------------------------

    // Step 5: Set status and save
    $sched_status = ($teacher_id && $teacher_id != "0") ? 1 : 0;
    
    // Handle school year (Remaining code unchanged)
    $existingYear = DB::table('school_year')
        // ... (rest of your school year logic) ...
        ->first();
    if (!$existingYear) {
        $schoolyear_ID = DB::table('school_year')->insertGetId([
            'schoolyear_name' => $schoolyear_name
        ]);
    } else {
        $schoolyear_ID = $existingYear->schoolyear_ID;
    }
    
    // Try to insert
 try {
    // 1. Check if a teacher is being assigned (avoid '0' or null)
    if ($teacher_id && $teacher_id != "0") {
        $scheduleCount = DB::table('schedules')
            ->where('teachers_id', $teacher_id)
            ->count();

        // 2. Maximum Limit Check
        if ($scheduleCount >= 5) {
            return back()->with('error', 'Limit Reached: This instructor already has 5 maximum schedules assigned.');
        }
    }

    // 3. Insert the Schedule
    DB::table('schedules')->insert([
        'subject_id'    => $subject_id,
        'section_id'    => $section_id,
        'grade_id'      => $grade_id,
        'teachers_id'   => $teacher_id ?: 0,
        'sub_date'      => $dayString,
        'sub_Stime'     => $start,
        'sub_Etime'     => $end,
        'schoolyear_id' => $schoolyear_ID,
        'sched_status'  => $sched_status,
    ]);

    // 4. Update Teacher Status ONLY if assigned
    if ($teacher_id && $teacher_id != "0") {
        DB::table('teacher')
            ->where('teachers_id', $teacher_id)
            ->update(['t_status' => 1]);
    }

    } catch (\Exception $e) {
        return back()->with('error', 'Failed to save schedule: ' . $e->getMessage());
    }

    // --- Success Logic (Outside the Try Block) ---

    // Fetch names for the activity log


    $subject = DB::table('subject')->where('subject_id', $subject_id)->first();
    $subject_name = $subject ? $subject->subject_name : 'unassigned';

    $teacher_data = DB::table('teacher')->where('teachers_id', $teacher_id)->first();
    $teacher_name = $teacher_data ? $teacher_data->name : 'Unassigned';

    $this->logActivity(
        'added',
        'Added schedule for teacher name ' . $teacher_name . ', subject name ' . $subject_name
    );

    session()->flash('save', 'Schedule saved successfully!');
    return redirect()->back();
}

  


public function update_schedule(Request $request) {
    $request->validate([
        'schedule_id'   => 'required',
        'subject_id'    => 'required|integer',
        'teachers_id'   => 'required|integer', // The NEW teacher ID
        'section_id'    => 'required|integer',
        'schoolyear_id' => 'required|integer',
        'days'          => 'required|array',
        'sub_Stime'     => 'required',
        'sub_Etime'     => 'required',
    ]);

    $schedule_id = $request->schedule_id;
    $new_teacher_id = $request->teachers_id;

    // 1. Get the OLD teacher ID before we perform the update
    $old_schedule = DB::table('schedules')->where('schedule_id', $schedule_id)->first();
    
    if (!$old_schedule) {
        return redirect()->back()->with('error', 'Schedule not found.');
    }

    $old_teacher_id = $old_schedule->teachers_id;

    // 2. Perform the update on the schedule
    $days = implode('-', $request->days);
    DB::table('schedules')
        ->where('schedule_id', $schedule_id)
        ->update([
            'subject_id'    => $request->subject_id,
            'teachers_id'   => $new_teacher_id,
            'section_id'    => $request->section_id,
            'sub_date'      => $days,
            'sub_Stime'     => $request->sub_Stime,
            'sub_Etime'     => $request->sub_Etime,
            'schoolyear_id' => $request->schoolyear_id,
        ]);

    // 3. Set the NEW teacher to 'Assigned' (1)
    DB::table('teacher')
        ->where('teachers_id', $new_teacher_id)
        ->update(['t_status' => 1]);

    // 4. Update the OLD teacher's status if the teacher was changed
    // We only need to check this if the ID actually changed
    if ($old_teacher_id && $old_teacher_id != $new_teacher_id) {
        
        // Count how many schedules the OLD teacher still has
        $remaining_count = DB::table('schedules')
            ->where('teachers_id', $old_teacher_id)
            ->count();

        // If they have no more schedules, set status to Unassigned (0)
        if ($remaining_count === 0) {
            DB::table('teacher')
                ->where('teachers_id', $old_teacher_id)
                ->update(['t_status' => 0]);
        }
    }

    // 5. Activity Logging
    $new_teacher = DB::table('teacher')->where('teachers_id', $new_teacher_id)->first();
    $teacher_name = $new_teacher ? ($new_teacher->name) : 'Unknown';

    $this->logActivity(
        'updated',
        'Updated schedule ID ' . $schedule_id . '. Assigned to: ' . $teacher_name
    );

    session()->flash('save', 'Schedule updated successfully!');
    return redirect()->back();
}

public function delete_schedule(Request $request) {
    $teacher_id = $request->teachers_id;
    $schedule_id = $request->schedule_id;

    // 1. Delete the schedule
    DB::table('schedules')->where('schedule_id', $schedule_id)->delete();

    // 2. Only proceed if there was a teacher assigned
    if ($teacher_id && $teacher_id != "0") {
        
        // Count remaining schedules for this teacher
        $remainingSchedules = DB::table('schedules')
            ->where('teachers_id', $teacher_id)
            ->count();

        // 3. Status Condition
        if ($remainingSchedules == 0) {
            // No more classes? Deactivate (hide from active list)
            DB::table('teacher')
                ->where('teachers_id', $teacher_id)
                ->update(['t_status' => 0]);
                
            session()->flash('deletschedule', 'Schedule deleted. Teacher has 0 classes left and is now unassigned.');
        } else {
            // Still has classes? Keep them active
            session()->flash('save', 'Schedule deleted. Teacher still has ' . $remainingSchedules . ' classes.');
        }
    } else {
        session()->flash('save', 'Schedule deleted successfully.');
    }

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
    
    return $pdf->setPaper('a4', 'portrait')->stream('Teacher_Load_'.$teacher->name.'.pdf');
}

   
        // VIEW SCHEDULES

public function view_schedule() {
    // 1. Fetch existing schedules for the table
    $view_schedule = DB::table('schedules')
        ->leftJoin('teacher', 'schedules.teachers_id', '=', 'teacher.teachers_id')
        ->leftJoin('grade_level', 'schedules.grade_id', '=', 'grade_level.grade_id')
        ->leftJoin('subject', 'schedules.subject_id', '=', 'subject.subject_id')
        ->leftJoin('status', 'status.status_id', '=', 'schedules.sched_status')
        ->leftJoin('section', 'schedules.section_id', '=', 'section.section_id')
        ->leftJoin('school_year', 'schedules.schoolyear_id', '=', 'school_year.schoolyear_ID')
        ->select(
            'schedules.*',
            'teacher.name as teacher_name',
            'grade_level.grade_title as grade_name',
            'subject.subject_name as sub_name',
            'section.section_name as sec_name',
            'status.status_name',
            'school_year.schoolyear_name'
        )
        ->get();

    // 2. Fetch Subjects JOINED with Grade Level for the Dropdown
    $subject = DB::table('subject')
        ->join('grade_level', 'subject.grade_id', '=', 'grade_level.grade_id')
        ->select('subject.subject_id', 'subject.subject_name', 'grade_level.grade_title as grade_name')
        ->get();

         $section = DB::table('section')
        ->join('grade_level', 'section.grade_id', '=', 'grade_level.grade_id')
        ->select('section.section_id', 'section.section_name', 'grade_level.grade_title as grade_name')
        ->get();

    // 3. Fetch other dropdown data
    $teachers = DB::table('teacher')
        ->whereRaw('(SELECT COUNT(*) FROM schedules WHERE schedules.teachers_id = teacher.teachers_id) < 5')
        ->get();

    $grade = DB::table('grade_level')->get();
    $school_year = DB::table('school_year')->get();

    return view('schedule', compact('view_schedule', 'subject', 'teachers', 'section', 'school_year', 'grade'));
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
