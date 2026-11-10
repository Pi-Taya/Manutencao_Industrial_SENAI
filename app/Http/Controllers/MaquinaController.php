<?php

namespace App\Http\Controllers;

use App\Models\Maquina;
use App\Models\Setor;
use App\Models\StatusMaquina;
use Illuminate\Http\Request;

class MaquinaController extends Controller
{
    /**
     * Listagem de todas as máquinas (RF04)
     */
    public function index()
    {
        $maquinas = Maquina::with(['setor', 'statusMaquina'])->paginate(10);
        return view('maquinas.index', compact('maquinas'));
    }

    /**
     * Formulário de criação de máquina
     */
    public function create()
    {
        $setores = Setor::all();
        $statusList = StatusMaquina::all();
        return view('maquinas.create', compact('setores', 'statusList'));
    }

    /**
     * Salvar nova máquina no banco (RF04)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo_identificador' => 'required|string|max:50|unique:maquinas,codigo_identificador',
            'nome' => 'required|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'fabricante' => 'nullable|string|max:100',
            'data_aquisicao' => 'nullable|date',
            'id_setor' => 'required|exists:setores,id_setor',
            'id_status_maquina' => 'required|exists:status_maquinas,id_status_maquina',
        ], [
            'codigo_identificador.required' => 'O código identificador é obrigatório.',
            'codigo_identificador.unique' => 'Este código identificador já está em uso.',
            'nome.required' => 'O nome da máquina é obrigatório.',
            'id_setor.required' => 'Selecione um setor.',
            'id_status_maquina.required' => 'Selecione o status inicial da máquina.',
        ]);

        Maquina::create($validated);

        return redirect()->route('maquinas.index')->with('success', 'Máquina cadastrada com sucesso!');
    }
}