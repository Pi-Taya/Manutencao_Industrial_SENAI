<?php

namespace App\Http\Controllers;

use App\Models\TipoManutencao;
use Illuminate\Http\Request;

class TipoManutencaoController extends Controller
{
    public function index()
    {
        $tipos = TipoManutencao::orderBy('id_tipo_manutencao', 'asc')->get();
        return view('tipos_manutencao.index', compact('tipos'));
    }

    public function create()
    {
        return view('tipos_manutencao.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_manutencao' => 'required|string|max:100|unique:tipo_manutencaos,nome_manutencao',
            'descricao'       => 'nullable|string|max:255',
        ], [
            'nome_manutencao.unique' => 'Este tipo de manutenção já está cadastrado.',
        ]);

        TipoManutencao::create([
            'nome_manutencao' => $request->nome_manutencao,
            'descricao'       => $request->descricao,
        ]);

        return redirect()->route('tipos-manutencao.index')
            ->with('success', 'Tipo de manutenção cadastrado com sucesso!');
    }

    public function edit($id)
    {
        $tipo = TipoManutencao::findOrFail($id);
        return view('tipos_manutencao.edit', compact('tipo'));
    }

    public function update(Request $request, $id)
    {
        $tipo = TipoManutencao::findOrFail($id);

        $request->validate([
            'nome_manutencao' => 'required|string|max:100|unique:tipo_manutencaos,nome_manutencao,' . $tipo->id_tipo_manutencao . ',id_tipo_manutencao',
            'descricao'       => 'nullable|string|max:255',
        ], [
            'nome_manutencao.unique' => 'Este tipo de manutenção já está cadastrado.',
        ]);

        $tipo->update([
            'nome_manutencao' => $request->nome_manutencao,
            'descricao'       => $request->descricao,
        ]);

        return redirect()->route('tipos-manutencao.index')
            ->with('success', 'Tipo de manutenção atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $tipo = TipoManutencao::findOrFail($id);

        if ($tipo->ordensServico()->count() > 0) {
            return redirect()->route('tipos-manutencao.index')
                ->with('error', 'Não é possível excluir: existem Ordens de Serviço vinculadas a este tipo de manutenção.');
        }

        $tipo->delete();

        return redirect()->route('tipos-manutencao.index')
            ->with('success', 'Tipo de manutenção excluído com sucesso!');
    }
}