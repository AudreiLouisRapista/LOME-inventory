<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StudentController;


/*
|--------------------------------------------------------------------------
| Web Routes
|---------------------------------------------*-
-----------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/index', function () {
    return view('welcome');
});

Route::get('/', [MainController::class, 'main'])->name('login');



Route::get('/logout', [MainController::class, 'logout'])->name('logout');
Route::post('/authenticate', [MainController::class, 'auth_user'])->name('auth_user');




Route::group(['prefix' => 'admin', 'middleware' => ['role:admin']], function () {
    // Place all admin routes here
    Route::get('/dashboard', [MainController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin_profile', [MainController::class, 'admin_profile'])->name('admin_profile');
    Route::get('/activity-log', [MainController::class, 'activityLogPage']);
    Route::get('/subject', [MainController::class, 'subject'])->name('subject');
    Route::get('/view_section', [MainController::class, 'view_section'])->name('view_section');
    Route::get('/view_subject', [MainController::class, 'view_subject'])->name('view_subject');
    Route::get('/view_student', [MainController::class, 'view_student'])->name('view_student');
    Route::get('/teachers', [MainController::class, 'teachers'])->name('teachers');
    Route::get('/view_products', [MainController::class, 'view_products'])->name('view_products');
    Route::get('/view_inventory', [MainController::class, 'view_inventory'])->name('view_inventory');
    Route::get('/pos_history', [MainController::class, 'pos_history'])->name('pos_history');
    Route::get('/product_report', [MainController::class, 'product_report'])->name('product_report');
    Route::get('/inventory_report', [MainController::class, 'inventory_report'])->name('inventory_report');
    Route::get('/teacher_status', [MainController::class, 'teacher_status'])->name('teacher_status');
    Route::get('/updateTeacherStatus', [MainController::class, 'updateTeacherStatus'])->name('updateTeacherStatus');
    Route::post('/save_product', [MainController::class, 'save_product'])->name('save_product');
    Route::get('/admin/add-product', [MainController::class, 'show_add_product_form'])->name('product.create');
    Route::post('/save_inventory', [MainController::class, 'save_inventory'])->name('save_inventory');
    Route::post('/save_subjects', [MainController::class, 'save_subjects'])->name('save_subjects');
    Route::post('/save_student', [MainController::class, 'save_student'])->name('save_student');
    Route::post('/save_section', [MainController::class, 'save_section'])->name('save_section');
    Route::post('/deact_teacher', [MainController::class, 'deact_teacher'])->name('deact_teacher');
    Route::post('/delete_schedule', [MainController::class, 'delete_schedule'])->name('delete_schedule');
    Route::post('/update_schedule', [MainController::class, 'update_schedule'])->name('update_schedule');
    Route::post('/update_product', [MainController::class, 'update_product'])->name('update_product');
    Route::post('/update_inventory', [MainController::class, 'update_inventory'])->name('update_inventory');
    Route::post('/update_section', [MainController::class, 'update_section'])->name('update_section');
    Route::post('/delete-schedule', [MainController::class, 'delete_schedule'])->name('delete_schedule');
    Route::post('/import-pos-sales', [MainController::class, 'import_pos_sales'])->name('import_pos_sales');

   Route::get('/get-products-by-category/{id}', [MainController::class, 'getProductsByCategory'])->name('get-products-by-category');
 

   // Add {id} to the URL
    Route::post('/admin-profile/{id}', [MainController::class, 'adminProfile'])->name('adminProfile');

    Route::post('/update_teacher/{teachers_id}', [MainController::class, 'update_teacher'])->name('update_teacher');
    Route::post('/system/set-schoolyear', [MainController::class, 'set_system_schoolyear'])->name('system.setSchoolYear');
});

// Worker Specific Routes
Route::group(['prefix' => 'Worker', 'middleware' => ['role:worker']], function () {
    
    // Main Dashboard for the Shop Worker
    Route::get('/workersDB', [MainController::class, 'workersDB'])->name('worker.workersDB');
    Route::get('/view_pig', [MainController::class, 'view_pig'])->name('view_pig');
    Route::post('/save_pig', [MainController::class, 'save_pig'])->name('save_pig');
    Route::post('/profile/update/{urs_id}', [MainController::class, 'Update_workerProfile'])->name('Update_workerProfile'); 

    // Example of a new route for managing tasks/orders
    // Route::get('/tasks', [MainController::class, 'WorkerTasks'])->name('worker.tasks');
}); 


Route::prefix('purchases')->group(function () {

    Route::get('/', [PurchaseController::class, 'index'])
        ->name('purchases.index');

    Route::get('/create', [PurchaseController::class, 'create'])
        ->name('purchases.create');

    Route::post('/', [PurchaseController::class, 'store'])
        ->name('purchases.store');
        
   
});

Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');