@extends('layouts.app')
@section('header', 'Editar Producto')
@section('main_content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group"><label>Nombre *</label><input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required></div>
                    <div class="form-group"><label>Código</label><input type="text" name="code" class="form-control" value="{{ old('code', $product->code) }}"></div>
                    <div class="form-group"><label>Categoría</label><select name="category_id" class="form-control"><option value="">Sin categoría</option>@foreach($categories as $c)<option value="{{ $c->id }}" {{ $product->category_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach</select></div>
                    <div class="form-group"><label>Descripción</label><textarea name="description" class="form-control">{{ old('description', $product->description) }}</textarea></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group"><label>Costo *</label><input type="number" step="0.01" name="cost" class="form-control" value="{{ old('cost', $product->cost) }}" required></div>
                    <div class="form-group"><label>Precio *</label><input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price) }}" required></div>
                    <div class="form-group"><label>Imagen</label><input type="file" name="image" class="form-control-file" accept="image/*">@if($product->image)<img src="{{ asset('storage/'.$product->image) }}" height="60" class="mt-1">@endif</div>
                    <div class="form-group"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="active" name="active" value="1" {{ $product->active ? 'checked' : '' }}><label class="custom-control-label" for="active">Activo</label></div></div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button> <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection
