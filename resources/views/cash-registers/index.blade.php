@extends('layouts.app')
@section('header', 'Cajas Registradoras')
@section('main_content')
@if($openRegister)
<div class="alert alert-success">
    <i class="fas fa-cash-register"></i> Tienes una caja abierta desde {{ $openRegister->opened_at->format('d/m/Y H:i') }}.
    <a href="{{ route('cash-registers.show', $openRegister) }}" class="btn btn-sm btn-success ml-2">Ver Caja</a>
    @can('cerrar caja')<a href="{{ route('cash-registers.close', $openRegister) }}" class="btn btn-sm btn-danger ml-1">Cerrar Caja</a>@endcan
</div>
@endif
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Historial de Cajas</span>
        @can('abrir caja')
            @if(!$openRegister)
                <a href="{{ route('cash-registers.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Abrir Caja</a>
            @endif
        @endcan
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Usuario</th>
                    <th>Apertura</th>
                    <th>Cierre</th>
                    <th>Monto Inicial</th>
                    <th>Monto Final</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($registers as $reg)
            <tr>
                <td>{{ $reg->id }}</td>
                <td>{{ $reg->user->name }}</td>
                <td>{{ $reg->opened_at?->format('d/m/Y H:i') }}</td>
                <td>{{ $reg->closed_at?->format('d/m/Y H:i') ?? '-' }}</td>
                <td>$ {{ number_format($reg->opening_amount, 0, ',', '.') }}</td>
                <td>{{ $reg->closing_amount ? '$ ' . number_format($reg->closing_amount, 0, ',', '.') : '-' }}</td>
                <td>
                    <span class="badge badge-{{ $reg->status === 'open' ? 'success' : 'secondary' }}">
                        {{ $reg->status === 'open' ? 'Abierta' : 'Cerrada' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('cash-registers.show', $reg) }}" class="btn btn-xs btn-info">Ver</a>
                    @can('cerrar caja')
                        @if($reg->status === 'open')
                            <a href="{{ route('cash-registers.close', $reg) }}" class="btn btn-xs btn-danger">Cerrar</a>
                        @endif
                    @endcan
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted">No hay cajas registradas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $registers->links() }}</div>
</div>
@endsection
