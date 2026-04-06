@extends('layouts.app')
@section('header', 'Nuevo Producto')
@section('main_content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group"><label>Nombre *</label><input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="form-group"><label>Código</label><input type="text" name="code" class="form-control" value="{{ old('code') }}"></div>
                    <div class="form-group"><label>Categoría</label><select name="category_id" class="form-control"><option value="">Sin categoría</option>@foreach($categories as $c)<option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach</select></div>
                    <div class="form-group"><label>Descripción</label><textarea name="description" class="form-control">{{ old('description') }}</textarea></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group"><label>Costo *</label><input type="number" step="0.01" name="cost" class="form-control @error('cost') is-invalid @enderror" value="{{ old('cost', 0) }}" required></div>
                    <div class="form-group"><label>Precio *</label><input type="number" step="0.01" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', 0) }}" required></div>
                    <div class="form-group"><label>Imagen</label><input type="file" name="image" class="form-control-file" accept="image/*"></div>
                    <div class="form-group"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="active" name="active" value="1" checked><label class="custom-control-label" for="active">Activo</label></div></div>
                </div>
            </div>
            <h5>Variantes</h5>
            <div id="variants-container"></div>
            <button type="button" id="add-variant" class="btn btn-sm btn-secondary mb-3"><i class="fas fa-plus"></i> Agregar Variante</button>
            <div><button type="submit" class="btn btn-primary">Guardar</button> <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancelar</a></div>
        </form>
    </div>
</div>
@endsection
@section('extra_js')
<script>
let variantCount = 0;
document.getElementById('add-variant').addEventListener('click', function() {
    const container = document.getElementById('variants-container');
    const div = document.createElement('div');
    div.className = 'row border rounded p-2 mb-2';
    div.innerHTML = `<div class="col-md-2"><label>Color</label><input type="text" name="variants[${variantCount}][color]" class="form-control form-control-sm"></div>
        <div class="col-md-2"><label>Material</label><input type="text" name="variants[${variantCount}][material]" class="form-control form-control-sm"></div>
        <div class="col-md-1"><label>Talla</label><input type="text" name="variants[${variantCount}][size]" class="form-control form-control-sm"></div>
        <div class="col-md-2"><label>Stock</label><input type="number" name="variants[${variantCount}][stock]" class="form-control form-control-sm" value="0"></div>
        <div class="col-md-2"><label>Código barras</label><input type="text" name="variants[${variantCount}][barcode]" class="form-control form-control-sm"></div>
        <div class="col-md-2"><label>Precio adicional</label><input type="number" step="0.01" name="variants[${variantCount}][additional_price]" class="form-control form-control-sm" value="0"></div>
        <div class="col-md-1 d-flex align-items-end"><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.row').remove()">X</button></div>`;
    container.appendChild(div);
    variantCount++;
});
</script>
@endsection
