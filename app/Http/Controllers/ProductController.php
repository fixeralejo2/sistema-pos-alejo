<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver productos')->only(['index', 'show']);
        $this->middleware('permission:crear productos')->only(['create', 'store']);
        $this->middleware('permission:editar productos')->only(['edit', 'update']);
        $this->middleware('permission:eliminar productos')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants']);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->active !== null && $request->active !== '') {
            $query->where('active', $request->active);
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::where('active', true)->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('active', true)->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100|unique:products',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }
        $validated['active'] = $request->boolean('active', true);

        $product = Product::create($validated);

        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                if (!empty($variant['color']) || !empty($variant['material']) || !empty($variant['size'])) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'color' => $variant['color'] ?? null,
                        'material' => $variant['material'] ?? null,
                        'size' => $variant['size'] ?? null,
                        'stock' => $variant['stock'] ?? 0,
                        'barcode' => $variant['barcode'] ?? null,
                        'additional_price' => $variant['additional_price'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'Producto creado exitosamente.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'variants', 'saleItems.sale']);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('active', true)->get();
        $product->load('variants');
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100|unique:products,code,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }
        $validated['active'] = $request->boolean('active', true);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Producto eliminado exitosamente.');
    }

    public function search(Request $request)
    {
        $term = $request->get('q', '');
        $products = Product::with('variants')
            ->where('active', true)
            ->where(function($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('code', 'like', "%{$term}%");
            })
            ->whereHas('variants', fn($q) => $q->where('stock', '>', 0))
            ->take(10)
            ->get();

        return response()->json($products->map(function($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'code' => $p->code,
                'price' => $p->price,
                'variants' => $p->variants->map(fn($v) => [
                    'id' => $v->id,
                    'name' => $v->name,
                    'stock' => $v->stock,
                    'final_price' => $v->final_price,
                ]),
            ];
        }));
    }
}
