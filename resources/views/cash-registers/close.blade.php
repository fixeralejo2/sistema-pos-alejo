@extends('layouts.app')
@section('header', 'Cerrar Caja #' . $cashRegister->id)
@section('main_content')
<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-lock"></i> Cerrar Caja</h5>
            </div>
            <form method="POST" action="{{ route('cash-registers.do-close', $cashRegister) }}">
                @csrf
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Monto Inicial:</strong> $ {{ number_format($cashRegister->opening_amount, 0, ',', '.') }}<br>
                        <strong>Total Ventas:</strong> $ {{ number_format($totalSales, 0, ',', '.') }}<br>
                        <strong>Apertura:</strong> {{ $cashRegister->opened_at?->format('d/m/Y H:i') }}
                    </div>
                    <div class="form-group">
                        <label for="closing_amount">Monto de Cierre (efectivo en caja) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                            <input type="number" step="0.01" min="0" name="closing_amount"
                                id="closing_amount"
                                class="form-control @error('closing_amount') is-invalid @enderror"
                                value="{{ old('closing_amount', '0') }}" required>
                            @error('closing_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <small class="text-muted">Cuenta el efectivo físico en caja y escríbelo aquí.</small>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notas de cierre (opcional)</label>
                        <textarea name="notes" id="notes" rows="3"
                            class="form-control @error('notes') is-invalid @enderror"
                            placeholder="Observaciones al cerrar...">{{ old('notes') }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('cash-registers.show', $cashRegister) }}" class="btn btn-default">Cancelar</a>
                    <button type="submit" class="btn btn-danger"
                        onclick="return confirm('¿Confirmas el cierre de caja?')">
                        <i class="fas fa-lock"></i> Confirmar Cierre
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><h5>Resumen por Método de Pago</h5></div>
            <div class="card-body p-0">
                <table class="table table-sm">
                    <thead><tr><th>Método</th><th>Cantidad</th><th>Total</th></tr></thead>
                    <tbody>
                    @forelse($salesByMethod as $row)
                    <tr>
                        <td>{{ $row->payment_method }}</td>
                        <td>{{ $row->count }}</td>
                        <td>$ {{ number_format($row->total, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted">Sin ventas en esta caja</td></tr>
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
</div>
@endsection
