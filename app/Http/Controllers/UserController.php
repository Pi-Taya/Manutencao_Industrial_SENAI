<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Perfil;
use App\Models\Turno;
use App\Models\Setor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private const PERFIS_GERENCIAVEIS_PELO_GESTOR = ['Técnico', 'Operador'];

    public function index(Request $request)
    {
        $userLogado = Auth::user();

        $usersQuery = User::with(['perfil', 'turno', 'setor']);

        // Gestores administram usuários operacionais, mas não perfis de governança.
        if ($userLogado->perfil->tipo_perfil === 'Gestor') {
            $usersQuery->whereHas('perfil', function ($q) {
                $q->whereIn('tipo_perfil', self::PERFIS_GERENCIAVEIS_PELO_GESTOR);
            });
        }

        // Filtro de Busca (Nome ou Email)
        if ($request->filled('search')) {
            $search = $request->search;
            $usersQuery->where(function ($q) use ($search) {
                $q->where('nome_usuario', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro por Perfil (apenas para Admin)
        if ($request->filled('perfil_id') && $userLogado->perfil->tipo_perfil === 'Administrador') {
            $usersQuery->where('id_perfil', $request->perfil_id);
        }

        // Filtro por Especialidade
        if ($request->filled('especialidade')) {
            $usersQuery->where('especialidade', $request->especialidade);
        }

        $usuarios = $usersQuery->paginate(10)->appends($request->all());
        $perfis = Perfil::all();

        return view('users.index', compact('usuarios', 'perfis'));
    }

    public function create()
    {
        $userLogado = Auth::user();

        if ($userLogado->perfil->tipo_perfil === 'Administrador') {
            $perfis = Perfil::all();
        } elseif ($userLogado->perfil->tipo_perfil === 'Gestor') {
            $perfis = Perfil::whereIn('tipo_perfil', self::PERFIS_GERENCIAVEIS_PELO_GESTOR)->get();
        } else {
            abort(403);
        }

        $turnos = Turno::all();
        $setores = Setor::all();

        return view('users.create', compact('perfis', 'turnos', 'setores'));
    }

    public function store(Request $request)
    {
        $userLogado = Auth::user();

        $validated = $request->validate([
            'nome_usuario'  => 'required|string|max:100',
            'email'         => 'required|email|max:100|unique:users,email',
            'senha'         => 'required|string|min:6',
            'id_perfil'     => 'required|exists:perfis,id_perfil',
            'id_turno'      => 'required|exists:turnos,id_turno',
            'id_setor'      => 'required|exists:setores,id_setor',
            'especialidade' => 'nullable|string|max:100',
        ]);

        $perfilSelecionado = Perfil::findOrFail($validated['id_perfil']);
        if (
            $userLogado->perfil->tipo_perfil === 'Gestor'
            && !in_array($perfilSelecionado->tipo_perfil, self::PERFIS_GERENCIAVEIS_PELO_GESTOR, true)
        ) {
            return back()->withErrors([
                'id_perfil' => 'Gestores só podem cadastrar usuários operacionais.',
            ])->withInput();
        }

        User::create([
            'nome_usuario'  => $validated['nome_usuario'],
            'email'         => $validated['email'],
            'senha'         => Hash::make($validated['senha']),
            'id_perfil'     => $validated['id_perfil'],
            'id_turno'      => $validated['id_turno'],
            'id_setor'      => $validated['id_setor'],
            'especialidade' => $validated['especialidade'] ?? 'Geral',
            'status'        => true,
        ]);

        return redirect()->route('users.index')->with('success', 'Usuário/Técnico cadastrado com sucesso!');
    }

    public function edit(string $id)
    {
        $userLogado = Auth::user();
        $usuario = User::findOrFail($id);

        // Gestores não podem alterar perfis de governança nem outros gestores.
        if (
            $userLogado->perfil->tipo_perfil === 'Gestor'
            && !in_array($usuario->perfil->tipo_perfil, self::PERFIS_GERENCIAVEIS_PELO_GESTOR, true)
        ) {
            abort(403, 'Ação não autorizada para este perfil.');
        }

        if ($userLogado->perfil->tipo_perfil === 'Administrador') {
            $perfis = Perfil::all();
        } elseif ($userLogado->perfil->tipo_perfil === 'Gestor') {
            $perfis = Perfil::whereIn('tipo_perfil', self::PERFIS_GERENCIAVEIS_PELO_GESTOR)->get();
        } else {
            abort(403);
        }

        $turnos = Turno::all();
        $setores = Setor::all();

        return view('users.edit', compact('usuario', 'perfis', 'turnos', 'setores'));
    }

    public function update(Request $request, string $id)
    {
        $userLogado = Auth::user();
        $usuario = User::findOrFail($id);

        // Gestores só podem alterar usuários operacionais.
        if (
            $userLogado->perfil->tipo_perfil === 'Gestor'
            && !in_array($usuario->perfil->tipo_perfil, self::PERFIS_GERENCIAVEIS_PELO_GESTOR, true)
        ) {
            abort(403, 'Ação não autorizada.');
        }

        $validated = $request->validate([
            'nome_usuario'  => 'required|string|max:100',
            'email'         => 'required|email|max:100|unique:users,email,' . $id . ',id_usuario',
            'senha'         => 'nullable|string|min:6',
            'id_perfil'     => 'required|exists:perfis,id_perfil',
            'id_turno'      => 'required|exists:turnos,id_turno',
            'id_setor'      => 'required|exists:setores,id_setor',
            'especialidade' => 'nullable|string|max:100',
            'status'        => 'required|boolean',
        ]);

        $perfilNovo = Perfil::findOrFail($validated['id_perfil']);

        // TRAVA DE SEGURANÇA: Proteção do último Administrador
        if ($usuario->perfil->tipo_perfil === 'Administrador') {
            // Conta quantos administradores ativos existem no sistema
            $totalAdminsAtivos = User::whereHas('perfil', function ($q) {
                $q->where('tipo_perfil', 'Administrador');
            })->where('status', true)->count();

            // Se for o único Administrador ativo e tentar mudar de perfil ou inativar
            if ($totalAdminsAtivos <= 1 && ($perfilNovo->tipo_perfil !== 'Administrador' || !$validated['status'])) {
                return back()->withErrors([
                    'id_perfil' => 'Segurança do Sistema: Não é possível alterar o perfil ou inativar o único Administrador do sistema.'
                ])->withInput();
            }
        }

        // Gestores não podem atribuir perfis de governança ou planejamento.
        if (
            $userLogado->perfil->tipo_perfil === 'Gestor'
            && !in_array($perfilNovo->tipo_perfil, self::PERFIS_GERENCIAVEIS_PELO_GESTOR, true)
        ) {
            return back()->withErrors([
                'id_perfil' => 'Gestores só podem atribuir perfis operacionais.',
            ])->withInput();
        }

        $usuario->nome_usuario  = $validated['nome_usuario'];
        $usuario->email         = $validated['email'];
        $usuario->id_perfil     = $validated['id_perfil'];
        $usuario->id_turno      = $validated['id_turno'];
        $usuario->id_setor      = $validated['id_setor'];
        $usuario->especialidade = $validated['especialidade'] ?? 'Geral';
        $usuario->status        = $validated['status'];

        if (!empty($validated['senha'])) {
            $usuario->senha = Hash::make($validated['senha']);
        }

        $usuario->save();

        return redirect()->route('users.index')->with('success', 'Usuário/Técnico atualizado com sucesso!');
    }

    public function show(string $id)
    {
        $userLogado = Auth::user();
        $usuario = User::with(['perfil', 'turno', 'setor'])->findOrFail($id);

        // Gestores só podem consultar usuários operacionais.
        if (
            $userLogado->perfil->tipo_perfil === 'Gestor'
            && !in_array($usuario->perfil->tipo_perfil, self::PERFIS_GERENCIAVEIS_PELO_GESTOR, true)
        ) {
            abort(403, 'Ação não autorizada.');
        }

        return view('users.show', compact('usuario'));
    }
}