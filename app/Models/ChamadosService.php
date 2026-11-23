<?php

declare(strict_types=1);

namespace App\Services;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;

/**
 * Lógica do módulo CHAMADOS (solicitações, prioridade e manutenção preventiva).
 *
 * Este arquivo NÃO acessa banco, Model, Request nem Controller.
 * Recebe arrays/datas e devolve arrays/datas. Depois ligamos ao resto do projeto.
 *
 * Destino sugerido no projeto: app/Services/ChamadoService.php
 *
 * Baseado no ER:
 *  - Status_prioridade (Baixa, Média, Alta, Crítica + tempo_limite_atendimento)
 *  - Status_agendamento (Agendado, Realizado, Atrasado, Cancelado)
 *  - Cronograma_preventiva (idMaquina, frequencia_dias, proxima_data, descricao_atividade, data_inicio, data_fim)
 *  - Agenda_preventiva (liga cronograma a status_agendamento)
 *  - Lista_manutencao (checklist por tipo de manutenção)
 *  - Tipo_manutencao (Preventiva, Corretiva, Preditiva)
 */
final class ChamadoService
{
    /** Prazo padrão de atendimento (horas) por prioridade. Depois vem de status_prioridade.tempo_limite_atendimento. */
    public const PRIORIDADES = [
        'Baixa'   => 168,
        'Média'   => 72,
        'Alta'    => 24,
        'Crítica' => 4,
    ];

    public const STATUS_AGENDAMENTO = ['Agendado', 'Realizado', 'Atrasado', 'Cancelado'];

    public const TIPOS_MANUTENCAO = ['Preventiva', 'Corretiva', 'Preditiva'];

    /** Prefixo do número exibido no painel (M457). */
    public const PREFIXO_CHAMADO = 'M';

    /** Cor do triângulo de prioridade no painel de alertas. */
    public const COR_PRIORIDADE = [
        'Crítica' => 'vermelho',
        'Alta'    => 'vermelho',
        'Média'   => 'amarelo',
        'Baixa'   => 'verde',
    ];

    /** Peso para ordenar a lista (maior = aparece primeiro). */
    private const PESO_PRIORIDADE = ['Crítica' => 4, 'Alta' => 3, 'Média' => 2, 'Baixa' => 1];

    /** Ciclo do chamado: Aberto (aparece como alerta) -> Convertido (virou OS) ou Cancelado. */
    public const STATUS_CHAMADO = ['Aberto', 'Convertido', 'Cancelado'];

    /** Único perfil que transforma chamado em ordem de serviço. Ajuste ao nome usado em perfis.tipo_perfil. */
    public const PERFIL_PCM = 'PCM';

    /** Peso da cor do alerta (vermelho primeiro). */
    private const PESO_COR = ['vermelho' => 3, 'amarelo' => 2, 'verde' => 1];

    /** Importância da máquina, definida no cadastro dela: 1 (menor) a 5 (maior). */
    public const IMPORTANCIA_MIN = 1;
    public const IMPORTANCIA_MAX = 5;

    /** Transições permitidas de status do agendamento. */
    private const TRANSICOES = [
        'Agendado'  => ['Realizado', 'Atrasado', 'Cancelado'],
        'Atrasado'  => ['Realizado', 'Cancelado'],
        'Realizado' => [],
        'Cancelado' => [],
    ];

    // ------------------------------------------------------------------
    // 1. PRIORIDADE E PRAZO DE ATENDIMENTO
    // ------------------------------------------------------------------

    /** Calcula até quando o chamado deve ser atendido. */
    public function prazoAtendimento(
        string $prioridade,
        DateTimeInterface $abertura,
        ?int $limiteHoras = null
    ): DateTimeImmutable {
        if (!isset(self::PRIORIDADES[$prioridade])) {
            throw new InvalidArgumentException("Prioridade inválida: {$prioridade}");
        }

        $horas = $limiteHoras ?? self::PRIORIDADES[$prioridade];

        return $this->data($abertura)->modify("+{$horas} hours");
    }

    /** Chamado está fora do prazo? (só vale se ainda não foi concluído) */
    public function atendimentoAtrasado(
        DateTimeInterface $prazo,
        DateTimeInterface $agora,
        bool $concluido = false
    ): bool {
        return !$concluido && $this->data($agora) > $this->data($prazo);
    }

    // ------------------------------------------------------------------
    // 2. CRONOGRAMA PREVENTIVO
    // ------------------------------------------------------------------

    /**
     * Valida os dados de um cronograma. Retorna lista de erros (vazia = válido).
     * Campos: id_maquina, frequencia_dias, descricao_atividade, data_inicio, data_fim (opcional)
     */
    public function validarCronograma(array $c): array
    {
        $erros = [];

        if (empty($c['id_maquina'])) {
            $erros[] = 'Selecione a máquina.';
        }
        if (!isset($c['frequencia_dias']) || (int) $c['frequencia_dias'] < 1) {
            $erros[] = 'A frequência deve ser de pelo menos 1 dia.';
        }
        if (trim((string) ($c['descricao_atividade'] ?? '')) === '') {
            $erros[] = 'Descreva a atividade preventiva.';
        }
        if (empty($c['data_inicio'])) {
            $erros[] = 'Informe a data de início.';
        } elseif (!empty($c['data_fim']) && $this->data($c['data_fim']) < $this->data($c['data_inicio'])) {
            $erros[] = 'A data de fim não pode ser anterior à data de início.';
        }

        return $erros;
    }

    /** Próxima data depois de $base. Retorna null se passar de data_fim. */
    public function proximaData(
        DateTimeInterface $base,
        int $frequenciaDias,
        ?DateTimeInterface $dataFim = null
    ): ?DateTimeImmutable {
        if ($frequenciaDias < 1) {
            throw new InvalidArgumentException('Frequência deve ser >= 1 dia.');
        }

        $proxima = $this->dia($base)->modify("+{$frequenciaDias} days");

        if ($dataFim !== null && $proxima > $this->dia($dataFim)) {
            return null;
        }

        return $proxima;
    }

    /**
     * Lista todas as datas previstas do cronograma entre data_inicio e $ate.
     * Útil para montar a agenda (Agenda_preventiva) e o calendário.
     *
     * @return DateTimeImmutable[]
     */
    public function ocorrencias(array $cronograma, DateTimeInterface $ate, int $limite = 366): array
    {
        $frequencia = (int) $cronograma['frequencia_dias'];
        $fim        = !empty($cronograma['data_fim']) ? $this->data($cronograma['data_fim']) : null;
        $ate        = $this->dia($ate);
        $atual      = $this->dia($cronograma['data_inicio']);
        $datas      = [];

        while ($atual <= $ate && count($datas) < $limite) {
            $datas[] = $atual;
            $atual = $this->proximaData($atual, $frequencia, $fim);
            if ($atual === null) {
                break;
            }
        }

        return $datas;
    }

    // ------------------------------------------------------------------
    // 3. STATUS DO AGENDAMENTO
    // ------------------------------------------------------------------

    /** Status que um agendamento deveria ter hoje. */
    public function statusAgendamento(
        DateTimeInterface $dataPrevista,
        DateTimeInterface $hoje,
        bool $realizada = false,
        bool $cancelada = false
    ): string {
        if ($cancelada) {
            return 'Cancelado';
        }
        if ($realizada) {
            return 'Realizado';
        }

        return $this->dia($dataPrevista) < $this->dia($hoje) ? 'Atrasado' : 'Agendado';
    }

    public function podeTransitar(string $de, string $para): bool
    {
        return in_array($para, self::TRANSICOES[$de] ?? [], true);
    }

    /** Valida e devolve o novo status; lança exceção se a mudança não for permitida. */
    public function transitar(string $de, string $para): string
    {
        if (!$this->podeTransitar($de, $para)) {
            throw new InvalidArgumentException("Não é possível mudar de '{$de}' para '{$para}'.");
        }

        return $para;
    }

    // ------------------------------------------------------------------
    // 4. GERAÇÃO AUTOMÁTICA DE ORDEM DE SERVIÇO
    // ------------------------------------------------------------------

    /** Filtra os cronogramas que já chegaram na data (proxima_data <= hoje). */
    public function cronogramasVencidos(array $cronogramas, DateTimeInterface $hoje): array
    {
        $hoje = $this->dia($hoje);

        return array_values(array_filter(
            $cronogramas,
            fn(array $c) => !empty($c['proxima_data']) && $this->dia($c['proxima_data']) <= $hoje
        ));
    }

    /**
     * Gera os dados da Ordem de Serviço preventiva e já calcula a próxima data do cronograma.
     * Retorna null se o cronograma ainda não venceu.
     *
     * Retorno:
     *  - 'ordem'      => dados para criar a OrdemServico
     *  - 'cronograma' => cronograma com proxima_data atualizada (null = plano encerrado)
     *  - 'agenda'     => registro para Agenda_preventiva
     */
    public function gerarOrdemServico(
        array $cronograma,
        DateTimeInterface $hoje,
        string $prioridade = 'Média'
    ): ?array {
        $hoje = $this->dia($hoje);
        $prevista = $this->dia($cronograma['proxima_data']);

        if ($prevista > $hoje) {
            return null;
        }

        $fim = !empty($cronograma['data_fim']) ? $this->data($cronograma['data_fim']) : null;

        // Avança mantendo o ritmo do plano até cair numa data futura
        // (evita gerar várias OS de uma vez se o sistema ficou parado).
        $proxima = $prevista;
        while ($proxima !== null && $proxima <= $hoje) {
            $proxima = $this->proximaData($proxima, (int) $cronograma['frequencia_dias'], $fim);
        }

        $cronograma['proxima_data'] = $proxima?->format('Y-m-d');

        return [
            'ordem' => [
                'id_maquina'      => $cronograma['id_maquina'],
                'tipo_manutencao' => 'Preventiva',
                'descricao_falha' => $cronograma['descricao_atividade'],
                'prioridade'      => $prioridade,
                'data_abertura'   => $hoje->format('Y-m-d H:i:s'),
                'data_prevista'   => $this->prazoAtendimento($prioridade, $hoje)->format('Y-m-d H:i:s'),
            ],
            'cronograma' => $cronograma,
            'agenda' => [
                'id_cronograma' => $cronograma['id_cronograma'] ?? null,
                'status'        => 'Agendado',
                'data_prevista' => $prevista->format('Y-m-d'),
            ],
        ];
    }

    // ------------------------------------------------------------------
    // 5. CHECKLIST (Lista_manutencao)
    // ------------------------------------------------------------------

    /**
     * Progresso do checklist de uma manutenção.
     * Cada item: ['descricao_atividade' => '...', 'status_manutencao' => true|false]
     */
    public function progressoChecklist(array $itens): array
    {
        $total      = count($itens);
        $concluidos = count(array_filter($itens, fn(array $i) => !empty($i['status_manutencao'])));

        return [
            'total'      => $total,
            'concluidos' => $concluidos,
            'percentual' => $total > 0 ? (int) round($concluidos / $total * 100) : 0,
            'completo'   => $total > 0 && $concluidos === $total,
        ];
    }

    // ------------------------------------------------------------------
    // 6. CHAMADOS (solicitação do operador) E PAINEL DE ALERTAS
    // ------------------------------------------------------------------

    /** Número exibido na tela: 457 -> "M457". */
    public function numeroChamado(int $id): string
    {
        return self::PREFIXO_CHAMADO . $id;
    }

    /** Valida a abertura de um chamado. Retorna lista de erros (vazia = válido). */
    public function validarChamado(array $c): array
    {
        $erros = [];

        if (empty($c['id_maquina'])) {
            $erros[] = 'Informe a máquina que precisa de reparo.';
        }
        if (empty($c['id_operador'])) {
            $erros[] = 'Informe o operador que abriu o chamado.';
        }
        if (!isset(self::PRIORIDADES[$c['prioridade'] ?? ''])) {
            $erros[] = 'Prioridade inválida.';
        }
        if (trim((string) ($c['descricao'] ?? '')) === '') {
            $erros[] = 'Descreva o problema.';
        }

        return $erros;
    }

    /**
     * Monta o chamado novo, pronto para salvar. O número (M457) vem do id gerado pelo banco.
     * Entrada: id_maquina, id_operador, prioridade, descricao.
     */
    public function abrirChamado(array $dados, DateTimeInterface $agora): array
    {
        $erros = $this->validarChamado($dados);
        if ($erros !== []) {
            throw new InvalidArgumentException(implode(' ', $erros));
        }

        $abertura = $this->data($agora);

        return [
            'id_maquina'        => $dados['id_maquina'],
            'id_operador'       => $dados['id_operador'],
            'prioridade'        => $dados['prioridade'],
            'descricao'         => trim($dados['descricao']),
            'status'            => 'Aberto',
            'data_abertura'     => $abertura->format('Y-m-d H:i:s'),
            'prazo_atendimento' => $this->prazoAtendimento($dados['prioridade'], $abertura)->format('Y-m-d H:i:s'),
        ];
    }

    /** Chamado ainda precisa de atenção? */
    public function chamadoAberto(array $chamado): bool
    {
        return ($chamado['status'] ?? 'Aberto') === 'Aberto';
    }

    /** Quantidade para o contador "5 CHAMADOS". */
    public function contarChamadosAbertos(array $chamados): int
    {
        return count(array_filter($chamados, fn(array $c) => $this->chamadoAberto($c)));
    }

    /**
     * Linha do painel de alertas:
     * CHAMADO: M457 | MÁQUINA: ID332 | OPERADOR: ID20582 | triângulo colorido | ENTRAR
     * Entrada: id_chamado, id_maquina, id_operador, prioridade, status, prazo_atendimento (opcional)
     */
    public function montarAlerta(array $chamado, DateTimeInterface $agora): array
    {
        $prioridade = $chamado['prioridade'];
        $prazo      = $chamado['prazo_atendimento'] ?? null;

        return [
            'id'         => $chamado['id_chamado'],
            'numero'     => $this->numeroChamado((int) $chamado['id_chamado']),
            'maquina'    => 'ID' . $chamado['id_maquina'],
            'operador'   => 'ID' . $chamado['id_operador'],
            'prioridade' => $prioridade,
            'cor'        => self::COR_PRIORIDADE[$prioridade] ?? 'verde',
            'atrasado'   => $prazo !== null
                && $this->atendimentoAtrasado($this->data($prazo), $agora, !$this->chamadoAberto($chamado)),
        ];
    }

    /**
     * Ordem do painel de alertas (só chamados Abertos):
     *  1. cor da prioridade (vermelho > amarelo > verde)
     *  2. dentro da mesma cor, máquina de maior importância primeiro
     *     (campo 'importancia_maquina' do chamado, vindo do cadastro da máquina)
     *  3. em empate, prioridade mais alta (Crítica antes de Alta)
     *  4. por fim, o chamado mais antigo primeiro
     */
    public function ordenarAlertas(array $chamados): array
    {
        $abertos = array_values(array_filter($chamados, fn(array $c) => $this->chamadoAberto($c)));

        usort($abertos, function (array $a, array $b): int {
            $ka = $this->chaveAlerta($a);
            $kb = $this->chaveAlerta($b);

            // Três primeiros critérios em ordem decrescente; data em ordem crescente.
            return [$kb[0], $kb[1], $kb[2], (string) ($a['data_abertura'] ?? '')]
                <=> [$ka[0], $ka[1], $ka[2], (string) ($b['data_abertura'] ?? '')];
        });

        return $abertos;
    }

    /** @return int[] [pesoCor, importanciaMaquina, pesoPrioridade] */
    private function chaveAlerta(array $c): array
    {
        $prioridade  = $c['prioridade'] ?? '';
        $cor         = self::COR_PRIORIDADE[$prioridade] ?? 'verde';
        $importancia = $c['importancia_maquina'] ?? 0;

        return [
            self::PESO_COR[$cor],
            $this->importanciaValida($importancia) ? (int) $importancia : 0, // sem importância válida: fica abaixo das demais
            self::PESO_PRIORIDADE[$prioridade] ?? 0,
        ];
    }

    // ------------------------------------------------------------------
    // 6.1 PCM: CHAMADO -> ORDEM DE SERVIÇO -> TÉCNICO
    // ------------------------------------------------------------------

    /** Importância válida = inteiro de 1 a 5. Use também na validação do cadastro da máquina. */
    public function importanciaValida(mixed $valor): bool
    {
        return filter_var($valor, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => self::IMPORTANCIA_MIN, 'max_range' => self::IMPORTANCIA_MAX],
        ]) !== false;
    }

    public function podeConverterChamado(string $perfil): bool
    {
        return $perfil === self::PERFIL_PCM;
    }

    /**
     * Técnicos livres para a PCM escolher. Quem decide é a PCM; aqui só se filtra.
     * Cada técnico: id_usuario, nome, ativo (bool), ordens_em_andamento (int)
     */
    public function tecnicosDisponiveis(array $tecnicos, int $maxSimultaneas = 1): array
    {
        return array_values(array_filter(
            $tecnicos,
            fn(array $t) => !empty($t['ativo']) && (int) ($t['ordens_em_andamento'] ?? 0) < $maxSimultaneas
        ));
    }

    /**
     * A PCM transforma o chamado em ordem de serviço.
     * O prazo continua contando a partir da abertura do chamado pelo operador.
     *
     * Retorno:
     *  - 'ordem'   => dados para criar a OrdemServico
     *  - 'chamado' => chamado atualizado (status Convertido, quem converteu e quando)
     */
    public function converterEmOrdemServico(
        array $chamado,
        int|string $idPcm,
        string $perfil,
        DateTimeInterface $agora,
        int|string|null $idTecnico = null
    ): array {
        if (!$this->podeConverterChamado($perfil)) {
            throw new InvalidArgumentException('Somente a PCM pode transformar chamado em ordem de serviço.');
        }
        if (!$this->chamadoAberto($chamado)) {
            throw new InvalidArgumentException('Este chamado já foi tratado.');
        }

        $agora = $this->data($agora);
        $prazo = $chamado['prazo_atendimento']
            ?? $this->prazoAtendimento($chamado['prioridade'], $this->data($chamado['data_abertura']))->format('Y-m-d H:i:s');

        $chamado['status']         = 'Convertido';
        $chamado['id_pcm']         = $idPcm;
        $chamado['data_conversao'] = $agora->format('Y-m-d H:i:s');

        return [
            'ordem' => [
                'id_chamado'      => $chamado['id_chamado'],
                'id_maquina'      => $chamado['id_maquina'],
                'id_solicitante'  => $chamado['id_operador'],
                'id_tecnico'      => $idTecnico,
                'tipo_manutencao' => 'Corretiva',
                'descricao_falha' => $chamado['descricao'],
                'prioridade'      => $chamado['prioridade'],
                'status'          => 'Aberta',
                'data_abertura'   => $agora->format('Y-m-d H:i:s'),
                'data_prevista'   => $prazo,
            ],
            'chamado' => $chamado,
        ];
    }

    /** Painel completo de alertas: contador + linhas ordenadas. */
    public function painelAlertas(array $chamados, DateTimeInterface $agora, int $limite = 5): array
    {
        $ordenados = $this->ordenarAlertas($chamados);

        return [
            'total'   => count($ordenados),
            'alertas' => array_map(
                fn(array $c) => $this->montarAlerta($c, $agora),
                array_slice($ordenados, 0, $limite)
            ),
        ];
    }

    // ------------------------------------------------------------------
    // 7. CALENDÁRIO DE MANUTENÇÃO (painéis "HOJE" e mês)
    // ------------------------------------------------------------------

    /**
     * Itens do painel HOJE, em ordem de horário.
     * Cada agendamento: titulo, id_maquina, data_prevista ('Y-m-d H:i')
     */
    public function agendaDoDia(array $agendamentos, DateTimeInterface $hoje): array
    {
        $dia = $this->dia($hoje)->format('Y-m-d');

        $itens = array_filter(
            $agendamentos,
            fn(array $a) => $this->data($a['data_prevista'])->format('Y-m-d') === $dia
        );

        usort($itens, fn(array $a, array $b) => strcmp($a['data_prevista'], $b['data_prevista']));

        return array_map(fn(array $a) => [
            'titulo'  => $a['titulo'],
            'maquina' => 'ID' . $a['id_maquina'],
            'hora'    => $this->data($a['data_prevista'])->format('H:i'),
        ], array_values($itens));
    }

    /** Dias do mês que têm manutenção (para destacar no calendário). */
    public function diasComManutencao(array $agendamentos, int $ano, int $mes): array
    {
        $dias = [];

        foreach ($agendamentos as $a) {
            $d = $this->data($a['data_prevista']);
            if ((int) $d->format('Y') === $ano && (int) $d->format('n') === $mes) {
                $dias[(int) $d->format('j')] = true;
            }
        }

        $dias = array_keys($dias);
        sort($dias);

        return $dias;
    }

    // ------------------------------------------------------------------
    // Auxiliares
    // ------------------------------------------------------------------

    private function data(DateTimeInterface|string $valor): DateTimeImmutable
    {
        return $valor instanceof DateTimeInterface
            ? DateTimeImmutable::createFromInterface($valor)
            : new DateTimeImmutable($valor);
    }

    /** Mesma data, zerada para 00:00 (comparar por dia). */
    private function dia(DateTimeInterface|string $valor): DateTimeImmutable
    {
        return $this->data($valor)->setTime(0, 0);
    }
}