<?php

namespace App\Http\Controllers;

use App\Models\Maquina;
use App\Models\Setor;
use App\Models\StatusMaquina;
use App\Models\Modelo;
use App\Models\Fabricante;
use Illuminate\Http\Request;

class MaquinaController extends Controller
{
    public function index()
    {
        $maquinas = Maquina::with(['modelo', 'setor', 'fabricante', 'statusMaquina'])->paginate(10);

        return view('maquinas.index', compact('maquinas'));
    }

    public function create()
    {
        $modelos    = Modelo::all();
        $fabricantes = Fabricante::all();
        $setores    = Setor::all();
        $statuses   = StatusMaquina::all();

        return view('maquinas.create', compact('modelos', 'fabricantes', 'setores', 'statuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo_tag'        => 'required|string|max:50|unique:maquinas,codigo_tag',
            'nome_maquina'      => 'required|string|max:100',
            'id_modelo'         => 'required|exists:modelos,id_modelo',
            'id_fabricante'     => 'required|exists:fabricantes,id_fabricante',
            'data_aquisicao'    => 'required|date',
            'id_setor'          => 'required|exists:setores,id_setor',
            'id_status_maquina' => 'required|exists:status_maquinas,id_status_maquina',
        ]);

        Maquina::create($validated);

        return redirect()->route('maquinas.index')->with('success', 'Máquina cadastrada com sucesso!');
    }

    public function show(string $id)
    {
        $maquina = Maquina::with(['modelo', 'fabricante', 'setor', 'statusMaquina'])->findOrFail($id);
        return view('maquinas.show', compact('maquina'));
    }

    public function edit(string $id)
    {
        $maquina    = Maquina::findOrFail($id);
        $modelos    = Modelo::all();
        $fabricantes = Fabricante::all();
        $setores    = Setor::all();
        $statuses   = StatusMaquina::all();

        return view('maquinas.edit', compact('maquina', 'modelos', 'fabricantes', 'setores', 'statuses'));
    }

    public function update(Request $request, string $id)
    {
        $maquina = Maquina::findOrFail($id);

        $validated = $request->validate([
            'codigo_tag'        => 'required|string|max:50|unique:maquinas,codigo_tag,' . $id . ',id_maquina',
            'nome_maquina'      => 'required|string|max:100',
            'id_modelo'         => 'required|exists:modelos,id_modelo',
            'id_fabricante'     => 'required|exists:fabricantes,id_fabricante',
            'data_aquisicao'    => 'required|date',
            'id_setor'          => 'required|exists:setores,id_setor',
            'id_status_maquina' => 'required|exists:status_maquinas,id_status_maquina',
        ]);

        $maquina->update($validated);

        return redirect()->route('maquinas.index')->with('success', 'Máquina atualizada com sucesso!');
    }

    public function destroy(string $id)
    {
        $maquina = Maquina::findOrFail($id);
        $maquina->delete();

        return redirect()->route('maquinas.index')->with('success', 'Máquina excluída com sucesso!');
    }
}