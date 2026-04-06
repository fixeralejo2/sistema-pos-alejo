@extends('layouts.app')
@section('header', 'Detalle Producto')
@section('main_content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>{{ $product->name }}</h5></div>
            <div class="card-body">
                @if($product->image)<img src="{{ asset('storage/'.$product->image) }}" class="img-fluid mb-2" style="max-height:150px">@endif
                <p><strong>Código:</strong> {{ $product->code ?? 'N/A' }}</p>
                <p><strong>Categoría:</strong> {{ $product->category?->name ?? 'N/A' }}</p>
                <p><strong>Costo:</strong> $ {{ number_format($product->cost, 0, ',', '.') }}</p>
                <p><strong>Precio:</strong> $ {{ number_format($product->price, 0, ',', '.') }}</p>
                <p><strong>Stock Total:</strong> {{ $product->total_stock }}</p>
                <p><strong>Estado:</strong> <span class="badge badge-{{ $product->active ? 'success' : 'secondary' }}">{{ $product->active ? 'Activo' : 'Inactivo' }}</span></p>
            </div>
            <div class="card-footer"><a href="{{ route('products.edit', $product) }}" class="btn btn-warning">Editar</a></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Variantes</h5></div>
            <div class="card-body p-0">
                <table class="table table-sm">
                    <thead><tr><th>Variante</th><th>Stock</th><th>Precio Final</th><th>Código Barras</th></tr></thead>
                    <tbody>
                    @foreach($product->variants as $v)
                    <tr><td>{{ $v->name }}</td><td>{{ $v->stock }}</td><td>$ {{ number_format($v->final_price, 0, ',', '.') }}</td><td>{{ $v->barcode }}</td></tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
