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
use Illuminate\Support\Facades\Schema;
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

    // POS sales table name is inconsistent in this project (posimportdata vs POSImportData).
    $posTable = null;
    if (Schema::hasTable('posimportdata')) {
        $posTable = 'posimportdata';
    } elseif (Schema::hasTable('POSImportData')) {
        $posTable = 'POSImportData';
    }

    $availableDates = $posTable
        ? DB::table($posTable)
            ->select(DB::raw('DATE(created_at) as date'))
            ->distinct()
            ->orderBy('date', 'desc')
            ->get()
        : collect();

    $logs = ActivityLog::latest()->take(10)->get();
   
            $logs = ActivityLog::whereIn('action', ['added','updated','deleted'])
                   ->latest()
                   ->take(10)
                   ->get();

        // Dashboard card stats should be computed from a single "current snapshot" per product.
        // Inventory can contain multiple rows per product, so we take the latest inventory_ID per product.
        $latestInventoryIds = DB::table('inventory')
            ->whereNull('deleted_at')
            ->selectRaw('MAX(inventory_ID) as inventory_ID, product_ID')
            ->groupBy('product_ID');

        $inventorySnapshot = DB::table('inventory as inv')
            ->joinSub($latestInventoryIds, 'latest', function ($join) {
                $join->on('inv.inventory_ID', '=', 'latest.inventory_ID');
            })
            ->join('products as p', 'inv.product_ID', '=', 'p.product_ID')
            ->whereNull('inv.deleted_at')
            ->whereNull('p.deleted_at');

        $totalProducts = DB::table('products')->whereNull('deleted_at')->count();
        $totalQuantity = (float) (clone $inventorySnapshot)->sum('inv.invt_remainingStock');
        $totalSold = (float) (clone $inventorySnapshot)->sum('inv.invt_totalSold');
        $instockProducts = (int) (clone $inventorySnapshot)->where('inv.status_ID', 1)->count();
        $lowStockProducts = (int) (clone $inventorySnapshot)->where('inv.status_ID', 2)->count();
        $outOfStock = (int) (clone $inventorySnapshot)->where('inv.status_ID', 3)->count();

        // Reorder Required (Dynamic)
        // Uses invt_StartingQuantity as the target level; falls back to products.tie_qty when starting quantity isn't set.
        $reorderRequiredRows = DB::table('inventory as inv')
            ->joinSub($latestInventoryIds, 'latest', function ($join) {
                $join->on('inv.inventory_ID', '=', 'latest.inventory_ID');
            })
            ->join('products as p', 'inv.product_ID', '=', 'p.product_ID')
            ->whereNull('inv.deleted_at')
            ->whereNull('p.deleted_at')
            ->select([
                'p.product_name',
                'inv.invt_remainingStock',
                'inv.invt_StartingQuantity',
                'p.tie_qty',
            ])
            ->get();

        $reorderRequired = $reorderRequiredRows
            ->map(function ($row) {
                $current = (int) ($row->invt_remainingStock ?? 0);
                $reorderTo = (int) ($row->invt_StartingQuantity ?? 0);
                if ($reorderTo <= 0) {
                    $reorderTo = (int) ($row->tie_qty ?? 0);
                }
                if ($reorderTo <= 0) {
                    return null;
                }

                $percent = (int) round(min(100, max(0, ($current / $reorderTo) * 100)));
                $needsReorder = $current < $reorderTo;
                if (!$needsReorder) {
                    return null;
                }

                $level = ($current <= 0 || $percent <= 25) ? 'urgent' : (($percent <= 60) ? 'soon' : 'soon');

                return [
                    'name' => $row->product_name,
                    'current' => $current,
                    'reorder_to' => $reorderTo,
                    'percent' => $percent,
                    'level' => $level,
                ];
            })
            ->filter()
            ->sortBy('percent')
            ->take(5)
            ->values();

        // Expiration Center (Dynamic)
        $itemsNearExpiry = 0;
        $criticalExpiryCount = 0;
        $warningExpiryCount = 0;
        $criticalExpiryItems = collect();
        $warningExpiryItems = collect();

        if (Schema::hasTable('batches')) {
            try {
                $today = \Carbon\Carbon::today();
                $critEnd = $today->copy()->addDays(7);
                $warnEnd = $today->copy()->addDays(30);

                $allNearExpiry = DB::table('batches')
                    ->join('products', 'batches.product_ID', '=', 'products.product_ID')
                    ->whereNull('products.deleted_at')
                    ->whereNotNull('batches.expiration_date')
                    ->whereDate('batches.expiration_date', '>=', $today)
                    ->whereDate('batches.expiration_date', '<=', $warnEnd)
                    ->select([
                        'batches.expiration_date',
                        'products.product_ID',
                        'products.product_name',
                    ])
                    ->orderBy('batches.expiration_date', 'asc')
                    ->get();

                $criticalExpiryItems = $allNearExpiry
                    ->filter(function ($row) use ($critEnd) {
                        return \Carbon\Carbon::parse($row->expiration_date)->lte($critEnd);
                    })
                    ->values();

                $warningExpiryItems = $allNearExpiry
                    ->filter(function ($row) use ($critEnd) {
                        return \Carbon\Carbon::parse($row->expiration_date)->gt($critEnd);
                    })
                    ->values();

                $criticalExpiryCount = $criticalExpiryItems->count();
                $warningExpiryCount = $warningExpiryItems->count();
                $itemsNearExpiry = $criticalExpiryCount + $warningExpiryCount;
            } catch (\Throwable $e) {
                // If the table exists but schema/columns differ, fall back to empty.
                $itemsNearExpiry = 0;
                $criticalExpiryCount = 0;
                $warningExpiryCount = 0;
                $criticalExpiryItems = collect();
                $warningExpiryItems = collect();
            }
        }

        $unpaidInvoiceTotal = (float) DB::table('purchases')
            ->whereNotIn('status', ['paid', 'Paid'])
            ->sum('net_amount');

        $totalStockPossible = $totalQuantity + $totalSold;
        $quantityPercent = ($totalStockPossible > 0)
            ? round(($totalQuantity / $totalStockPossible) * 100, 2)
            : 0;

        $importedData = $posTable
            ? DB::table($posTable)
                ->join('products', $posTable . '.product_ID', '=', 'products.product_ID')
                ->select('products.product_name', DB::raw('SUM(' . $posTable . '.TotalSalesPerQty) as TotalSalesPerQty'))
                ->groupBy('products.product_name')
            : DB::table('products')->select('products.product_name', DB::raw('0 as TotalSalesPerQty'))->whereRaw('1=0');
        if ($filter !== 'all' && !empty($filter)) {
            // If the user selects a specific date (e.g., 2026-03-10)
            if ($posTable) {
                $importedData->whereDate($posTable . '.created_at', $filter);
            }
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

    // Top Sales Product list (Top 5), percent is relative to the best seller
    $topSalesRaw = $allFilteredSales->sortByDesc('TotalSalesPerQty')->take(5)->values();
    $topSalesMax = (float) ($topSalesRaw->max('TotalSalesPerQty') ?? 0);
    $topSalesProducts = $topSalesRaw->map(function ($row) use ($topSalesMax) {
        $percent = $topSalesMax > 0
            ? (int) round(((float) $row->TotalSalesPerQty / $topSalesMax) * 100)
            : 0;

        return [
            'name' => $row->product_name,
            'percent' => $percent,
        ];
    });

    // 4. GET TOP 10 FOR CHART ONLY
    $chartData = $allFilteredSales->sortByDesc('TotalSalesPerQty')->take(10)->reverse();
    $labels = $chartData->pluck('product_name');
    $values = $chartData->pluck('TotalSalesPerQty');

        // 5. Expense vs Profit (Monthly) - Profit = Revenue - Expenses, last 6 months
        $monthsBack = 5;
        $startMonth = \Carbon\Carbon::now()->startOfMonth()->subMonths($monthsBack);
        $months = collect(range($monthsBack, 0))->map(function ($i) {
            return \Carbon\Carbon::now()->startOfMonth()->subMonths($i);
        });

        $monthKeys = $months->map(function ($d) {
            return $d->format('Y-m');
        })->all();
        $expenseProfitLabels = $months->map(function ($d) {
            return $d->format('M Y');
        })->all();

        // Revenue (Sales) by month
        $revenueRows = collect();
        if ($posTable) {
            $revenueRows = DB::table($posTable)
                ->selectRaw('YEAR(created_at) as y, MONTH(created_at) as m, SUM(TotalSalesPerQty) as total')
                ->whereBetween('created_at', [$startMonth->copy()->startOfDay(), \Carbon\Carbon::now()->endOfDay()])
                ->groupBy('y', 'm')
                ->get();
        }

        $revenueByMonth = [];
        foreach ($revenueRows as $row) {
            $key = sprintf('%04d-%02d', (int) $row->y, (int) $row->m);
            $revenueByMonth[$key] = (float) $row->total;
        }

        // Expenses by month: purchases invoice totals (net_amount)
        $expenseByMonth = [];
        if (Schema::hasTable('purchases')) {
            $expenseRows = DB::table('purchases')
                ->selectRaw('YEAR(created_at) as y, MONTH(created_at) as m, SUM(COALESCE(net_amount, 0)) as total')
                ->whereBetween('created_at', [$startMonth->copy()->startOfDay(), \Carbon\Carbon::now()->endOfDay()])
                ->groupBy('y', 'm')
                ->get();

            foreach ($expenseRows as $row) {
                $key = sprintf('%04d-%02d', (int) $row->y, (int) $row->m);
                $expenseByMonth[$key] = (float) $row->total;
            }
        }

        $revenueSeries = [];
        $expenseSeries = [];
        foreach ($monthKeys as $key) {
            $revenue = (float) ($revenueByMonth[$key] ?? 0);
            $expense = (float) ($expenseByMonth[$key] ?? 0);
            $revenueSeries[] = round($revenue, 2);
            $expenseSeries[] = round($expense, 2);
        }

        // 6. Monthly Inventory vs Sales (Last 6 months)
        $inventorySalesLabels = $expenseProfitLabels;

        $soldRows = $posTable
            ? DB::table($posTable)
            ->selectRaw('YEAR(created_at) as y, MONTH(created_at) as m, SUM(QuantitySold) as total')
            ->whereBetween('created_at', [$startMonth->copy()->startOfDay(), \Carbon\Carbon::now()->endOfDay()])
            ->groupBy('y', 'm')
            ->get()
            : collect();

        $soldByMonth = [];
        foreach ($soldRows as $row) {
            $key = sprintf('%04d-%02d', (int) $row->y, (int) $row->m);
            $soldByMonth[$key] = (float) $row->total;
        }

        $stockByMonth = [];
        if (Schema::hasTable('inventory_history')) {
            try {
                $stockRows = DB::table('inventory_history')
                    ->selectRaw('YEAR(snapshot_date) as y, MONTH(snapshot_date) as m, SUM(closing_stock) as total')
                    ->where('snapshot_date', '>=', $startMonth)
                    ->groupBy('y', 'm')
                    ->get();

                foreach ($stockRows as $row) {
                    $key = sprintf('%04d-%02d', (int) $row->y, (int) $row->m);
                    $stockByMonth[$key] = (float) $row->total;
                }
            } catch (\Throwable $e) {
                // If the table exists but schema/columns differ, fall back to current stock.
                $stockByMonth = [];
            }
        }

        $itemsSoldSeries = [];
        $stockLevelSeries = [];
        foreach ($monthKeys as $key) {
            $itemsSoldSeries[] = (int) round((float) ($soldByMonth[$key] ?? 0));
            $stockLevelSeries[] = (int) round((float) ($stockByMonth[$key] ?? $totalQuantity));
        }
    
    // AJAX Check
    if ($request->ajax()) {
        return response()->json([
            'labels' => $labels,
            'values' => $values,
            'totalSum' => $totalSum,
            'totalAverages' => $totalAverages,
            'bestSellerName' => $bestSellerName,
            'topSalesProducts' => $topSalesProducts,
        ]);
    }

    return view('dashboard', compact(
        'logs',
        'totalProducts',
        'totalQuantity',
        'totalSold',
        'instockProducts',
        'lowStockProducts',
        'outOfStock',
        'quantityPercent',
        'unpaidInvoiceTotal',
        'reorderRequired',
        'itemsNearExpiry',
        'criticalExpiryCount',
        'warningExpiryCount',
        'criticalExpiryItems',
        'warningExpiryItems',
        'totalSales',
        'totalStockPossible',
        'totalSum',
        'labels',
        'values',
        'expenseProfitLabels',
        'revenueSeries',
        'expenseSeries',
        'inventorySalesLabels',
        'itemsSoldSeries',
        'stockLevelSeries',
        'totalAverages',
        'topSalesProducts',
        'filter',
        'availableDates',
        'importedData',
        'allFilteredSales',
        'bestSellerName',
        'chartData',
        'bestSellerRecord'
    ));
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
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        return $disk->download($log->FilePath, $log->FileName);
    }

    return back()->with('error', 'File not found.');
}




public function inventory_report(Request $request)
{
    $period = (string) $request->query('period', 'this_month');
    $categoryId = (string) $request->query('category_id', 'all');

    // POS sales table name is inconsistent in this project (posimportdata vs POSImportData).
    // Pick whichever exists so reports don't silently show zero.
    $posTable = null;
    if (Schema::hasTable('posimportdata')) {
        $posTable = 'posimportdata';
    } elseif (Schema::hasTable('POSImportData')) {
        $posTable = 'POSImportData';
    }

    $categories = DB::table('category')
        ->orderBy('category_name', 'ASC')
        ->get();

    $now = \Carbon\Carbon::now();
    $start = null;
    $end = null;
    $prevStart = null;
    $prevEnd = null;

    if ($period === 'last_quarter') {
        // Previous full quarter
        $end = $now->copy()->firstOfQuarter()->subDay()->endOfDay();
        $start = $end->copy()->firstOfQuarter()->startOfDay();

        // Quarter before that (for % change)
        $prevEnd = $start->copy()->subDay()->endOfDay();
        $prevStart = $prevEnd->copy()->firstOfQuarter()->startOfDay();
    } else {
        // Default: this month
        $period = 'this_month';
        $start = $now->copy()->startOfMonth()->startOfDay();
        $end = $now->copy()->endOfDay();

        $prevStart = $start->copy()->subMonth()->startOfMonth()->startOfDay();
        $prevEnd = $prevStart->copy()->endOfMonth()->endOfDay();
    }

    $getRevenueAndCogs = function (\Carbon\Carbon $rangeStart, \Carbon\Carbon $rangeEnd) use ($categoryId, $posTable) {
        if (!$posTable) {
            return ['revenue' => 0.0, 'cogs' => 0.0];
        }

        // Revenue: sum of POS imported sales value for the range.
        $salesBase = DB::table($posTable . ' as pos')
            ->join('products', 'pos.product_ID', '=', 'products.product_ID')
            ->whereNull('products.deleted_at')
            ->whereBetween('pos.created_at', [$rangeStart, $rangeEnd]);

        if ($categoryId !== 'all' && $categoryId !== '') {
            $salesBase->where('products.category_ID', $categoryId);
        }

        $revenue = (float) (clone $salesBase)->sum('pos.TotalSalesPerQty');

        // COGS/Expenses source (requested): purchases + purchase_items within the range.
        // NOTE: purchases.invoice_date is a varchar in this DB; use purchases.created_at for reliable filtering.
        if (!Schema::hasTable('purchases') || !Schema::hasTable('purchase_items')) {
            return ['revenue' => $revenue, 'cogs' => 0.0];
        }

        $purchasesBase = DB::table('purchases as pur')
            ->whereBetween('pur.created_at', [$rangeStart, $rangeEnd]);

        if ($categoryId !== 'all' && $categoryId !== '') {
            $purchaseIds = DB::table('purchases as pur')
                ->join('purchase_items as pi', 'pur.purchase_id', '=', 'pi.purchase_id')
                ->join('products', 'pi.product_id', '=', 'products.product_ID')
                ->whereNull('products.deleted_at')
                ->whereBetween('pur.created_at', [$rangeStart, $rangeEnd])
                ->where('products.category_ID', $categoryId)
                ->select('pur.purchase_id')
                ->distinct();

            $purchasesBase->whereIn('pur.purchase_id', $purchaseIds);
        }

        // Total expenses = sum of invoice net_amount (matches DB's SUM(net_amount)).
        $cogs = (float) ((clone $purchasesBase)
            ->selectRaw('SUM(COALESCE(pur.net_amount, 0)) as total')
            ->value('total') ?? 0);

        return ['revenue' => $revenue, 'cogs' => $cogs];
    };

    $current = $getRevenueAndCogs($start, $end);
    $previous = $getRevenueAndCogs($prevStart, $prevEnd);

    $revenue = (float) ($current['revenue'] ?? 0);
    $cogs = (float) ($current['cogs'] ?? 0);
    $grossProfit = $revenue - $cogs;

    // No separate operating-expense table in this project yet; treat net profit as gross profit.
    $netProfit = $grossProfit;

    $grossMargin = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;
    $netMargin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;

    $revenuePrev = (float) ($previous['revenue'] ?? 0);
    $cogsPrev = (float) ($previous['cogs'] ?? 0);
    $netProfitPrev = ($revenuePrev - $cogsPrev);

    $changeBadge = function (float $cur, float $prev): array {
        // When previous period is zero, percent-change is undefined.
        // Show something user-friendly instead.
        if (abs($prev) < 0.0000001) {
            if (abs($cur) < 0.0000001) {
                return ['text' => '0.0%', 'style' => 'secondary'];
            }
            return ['text' => 'New', 'style' => 'success'];
        }

        $pct = (($cur - $prev) / $prev) * 100;
        $sign = $pct > 0 ? '+' : '';
        $text = $sign . number_format($pct, 1) . '%';

        $style = 'secondary';
        if ($pct > 0) {
            $style = 'success';
        } elseif ($pct < 0) {
            $style = 'danger';
        }

        return ['text' => $text, 'style' => $style];
    };

    $revenueBadge = $changeBadge($revenue, $revenuePrev);
    $profitBadge = $changeBadge($netProfit, $netProfitPrev);

    $formatPeso = function (float $amount): string {
        return '₱' . number_format($amount, 2);
    };

    $marginStatus = function (float $pct): array {
        if ($pct >= 40) {
            return ['text' => 'Healthy', 'style' => 'success'];
        }
        if ($pct >= 25) {
            return ['text' => 'Fair', 'style' => 'warning'];
        }
        return ['text' => 'Low', 'style' => 'danger'];
    };

    $grossMarginStatus = $marginStatus((float) $grossMargin);
    $netMarginStatus = $marginStatus((float) $netMargin);
    $netMarginStatus['text'] = $netMarginStatus['style'] === 'success' ? 'Strong' : $netMarginStatus['text'];

    // ----- Charts (Dynamic) -----
    // 1) Revenue & Profit trend (last 6 months ending at $end)
    $trendChart = [
        'labels' => [],
        'revenue' => [],
        'profit' => [],
    ];

    // 2) Revenue sources (category when viewing all; otherwise products within the selected category)
    $sourcesChart = [
        'labels' => [],
        'values' => [],
    ];

    if ($posTable && Schema::hasTable('products')) {
        $monthsBack = 5;
        $trendStart = $end->copy()->startOfMonth()->subMonths($monthsBack)->startOfDay();

        $months = collect(range($monthsBack, 0))->map(function ($i) use ($end) {
            return $end->copy()->startOfMonth()->subMonths($i);
        });

        $monthKeys = $months->map(function ($d) {
            return $d->format('Y-m');
        })->all();

        $trendChart['labels'] = $months->map(function ($d) {
            return $d->format('M Y');
        })->all();

	   $trendBase = DB::table($posTable . ' as pos')
            ->join('products', 'pos.product_ID', '=', 'products.product_ID')
            ->whereNull('products.deleted_at')
            ->whereBetween('pos.created_at', [$trendStart, $end]);

    if ($categoryId !== 'all' && $categoryId !== '') {
        $trendBase->where('products.category_ID', $categoryId);
    }

    $revenueRows = (clone $trendBase)
        ->selectRaw('YEAR(pos.created_at) as y, MONTH(pos.created_at) as m, SUM(pos.TotalSalesPerQty) as total')
        ->groupBy('y', 'm')
        ->get();

$revenueByMonth = [];
foreach ($revenueRows as $row) {
    $key = sprintf('%04d-%02d', (int) $row->y, (int) $row->m);
    $revenueByMonth[$key] = (float) $row->total;
}

        // Expenses by month from Purchases (sum purchases.net_amount)
        $cogsByMonth = [];
        if (Schema::hasTable('purchases') && Schema::hasTable('purchase_items')) {
            $purchasesBase = DB::table('purchases as pur')
                ->whereBetween('pur.created_at', [$trendStart, $end]);

            if ($categoryId !== 'all' && $categoryId !== '') {
                $purchaseIds = DB::table('purchases as pur')
                    ->join('purchase_items as pi', 'pur.purchase_id', '=', 'pi.purchase_id')
                    ->join('products', 'pi.product_id', '=', 'products.product_ID')
                    ->whereNull('products.deleted_at')
                    ->whereBetween('pur.created_at', [$trendStart, $end])
                    ->where('products.category_ID', $categoryId)
                    ->select('pur.purchase_id')
                    ->distinct();

                $purchasesBase->whereIn('pur.purchase_id', $purchaseIds);
            }

            $cogsRows = (clone $purchasesBase)
                ->selectRaw('YEAR(pur.created_at) as y, MONTH(pur.created_at) as m, SUM(COALESCE(pur.net_amount, 0)) as total')
                ->groupBy('y', 'm')
                ->get();

            foreach ($cogsRows as $row) {
                $key = sprintf('%04d-%02d', (int) $row->y, (int) $row->m);
                $cogsByMonth[$key] = (float) $row->total;
            }
        }

        foreach ($monthKeys as $key) {
            $rev = (float) ($revenueByMonth[$key] ?? 0);
            $cogsMonth = (float) ($cogsByMonth[$key] ?? 0);
            $trendChart['revenue'][] = round($rev, 2);
            $trendChart['profit'][] = round($rev - $cogsMonth, 2);
        }

        // Revenue sources for the selected range
        $sourceBase = DB::table($posTable . ' as pos')
            ->join('products', 'pos.product_ID', '=', 'products.product_ID')
            ->whereNull('products.deleted_at')
            ->whereBetween('pos.created_at', [$start, $end]);

        if ($categoryId !== 'all' && $categoryId !== '') {
            $sourceBase->where('products.category_ID', $categoryId);
        }

        $sourceRows = null;
        if ($categoryId === 'all' || $categoryId === '') {
            if (Schema::hasTable('category')) {
                $sourceRows = (clone $sourceBase)
                    ->join('category', 'products.category_ID', '=', 'category.category_ID')
                    ->selectRaw('category.category_name as label, SUM(pos.TotalSalesPerQty) as total')
                    ->groupBy('label')
                    ->orderByDesc('total')
                    ->get();
            }
        } else {
            $sourceRows = (clone $sourceBase)
                ->selectRaw('products.product_name as label, SUM(pos.TotalSalesPerQty) as total')
                ->groupBy('label')
                ->orderByDesc('total')
                ->get();
        }

        if ($sourceRows) {
            $sourceRows = $sourceRows
                ->filter(function ($r) {
                    return (float) $r->total > 0;
                })
                ->values();

            // Keep the doughnut chart simple (3 slices max): Top 2 + Other
            if ($sourceRows->count() > 3) {
                $top = $sourceRows->take(2);
                $otherSum = (float) $sourceRows->slice(2)->sum('total');
                $sourcesChart['labels'] = [
                    (string) $top[0]->label,
                    (string) $top[1]->label,
                    'Other',
                ];
                $sourcesChart['values'] = [
                    round((float) $top[0]->total, 2),
                    round((float) $top[1]->total, 2),
                    round($otherSum, 2),
                ];
            } else {
                $sourcesChart['labels'] = $sourceRows->pluck('label')->map(function ($v) {
                    return (string) $v;
                })->all();
                $sourcesChart['values'] = $sourceRows->pluck('total')->map(function ($v) {
                    return round((float) $v, 2);
                })->all();
            }
        }
    }

    // ----- Top Products by Profit (Dynamic) -----
   $topProductsByProfit = collect();
	if ($posTable && Schema::hasTable('products')) {
		$revenueRows = DB::table($posTable . ' as pos')
			->join('products', 'pos.product_ID', '=', 'products.product_ID')
			->whereNull('products.deleted_at')
			->whereBetween('pos.created_at', [$start, $end]);

		if ($categoryId !== 'all' && $categoryId !== '') {
			$revenueRows->where('products.category_ID', $categoryId);
		}

		$revenueRows = $revenueRows
			->selectRaw('products.product_ID, products.product_name, SUM(pos.TotalSalesPerQty) as revenue')
			->groupBy('products.product_ID', 'products.product_name')
			->get();

		$cogsByProduct = collect();
		if (Schema::hasTable('purchases') && Schema::hasTable('purchase_items')) {
			$cogsQuery = DB::table('purchases as pur')
				->join('purchase_items as pi', 'pur.purchase_id', '=', 'pi.purchase_id')
				->join('products', 'pi.product_id', '=', 'products.product_ID')
				->whereNull('products.deleted_at')
				->whereBetween('pur.created_at', [$start, $end]);

			if ($categoryId !== 'all' && $categoryId !== '') {
				$cogsQuery->where('products.category_ID', $categoryId);
			}

			$cogsByProduct = collect($cogsQuery
				->selectRaw('pi.product_id as product_ID, SUM(COALESCE(pi.total_price, 0)) as cogs')
				->groupBy('pi.product_id')
				->get())
				->keyBy('product_ID');
		}

        $topProductsByProfit = collect($revenueRows)
            ->map(function ($r) use ($cogsByProduct) {
                /** @var object $r */
                $revenue = (float) ($r->revenue ?? 0);
                $cogsRow = $cogsByProduct->get($r->product_ID);
                $cogs = (float) ($cogsRow->cogs ?? 0);
                $profit = $revenue - $cogs;
                $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

                return [
                    'name' => (string) ($r->product_name ?? ''),
                    'revenue' => round($revenue, 2),
                    'profit' => round($profit, 2),
                    'margin' => round($margin, 1),
                ];
            })
			->sortByDesc('profit')
			->values()
			->take(5);
	}

    // ----- Expense Breakdown (Dynamic) -----
    // This project does not have an operating-expense table yet, so we break down "expenses" as COGS.
$expenseTotal = 0.0;
$expenseBreakdown = collect();
if (Schema::hasTable('purchases') && Schema::hasTable('purchase_items') && Schema::hasTable('products')) {
    // Total expenses should match purchases.net_amount (invoice totals)
    $expensePurchasesBase = DB::table('purchases as pur')
        ->whereBetween('pur.created_at', [$start, $end]);

    if ($categoryId !== 'all' && $categoryId !== '') {
        $purchaseIds = DB::table('purchases as pur')
            ->join('purchase_items as pi', 'pur.purchase_id', '=', 'pi.purchase_id')
            ->join('products', 'pi.product_id', '=', 'products.product_ID')
            ->whereNull('products.deleted_at')
            ->whereBetween('pur.created_at', [$start, $end])
            ->where('products.category_ID', $categoryId)
            ->select('pur.purchase_id')
            ->distinct();

        $expensePurchasesBase->whereIn('pur.purchase_id', $purchaseIds);
    }

    $expenseTotal = (float) ((clone $expensePurchasesBase)
        ->selectRaw('SUM(COALESCE(pur.net_amount, 0)) as total')
        ->value('total') ?? 0);

    // Breakdown is derived from purchase_items totals, then scaled to match invoice net_amount.
    $expenseBase = DB::table('purchases as pur')
        ->join('purchase_items as pi', 'pur.purchase_id', '=', 'pi.purchase_id')
        ->join('products', 'pi.product_id', '=', 'products.product_ID')
        ->whereNull('products.deleted_at')
        ->whereBetween('pur.created_at', [$start, $end]);

    if ($categoryId !== 'all' && $categoryId !== '') {
        $expenseBase->where('products.category_ID', $categoryId);
    }

    $expenseRows = null;
    if ($categoryId === 'all' || $categoryId === '') {
        if (Schema::hasTable('category')) {
            $expenseRows = (clone $expenseBase)
                ->join('category', 'products.category_ID', '=', 'category.category_ID')
                ->selectRaw('category.category_name as label, SUM(COALESCE(pi.total_price, 0)) as total')
                ->groupBy('label')
                ->orderByDesc('total')
                ->get();
        } else {
            $expenseRows = (clone $expenseBase)
                ->selectRaw('products.product_name as label, SUM(COALESCE(pi.total_price, 0)) as total')
                ->groupBy('label')
                ->orderByDesc('total')
                ->get();
        }
    } else {
        $expenseRows = (clone $expenseBase)
            ->selectRaw('products.product_name as label, SUM(COALESCE(pi.total_price, 0)) as total')
            ->groupBy('label')
            ->orderByDesc('total')
            ->get();
    }

        if ($expenseRows) {
            $expenseRows = collect($expenseRows)
                ->filter(function ($r) {
                    /** @var object $r */
                    return (float) $r->total > 0;
                })
                ->values();

            $rawTotal = (float) $expenseRows->sum('total');
            $scale = ($rawTotal > 0 && $expenseTotal > 0) ? ($expenseTotal / $rawTotal) : 1.0;
            if ($scale !== 1.0) {
                $expenseRows = $expenseRows->map(function ($r) use ($scale) {
                    /** @var object $r */
                    $r->total = (float) $r->total * $scale;
                    return $r;
                });
            }

            // Keep list compact: Top 4 + Other
            if ($expenseRows->count() > 5) {
                $top = $expenseRows->take(4);
                $otherSum = (float) $expenseRows->slice(4)->sum('total');
                $expenseRows = $top;
                if ($otherSum > 0) {
                    $expenseRows = $expenseRows->concat([
                        (object) ['label' => 'Other', 'total' => $otherSum],
                    ]);
                }
            }

            $expenseBreakdown = $expenseRows->map(function ($r) use ($expenseTotal) {
                /** @var object $r */
                $total = (float) ($r->total ?? 0);
                $pct = $expenseTotal > 0 ? ($total / $expenseTotal) * 100 : 0;
                return [
                    'label' => (string) $r->label,
                    'total' => round($total, 2),
                    'percent' => (int) round($pct),
                ];
            });
        }
    }

    $stats = [
        [
            'label' => 'Total Revenue',
            'value' => $formatPeso($revenue),
            'badge' => $revenueBadge['text'],
            'badgeStyle' => $revenueBadge['style'],
            'icon' => 'currency-dollar',
            'color' => 'primary',
        ],
        [
            'label' => 'Net Profit',
            'value' => $formatPeso($netProfit),
            'badge' => $profitBadge['text'],
            'badgeStyle' => $profitBadge['style'],
            'icon' => 'wallet2',
            'color' => 'success',
        ],
        [
            'label' => 'Gross Margin',
            'value' => number_format($grossMargin, 1) . '%',
            'badge' => $grossMarginStatus['text'],
            'badgeStyle' => $grossMarginStatus['style'],
            'icon' => 'percent',
            'color' => 'purple',
        ],
        [
            'label' => 'Net Margin',
            'value' => number_format($netMargin, 1) . '%',
            'badge' => $netMarginStatus['text'],
            'badgeStyle' => $netMarginStatus['style'],
            'icon' => 'graph-up',
            'color' => 'orange',
        ],
    ];

    $viewData = compact(
        'stats',
        'trendChart',
        'sourcesChart',
        'topProductsByProfit',
        'expenseBreakdown',
        'expenseTotal',
        'categories',
        'period',
        'categoryId'
    );

    if ((string) $request->query('export') === 'pdf') {
        $fileName = 'financial-report-' . now()->format('Y-m-d_His') . '.pdf';
        return Pdf::loadView('reports.inventory_report_pdf', $viewData)->download($fileName);
    }

    return view('inventory_report', $viewData);
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
    // 1. Validation (CRITICAL for Professional Documentation)
    // This ensures no null values hit your database
    $request->validate([
        'product_name'  => 'required|string|max:255',
        'category_ID'   => 'required|integer',
        'perishable_ID' => 'required|integer',
        'tie_number'    => 'required|numeric|min:0',
        'tie_qty'       => 'required|numeric|min:0',
    ]);

    $product    = $request->product_name;
    $category   = $request->category_ID;
    $perishable = $request->perishable_ID;
    $tie_number = $request->tie_number;
    $tie_qty    = $request->tie_qty;

    // 2. Check for Duplicates (Excluding Soft-Deleted items)
    $duplicate = DB::table('products')
        ->where('category_ID', $category)
        ->where('product_name', $product)
        ->where('perishable_ID', $perishable)
        ->where('tie_number', $tie_number)
        ->where('tie_qty', $tie_qty)
        ->whereNull('deleted_at')
        ->exists();

    if ($duplicate) {
        return response()->json([
            'duplicate' => "The product '$product' with these specific bundle details already exists."
        ], 422); // 422 is the standard code for validation/logic errors
    }

    // 3. Execution with Transaction
    try {
        DB::transaction(function () use ($request, $product, $category, $perishable, $tie_number, $tie_qty) {
            // Insert Product
            DB::table('products')->insert([
                'product_name'  => $product,
                'category_ID'   => $category,
                'perishable_ID' => $perishable,
                'tie_number'    => $tie_number,
                'tie_qty'       => $tie_qty,
                'created_at'    => now(),
                'updated_at'    => now()
            ]);

            // Create Activity Log
            $this->logActivity('added', 'Added Product: ' . $product);
        });

        // 4. Success Response
        return response()->json([
            'save' => 'Product added successfully!'
        ], 200);

    } catch (\Exception $e) {
        // 5. Error Handling
        return response()->json([
            'error' => 'An error occurred while saving the product. Please try again.'
        ], 500);
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
        $categoryId = (string) $request->get('category_id', 'all');
        $inventoryHasDeletedAt = Schema::hasColumn('inventory', 'deleted_at');
        $productsHasDeletedAt = Schema::hasColumn('products', 'deleted_at');

        // When viewing ALL, aggregate by category.
        if ($categoryId === 'all' || $categoryId === '') {
            $query = DB::table('inventory')
                ->join('products', 'inventory.product_ID', '=', 'products.product_ID')
                ->join('category', 'products.category_ID', '=', 'category.category_ID')
                ->selectRaw(
                    'category.category_name as name, ' .
                        'COALESCE(SUM(inventory.invt_totalSold), 0) as sold, ' .
                        'COALESCE(SUM(inventory.invt_remainingStock), 0) as remaining'
                )
                ->groupBy('category.category_ID', 'category.category_name');

            if ($inventoryHasDeletedAt) {
                $query->whereNull('inventory.deleted_at');
            }
            if ($productsHasDeletedAt) {
                $query->whereNull('products.deleted_at');
            }

            return response()->json(
                $query->orderByDesc('sold')->orderByDesc('remaining')->limit(12)->get()
            );
        }

        // When a category is selected, aggregate by product within that category.
        $query = DB::table('inventory')
            ->join('products', 'inventory.product_ID', '=', 'products.product_ID')
            ->selectRaw(
                'products.product_name as name, ' .
                    'COALESCE(SUM(inventory.invt_totalSold), 0) as sold, ' .
                    'COALESCE(SUM(inventory.invt_remainingStock), 0) as remaining'
            )
            ->where('products.category_ID', $categoryId)
            ->groupBy('products.product_ID', 'products.product_name');

        if ($inventoryHasDeletedAt) {
            $query->whereNull('inventory.deleted_at');
        }
        if ($productsHasDeletedAt) {
            $query->whereNull('products.deleted_at');
        }

        return response()->json(
            $query->orderByDesc('sold')->orderByDesc('remaining')->limit(12)->get()
        );
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
    $inventoryHasDeletedAt = Schema::hasColumn('inventory', 'deleted_at');
    $inventoryHasCreatedAt = Schema::hasColumn('inventory', 'created_at');

    $inventoryBaseQuery = DB::table('inventory');
    if ($inventoryHasDeletedAt) {
        $inventoryBaseQuery->whereNull('deleted_at');
    }

    $totalProducts = (clone $inventoryBaseQuery)->count();
    $totalQuantity = (clone $inventoryBaseQuery)->sum('invt_remainingStock');
    $totalSold = (clone $inventoryBaseQuery)->sum('invt_totalSold');
    $instockProducts = (clone $inventoryBaseQuery)->where('status_ID', 1)->count();
    $lowStockProducts = (clone $inventoryBaseQuery)->where('status_ID', 2)->count();
    $outOfStock = (clone $inventoryBaseQuery)->where('status_ID', 3)->count();

    // Week-over-week deltas (dynamic, from DB)
    $now = \Carbon\Carbon::now();
    $weekStart = $now->copy()->subDays(7);
    $prevWeekStart = $now->copy()->subDays(14);

    $calcPercentChange = function ($currentValue, $previousValue): float {
        $current = (float) ($currentValue ?? 0);
        $previous = (float) ($previousValue ?? 0);

        if ($previous == 0.0) {
            return $current == 0.0 ? 0.0 : 100.0;
        }

        return round((($current - $previous) / abs($previous)) * 100, 1);
    };

    $direction = function (float $pct): string {
        return $pct < 0 ? 'down' : 'up';
    };

    // Total Products: compare current snapshot vs 7 days ago (requires inventory timestamps)
    $totalProductsLastWeek = $totalProducts;
    if ($inventoryHasCreatedAt) {
        $totalProductsLastWeekQuery = DB::table('inventory')->where('created_at', '<=', $weekStart);

        if ($inventoryHasDeletedAt) {
            $totalProductsLastWeekQuery->where(function ($q) use ($weekStart) {
                $q->whereNull('deleted_at')->orWhere('deleted_at', '>', $weekStart);
            });
        }

        $totalProductsLastWeek = $totalProductsLastWeekQuery->count();
    }
    $totalProductsPct = $calcPercentChange($totalProducts, $totalProductsLastWeek);

    // Stock snapshots + status snapshots from stock_movements if available
    $hasStockMovements = Schema::hasTable('stock_movements')
        && Schema::hasColumn('stock_movements', 'StockMovementID')
        && Schema::hasColumn('stock_movements', 'Product_ID')
        && Schema::hasColumn('stock_movements', 'Balance_After')
        && Schema::hasColumn('stock_movements', 'MovementType')
        && Schema::hasColumn('stock_movements', 'Quantity')
        && Schema::hasColumn('stock_movements', 'created_at');

    $stockSnapshot = function (\Carbon\Carbon $cutoff) use ($hasStockMovements) {
        if (!$hasStockMovements) {
            return [
                'available' => null,
                'low' => null,
                'out' => null,
            ];
        }

        $latestIdsPerProduct = DB::table('stock_movements')
            ->select('Product_ID', DB::raw('MAX(StockMovementID) as max_id'))
            ->where('created_at', '<=', $cutoff)
            ->groupBy('Product_ID');

        $latestAtCutoff = DB::table('stock_movements as sm')
            ->joinSub($latestIdsPerProduct, 'latest', function ($join) {
                $join->on('sm.StockMovementID', '=', 'latest.max_id');
            });

        $available = (clone $latestAtCutoff)->sum('sm.Balance_After');
        $counts = (clone $latestAtCutoff)
            ->selectRaw('SUM(CASE WHEN sm.Balance_After > 0 AND sm.Balance_After <= 5 THEN 1 ELSE 0 END) as low_count')
            ->selectRaw('SUM(CASE WHEN sm.Balance_After <= 0 THEN 1 ELSE 0 END) as out_count')
            ->first();

        return [
            'available' => (int) $available,
            'low' => (int) ($counts->low_count ?? 0),
            'out' => (int) ($counts->out_count ?? 0),
        ];
    };

    $stockNow = $stockSnapshot($now);
    $stockLastWeek = $stockSnapshot($weekStart);
    $availableNow = $stockNow['available'] ?? $totalQuantity;
    $availableLastWeek = $stockLastWeek['available'] ?? $totalQuantity;
    $availableStockPct = $calcPercentChange($availableNow, $availableLastWeek);

    $lowNow = $stockNow['low'] ?? $lowStockProducts;
    $lowLastWeek = $stockLastWeek['low'] ?? $lowStockProducts;
    $lowStockPct = $calcPercentChange($lowNow, $lowLastWeek);

    $outNow = $stockNow['out'] ?? $outOfStock;
    $outLastWeek = $stockLastWeek['out'] ?? $outOfStock;
    $outOfStockPct = $calcPercentChange($outNow, $outLastWeek);

    // Total Sold: compare sold in last 7 days vs the 7 days before that
    $totalSoldPct = 0.0;
    if ($hasStockMovements) {
        $soldThisWeek = DB::table('stock_movements')
            ->where('MovementType', 'OUT')
            ->whereBetween('created_at', [$weekStart, $now])
            ->sum('Quantity');
        $soldPrevWeek = DB::table('stock_movements')
            ->where('MovementType', 'OUT')
            ->whereBetween('created_at', [$prevWeekStart, $weekStart])
            ->sum('Quantity');

        $totalSoldPct = $calcPercentChange($soldThisWeek, $soldPrevWeek);
    }

    $cardDeltas = [
        'totalProducts' => ['pct' => $totalProductsPct, 'dir' => $direction($totalProductsPct)],
        'availableStock' => ['pct' => $availableStockPct, 'dir' => $direction($availableStockPct)],
        'lowStock' => ['pct' => $lowStockPct, 'dir' => $direction($lowStockPct)],
        'outOfStock' => ['pct' => $outOfStockPct, 'dir' => $direction($outOfStockPct)],
        'totalSold' => ['pct' => $totalSoldPct, 'dir' => $direction($totalSoldPct)],
    ];

    return view('inventory', compact(
        'categories',
        'totalProducts',
        'instockProducts',
        'lowStockProducts',
        'outOfStock',
        'totalQuantity',
        'selectedCategory',
        'products',
        'totalSold',
        'cardDeltas'
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

        // STEP 2: Loop through each row submitted from the table
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
    $transferCount = 0;
    $adjustmentCount = 0;

    if (
        Schema::hasTable('stock_movements')
        && Schema::hasColumn('stock_movements', 'MovementType')
    ) {
        $transferCount = (int) DB::table('stock_movements')
            ->where('MovementType', 'RETURN')
            ->count();

        $adjustmentCount = (int) DB::table('stock_movements')
            ->where('MovementType', 'ADJUSTMENT')
            ->count();
    }

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

    return view('stockMovement', compact('movements', 'recentIn', 'recentOut', 'transferCount', 'adjustmentCount'));
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
