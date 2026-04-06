@extends('layouts.app')
@section('header', 'Abrir Caja')
@section('main_content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-cash-register"></i> Apertura de Caja</h5>
            </div>
            <form method="POST" action="{{ route('cash-registers.store') }}">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="opening_amount">Monto Inicial <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                            <input type="number" step="0.01" min="0" name="opening_amount"
                                id="opening_amount"
                                class="form-control @error('opening_amount') is-invalid @enderror"
                                value="{{ old('opening_amount', '0') }}" required>
                            @error('opening_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <small class="text-muted">Ingresa el dinero que hay en caja al iniciar el turno.</small>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notas (opcional)</label>
                        <textarea name="notes" id="notes" rows="3"
                            class="form-control @error('notes') is-invalid @enderror"
                            placeholder="Observaciones de apertura...">{{ old('notes') }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('cash-registers.index') }}" class="btn btn-default">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-lock-open"></i> Abrir Caja</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
