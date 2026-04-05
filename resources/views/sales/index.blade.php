@extends('layouts.app')
@section('header', 'Ventas')
@section('main_content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <form method="GET" class="d-flex gap-2">
            <select name="status" class="form-control form-control-sm"><option value="">Estado</option><option value="pagada">Pagada</option><option value="abonada">Abonada</option><option value="anulada">Anulada</option></select>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            <button class="btn btn-sm btn-default">Filtrar</button>
        </form>
        @can('crear ventas')<a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nueva Venta</a>@endcan
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-sm">
            <thead><tr><th>#</th><th>Fecha</th><th>Cliente</th><th>Total</th><th>Método</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
            @foreach($sales as $s)
            <tr>
                <td>{{ $s->id }}</td>
                <td>{{ $s->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $s->customer?->name ?? 'Consumidor final' }}</td>
                <td>$ {{ number_format($s->total, 0, ',', '.') }}</td>
                <td>{{ $s->payment_method }}</td>
                <td><span class="badge badge-{{ $s->status === 'pagada' ? 'success' : ($s->status === 'anulada' ? 'danger' : 'warning') }}">{{ $s->status }}</span></td>
                <td>
                    <a href="{{ route('sales.show', $s) }}" class="btn btn-xs btn-info">Ver</a>
                    @can('anular ventas')@if(!in_array($s->status, ['anulada','cancelada']))<form method="POST" action="{{ route('sales.cancel', $s) }}" style="display:inline" onsubmit="return confirm('¿Anular venta?')">@csrf<button class="btn btn-xs btn-danger">Anular</button></form>@endif@endcan
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $sales->links() }}</div>
</div>
@endsection
