<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF;

use App\Domain\Financeiro\Contabilidade\Models\LrfValorManual;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\BalanceteVerificacaoInformacaoComplementarService;
use DateTime;
use Illuminate\Support\Facades\DB;
use stdClass;

abstract class AnexosExecucaoMensalLrfService extends AnexosService
{
    /**
     * Armazena a coleção dos ultimos doze meses de acordo com o período selecionado
     * @var array
     */
    protected $mesesProcessar = [];

    protected $mscMes = [];

    protected function executarMscMeses($estruturais = [])
    {
        $meses = $this->getMesesProcessar();

        foreach ($meses as $mes) {
            $this->mscMes[$mes->ano][$mes->mes][] = $this->executarMscMes($mes, $estruturais);
        }
    }

    /**
     * Retorna o service do Balancete de Verificação Complementar que processa a MSC
     * setando os filtros de dados mensais
     * @param stdClass $mes
     * @param array $estruturais
     * @return BalanceteVerificacaoInformacaoComplementarService
     */
    protected function getServiceMsc(stdClass $mes, array $estruturais)
    {
        $service = new BalanceteVerificacaoInformacaoComplementarService();
        $filtros = $this->getWhere();

        $filtros['exercicio'] = $mes->ano;
        $filtros['dataInicial'] = $mes->data_inicio;
        $filtros['dataFinal'] = $mes->data_fim;
        $filtros['estruturaisUniao'] = $estruturais;
        $service->setFiltrosArray($filtros);
        return $service;
    }

    /**
     * Valida se a conta tem mapeamento, se não tem retorna false
     * @param stdClass $mes
     * @param array $estruturais
     * @return bool
     */
    protected function validaConta(stdClass $mes, array $estruturais)
    {
        $service = $this->getServiceMsc($mes, $estruturais);
        $sql = $service->sqlFiltrarReduzidos();
        return count(DB::select($sql)) > 0;
    }

    /**
     * Executa a MSC no período do mês informado por parâmetro, filtrando as contas desejadas
     * @param stdClass $mes
     * @param array $estruturais
     * @return mixed
     */
    protected function executarMscMes(stdClass $mes, array $estruturais)
    {
        $service = $this->getServiceMsc($mes, $estruturais);
        return $service->processar();
    }

    public function getMesesProcessar()
    {
        if (!empty($this->mesesProcessar)) {
            return $this->mesesProcessar;
        }

        $dataInical = new DateTime("{$this->exercicio}-01-01");

        $mesFinal = $this->dataFim->format('m');
        if ($mesFinal != 12) {
            $mesInicial = $mesFinal + 1;
            $ano = $this->dataFim->format('y') - 1;
            $dataInical = new DateTime("{$ano}-{$mesInicial}-01");
        }

        $meses = 1;
        $listaMeses = \DBDate::getMesesExtenso();
        while ($meses <= 12) {
            $mes = $dataInical->format('m');
            $ano = $dataInical->format('Y');
            $dia = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
            $this->mesesProcessar[] = (object)[
                'nome' => $listaMeses[(int)$mes],
                'nome_abreviado' => \DBDate::getMesAbreviado((int)$mes),
                'mes' => $mes,
                'ano' => $ano,
                'coluna' => "mes_{$meses}",
                'data_inicio' => $dataInical->format('Y-m-d'),
                'data_fim' => "{$ano}-{$mes}-{$dia}",
            ];

            $dataInical->modify('+1 month');
            $meses++;
        }

        return $this->mesesProcessar;
    }

    protected function totalizaMeses()
    {
        foreach ($this->linhas as $linha) {
            if ($linha->totalizadora) {
                continue;
            }
            $linha->total_meses =
                $linha->mes_1 +
                $linha->mes_2 +
                $linha->mes_3 +
                $linha->mes_4 +
                $linha->mes_5 +
                $linha->mes_6 +
                $linha->mes_7 +
                $linha->mes_8 +
                $linha->mes_9 +
                $linha->mes_10 +
                $linha->mes_11 +
                $linha->mes_12;
        }
    }

    protected function totalizarSomaLinhasMensais(array $colunasSomar = [])
    {
        $mesesProcessar = $this->getMesesProcessar();
        foreach ($this->linhas as $linha) {
            if (!$linha->totalizadora || !isset($linha->somar)) {
                continue;
            }

            foreach ($linha->somar as $ordem) {
                $linhaSomar = $this->linhas[$ordem];
                // totaliza os meses
                foreach ($mesesProcessar as $mesProcessar) {
                    $linha->{$mesProcessar->coluna} += $linhaSomar->{$mesProcessar->coluna};
                }
                // to
                foreach ($colunasSomar as $coluna) {
                    $linha->{$coluna} += $linhaSomar->{$coluna};
                }
            }
        }
    }

    /**
     *
     * @param array $colunasSubtrair colunas
     * @return void
     */
    protected function totalizarSubtracaoLinhasMensais(array $colunasSubtrair = [])
    {
        $mesesProcessar = $this->getMesesProcessar();
        foreach ($this->linhas as $linha) {
            if ((!$linha->totalizadora || !isset($linha->subtrair))) {
                continue;
            }
            $primeira = array_shift($linha->subtrair); // extrai a ordem da primeira coluna a ser subtraída
            foreach ($mesesProcessar as $mes) {
                // define o valor inicial da coluna para após aplicar a subtração das demais
                $linha->{$mes->coluna} = $this->linhas[$primeira]->{$mes->coluna};
                foreach ($linha->subtrair as $ordem) {
                    $linha->{$mes->coluna} -= $this->linhas[$ordem]->{$mes->coluna};
                }
            }

            // define o valor inicial da coluna para após aplicar a subtração das demais
            foreach ($colunasSubtrair as $coluna) {
                $linha->{$coluna} = $this->linhas[$primeira]->{$coluna};
            }

            foreach ($linha->subtrair as $ordem) {
                foreach ($colunasSubtrair as $coluna) {
                    $linha->{$coluna} -= $this->linhas[$ordem]->{$coluna};
                }
            }
        }
    }

    /**
     * @param \Illuminate\Support\Collection $valoresManuais
     * @return void
     */
    public function calculaValorManualMensal(\Illuminate\Support\Collection $valoresManuais)
    {
        $meses = $this->getMesesProcessar();
        foreach ($meses as $mes) {
            $valoresManuais->filter(function ($valorManual) use ($mes) {
                return $valorManual['c180_exercicio'] === (int)$mes->ano
                    && $valorManual['c180_mes'] === (int)$mes->mes
                    && $valorManual['c180_coluna'] === 'vlr_mes';
            })->each(function ($valorManual) use ($mes) {
                $this->linhas[$valorManual['c180_linha']]->{$mes->coluna} += $valorManual['c180_valor'];
            });
        }
    }

    protected function getValoresManuais($linhas = [])
    {
        $meses = $this->getMesesProcessar();

        $valoresManuais = [];
        foreach ($meses as $mes) {
            $dados = LrfValorManual::query()
                ->where('c180_relatorio', $this->relatorio)
                ->whereIn('c180_instituicao', $this->listaInstituicoes)
                ->where('c180_exercicio', $mes->ano)
                ->where('c180_mes', (int)$mes->mes)
                ->whereIn('c180_linha', $linhas)
                ->get();
            if ($dados->count()) {
                $valoresManuais = array_merge($valoresManuais, $dados->toArray());
            }
        }

        return collect($valoresManuais);
    }

    /**
     * Retorna a descrição dos meses processado no período emitido
     * @return string
     */
    protected function mesesProcessadosPeriodo()
    {
        $mesesProcessar = $this->getMesesProcessar();

        foreach ($mesesProcessar as $mesProcessar) {
            $mes = "{$mesProcessar->nome_abreviado}/{$mesProcessar->ano}";
            $this->parser->setVariavel($mesProcessar->coluna, $mes);
        }

        $mes1 = $mesesProcessar[0];
        $mes12 = $mesesProcessar[11];

        $mesesPeriodo = sprintf(
            '%s - %s',
            "{$mes1->nome}/{$mes1->ano}",
            "{$mes12->nome}/{$mes12->ano}"
        );

        return $mesesPeriodo;
    }
}
