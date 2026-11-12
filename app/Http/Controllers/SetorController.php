<?php

namespace App\Http\Controllers;

use App\Models\Setor;
use Illuminate\Http\Request;

class SetorController extends Controller
{
    /**
     * Exibe a listagem dos setores.
     */
    public function index()
    {
        $setores = Setor::paginate(10);
        return view('setores.index', compact('setores'));
    }

    /**
     * Exibe o formulário de criação de um novo setor.
     */
    public function create()
    {
        return view('setores.create');
    }

    /**
     * Guarda um novo setor na base de dados.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome_setor' => 'required|string|max:100|unique:setores,nome_setor',
            'descricao'  => 'nullable|string|max:255',
        ], [
            'nome_setor.required' => 'O nome do setor é obrigatório.',
            'nome_setor.unique'   => 'Este setor já está cadastrado.',
        ]);

        Setor::create([
            'nome_setor' => $request->nome_setor,
            'descricao'  => $request->descricao,
        ]);

        return redirect()->route('setores.index')->with('success', 'Setor cadastrado com sucesso!');
    }

    /**
     * Exibe o formulário de edição do setor.
     */
    public function edit(string $id)
    {
        $setor = Setor::findOrFail($id);
        return view('setores.edit', compact('setor'));
    }

    /**
     * Atualiza o setor na base de dados.
     */
    public function update(Request $request, string $id)
    {
        $setor = Setor::findOrFail($id);

        $request->validate([
            'nome_setor' => 'required|string|max:100|unique:setores,nome_setor,' . $id . ',id_setor',
            'descricao'  => 'nullable|string|max:255',
        ], [
            'nome_setor.required' => 'O nome do setor é obrigatório.',
            'nome_setor.unique'   => 'Este setor já está cadastrado.',
        ]);

        $setor->update([
            'nome_setor' => $request->nome_setor,
            'descricao'  => $request->descricao,
        ]);

        return redirect()->route('setores.index')->with('success', 'Setor atualizado com sucesso!');
    }

    /**
     * Remove um setor da base de dados.
     */
    public function destroy(string $id)
    {
        $setor = Setor::findOrFail($id);
        
        // Evita a exclusão se existirem máquinas associadas
        if ($setor->maquinas()->exists()) {
            return redirect()->route('setores.index')->with('error', 'Não é possível excluir um setor que possui máquinas associadas!');
        }

        $setor->delete();

        return redirect()->route('setores.index')->with('success', 'Setor excluído com sucesso!');
    }
}