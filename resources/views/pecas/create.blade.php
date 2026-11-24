@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Cadastrar Nova Peça</h2>

    <form action="{{ route('pecas.store') }}" method="POST">
        @csrf

        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Código Interno (SKU) *</label>
                <input type="text" name="codigo_item" class="form-control" placeholder="Ex: 10452" maxlength="50" required>
            </div>

            <div class="col-md-9">
                <label class="form-label">Descrição Padronizada *</label>
                <input type="text" name="nome_peca" class="form-control" placeholder="Ex: ROLAMENTO 6309-2Z SKF" maxlength="100" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Unidade de Medida *</label>
                <select name="unidade_medida" class="form-select" required>
                    <option value="UN">UN - Unidade</option>
                    <option value="KG">KG - Quilograma</option>
                    <option value="L">L - Litro</option>
                    <option value="M">M - Metro</option>
                    <option value="CX">CX - Caixa</option>
                    <option value="PCT">PCT - Pacote</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Estoque Atual *</label>
                <input type="number" name="quantidade_estoque" class="form-control" value="0" min="0" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Ponto de Pedido (Gatilho Compra) *</label>
                <input type="number" name="ponto_pedido" class="form-control" value="2" min="0" required>
            </div>
