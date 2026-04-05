<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver inventario')->only(['index', 'movements']);
        $this->middleware('permission:gestionar inventario')->only(['adjust', 'doAdjust']);
    }

    public function index(Request $request)
    {
        $query = ProductVariant::with(['product.category'])->orderBy('stock');
        if ($request->search) $query->whereHas('product', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        if ($request->stock_filter === 'low') $query->where('stock', '<=', 5)->where('stock', '>', 0);
        elseif ($request->stock_filter === 'out') $query->where('stock', 0);
        $variants = $query->paginate(20)->withQueryString();
        return view('inventory.index', compact('variants'));
    }

    public function movements(Request $request)
    {
        $movements = InventoryMovement::with(['variant.product', 'user'])->latest()->paginate(20);
        return view('inventory.movements', compact('movements'));
    }

    public function adjust(ProductVariant $variant)
    {
        $variant->load('product');
        return view('inventory.adjust', compact('variant'));
    }

    public function doAdjust(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'type' => 'required|in:entrada,ajuste,merma',
            'quantity' => 'required|integer|min:1',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        if ($validated['type'] === 'entrada') { $variant->increment('stock', $validated['quantity']); $qty = $validated['quantity']; }
        elseif ($validated['type'] === 'merma') { $variant->decrement('stock', min($validated['quantity'], $variant->stock)); $qty = -$validated['quantity']; }
        else { $qty = $validated['quantity'] - $variant->stock; $variant->update(['stock' => $validated['quantity']]); }
        InventoryMovement::create(['product_variant_id' => $variant->id, 'type' => $validated['type'],
            'quantity' => $qty, 'cost' => $validated['cost'] ?? null, 'notes' => $validated['notes'] ?? null, 'user_id' => Auth::id()]);
        return redirect()->route('inventory.index')->with('success', 'Inventario ajustado exitosamente.');
    }
}
