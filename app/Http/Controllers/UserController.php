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
    public function index(Request $request)
    {
        $userLogado = Auth::user();

        $usersQuery = User::with(['perfil', 'turno', 'setor']);

        // Gestor visualiza apenas Técnicos
        if ($userLogado->perfil->tipo_perfil === 'Gestor') {
            $usersQuery->whereHas('perfil', function ($q) {
                $q->where('tipo_perfil', 'Técnico');
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

        // Bloqueio de acesso se um Gestor tentar editar um Admin/Gestor pela URL
        if ($userLogado->perfil->tipo_perfil === 'Gestor' && $usuario->perfil->tipo_perfil !== 'Técnico') {
            abort(403, 'Ação não autorizada. Gestores só podem editar técnicos.');
        }

        if ($userLogado->perfil->tipo_perfil === 'Administrador') {
            $perfis = Perfil::all();
        } else {
            $perfis = Perfil::where('tipo_perfil', 'Técnico')->get();
        }

        $turnos = Turno::all();
        $setores = Setor::all();

        return view('users.edit', compact('usuario', 'perfis', 'turnos', 'setores'));
    }

    public function update(Request $request, string $id)
    {
        $userLogado = Auth::user();
        $usuario = User::findOrFail($id);

        // Bloqueio de acesso para Gestores alterarem Admins/Gestores
        if ($userLogado->perfil->tipo_perfil === 'Gestor' && $usuario->perfil->tipo_perfil !== 'Técnico') {
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

        // Regra para Gestores
        if ($userLogado->perfil->tipo_perfil === 'Gestor' && $perfilNovo->tipo_perfil !== 'Técnico') {
            return back()->withErrors(['id_perfil' => 'Gestores só podem atribuir o perfil de Técnico.'])->withInput();
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

        // Se for Gestor, só pode visualizar detalhes de Técnicos
        if ($userLogado->perfil->tipo_perfil === 'Gestor' && $usuario->perfil->tipo_perfil !== 'Técnico') {
            abort(403, 'Ação não autorizada.');
        }

        return view('users.show', compact('usuario'));
    }
}