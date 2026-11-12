<?php

namespace App\Http\Controllers;

use App\Models\OrdemServico;
use App\Models\Maquina;
use App\Models\TipoManutencao;
use App\Models\StatusPrioridade;
use App\Models\StatusMaquina;
use App\Models\User;
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
            $query->where('status',$request->status);
        }

        // Filtro por prioridade
        if ($request->filled('id_status_prioridade')) {
            $query->where('id_status_prioridade',$request->id_status_prioridade);
        }

        $ordens = $query->orderBy('created_at', 'desc')->paginate(10);$prioridades = StatusPrioridade::all();

        return view('ordens.index', compact('ordens', 'prioridades'));
    }

    /**
     * Formulário de Abertura de OS (RF07)
     */
    public function create()
    {
        $maquinas = Maquina::where('status', true)->get();
        $tiposManutencao = TipoManutencao::all();$prioridades = StatusPrioridade::all();

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
        $horasSla = explode(':',$prioridade->tempo_limite_atendimento)[0] ?? 24;
        $dataPrevisao = Carbon::now()->addHours((int)$horasSla);

        $parouProducao =$request->has('parou_producao');

        // 1. Criar a Ordem de Serviço
        $os = OrdemServico::create([
            'id_maquina'           => $request->id_maquina,
            'id_tipo_manutencao'   => $request->id_tipo_manutencao,
            'id_usuario'           => Auth::id(),
            'id_status_prioridade' => $request->id_status_prioridade,
            'descricao_falha'      => $request->descricao_falha,
            'parou_producao'       => $parouProducao,
            'status'               => 'Aberta',
            'data_abertura'        => Carbon::now(),
            'data_previsao'        => $dataPrevisao,
        ]);

        // 2. Regra de Negócio RN04: Atualiza a máquina para "Em Manutenção" se parou a produção
        if ($parouProducao) {$statusEmManutencao = StatusMaquina::where('estado_da_maquina', 'Em Manutenção')->first();
            if ($statusEmManutencao) {
                $maquina = Maquina::findOrFail($request->id_maquina);
                $maquina->update(['id_status_maquina' =>$statusEmManutencao->id_status_maquina]);
            }
        }

        return redirect()->route('ordens.index')
            ->with('success', "Ordem de Serviço #{$os->id_ordem_servico} criada com sucesso!");
    }

    /**
     * Exibir detalhes completos da Ordem de Serviço e técnicos disponíveis (RF08)
     */
    public function show($id)
    {
        $ordem = OrdemServico::with([ 'maquina.setor', 'solicitante', 'tipoManutencao', 'prioridade', 'tecnicos.turno' ])->findOrFail($id);

        // Lista apenas usuários ativos com perfil Técnico para seleção na OS
        $tecnicosDisponiveis = User::whereHas('perfil', function ($q) {$q->where('tipo_perfil', 'Técnico');
        })->where('status', true)->get();

        return view('ordens.show', compact('ordem', 'tecnicosDisponiveis'));
    }

    /**
     * Alocar ou atualizar técnicos na OS (RF08)
     */
    public function alocarTecnicos(Request $request,$id)
    {
        $request->validate([
            'tecnicos'   => 'nullable|array',
            'tecnicos.*' => 'exists:users,id_usuario',
        ]);

        $ordem = OrdemServico::findOrFail($id);
        
        // Sincroniza a tabela pivô ordem_servico_tecnico
        $ordem->tecnicos()->sync($request->tecnicos ?? []);

        return redirect()->route('ordens.show', $ordem->id_ordem_servico)
            ->with('success', 'Equipe técnica atualizada com sucesso!');
    }

    /**
     * Atualizar o status do atendimento e encerrar a OS (RF09)
     */
    public function atualizarStatus(Request $request,$id)
    {
        $request->validate([
            'status' => 'required|in:Aberta,Aguardando Aprovação,Em Andamento,Aguardando Peça,Aguardando Validação,Concluída,Cancelada',
        ]);

        $ordem = OrdemServico::findOrFail($id);
        $novoStatus =$request->status;
        $agora = Carbon::now();

        $dadosAtualizacao = ['status' =>$novoStatus];

        // Se iniciou o atendimento agora, grava o timestamp de início
        if ($novoStatus === 'Em Andamento' && is_null($ordem->data_inicio_atendimento)) {
            $dadosAtualizacao['data_inicio_atendimento'] =$agora;
        }

        // Se concluiu a OS, calcula o tempo total de parada e libera a máquina (RN04 reversa)
        if ($novoStatus === 'Concluída' && is_null($ordem->data_fechamento)) {
            $dadosAtualizacao['data_fechamento'] =$agora;
            $dadosAtualizacao['tempo_parada_minutos'] = $ordem->data_abertura->diffInMinutes($agora);

            if ($ordem->parou_producao) {$statusAtiva = StatusMaquina::where('estado_da_maquina', 'Ativa')->first();
                if ($statusAtiva &&$ordem->maquina) {
                    $ordem->maquina->update(['id_status_maquina' =>$statusAtiva->id_status_maquina]);
                }
            }
        }

        $ordem->update($dadosAtualizacao);

        return redirect()->route('ordens.show', $ordem->id_ordem_servico)
            ->with('success', "Status da OS alterado para: {$novoStatus}");
    }
}