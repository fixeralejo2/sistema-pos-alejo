@extends('layouts.app')
@section('header', 'Detalle de Caja #' . $cashRegister->id)
@section('main_content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-cash-register"></i> Información de Caja</h5>
            </div>
            <div class="card-body">
                <p><strong>Usuario:</strong> {{ $cashRegister->user->name }}</p>
                <p><strong>Apertura:</strong> {{ $cashRegister->opened_at?->format('d/m/Y H:i') }}</p>
                <p><strong>Cierre:</strong> {{ $cashRegister->closed_at?->format('d/m/Y H:i') ?? 'Aún abierta' }}</p>
                <p><strong>Monto Inicial:</strong> $ {{ number_format($cashRegister->opening_amount, 0, ',', '.') }}</p>
                @if($cashRegister->closing_amount)
                <p><strong>Monto Final:</strong> $ {{ number_format($cashRegister->closing_amount, 0, ',', '.') }}</p>
                @endif
                <p>
                    <strong>Estado:</strong>
                    <span class="badge badge-{{ $cashRegister->status === 'open' ? 'success' : 'secondary' }}">
                        {{ $cashRegister->status === 'open' ? 'Abierta' : 'Cerrada' }}
                    </span>
                </p>
                @if($cashRegister->notes)
                <p><strong>Notas:</strong> {{ $cashRegister->notes }}</p>
                @endif
            </div>
            <div class="card-footer">
                @can('cerrar caja')
                    @if($cashRegister->status === 'open')
                    <a href="{{ route('cash-registers.close', $cashRegister) }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-lock"></i> Cerrar Caja
                    </a>
                    @endif
                @endcan
                <a href="{{ route('cash-registers.index') }}" class="btn btn-default btn-sm">Volver</a>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h5>Resumen por Método de Pago</h5></div>
            <div class="card-body p-0">
                <table class="table table-sm">
                    <thead><tr><th>Método</th><th>Ventas</th><th>Total</th></tr></thead>
                    <tbody>
                    @forelse($salesByMethod as $row)
                    <tr>
                        <td>{{ $row->payment_method }}</td>
                        <td>{{ $row->count }}</td>
                        <td>$ {{ number_format($row->total, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted">Sin ventas</td></tr>
                    @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-active">
                            <td><strong>TOTAL</strong></td>
                            <td></td>
                            <td><strong>$ {{ number_format($totalSales, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5>Ventas de esta Caja</h5></div>
            <div class="card-body p-0">
                <table class="table table-striped table-sm">
                    <thead><tr><th>#</th><th>Fecha</th><th>Cliente</th><th>Total</th><th>Método</th><th>Estado</th><th></th></tr></thead>
                    <tbody>
                    @forelse($cashRegister->sales as $sale)
                    <tr>
                        <td>{{ $sale->id }}</td>
                        <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $sale->customer?->name ?? 'Consumidor final' }}</td>
                        <td>$ {{ number_format($sale->total, 0, ',', '.') }}</td>
                        <td>{{ $sale->payment_method }}</td>
                        <td>
                            <span class="badge badge-{{ $sale->status === 'pagada' ? 'success' : ($sale->status === 'anulada' ? 'danger' : 'warning') }}">
                                {{ $sale->status }}
                            </span>
                        </td>
                        <td><a href="{{ route('sales.show', $sale) }}" class="btn btn-xs btn-info">Ver</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted">No hay ventas registradas en esta caja.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
