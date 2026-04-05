<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Customer;
use App\Models\CashRegister;
use App\Models\Payment;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver ventas')->only(['index', 'show']);
        $this->middleware('permission:crear ventas')->only(['create', 'store']);
        $this->middleware('permission:anular ventas')->only(['cancel']);
    }

    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'user'])->latest();
        if ($request->status) $query->where('status', $request->status);
        if ($request->date_from) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->date_to) $query->whereDate('created_at', '<=', $request->date_to);
        if ($request->customer_id) $query->where('customer_id', $request->customer_id);
        $sales = $query->paginate(15)->withQueryString();
        $customers = Customer::orderBy('name')->get();
        return view('sales.index', compact('sales', 'customers'));
    }

    public function create()
    {
        $openRegister = CashRegister::where('user_id', Auth::id())->where('status', 'open')->latest()->first();
        $customers = Customer::orderBy('name')->get();
        $categories = \App\Models\Category::where('active', true)->get();
        return view('sales.create', compact('openRegister', 'customers', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            'payment_received' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        DB::transaction(function () use ($request) {
            $subtotal = 0; $totalDiscount = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['price'] * $item['quantity'];
                $totalDiscount += ($item['discount'] ?? 0) * $item['quantity'];
            }
            $total = $subtotal - $totalDiscount;
            $changeGiven = max(0, $request->payment_received - $total);
            $openRegister = CashRegister::where('user_id', Auth::id())->where('status', 'open')->latest()->first();
            $sale = Sale::create([
                'user_id' => Auth::id(), 'customer_id' => $request->customer_id,
                'cash_register_id' => $openRegister?->id, 'status' => 'pagada',
                'subtotal' => $subtotal, 'discount' => $totalDiscount, 'total' => $total,
                'payment_method' => $request->payment_method,
                'payment_received' => $request->payment_received, 'change_given' => $changeGiven,
                'notes' => $request->notes,
            ]);
            foreach ($request->items as $item) {
                $qty = $item['quantity']; $price = $item['price']; $discount = $item['discount'] ?? 0;
                SaleItem::create(['sale_id' => $sale->id, 'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'] ?? null, 'quantity' => $qty,
                    'unit_price' => $price, 'discount' => $discount, 'subtotal' => ($price - $discount) * $qty]);
                if (!empty($item['variant_id'])) {
                    $variant = ProductVariant::find($item['variant_id']);
                    if ($variant) {
                        if ($variant->stock < $qty) throw new \Exception("Stock insuficiente para {$variant->product->name}");
                        $variant->decrement('stock', $qty);
                        InventoryMovement::create(['product_variant_id' => $variant->id, 'type' => 'salida',
                            'quantity' => -$qty, 'notes' => "Venta #{$sale->id}", 'user_id' => Auth::id()]);
                    }
                }
            }
            Payment::create(['sale_id' => $sale->id, 'payment_method' => $request->payment_method,
                'amount' => $request->payment_received, 'notes' => null]);
            session(['last_sale_id' => $sale->id]);
        });

        return redirect()->route('sales.show', session('last_sale_id'))->with('success', 'Venta registrada exitosamente.');
    }

    public function show(Sale $sale)
    {
        $sale->load(['items.product', 'items.variant', 'customer', 'user', 'payments']);
        return view('sales.show', compact('sale'));
    }

    public function cancel(Sale $sale)
    {
        if (in_array($sale->status, ['cancelada', 'anulada']))
            return redirect()->back()->with('warning', 'Esta venta ya está cancelada/anulada.');
        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                if ($item->product_variant_id) {
                    $variant = ProductVariant::find($item->product_variant_id);
                    if ($variant) {
                        $variant->increment('stock', $item->quantity);
                        InventoryMovement::create(['product_variant_id' => $variant->id, 'type' => 'devolucion',
                            'quantity' => $item->quantity, 'notes' => "Anulación venta #{$sale->id}", 'user_id' => Auth::id()]);
                    }
                }
            }
            $sale->update(['status' => 'anulada']);
        });
        return redirect()->route('sales.index')->with('success', 'Venta anulada exitosamente.');
    }

    public function ticket(Sale $sale)
    {
        $sale->load(['items.product', 'items.variant', 'customer', 'user']);
        $pdf = Pdf::loadView('sales.ticket', compact('sale'))->setPaper([0, 0, 226.77, 800], 'portrait');
        return $pdf->stream("ticket-{$sale->id}.pdf");
    }

    public function receipt(Sale $sale)
    {
        $sale->load(['items.product', 'items.variant', 'customer', 'user']);
        return view('sales.receipt', compact('sale'));
    }
}
