@extends('layouts.app')
@section('header', 'Dashboard')
@section('main_content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner"><h3>$ {{ number_format($todaySales, 0, ',', '.') }}</h3><p>Ventas Hoy</p></div>
            <div class="icon"><i class="fas fa-shopping-cart"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner"><h3>$ {{ number_format($monthSales, 0, ',', '.') }}</h3><p>Ventas del Mes</p></div>
            <div class="icon"><i class="fas fa-chart-line"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner"><h3>{{ $totalProducts }}</h3><p>Productos Activos</p></div>
            <div class="icon"><i class="fas fa-box"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner"><h3>{{ $outOfStock }}</h3><p>Sin Stock</p></div>
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
        </div>
    </div>
</div>
@if(!$openRegister)
<div class="alert alert-warning">
    <i class="fas fa-cash-register"></i> No tienes caja abierta.
    @can('abrir caja')<a href="{{ route('cash-registers.create') }}" class="btn btn-sm btn-warning ml-2">Abrir Caja</a>@endcan
</div>
@endif
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Ventas Recientes</h3></div>
            <div class="card-body p-0">
                <table class="table table-sm">
                    <thead><tr><th>#</th><th>Cliente</th><th>Total</th><th>Método</th><th>Estado</th><th></th></tr></thead>
                    <tbody>
                    @foreach($recentSales as $sale)
                    <tr>
                        <td>{{ $sale->id }}</td>
                        <td>{{ $sale->customer?->name ?? 'Consumidor final' }}</td>
                        <td>$ {{ number_format($sale->total, 0, ',', '.') }}</td>
                        <td>{{ $sale->payment_method }}</td>
                        <td><span class="badge badge-{{ $sale->status === 'pagada' ? 'success' : ($sale->status === 'anulada' ? 'danger' : 'warning') }}">{{ $sale->status }}</span></td>
                        <td><a href="{{ route('sales.show', $sale) }}" class="btn btn-xs btn-info">Ver</a></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Top Productos</h3></div>
            <div class="card-body p-0">
                <table class="table table-sm">
                    <thead><tr><th>Producto</th><th>Vendidos</th></tr></thead>
                    <tbody>
                    @foreach($topProducts as $item)
                    <tr><td>{{ $item->product?->name }}</td><td>{{ $item->total_qty }}</td></tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
