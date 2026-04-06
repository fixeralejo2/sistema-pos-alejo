@extends('layouts.app')
@section('header', 'Nueva Categoría')
@section('main_content')
<div class="card col-md-6">
    <div class="card-body">
        <form method="POST" action="{{ route('categories.store') }}">
            @csrf
            <div class="form-group"><label>Nombre *</label><input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label>Descripción</label><textarea name="description" class="form-control">{{ old('description') }}</textarea></div>
            <div class="form-group"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="active" name="active" value="1" checked><label class="custom-control-label" for="active">Activa</label></div></div>
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection
