@extends('layouts.app')

@section('title', 'PITAYA Control')

@section('content')
    <section class="card">
        <h1>PITAYA CONTROL</h1>
        <p>Sistema de Gestão de Manutenção Industrial.</p>
        <a class="button" href="{{ route('dashboard') }}">Acessar painel</a>
    </section>
@endsection
