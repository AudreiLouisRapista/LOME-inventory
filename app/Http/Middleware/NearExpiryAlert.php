<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class NearExpiryAlert
{
    /**
     * Flash a SweetAlert payload when there are batches nearing expiry.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only run for normal page loads (avoid DataTables/AJAX/JSON endpoints).
        if ($request->ajax() || $request->expectsJson()) {
            return $next($request);
        }

        // Only for logged-in admins (this middleware is intended for admin routes).
        if (Session::get('user_role') !== 'admin') {
            return $next($request);
        }

        // If required tables/columns don't exist, do nothing (keeps it robust across environments).
        if (!Schema::hasTable('batches') || !Schema::hasTable('products')) {
            return $next($request);
        }

        if (!Schema::hasColumn('batches', 'expiration_date')) {
            return $next($request);
        }

        // batches table has inconsistent naming in this repo; detect the correct FK column.
        $batchProductColumn = null;
        if (Schema::hasColumn('batches', 'product_id')) {
            $batchProductColumn = 'product_id';
        } elseif (Schema::hasColumn('batches', 'product_ID')) {
            $batchProductColumn = 'product_ID';
        }

        if (!$batchProductColumn) {
            return $next($request);
        }

        $days = (int) (Config::get('inventory.near_expiry_days') ?? 7);
        if ($days <= 0) {
            $days = 7;
        }
        $today = now()->toDateString();
        $until = now()->addDays($days)->toDateString();

        // Count and sample list (top 10 soonest), per PRODUCT.
        // If batches has a quantity column, ignore empty batches.
        $base = DB::table('batches')
            ->join('products', 'batches.' . $batchProductColumn, '=', 'products.product_ID')
            ->whereNotNull('batches.expiration_date')
            ->whereBetween('batches.expiration_date', [$today, $until]);

        if (Schema::hasColumn('batches', 'quantity')) {
            $base->where('batches.quantity', '>', 0);
        }

        $count = (int) (clone $base)
            ->distinct('products.product_ID')
            ->count('products.product_ID');

        if ($count > 0) {
            $rows = (clone $base)
                ->selectRaw('products.product_ID, products.product_name, MIN(batches.expiration_date) as expiration_date')
                ->groupBy('products.product_ID', 'products.product_name')
                ->orderBy('expiration_date', 'asc')
                ->limit(10)
                ->get();

            $items = $rows->map(function ($row) {
                $expiry = $row->expiration_date ? (string) $row->expiration_date : null;
                $daysLeft = null;
                if ($expiry) {
                    try {
                        $daysLeft = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($expiry)->startOfDay(), false);
                    } catch (\Exception $e) {
                        $daysLeft = null;
                    }
                }

                return [
                    'name' => (string) $row->product_name,
                    'expiry' => $expiry,
                    'daysLeft' => $daysLeft,
                ];
            })->values()->all();

            // Only show again if something has changed (keeps it from popping on every click).
            $signature = md5($count . '|' . json_encode($items));
            if (Session::get('near_expiry_signature') !== $signature) {
                Session::flash('nearExpiry', [
                    'count' => $count,
                    'days' => $days,
                    'items' => $items,
                ]);
                Session::put('near_expiry_signature', $signature);
            }
        }

        return $next($request);
    }
}
