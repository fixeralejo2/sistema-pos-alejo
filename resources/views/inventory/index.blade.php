@extends('layouts.app')
@section('header', 'Inventario')
@section('main_content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm"
                placeholder="Buscar producto..." value="{{ request('search') }}">
            <select name="stock_filter" class="form-control form-control-sm">
                <option value="">Todo el stock</option>
                <option value="low" {{ request('stock_filter') === 'low' ? 'selected' : '' }}>Stock bajo (≤5)</option>
                <option value="out" {{ request('stock_filter') === 'out' ? 'selected' : '' }}>Sin stock</option>
            </select>
            <button class="btn btn-sm btn-default">Filtrar</button>
            <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-secondary">Limpiar</a>
        </form>
        <a href="{{ route('inventory.movements') }}" class="btn btn-info btn-sm">
            <i class="fas fa-history"></i> Movimientos
        </a>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Variante</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    @can('gestionar inventario')<th>Acciones</th>@endcan
                </tr>
            </thead>
            <tbody>
            @forelse($variants as $v)
            <tr class="{{ $v->stock == 0 ? 'table-danger' : ($v->stock <= 5 ? 'table-warning' : '') }}">
                <td>{{ $v->product->name }}</td>
                <td>{{ $v->product->category?->name ?? '-' }}</td>
                <td>{{ $v->name }}</td>
                <td><strong>{{ $v->stock }}</strong></td>
                <td>
                    @if($v->stock == 0)
                        <span class="badge badge-danger">Sin Stock</span>
                    @elseif($v->stock <= 5)
                        <span class="badge badge-warning">Stock Bajo</span>
                    @else
                        <span class="badge badge-success">OK</span>
                    @endif
                </td>
                @can('gestionar inventario')
                <td>
                    <a href="{{ route('inventory.adjust', $v) }}" class="btn btn-xs btn-warning">
                        <i class="fas fa-edit"></i> Ajustar
                    </a>
                </td>
                @endcan
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted">No se encontraron variantes.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $variants->links() }}</div>
</div>
@endsection
