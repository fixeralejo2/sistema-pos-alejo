<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\CashRegister;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $today = Carbon::today();

        $todaySales = Sale::whereDate('created_at', $today)
            ->whereNotIn('status', ['cancelada', 'anulada'])
            ->sum('total');

        $todayCount = Sale::whereDate('created_at', $today)
            ->whereNotIn('status', ['cancelada', 'anulada'])
            ->count();

        $monthSales = Sale::whereMonth('created_at', $today->month)
            ->whereYear('created_at', $today->year)
            ->whereNotIn('status', ['cancelada', 'anulada'])
            ->sum('total');

        $totalProducts = Product::where('active', true)->count();
        $totalCustomers = Customer::count();
        $lowStock = \App\Models\ProductVariant::where('stock', '<=', 5)->where('stock', '>', 0)->count();
        $outOfStock = \App\Models\ProductVariant::where('stock', 0)->count();

        $openRegister = CashRegister::where('user_id', Auth::id())
            ->where('status', 'open')
            ->latest()
            ->first();

        $recentSales = Sale::with(['customer', 'user'])
            ->latest()
            ->take(10)
            ->get();

        $topProducts = \App\Models\SaleItem::with('product')
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->whereHas('sale', fn($q) => $q->whereNotIn('status', ['cancelada', 'anulada']))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'todaySales', 'todayCount', 'monthSales',
            'totalProducts', 'totalCustomers', 'lowStock', 'outOfStock',
            'openRegister', 'recentSales', 'topProducts'
        ));
    }
}
