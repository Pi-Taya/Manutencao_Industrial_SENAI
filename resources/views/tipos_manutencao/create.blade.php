@extends('layouts.app')

@section('title', 'Novo tipo de manutenção - PITAYA Control')

@section('content')
    <h1>Cadastrar tipo de manutenção</h1>
    @if($errors->any())
        <div class="alert alert-error"><ul>@foreach($errors->all() as $erro)<li>{{ $erro }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ route('tipos-manutencao.store') }}" method="POST" class="card">
        @csrf
        <div class="form-field">
            <label for="nome_manutencao">Tipo de manutenção *</label>
            <input type="text" name="nome_manutencao" id="nome_manutencao" value="{{ old('nome_manutencao') }}" maxlength="100" required placeholder="Ex.: Lubrificação, Calibração, Emergencial">
        </div>
        <div class="form-field">
            <label for="descricao">Descrição</label>
            <textarea name="descricao" id="descricao" rows="3" maxlength="255" placeholder="Descreva a finalidade deste tipo de manutenção">{{ old('descricao') }}</textarea>
        </div>
        <div class="form-actions">
            <button type="submit">Salvar tipo de manutenção</button>
            <a class="button button-secondary" href="{{ route('tipos-manutencao.index') }}">Cancelar</a>
        </div>
    </form>
@endsection
