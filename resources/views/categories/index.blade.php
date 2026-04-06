@extends('layouts.app')
@section('header', 'Categorías')
@section('main_content')
<div class="card">
    <div class="card-header">
        @can('crear categorias')<a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nueva Categoría</a>@endcan
    </div>
    <div class="card-body p-0">
        <table class="table table-striped">
            <thead><tr><th>#</th><th>Nombre</th><th>Descripción</th><th>Productos</th><th>Activa</th><th>Acciones</th></tr></thead>
            <tbody>
            @foreach($categories as $cat)
            <tr>
                <td>{{ $cat->id }}</td><td>{{ $cat->name }}</td><td>{{ $cat->description }}</td>
                <td>{{ $cat->products_count }}</td>
                <td><span class="badge badge-{{ $cat->active ? 'success' : 'secondary' }}">{{ $cat->active ? 'Sí' : 'No' }}</span></td>
                <td>
                    @can('editar categorias')<a href="{{ route('categories.edit', $cat) }}" class="btn btn-xs btn-warning">Editar</a>@endcan
                    @can('eliminar categorias')
                    <form method="POST" action="{{ route('categories.destroy', $cat) }}" style="display:inline" onsubmit="return confirm('¿Eliminar?')">
                        @csrf @method('DELETE') <button class="btn btn-xs btn-danger">Eliminar</button>
                    </form>@endcan
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $categories->links() }}</div>
</div>
@endsection
