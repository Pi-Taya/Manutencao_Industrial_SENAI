<?php

namespace App\Http\Controllers;

use App\Models\Peca;
use App\Models\StatusPeca;
use Illuminate\Http\Request;

class PecaController extends Controller
{
    public function index()
    {
        $pecas = Peca::with('ultimoStatus')->orderBy('nome_peca')->get();
        return view('pecas.index', compact('pecas'));
    }

    public function create()
    {
        return view('pecas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo_item' => 'required|string|max:50|unique:pecas,codigo_item',
            'nome_peca' => 'required|string|max:100',
            'unidade_medida' => 'required|in:UN,KG,L,M,CX,PCT',
            'quantidade_estoque' => 'required|integer|min:0',
            'criticidade' => 'required|in:X,Y,Z',
            'ponto_pedido' => 'required|integer|min:0',
            'localizacao' => 'nullable|string|max:80',
        ]);

        // 1. Grava na tabela 'pecas'
        $peca = Peca::create([
            'codigo_item' => $request->codigo_item,
            'nome_peca' => mb_strtoupper($request->nome_peca),
            'unidade_medida' => $request->unidade_medida,
            'quantidade_estoque' => $request->quantidade_estoque,
            'status' => $request->has('status'),
        ]);

        // 2. Define o status automático do estoque baseado no Ponto de Pedido
        $situacao = $peca->quantidade_estoque == 0 
            ? 'Crítico (Ruptura)' 
            : ($peca->quantidade_estoque <= $request->ponto_pedido ? 'Comprar (Abaixo PP)' : 'Normal');

        // Monta a ficha resumida para salvar em 'status_pecas' (limite de 200 caracteres)
        $descricao = "Crit: {$request->criticidade} | PP: {$request->ponto_pedido} | {$situacao}";
        if ($request->filled('localizacao')) {
            $descricao .= " | Loc: {$request->localizacao}";
        }

        StatusPeca::create([
            'id_peca' => $peca->id_peca,
            'descricao_status_peca' => mb_substr($descricao, 0, 200),
        ]);

        return redirect()->route('pecas.index')
            ->with('success', 'Peça cadastrada com sucesso!');
    }

    public function edit(Peca $peca)
    {
        $peca->load('statusPecas');
        return view('pecas.edit', compact('peca'));
    }

    public function update(Request $request, Peca $peca)
    {
        $request->validate([
            'codigo_item' => 'required|string|max:50|unique:pecas,codigo_item,' . $peca->id_peca . ',id_peca',
            'nome_peca' => 'required|string|max:100',
            'unidade_medida' => 'required|in:UN,KG,L,M,CX,PCT',
            'quantidade_estoque' => 'required|integer|min:0',
            'observacao_status' => 'nullable|string|max:200',
        ]);

        $peca->update([
            'codigo_item' => $request->codigo_item,
            'nome_peca' => mb_strtoupper($request->nome_peca),
            'unidade_medida' => $request->unidade_medida,
            'quantidade_estoque' => $request->quantidade_estoque,
            'status' => $request->has('status'),
        ]);

        if ($request->filled('observacao_status')) {
            StatusPeca::create([
                'id_peca' => $peca->id_peca,
                'descricao_status_peca' => $request->observacao_status,
            ]);
        }

        return redirect()->route('pecas.index')
            ->with('success', 'Peça atualizada com sucesso!');
    }
}