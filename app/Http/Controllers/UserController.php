<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Perfil;
use App\Models\Turno;
use App\Models\Setor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $userLogado = Auth::user();

        // Se for Gestor, visualiza apenas Técnicos. Se for Admin, visualiza todos.
        $usersQuery = User::with(['perfil', 'turno', 'setor']);

        if ($userLogado->perfil->tipo_perfil === 'Gestor') {
            $usersQuery->whereHas('perfil', function ($q) {
                $q->where('tipo_perfil', 'Técnico');
            });
        }

        $usuarios = $usersQuery->paginate(10);

        return view('users.index', compact('usuarios'));
    }

    public function create()
    {
        $userLogado = Auth::user();

        // Admin pode cadastrar qualquer perfil, Gestor só pode cadastrar Técnico
        if ($userLogado->perfil->tipo_perfil === 'Administrador') {
            $perfis = Perfil::all();
        } else {
            $perfis = Perfil::where('tipo_perfil', 'Técnico')->get();
        }

        $turnos = Turno::all();
        $setores = Setor::all();

        return view('users.create', compact('perfis', 'turnos', 'setores'));
    }

    public function store(Request $request)
    {
        $userLogado = Auth::user();
        $perfilSelecionado = Perfil::findOrFail($request->id_perfil);

        // Bloqueio no backend: Gestor não pode cadastrar Admin ou Gestor
        if ($userLogado->perfil->tipo_perfil === 'Gestor' && $perfilSelecionado->tipo_perfil !== 'Técnico') {
            return back()->withErrors(['id_perfil' => 'Gestores só podem cadastrar o perfil de Técnico.'])->withInput();
        }

        $validated = $request->validate([
            'nome_usuario'  => 'required|string|max:100',
            'email'         => 'required|email|max:100|unique:users,email',
            'senha'         => 'required|string|min:6',
            'id_perfil'     => 'required|exists:perfis,id_perfil',
            'id_turno'      => 'required|exists:turnos,id_turno',
            'id_setor'      => 'required|exists:setores,id_setor',
            'especialidade' => 'nullable|string|max:100',
        ]);

        User::create([
            'nome_usuario'  => $validated['nome_usuario'],
            'email'         => $validated['email'],
            'senha'         => $validated['senha'],
            'id_perfil'     => $validated['id_perfil'],
            'id_turno'      => $validated['id_turno'],
            'id_setor'      => $validated['id_setor'],
            'especialidade' => $validated['especialidade'] ?? 'Geral',
            'status'        => true,
        ]);

        return redirect()->route('users.index')->with('success', 'Usuário/Técnico cadastrado com sucesso!');
    }
}