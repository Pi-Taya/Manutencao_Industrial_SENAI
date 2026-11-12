<?php

namespace App\Http\Controllers;

use App\Models\Fabricante;
use Illuminate\Http\Request;

class FabricanteController extends Controller
{
    public function index()
    {
        $fabricantes = Fabricante::paginate(10);
        return view('fabricantes.index', compact('fabricantes'));
    }

    public function create()
    {
        return view('fabricantes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_fabricante' => 'required|string|max:100|unique:fabricantes,nome_fabricante',
            'telefone'        => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:100',
        ], [
            'nome_fabricante.required' => 'O nome do fabricante é obrigatório.',
            'nome_fabricante.unique'   => 'Este fabricante já está cadastrado.',
        ]);

        Fabricante::create($request->all());

        return redirect()->route('fabricantes.index')->with('success', 'Fabricante cadastrado com sucesso!');
    }

    public function edit(string $id)
    {
        $fabricante = Fabricante::findOrFail($id);
        return view('fabricantes.edit', compact('fabricante'));
    }

    public function update(Request $request, string$id)
    {
        $fabricante = Fabricante::findOrFail($id);

        $request->validate([
            'nome_fabricante' => 'required|string|max:100|unique:fabricantes,nome_fabricante,' . $id . ',id_fabricante',
            'telefone'        => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:100',
        ], [
            'nome_fabricante.required' => 'O nome do fabricante é obrigatório.',
            'nome_fabricante.unique'   => 'Este fabricante já está cadastrado.',
        ]);

        $fabricante->update($request->all());

        return redirect()->route('fabricantes.index')->with('success', 'Fabricante atualizado com sucesso!');
    }

    public function destroy(string $id)
    {
        $fabricante = Fabricante::findOrFail($id);

        // Código correto para a verificação:
        if ($fabricante->maquinas()->exists() || $fabricante->modelos()->exists()) {
            return redirect()->route('fabricantes.index')->with('error', 'Não é possível excluir um fabricante vinculado a máquinas ou modelos!');
        }

        $fabricante->delete();

        return redirect()->route('fabricantes.index')->with('success', 'Fabricante excluído com sucesso!');
    }
}