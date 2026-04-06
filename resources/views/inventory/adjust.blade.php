@extends('layouts.app')
@section('header', 'Ajustar Inventario')
@section('main_content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-edit"></i> Ajuste de Inventario</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Producto:</strong> {{ $variant->product->name }}<br>
                    <strong>Variante:</strong> {{ $variant->name }}<br>
                    <strong>Stock actual:</strong> <span class="font-weight-bold">{{ $variant->stock }}</span>
                </div>
                <form method="POST" action="{{ route('inventory.do-adjust', $variant) }}">
                    @csrf
                    <div class="form-group">
                        <label for="type">Tipo de Movimiento <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                            <option value="">-- Selecciona --</option>
                            <option value="entrada" {{ old('type') === 'entrada' ? 'selected' : '' }}>
                                Entrada (agregar stock)
                            </option>
                            <option value="ajuste" {{ old('type') === 'ajuste' ? 'selected' : '' }}>
                                Ajuste (fijar cantidad exacta)
                            </option>
                            <option value="merma" {{ old('type') === 'merma' ? 'selected' : '' }}>
                                Merma (quitar stock)
                            </option>
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label for="quantity">Cantidad <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="quantity" min="1"
                            class="form-control @error('quantity') is-invalid @enderror"
                            value="{{ old('quantity') }}" required>
                        <small class="text-muted" id="qty-help">
                            Para "Ajuste": escribe la cantidad total que debe quedar en stock.
                        </small>
                        @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label for="cost">Costo unitario (opcional)</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                            <input type="number" step="0.01" min="0" name="cost" id="cost"
                                class="form-control @error('cost') is-invalid @enderror"
                                value="{{ old('cost') }}">
                            @error('cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notas / Motivo</label>
                        <textarea name="notes" id="notes" rows="3"
                            class="form-control @error('notes') is-invalid @enderror"
                            placeholder="Describe el motivo del ajuste...">{{ old('notes') }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('inventory.index') }}" class="btn btn-default">Cancelar</a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Guardar Ajuste
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('extra_js')
<script>
document.getElementById('type').addEventListener('change', function() {
    var help = document.getElementById('qty-help');
    if (this.value === 'entrada') help.textContent = 'Ingresa cuántas unidades agregas al stock actual.';
    else if (this.value === 'ajuste') help.textContent = 'Para "Ajuste": escribe la cantidad total que debe quedar en stock.';
    else if (this.value === 'merma') help.textContent = 'Ingresa cuántas unidades se pierden/retiran del stock.';
    else help.textContent = '';
});
</script>
@endsection
