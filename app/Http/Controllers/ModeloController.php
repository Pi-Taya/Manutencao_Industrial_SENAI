<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Models\Fabricante;
use Illuminate\Http\Request;

class ModeloController extends Controller
{
    public function index()
    {
        $modelos = Modelo::with('fabricante')->paginate(10);
        return view('modelos.index', compact('modelos'));
    }

    public function create()
    {
        $fabricantes = Fabricante::all();
        return view('modelos.create', compact('fabricantes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_modelo'   => 'required|string|max:100',
            'id_fabricante' => 'required|exists:fabricantes,id_fabricante',
            'especificacoes_tecnicas' => 'nullable|string',
        ], [
            'nome_modelo.required'   => 'O nome do modelo é obrigatório.',
            'id_fabricante.required' => 'Selecione um fabricante válido.',
        ]);

        Modelo::create($request->all());

        return redirect()->route('modelos.index')->with('success', 'Modelo cadastrado com sucesso!');
    }

    public function edit(string $id)
    {
        $modelo = Modelo::findOrFail($id);
        $fabricantes = Fabricante::all();
        return view('modelos.edit', compact('modelo', 'fabricantes'));
    }

    public function update(Request $request, string $id)
    {
        $modelo = Modelo::findOrFail($id);

        $request->validate([
            'nome_modelo'   => 'required|string|max:100',
            'id_fabricante' => 'required|exists:fabricantes,id_fabricante',
            'especificacoes_tecnicas' => 'nullable|string',
        ], [
            'nome_modelo.required'   => 'O nome do modelo é obrigatório.',
            'id_fabricante.required' => 'Selecione um fabricante válido.',
        ]);

        $modelo->update($request->all());

        return redirect()->route('modelos.index')->with('success', 'Modelo atualizado com sucesso!');
    }

    public function destroy(string $id)
    {
        $modelo = Modelo::findOrFail($id);

        if ($modelo->maquinas()->exists()) {
            return redirect()->route('modelos.index')->with('error', 'Não é possível excluir um modelo vinculado a máquinas!');
        }

        $modelo->delete();

        return redirect()->route('modelos.index')->with('success', 'Modelo excluído com sucesso!');
    }
}