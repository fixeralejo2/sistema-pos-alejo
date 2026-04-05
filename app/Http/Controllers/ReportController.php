<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver reportes');
    }

    public function index()
    {
        return view('reports.index');
    }

    public function sales(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now()->endOfDay();
        $sales = Sale::with(['customer', 'user'])->whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereNotIn('status', ['cancelada', 'anulada'])->latest()->get();
        $totalRevenue = $sales->sum('total');
        $totalCount = $sales->count();
        $byPaymentMethod = $sales->groupBy('payment_method')->map(fn($g) => ['count' => $g->count(), 'total' => $g->sum('total')]);
        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('reports.sales-pdf', compact('sales', 'totalRevenue', 'totalCount', 'byPaymentMethod', 'dateFrom', 'dateTo'));
            return $pdf->download("reporte-ventas-{$dateFrom->format('Y-m-d')}.pdf");
        }
        return view('reports.sales', compact('sales', 'totalRevenue', 'totalCount', 'byPaymentMethod', 'dateFrom', 'dateTo'));
    }

    public function inventory(Request $request)
    {
        $products = Product::with(['variants', 'category'])->where('active', true)->get();
        $totalValue = $products->sum(fn($p) => $p->variants->sum(fn($v) => $v->stock * $p->cost));
        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('reports.inventory-pdf', compact('products', 'totalValue'));
            return $pdf->download("reporte-inventario.pdf");
        }
        return view('reports.inventory', compact('products', 'totalValue'));
    }

    public function topProducts(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now()->endOfDay();
        $products = SaleItem::with('product')
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->whereHas('sale', fn($q) => $q->whereBetween('created_at', [$dateFrom, $dateTo])->whereNotIn('status', ['cancelada', 'anulada']))
            ->groupBy('product_id')->orderByDesc('total_qty')->take(20)->get();
        return view('reports.top-products', compact('products', 'dateFrom', 'dateTo'));
    }
}
