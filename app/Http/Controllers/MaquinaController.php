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
        $maquinas = Maquina::with(['setor', 'statusMaquina', 'modelo', 'fabricante'])->paginate(10);
        return view('maquinas.index', compact('maquinas'));
    }

    public function create()
    {
        $setores = Setor::all();
        $statusList = StatusMaquina::all();
        $modelos = Modelo::all();
        $fabricantes = Fabricante::all();

        return view('maquinas.create', compact('setores', 'statusList', 'modelos', 'fabricantes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo_tag' => 'required|string|max:50|unique:maquinas,codigo_tag',
            'nome_maquina' => 'required|string|max:100',
            'id_modelo' => 'required|exists:modelos,id_modelo',
            'id_fabricante' => 'required|exists:fabricantes,id_fabricante',
            'data_aquisicao' => 'required|date',
            'id_setor' => 'required|exists:setores,id_setor',
            'id_status_maquina' => 'required|exists:status_maquinas,id_status_maquina',
        ]);

        Maquina::create($validated);

        return redirect()->route('maquinas.index')->with('success', 'Máquina cadastrada com sucesso!');
    }
}