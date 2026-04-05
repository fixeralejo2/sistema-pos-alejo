<?php
namespace App\Http\Controllers;

use App\Models\Layaway;
use App\Models\LayawayPayment;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\CashRegister;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LayawayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver apartados')->only(['index', 'show']);
        $this->middleware('permission:crear apartados')->only(['create', 'store']);
        $this->middleware('permission:editar apartados')->only(['addPayment', 'complete', 'cancel']);
    }

    public function index(Request $request)
    {
        $query = Layaway::with(['customer', 'user', 'sale'])->latest();
        if ($request->status) $query->where('status', $request->status);
        $layaways = $query->paginate(15)->withQueryString();
        return view('layaways.index', compact('layaways'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::with('variants')->where('active', true)->get();
        $openRegister = CashRegister::where('user_id', Auth::id())->where('status', 'open')->latest()->first();
        return view('layaways.create', compact('customers', 'products', 'openRegister'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'initial_payment' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'due_date' => 'required|date|after:today',
        ]);
        DB::transaction(function () use ($request) {
            $subtotal = 0;
            foreach ($request->items as $item) $subtotal += $item['price'] * $item['quantity'];
            $minimumAmount = $subtotal * 0.30;
            if ($request->initial_payment < $minimumAmount)
                throw new \Exception("El abono mínimo es del 30%: $" . number_format($minimumAmount, 0, ',', '.'));
            $openRegister = CashRegister::where('user_id', Auth::id())->where('status', 'open')->latest()->first();
            $sale = Sale::create(['user_id' => Auth::id(), 'customer_id' => $request->customer_id,
                'cash_register_id' => $openRegister?->id, 'status' => 'abonada',
                'subtotal' => $subtotal, 'discount' => 0, 'total' => $subtotal,
                'payment_method' => $request->payment_method, 'payment_received' => $request->initial_payment,
                'change_given' => 0, 'notes' => 'Apartado']);
            foreach ($request->items as $item)
                SaleItem::create(['sale_id' => $sale->id, 'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'] ?? null, 'quantity' => $item['quantity'],
                    'unit_price' => $item['price'], 'discount' => 0, 'subtotal' => $item['price'] * $item['quantity']]);
            $layaway = Layaway::create(['customer_id' => $request->customer_id, 'user_id' => Auth::id(),
                'sale_id' => $sale->id, 'status' => 'activo', 'minimum_percent' => 30,
                'minimum_amount' => $minimumAmount, 'due_date' => $request->due_date, 'notes' => $request->notes]);
            LayawayPayment::create(['layaway_id' => $layaway->id, 'amount' => $request->initial_payment,
                'payment_method' => $request->payment_method, 'notes' => 'Abono inicial']);
            session(['last_layaway_id' => $layaway->id]);
        });
        return redirect()->route('layaways.show', session('last_layaway_id'))->with('success', 'Apartado creado exitosamente.');
    }

    public function show(Layaway $layaway)
    {
        $layaway->load(['customer', 'user', 'sale.items.product', 'layawayPayments']);
        return view('layaways.show', compact('layaway'));
    }

    public function addPayment(Request $request, Layaway $layaway)
    {
        $request->validate(['amount' => 'required|numeric|min:1', 'payment_method' => 'required|string', 'notes' => 'nullable|string']);
        $remaining = $layaway->remaining;
        $amount = min($request->amount, $remaining);
        DB::transaction(function () use ($layaway, $request, $amount) {
            LayawayPayment::create(['layaway_id' => $layaway->id, 'amount' => $amount,
                'payment_method' => $request->payment_method, 'notes' => $request->notes]);
            if ($layaway->remaining - $amount <= 0) {
                $layaway->update(['status' => 'completado']);
                if ($layaway->sale) {
                    $layaway->sale->update(['status' => 'pagada']);
                    foreach ($layaway->sale->items as $item) {
                        if ($item->product_variant_id) {
                            $variant = ProductVariant::find($item->product_variant_id);
                            if ($variant) {
                                $variant->decrement('stock', $item->quantity);
                                InventoryMovement::create(['product_variant_id' => $variant->id, 'type' => 'salida',
                                    'quantity' => -$item->quantity, 'notes' => "Apartado completado #{$layaway->id}", 'user_id' => Auth::id()]);
                            }
                        }
                    }
                }
            }
        });
        return redirect()->route('layaways.show', $layaway)->with('success', 'Abono registrado exitosamente.');
    }

    public function cancel(Layaway $layaway)
    {
        $layaway->update(['status' => 'cancelado']);
        if ($layaway->sale) $layaway->sale->update(['status' => 'cancelada']);
        return redirect()->route('layaways.index')->with('success', 'Apartado cancelado exitosamente.');
    }
}
