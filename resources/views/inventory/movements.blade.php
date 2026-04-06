@extends('layouts.app')
@section('header', 'Movimientos de Inventario')
@section('main_content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Historial de Movimientos</span>
        <a href="{{ route('inventory.index') }}" class="btn btn-default btn-sm">
            <i class="fas fa-arrow-left"></i> Volver a Inventario
        </a>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Variante</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Costo</th>
                    <th>Usuario</th>
                    <th>Notas</th>
                </tr>
            </thead>
            <tbody>
            @forelse($movements as $m)
            <tr>
                <td>{{ $m->id }}</td>
                <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $m->variant?->product?->name ?? '-' }}</td>
                <td>{{ $m->variant?->name ?? '-' }}</td>
                <td>
                    @php
                        $typeColors = [
                            'entrada'   => 'success',
                            'salida'    => 'danger',
                            'ajuste'    => 'warning',
                            'merma'     => 'danger',
                            'devolucion'=> 'info',
                        ];
                        $color = $typeColors[$m->type] ?? 'secondary';
                    @endphp
                    <span class="badge badge-{{ $color }}">{{ ucfirst($m->type) }}</span>
                </td>
                <td>
                    <span class="{{ $m->quantity >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $m->quantity >= 0 ? '+' : '' }}{{ $m->quantity }}
                    </span>
                </td>
                <td>{{ $m->cost ? '$ ' . number_format($m->cost, 0, ',', '.') : '-' }}</td>
                <td>{{ $m->user?->name ?? '-' }}</td>
                <td>{{ $m->notes ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center text-muted">No hay movimientos registrados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $movements->links() }}</div>
</div>
@endsection
