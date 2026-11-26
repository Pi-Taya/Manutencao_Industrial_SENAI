<?php

namespace App\Http\Controllers;

use App\Models\OrdemServico;
use App\Models\Maquina;
use App\Models\TipoManutencao;
use App\Models\StatusPrioridade;
use App\Models\StatusMaquina;
use App\Models\ParecerTecnico;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrdemServicoController extends Controller
{
    /**
     * Listagem de Ordens de Serviço
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

        // OS abertas/pendentes com prioridade Crítica entram no topo da listagem (RN02)
        $ordens = $query
            ->orderByRaw("CASE WHEN status NOT IN ('Concluída', 'Cancelada') AND id_status_prioridade = (SELECT id_status_prioridade FROM status_prioridades WHERE nome_prioridade = 'Crítica' LIMIT 1) THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $prioridades = StatusPrioridade::all();

        return view('ordens.index', compact('ordens', 'prioridades'));
    }

    /**
     * Formulário de Abertura de OS
     */
    public function create()
    {
        $maquinas = Maquina::where('status', true)->get();
        $tiposManutencao = TipoManutencao::all();
        $prioridades = StatusPrioridade::all();

        return view('ordens.create', compact('maquinas', 'tiposManutencao', 'prioridades'));
    }

    /**
     * Gravação da nova OS
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_maquina'           => 'required|exists:maquinas,id_maquina',
            'id_tipo_manutencao'   => 'required|exists:tipo_manutencaos,id_tipo_manutencao',
            'id_status_prioridade' => 'required|exists:status_prioridades,id_status_prioridade',
            'descricao_falha'      => 'required|string|max:255',
            'parou_producao'       => 'nullable|boolean',
            'id_chamado'           => 'nullable|integer|min:1',
            'data_programada'      => 'nullable|date',
            'observacoes_pcm'      => 'nullable|string',
        ]);

        $perfil = Auth::user()->perfil->tipo_perfil ?? '';
        $podePlanejar = in_array($perfil, ['Gestor', 'PCM'], true);

        $enviouDadosPlanejamento = $request->filled('id_chamado')
            || $request->filled('data_programada')
            || $request->filled('observacoes_pcm');

        if (!$podePlanejar && $enviouDadosPlanejamento) {
            abort(403, 'Somente Gestor ou PCM podem informar dados de planejamento.');
        }

        $prioridade = StatusPrioridade::findOrFail($request->id_status_prioridade);

        // Cálculo da previsão limite de atendimento (SLA)
        $horasSla = explode(':', $prioridade->tempo_limite_atendimento)[0] ?? 24;
        $dataPrevisao = Carbon::now()->addHours((int)$horasSla);

        $parouProducao = $request->has('parou_producao');

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
            'id_chamado'           => $podePlanejar ? $request->input('id_chamado') : null,
            'data_programada'      => $podePlanejar ? $request->input('data_programada') : null,
            'observacoes_pcm'      => $podePlanejar ? $request->input('observacoes_pcm') : null,
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
     * Exibir detalhes completos da Ordem de Serviço e técnicos disponíveis (RF08)
     */
    public function show($id)
    {
        $ordem = OrdemServico::with([
            'maquina.setor',
            'solicitante',
            'tipoManutencao',
            'prioridade',
            'tecnicos.turno',
            'parecer.perfil'
        ])->findOrFail($id);

        // Lista apenas usuários ativos com perfil Técnico para seleção na OS
        $tecnicosDisponiveis = User::whereHas('perfil', function ($q) {
            $q->where('tipo_perfil', 'Técnico');
        })->where('status', true)->get();

        return view('ordens.show', compact('ordem', 'tecnicosDisponiveis'));
    }

    /**
     * Alocar ou atualizar técnicos na OS (RF08, RN03 e RN10)
     */
    public function alocarTecnicos(Request $request, $id)
    {
        $request->validate([
            'tecnicos'   => 'nullable|array',
            'tecnicos.*' => 'exists:users,id_usuario',
        ]);

        $ordem = OrdemServico::with('prioridade')->findOrFail($id);

        // RN03: Histórico Imutável - OS concluída não permite alterar equipe técnica
        if ($ordem->status === 'Concluída') {
            return redirect()->route('ordens.show', $ordem->id_ordem_servico)
                ->with('error', 'Não é possível alterar técnicos de uma OS já concluída.');
        }

        $tecnicosSelecionados = $request->tecnicos ?? [];
        $prioridadeAtual = $ordem->prioridade->nome_prioridade ?? '';

        // RN10: Impede alocar técnico simultaneamente em duas OS abertas de prioridade Alta ou Crítica
        if (in_array($prioridadeAtual, ['Alta', 'Crítica']) && !empty($tecnicosSelecionados)) {
            $conflito = OrdemServico::where('id_ordem_servico', '!=', $ordem->id_ordem_servico)
                ->whereNotIn('status', ['Concluída', 'Cancelada'])
                ->whereHas('prioridade', function ($q) {
                    $q->whereIn('nome_prioridade', ['Alta', 'Crítica']);
                })
                ->whereHas('tecnicos', function ($q) use ($tecnicosSelecionados) {
                    $q->whereIn('users.id_usuario', $tecnicosSelecionados);
                })
                ->with(['tecnicos', 'prioridade'])
                ->first();

            if ($conflito) {
                $tecnicoConflitante = $conflito->tecnicos
                    ->whereIn('id_usuario', $tecnicosSelecionados)
                    ->first();

                return redirect()->route('ordens.show', $ordem->id_ordem_servico)
                    ->with('error', "Conflito de agenda (RN10): O técnico {$tecnicoConflitante->nome_usuario} já está alocado na OS #{$conflito->id_ordem_servico} (Prioridade {$conflito->prioridade->nome_prioridade}).");
            }
        }

        // Sincroniza a tabela pivô ordem_servico_tecnico
        $ordem->tecnicos()->sync($tecnicosSelecionados);

        return redirect()->route('ordens.show', $ordem->id_ordem_servico)
            ->with('success', 'Equipe técnica atualizada com sucesso!');
    }

    /**
     * Atualizar o status do atendimento e encerrar a OS (RF10, RN01, RN03, RN05, RN07)
     */
    public function atualizarStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Aberta,Aguardando Aprovação,Em Andamento,Aguardando Peça,Aguardando Validação,Concluída,Cancelada',
        ]);

        $ordem = OrdemServico::with('tecnicos')->findOrFail($id);
        $usuario = Auth::user();

        // RN03: Histórico Imutável - OS já concluída não pode ter o status alterado
        if ($ordem->status === 'Concluída') {
            return redirect()->route('ordens.show', $ordem->id_ordem_servico)
                ->with('error', 'Esta OS já foi concluída e não pode mais ser alterada (Histórico Imutável).');
        }

        // RN05: Técnico só altera status de OS na qual esteja alocado
        if (($usuario->perfil->tipo_perfil ?? '') === 'Técnico' && !$ordem->tecnicos->contains('id_usuario', $usuario->id_usuario)) {
            return redirect()->route('ordens.show', $ordem->id_ordem_servico)
                ->with('error', 'Acesso negado: você só pode atualizar o status de Ordens de Serviço em que está alocado.');
        }

        $novoStatus = $request->status;

        // RN01: Exige parecer técnico cadastrado para concluir a OS
        if ($novoStatus === 'Concluída' && is_null($ordem->id_parecer)) {
            return redirect()->route('ordens.show', $ordem->id_ordem_servico)
                ->with('error', 'Não é possível concluir a OS sem antes registrar o Parecer Técnico.');
        }

        $agora = Carbon::now();
        $dadosAtualizacao = ['status' => $novoStatus];

        if ($novoStatus === 'Em Andamento' && is_null($ordem->data_inicio_atendimento)) {
            $dadosAtualizacao['data_inicio_atendimento'] = $agora;
        }

        if ($novoStatus === 'Concluída' && is_null($ordem->data_fechamento)) {
            $dadosAtualizacao['data_fechamento'] = $agora;
            $dadosAtualizacao['tempo_parada_minutos'] = $ordem->data_abertura->diffInMinutes($agora);

            // RN07: Atualiza status da máquina para Ativa após conclusão
            if ($ordem->parou_producao) {
                $statusAtiva = StatusMaquina::where('estado_da_maquina', 'Ativa')->first();
                if ($statusAtiva && $ordem->maquina) {
                    $ordem->maquina->update(['id_status_maquina' => $statusAtiva->id_status_maquina]);
                }
            }
        }

        $ordem->update($dadosAtualizacao);

        return redirect()->route('ordens.show', $ordem->id_ordem_servico)
            ->with('success', "Status da OS alterado para: {$novoStatus}");
    }

    /**
     * Registrar ou atualizar o Parecer Técnico vinculado à OS (RF10, RN03, RN05)
     */
    public function registrarParecer(Request $request, $id)
    {
        $request->validate([
            'descricao_laudo'     => 'required|string',
            'condicao_pos_reparo' => 'required|in:Operacional / Aprovado,Operacional com Restrições / Parcial,Aguardando Peça,Condenado / Inoperante',
        ]);

        $ordem = OrdemServico::with('tecnicos')->findOrFail($id);
        $usuario = Auth::user();

        // RN03: Histórico Imutável - OS concluída não permite editar parecer
        if ($ordem->status === 'Concluída') {
            return redirect()->route('ordens.show', $ordem->id_ordem_servico)
                ->with('error', 'Não é possível alterar o parecer técnico de uma OS já concluída.');
        }

        // RN05: Técnico só registra parecer em OS na qual esteja alocado
        if (($usuario->perfil->tipo_perfil ?? '') === 'Técnico' && !$ordem->tecnicos->contains('id_usuario', $usuario->id_usuario)) {
            return redirect()->route('ordens.show', $ordem->id_ordem_servico)
                ->with('error', 'Acesso negado: você só pode registrar parecer técnico em Ordens de Serviço em que está alocado.');
        }

        if ($ordem->id_parecer) {
            $ordem->parecer->update([
                'id_perfil'           => $usuario->id_perfil,
                'data_registro'       => Carbon::now(),
                'descricao_laudo'     => $request->descricao_laudo,
                'condicao_pos_reparo' => $request->condicao_pos_reparo,
            ]);
        } else {
            $parecer = ParecerTecnico::create([
                'id_perfil'           => $usuario->id_perfil,
                'data_registro'       => Carbon::now(),
                'descricao_laudo'     => $request->descricao_laudo,
                'condicao_pos_reparo' => $request->condicao_pos_reparo,
            ]);

            $ordem->update(['id_parecer' => $parecer->id_parecer]);
        }

        return redirect()->route('ordens.show', $ordem->id_ordem_servico)
            ->with('success', 'Parecer técnico registrado com sucesso!');
    }

    /**
     * Bloqueio de exclusão para histórico imutável (RN03)
     */
    public function destroy($id)
    {
        $ordem = OrdemServico::findOrFail($id);

        if ($ordem->status === 'Concluída') {
            return redirect()->route('ordens.index')
                ->with('error', 'Registros de Ordens de Serviço concluídas são imutáveis e não podem ser excluídos.');
        }

        $ordem->tecnicos()->detach();
        $ordem->delete();

        return redirect()->route('ordens.index')
            ->with('success', 'Ordem de Serviço excluída com sucesso.');
    }
}