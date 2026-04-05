@extends('layouts.app')
@section('header', 'Editar Categoría')
@section('main_content')
<div class="card col-md-6">
    <div class="card-body">
        <form method="POST" action="{{ route('categories.update', $category) }}">
            @csrf @method('PUT')
            <div class="form-group"><label>Nombre *</label><input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label>Descripción</label><textarea name="description" class="form-control">{{ old('description', $category->description) }}</textarea></div>
            <div class="form-group"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="active" name="active" value="1" {{ $category->active ? 'checked' : '' }}><label class="custom-control-label" for="active">Activa</label></div></div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection
