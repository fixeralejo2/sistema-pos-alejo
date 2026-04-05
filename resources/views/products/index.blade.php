@extends('layouts.app')
@section('header', 'Productos')
@section('main_content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar..." value="{{ request('search') }}">
            <select name="category_id" class="form-control form-control-sm"><option value="">Categoría</option>@foreach($categories as $c)<option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach</select>
            <button class="btn btn-sm btn-default">Filtrar</button>
            <a href="{{ route('products.index') }}" class="btn btn-sm btn-secondary">Limpiar</a>
        </form>
        @can('crear productos')<a href="{{ route('products.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nuevo</a>@endcan
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-sm">
            <thead><tr><th>#</th><th>Código</th><th>Nombre</th><th>Categoría</th><th>Precio</th><th>Stock Total</th><th>Activo</th><th>Acciones</th></tr></thead>
            <tbody>
            @foreach($products as $p)
            <tr>
                <td>{{ $p->id }}</td><td>{{ $p->code }}</td><td>{{ $p->name }}</td>
                <td>{{ $p->category?->name }}</td>
                <td>$ {{ number_format($p->price, 0, ',', '.') }}</td>
                <td>{{ $p->total_stock }}</td>
                <td><span class="badge badge-{{ $p->active ? 'success' : 'secondary' }}">{{ $p->active ? 'Sí' : 'No' }}</span></td>
                <td>
                    <a href="{{ route('products.show', $p) }}" class="btn btn-xs btn-info">Ver</a>
                    @can('editar productos')<a href="{{ route('products.edit', $p) }}" class="btn btn-xs btn-warning">Editar</a>@endcan
                    @can('eliminar productos')<form method="POST" action="{{ route('products.destroy', $p) }}" style="display:inline" onsubmit="return confirm('¿Eliminar?')">@csrf @method('DELETE')<button class="btn btn-xs btn-danger">Eliminar</button></form>@endcan
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $products->links() }}</div>
</div>
@endsection
