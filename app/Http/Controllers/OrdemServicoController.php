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
use App\Models\Peca;//usado para pecas
use App\Models\StatusPeca;//usado para pecas
use App\Models\Utiliza;//usado para pecas
use Illuminate\Support\Facades\DB;//usado para pecas

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
    //    --------------->                                                       Adicionado: carrega as peças já usadas nesta OS 'pecas'
        $ordem = OrdemServico::with([ 'maquina.setor', 'solicitante', 'tipoManutencao', 'prioridade', 'tecnicos.turno', 'pecas' ])->findOrFail($id);

        // Lista apenas usuários ativos com perfil Técnico para seleção na OS
        $tecnicosDisponiveis = User::whereHas('perfil', function ($q) {$q->where('tipo_perfil', 'Técnico');
        })->where('status', true)->get();

        // <-- Adicionado: busca peças ativas com saldo no almoxarifado
        $pecasDisponiveis = Peca::where('status', true)
            ->where('quantidade_estoque', '>', 0)
            ->orderBy('nome_peca')
            ->get();

        return view('ordens.show', compact('ordem', 'tecnicosDisponiveis','pecasDisponiveis'));
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




    /**
     * Vincular peça na OS e dar baixa automática no estoque UTILIZADO PARA PECAS//sabrina
     */
    public function adicionarPeca(Request $request, $id)
    {
        $request->validate([
            'id_peca' => 'required|exists:pecas,id_peca',
            'quantidade_utilizada' => 'required|integer|min:1',
        ]);

        $ordem = OrdemServico::findOrFail($id);
        $peca = Peca::findOrFail($request->id_peca);

        // Impede baixa maior que o saldo físico disponível
        if ($peca->quantidade_estoque < $request->quantidade_utilizada) {
            return redirect()->route('ordens.show', $ordem->id_ordem_servico)
                ->withErrors(['quantidade_utilizada' => "Saldo insuficiente! Estoque atual de {$peca->nome_peca}: {$peca->quantidade_estoque} {$peca->unidade_medida}."]);
        }

        DB::transaction(function () use ($peca, $ordem, $request) {
            // 1. Grava na tabela 'utilizas'
            Utiliza::create([
                'id_ordem_servico' => $ordem->id_ordem_servico,
                'id_peca' => $peca->id_peca,
                'quantidade_utilizada' => $request->quantidade_utilizada,
            ]);

            // 2. Abate do estoque da peça
            $peca->decrement('quantidade_estoque', $request->quantidade_utilizada);

            // 3. Registra no histórico 'status_pecas'
            $alerta = $peca->quantidade_estoque == 0 ? ' [ALERTA: RUPTURA]' : '';
            StatusPeca::create([
                'id_peca' => $peca->id_peca,
                'descricao_status_peca' => mb_substr(
                    "Baixa OS #{$ordem->id_ordem_servico}: -{$request->quantidade_utilizada} {$peca->unidade_medida} (Restam: {$peca->quantidade_estoque}){$alerta}",
                    0,
                    200
                ),
            ]);
        });

        return redirect()->route('ordens.show', $ordem->id_ordem_servico)
            ->with('success', 'Peça adicionada à OS e estoque atualizado!');
    }

    /**
     * Remover peça da OS e estornar quantidade ao estoque
     */
    public function removerPeca($idUtiliza)
    {
        $utiliza = Utiliza::findOrFail($idUtiliza);
        $peca = Peca::findOrFail($utiliza->id_peca);
        $idOrdem = $utiliza->id_ordem_servico;

        DB::transaction(function () use ($utiliza, $peca, $idOrdem) {
            // 1. Devolve a quantidade ao estoque
            $peca->increment('quantidade_estoque', $utiliza->quantidade_utilizada);

            // 2. Registra o estorno em 'status_pecas'
            StatusPeca::create([
                'id_peca' => $peca->id_peca,
                'descricao_status_peca' => mb_substr(
                    "Estorno OS #{$idOrdem}: +{$utiliza->quantidade_utilizada} {$peca->unidade_medida} (Saldo: {$peca->quantidade_estoque})",
                    0,
                    200
                ),
            ]);

            // 3. Exclui o vínculo
            $utiliza->delete();
        });

        return redirect()->route('ordens.show', $idOrdem)
            ->with('success', 'Peça removida da OS e devolvida ao estoque!');
    }
    //FIM DA UTILIZAÇAO PARA PEÇAS AQUI
}