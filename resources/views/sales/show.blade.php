@extends('layouts.app')
@section('header', 'Detalle Venta #{{ $sale->id }}')
@section('main_content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span>Venta #{{ $sale->id }} - {{ $sale->created_at->format('d/m/Y H:i') }}</span>
                <span class="badge badge-{{ $sale->status === 'pagada' ? 'success' : ($sale->status === 'anulada' ? 'danger' : 'warning') }} badge-lg">{{ strtoupper($sale->status) }}</span>
            </div>
            <div class="card-body">
                <p><strong>Cliente:</strong> {{ $sale->customer?->name ?? 'Consumidor final' }}</p>
                <p><strong>Vendedor:</strong> {{ $sale->user->name }}</p>
                <table class="table table-sm">
                    <thead><tr><th>Producto</th><th>Variante</th><th>Cant</th><th>Precio</th><th>Desc</th><th>Subtotal</th></tr></thead>
                    <tbody>
                    @foreach($sale->items as $item)
                    <tr>
                        <td>{{ $item->product?->name }}</td>
                        <td>{{ $item->variant?->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>$ {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td>$ {{ number_format($item->discount, 0, ',', '.') }}</td>
                        <td>$ {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr><td colspan="5" class="text-right"><strong>Subtotal:</strong></td><td>$ {{ number_format($sale->subtotal, 0, ',', '.') }}</td></tr>
                        <tr><td colspan="5" class="text-right"><strong>Descuento:</strong></td><td>$ {{ number_format($sale->discount, 0, ',', '.') }}</td></tr>
                        <tr><td colspan="5" class="text-right"><strong>Total:</strong></td><td><strong>$ {{ number_format($sale->total, 0, ',', '.') }}</strong></td></tr>
                        <tr><td colspan="5" class="text-right">Recibido:</td><td>$ {{ number_format($sale->payment_received, 0, ',', '.') }}</td></tr>
                        <tr><td colspan="5" class="text-right">Cambio:</td><td>$ {{ number_format($sale->change_given, 0, ',', '.') }}</td></tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-info btn-sm" target="_blank"><i class="fas fa-print"></i> Imprimir Recibo</a>
                <a href="{{ route('sales.ticket', $sale) }}" class="btn btn-secondary btn-sm" target="_blank"><i class="fas fa-receipt"></i> Ticket PDF</a>
                @can('anular ventas')@if(!in_array($sale->status, ['anulada','cancelada']))<form method="POST" action="{{ route('sales.cancel', $sale) }}" style="display:inline" onsubmit="return confirm('¿Anular venta?')">@csrf<button class="btn btn-danger btn-sm">Anular Venta</button></form>@endif@endcan
                <a href="{{ route('sales.index') }}" class="btn btn-default btn-sm">Volver</a>
            </div>
        </div>
    </div>
</div>
@endsection
