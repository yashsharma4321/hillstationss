<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Property;
use App\Models\Booking;
use App\Models\User;
use App\Models\Contact;
use App\Models\VendorWallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now   = Carbon::now();
        $start = $now->copy()->subDays(29)->startOfDay();

        // ── Core Stats ──────────────────────────────────────────────────────
        $totalRevenue        = Booking::where('payment_status', 'paid')->sum('final_amount');
        $totalCommission     = Booking::where('payment_status', 'paid')->sum('commission_amount');
        $totalVendorPayout   = Booking::where('payment_status', 'paid')->sum('vendor_amount');
        $totalGst            = Booking::where('payment_status', 'paid')->sum('gst_amount');
        $totalRefunded       = Booking::where('payment_status', 'refunded')->sum('final_amount');

        $thisMonthRevenue    = Booking::where('payment_status', 'paid')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('final_amount');

        $lastMonthRevenue    = Booking::where('payment_status', 'paid')
            ->whereMonth('created_at', $now->copy()->subMonth()->month)
            ->whereYear('created_at', $now->copy()->subMonth()->year)
            ->sum('final_amount');

        $revenueGrowth = $lastMonthRevenue > 0
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        $stats = [
            'total_vendors'          => Vendor::count(),
            'total_properties'       => Property::count(),
            'total_bookings'         => Booking::count(),
            'paid_bookings'          => Booking::where('payment_status', 'paid')->count(),
            'pending_bookings'       => Booking::where('payment_status', 'pending')->count(),
            'refunded_bookings'      => Booking::where('payment_status', 'refunded')->count(),
            'total_customers'        => User::where('role', 'customer')->count(),
            'pending_verifications'  => Vendor::where('kyc_status', 'pending')->count(),
            'pending_properties'     => Property::where('status', 'pending')->count(),
            'total_enquiries'        => class_exists(\App\Models\Contact::class) ? \App\Models\Contact::count() : 0,
            'total_revenue'          => $totalRevenue,
            'total_commission'       => $totalCommission,
            'total_vendor_payout'    => $totalVendorPayout,
            'total_gst'              => $totalGst,
            'total_refunded'         => $totalRefunded,
            'this_month_revenue'     => $thisMonthRevenue,
            'revenue_growth'         => $revenueGrowth,
        ];

        // ── Last 30 Days – Daily Bookings & Revenue ──────────────────────────
        $dailyData = Booking::where('created_at', '>=', $start)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as bookings'),
                DB::raw('SUM(CASE WHEN payment_status="paid" THEN final_amount ELSE 0 END) as revenue'),
                DB::raw('SUM(CASE WHEN payment_status="paid" THEN commission_amount ELSE 0 END) as commission'),
                DB::raw('SUM(CASE WHEN payment_status="paid" THEN vendor_amount ELSE 0 END) as vendor_payout')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill in missing dates with zeros
        $chartLabels      = [];
        $chartBookings    = [];
        $chartRevenue     = [];
        $chartCommission  = [];
        $chartVendor      = [];

        for ($d = $start->copy(); $d->lte($now); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $chartLabels[]     = $d->format('d M');
            $chartBookings[]   = $dailyData->get($key)?->bookings    ?? 0;
            $chartRevenue[]    = $dailyData->get($key)?->revenue      ?? 0;
            $chartCommission[] = $dailyData->get($key)?->commission   ?? 0;
            $chartVendor[]     = $dailyData->get($key)?->vendor_payout ?? 0;
        }

        // ── Last 12 Months – Monthly Bookings ───────────────────────────────
        $monthlyData = Booking::where('created_at', '>=', $now->copy()->subMonths(11)->startOfMonth())
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as bookings'),
                DB::raw('SUM(CASE WHEN payment_status="paid" THEN final_amount ELSE 0 END) as revenue')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get();

        $monthlyLabels   = [];
        $monthlyBookings = [];
        $monthlyRevenue  = [];

        for ($i = 11; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i);
            $monthlyLabels[] = $m->format('M Y');
            $row = $monthlyData->first(fn($r) => $r->year == $m->year && $r->month == $m->month);
            $monthlyBookings[] = $row ? (int)$row->bookings : 0;
            $monthlyRevenue[]  = $row ? (float)$row->revenue  : 0;
        }

        // ── Booking Status Distribution (Donut) ─────────────────────────────
        $statusDist = Booking::select('payment_status', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_status')->get()
            ->pluck('count', 'payment_status');

        // ── Top Vendors by Revenue ───────────────────────────────────────────
        $topVendors = Booking::where('payment_status', 'paid')
            ->select('vendor_id',
                DB::raw('SUM(final_amount) as total_revenue'),
                DB::raw('SUM(vendor_amount) as vendor_payout'),
                DB::raw('SUM(commission_amount) as admin_commission'),
                DB::raw('COUNT(*) as bookings')
            )
            ->with('vendor:id,business_name')
            ->groupBy('vendor_id')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        // ── Recent Bookings ──────────────────────────────────────────────────
        $recentBookings = Booking::with(['customer', 'property', 'vendor'])
            ->latest()->limit(6)->get();

        // ── Enquiries per month (last 6) ─────────────────────────────────────
        $enquiryLabels  = [];
        $enquiryData    = [];
        try {
            $enquiries = \App\Models\Contact::where('created_at', '>=', $now->copy()->subMonths(5)->startOfMonth())
                ->select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as cnt'))
                ->groupBy('year', 'month')->orderBy('year')->orderBy('month')->get();

            for ($i = 5; $i >= 0; $i--) {
                $m = $now->copy()->subMonths($i);
                $enquiryLabels[] = $m->format('M');
                $row = $enquiries->first(fn($r) => $r->year == $m->year && $r->month == $m->month);
                $enquiryData[] = $row ? (int)$row->cnt : 0;
            }
        } catch (\Exception $e) {
            for ($i = 5; $i >= 0; $i--) {
                $enquiryLabels[] = $now->copy()->subMonths($i)->format('M');
                $enquiryData[]   = 0;
            }
        }

        return view('admin.dashboard', compact(
            'stats',
            'chartLabels', 'chartBookings', 'chartRevenue', 'chartCommission', 'chartVendor',
            'monthlyLabels', 'monthlyBookings', 'monthlyRevenue',
            'statusDist',
            'topVendors',
            'recentBookings',
            'enquiryLabels', 'enquiryData'
        ));
    }
}
