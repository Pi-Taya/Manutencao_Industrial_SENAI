<?php

namespace App\Http\Controllers;

use App\Models\OrdemServico;
use App\Models\Maquina;
use App\Models\TipoManutencao;
use App\Models\StatusPrioridade;
use App\Models\StatusMaquina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrdemServicoController extends Controller
{
    /**
     * Listagem de Ordens de Serviço (RF07)
     */
    public function index(Request $request)
    {
        $query = OrdemServico::with(['maquina', 'solicitante', 'tipoManutencao', 'prioridade', 'tecnicos']);

        // Filtro por status da OS
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por prioridade
        if ($request->filled('id_status_prioridade')) {
            $query->where('id_status_prioridade', $request->id_status_prioridade);
        }

        $ordens = $query->orderBy('created_at', 'desc')->paginate(10);
        $prioridades = StatusPrioridade::all();

        return view('ordens.index', compact('ordens', 'prioridades'));
    }

    /**
     * Formulário de Abertura de OS (RF07)
     */
    public function create()
    {
        $maquinas = Maquina::where('status', true)->get();
        $tiposManutencao = TipoManutencao::all();
        $prioridades = StatusPrioridade::all();

        return view('ordens.create', compact('maquinas', 'tiposManutencao', 'prioridades'));
    }

    /**
     * Gravação da nova OS e aplicação da RN04
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_maquina'           => 'required|exists:maquinas,id_maquina',
            'id_tipo_manutencao'   => 'required|exists:tipo_manutencaos,id_tipo_manutencao',
            'id_status_prioridade' => 'required|exists:status_prioridades,id_status_prioridade',
            'descricao_falha'      => 'required|string|max:255',
            'parou_producao'       => 'nullable|boolean',
        ]);

        $prioridade = StatusPrioridade::findOrFail($request->id_status_prioridade);

        // Cálculo da previsão limite de atendimento (SLA)
        $horasSla = explode(':', $prioridade->tempo_limite_atendimento)[0] ?? 24;
        $dataPrevisao = Carbon::now()->addHours((int)$horasSla);

        $parouProducao = $request->has('parou_producao');

        // 1. Criar a Ordem de Serviço
        $os = OrdemServico::create([
            'id_maquina'           => $request->id_maquina,
            'id_tipo_manutencao'   => $request->id_tipo_manutencao,
            'id_usuario'           => Auth::id(), // Usuário logado
            'id_status_prioridade' => $request->id_status_prioridade,
            'descricao_falha'      => $request->descricao_falha,
            'parou_producao'       => $parouProducao,
            'status'               => 'Aberta',
            'data_abertura'        => Carbon::now(),
            'data_previsao'        => $dataPrevisao,
        ]);

        // 2. Regra de Negócio RN04: Atualiza a máquina para "Em Manutenção" se parou a produção
        if ($parouProducao) {
            $statusEmManutencao = StatusMaquina::where('estado_da_maquina', 'Em Manutenção')->first();
            if ($statusEmManutencao) {
                $maquina = Maquina::findOrFail($request->id_maquina);
                $maquina->update(['id_status_maquina' => $statusEmManutencao->id_status_maquina]);
            }
        }

        return redirect()->route('ordens.index')
            ->with('success', "Ordem de Serviço #{$os->id_ordem_servico} criada com sucesso!");
    }

    /**
     * Exibir detalhes completos da Ordem de Serviço
     */
    public function show($id)
    {
        $ordem = OrdemServico::with([
            'maquina', 
            'solicitante', 
            'tipoManutencao', 
            'prioridade', 
            'tecnicos'
        ])->findOrFail($id);

        return view('ordens.show', compact('ordem'));
    }
}