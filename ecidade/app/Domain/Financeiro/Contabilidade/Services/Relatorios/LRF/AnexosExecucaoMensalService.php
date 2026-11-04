<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF;

use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Class AnexosExecucaoMensalService
 * --------------------------------------------------------------------------------------------------------------------
 *                                                ATENÇÃO
 * Essa classe executa os balancetes necessários mensalmente dos últimos 12 meses a partir do período informado.
 *
 * Outra coisa importante realçar que o nome das colunas deve ser: mes_1, mes_2..., mes_12.
 * --------------------------------------------------------------------------------------------------------------------
 * @package App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF
 */
abstract class AnexosExecucaoMensalService extends AnexosService
{
    /**
     * Armazena a coleção dos ultimos doze meses de acordo com o período selecionado
     * @var array
     */
    protected $mesesProcessar = [];
    /**
     * Mapeia as linhas que não devem ser processada/calculada ao executar os balancetes
     * @var array
     */
    protected $linhasNaoProcessarMensal = [];
    /**
     * Mapeia as linhas da despesa que devem olhar o elemento por desdobramento
     * @var array
     */
    protected $linhasProcessarDespesaDesdobramento = [];
    /**
     * Execução dos últimos doze (12) meses do Balancete da Receita
     * @var array
     */
    protected $balanceteReceitaMensal = [];
    /**
     * Execução dos últimos doze (12) meses do Balancete de Verificação
     * @var array
     */
    protected $balanceteVerificacaoMensal = [];
    /**
     * Execução dos últimos doze (12) meses do Balancete da Despesa
     * @var array
     */
    protected $balanceteDespesaMensal = [];
    /**
     * Execução dos últimos doze (12) meses do Restos a Pagar
     * @var array
     */
    protected $restosPagarMensal = [];

    /**
     * Execução dos últimos doze (12) meses da despesa por desdobramento.
     * @var array
     */
    protected $despesaMensalDesdobramento = [];

    /**
     * Mapeia as linhas que são processadas mensalmente.
     * Isso é usado na hora de totalizar as linhas e só precisa ser utilizado caso haja necessidade de calcular os
     * totais
     * @var int[]
     */
    protected $linhasMensais = [];
    /**
     * @return array
     * @throws Exception
     */
    public function getMesesProcessar()
    {
        if (!empty($this->mesesProcessar)) {
            return $this->mesesProcessar;
        }

        $dataInical = new DateTime("{$this->exercicio}-01-01");
        $dataFinal = $this->periodo->getDataFinal($this->exercicio);
        if ($dataFinal->getMes() != 12) {
            $mesInicial = $dataFinal->getMes() + 1;
            $ano = $dataFinal->getAno() - 1;
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

    /**
     * @param $linhas
     * @throws Exception
     */
    protected function processaLinhasMensais(&$linhas)
    {
        foreach ($linhas as $linha) {
            if (in_array($linha->ordem, $this->linhasNaoProcessarMensal) || $linha->totalizadora) {
                continue;
            }

            if ((int)$linha->origem === self::ORIGEM_RECEITA) {
                $this->processaReceitaMensal($this->getDadosReceitaMensal(), $linha);
            }

            if ((int)$linha->origem === self::ORIGEM_DESPESA) {
                $this->processaDespesaMensal($this->getDadosDespesaMensal(), $linha);
            }
            /*
             * vou deixar comentado pos não irei implementar agora
             *
            if ((int)$linha->origem === self::ORIGEM_VERIFICACAO) {
                $this->processaBalanceteVerificacaoMensal($this->getDadosVerificacaoMensal(), $linha);
            }
            if ((int)$linha->origem === self::ORIGEM_RP) {
                $this->processaRestoPagarMensal($this->getDadosRestosPagarMensal(), $linha);
            }
            */
        }
    }

    /**
     * Executa o balancete da receita por mês
     * @return array
     * @throws Exception
     */
    public function getDadosReceitaMensal()
    {
        if (empty($this->balanceteReceitaMensal)) {
            $mesesProcessar = $this->getMesesProcessar();
            foreach ($mesesProcessar as $mesProcessar) {
                $this->balanceteReceitaMensal[$mesProcessar->coluna] = $this->executarBalanceteReceita(
                    $mesProcessar->ano,
                    $mesProcessar->data_inicio,
                    $mesProcessar->data_fim
                );
            }
        }
        return $this->balanceteReceitaMensal;
    }

    /**
     * Executa o balancete da despesa por mês
     * @return array
     * @throws Exception
     */
    public function getDadosDespesaMensal()
    {
        if (empty($this->balanceteDespesaMensal)) {
            $mesesProcessar = $this->getMesesProcessar();
            foreach ($mesesProcessar as $mesProcessar) {
                $this->balanceteDespesaMensal[$mesProcessar->coluna] = $this->executarBalanceteDespesa(
                    $mesProcessar->ano,
                    $mesProcessar->data_inicio,
                    $mesProcessar->data_fim
                );
            }
        }

        return $this->balanceteDespesaMensal;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getDadosDespesaPorDesdobramento()
    {
        if (empty($this->despesaMensalDesdobramento)) {
            $mesesProcessar = $this->getMesesProcessar();
            foreach ($mesesProcessar as $mesProcessar) {
                $this->despesaMensalDesdobramento[$mesProcessar->coluna] = $this->executarDespesaAteDesdobramento(
                    $mesProcessar->ano,
                    $mesProcessar->data_inicio,
                    $mesProcessar->data_fim
                );
            }
        }

        return $this->despesaMensalDesdobramento;
    }

    protected function executarDespesaAteDesdobramento($exercicio, $dataInicio, $dataFim)
    {
        $instituicoes = $this->listaInstituicoes->implode(',');
        $where = [
            "empempenho.e60_instit in ($instituicoes)",
            "conlancam.c70_data between '{$dataInicio}' and '{$dataFim}'",
            "conhistdoc.c53_tipo IN (10, 11, 20, 21, 30, 31)",
            "orcdotacao.o58_anousu = {$exercicio}",
            "empempenho.e60_anousu = {$exercicio}",
            "conplanoorcamento.c60_anousu = {$exercicio}"
        ];

        $where = implode(' and ', $where);
        $sql = "
        select elemento,
               orgao,
               unidade,
               funcao,
               subfuncao,
               programa,
               projeto,
               recurso,
               fonte_recurso,
               complemento,
               (empenhado - empenhado_estornado) as empenhado,
               (liquidado - liquidado_estornado) as liquidado,
               (pagamento - pagamento_estornado) as pagamento
          from (
                SELECT ele.o56_elemento AS elemento,
                       o58_orgao as orgao,
                       o58_unidade as unidade,
                       o58_funcao as funcao,
                       o58_subfuncao as subfuncao,
                       o58_programa as programa,
                       o58_projativ as projeto,
                       sum(CASE WHEN c53_tipo = 10 THEN c70_valor ELSE 0 END) AS empenhado,
                       sum(CASE WHEN c53_tipo = 11 THEN c70_valor ELSE 0 END) AS empenhado_estornado,
                       sum(CASE WHEN c53_tipo = 20 THEN c70_valor ELSE 0 END) AS liquidado,
                       sum(CASE WHEN c53_tipo = 21 THEN c70_valor ELSE 0 END) AS liquidado_estornado,
                       sum(CASE WHEN c53_tipo = 30 THEN c70_valor ELSE 0 END) AS pagamento,
                       sum(CASE WHEN c53_tipo = 31 THEN c70_valor ELSE 0 END) AS pagamento_estornado,
                       o15_codigo as recurso,
                       o15_recurso as fonte_recurso,
                       o15_complemento as complemento
                FROM conlancam
                JOIN conlancamele ON conlancamele.c67_codlan = conlancam.c70_codlan
                JOIN conlancamemp ON conlancamemp.c75_codlan = conlancam.c70_codlan
                JOIN empempenho ON empempenho.e60_numemp = conlancamemp.c75_numemp
                JOIN orcdotacao ON orcdotacao.o58_coddot = empempenho.e60_coddot
                     AND orcdotacao.o58_anousu = empempenho.e60_anousu
                JOIN conplanoorcamento ON conplanoorcamento.c60_codcon = orcdotacao.o58_codele
                JOIN conlancamdoc ON conlancamdoc.c71_codlan = conlancam.c70_codlan
                JOIN conhistdoc ON conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
                JOIN orcelemento ele ON ele.o56_codele = conlancamele.c67_codele
                     AND ele.o56_anousu = o58_anousu
                join conlancamcomplementorecurso on conlancamcomplementorecurso.o201_codlan = conlancam.c70_codlan
                join orctiporec on orctiporec.o15_codigo = conlancamcomplementorecurso.o201_orctiporec
                WHERE {$where}
                GROUP BY o56_elemento, o58_orgao, o58_unidade, o58_funcao, o58_subfuncao, o58_programa, projeto,
                recurso, fonte_recurso, complemento
        ) AS X
        ";

        return DB::select($sql);
    }

    /**
     * Executa o balancete da despesa por mês
     * @return array
     * @throws Exception
     */
    public function getDadosVerificacaoMensal()
    {
        if (empty($this->balanceteVerificacaoMensal)) {
            $mesesProcessar = $this->getMesesProcessar();
            foreach ($mesesProcessar as $mesProcessar) {
                $this->balanceteVerificacaoMensal[$mesProcessar->coluna] = $this->executarBalanceteVerificacao(
                    $mesProcessar->ano,
                    $mesProcessar->data_inicio,
                    $mesProcessar->data_fim
                );
            }
        }
        return $this->balanceteVerificacaoMensal;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getDadosRestosPagarMensal()
    {
        if (empty($this->restosPagarMensal)) {
            $mesesProcessar = $this->getMesesProcessar();
            foreach ($mesesProcessar as $mesProcessar) {
                $this->restosPagarMensal[$mesProcessar->coluna] = $this->executarRestosPagar(
                    $mesProcessar->ano,
                    $mesProcessar->data_inicio,
                    $mesProcessar->data_fim
                );
            }
        }
        return $this->restosPagarMensal;
    }

    protected function processaReceitaMensal(array $receitas, stdClass $linha)
    {
        foreach ($receitas as $colunaPeriodo => $receitas) {
            foreach ($receitas as $receita) {
                if (!$this->matchReceita($receita, $linha)) {
                    continue;
                }

                foreach ($linha->colunas as $coluna) {
                    if ($colunaPeriodo !== $coluna->coluna) {
                        continue;
                    }

                    // caso não tenha configurado a variável do balancete, ignora a coluna
                    if (!array_key_exists($coluna->coluna, $this->colunaMesReceita)) {
                        continue;
                    }

                    $colunaReceita = $this->colunaMesReceita[$coluna->coluna];
                    if (empty($colunaReceita)) {
                        continue;
                    }
                    $coluna->valor += $receita->{$colunaReceita};
                }
            }
        }
    }

    protected function processaDespesaMensal(array $despesas, $linha)
    {
        foreach ($despesas as $colunaPeriodo => $despesas) {
            foreach ($despesas as $despesa) {
                if (!$this->matchDespesa($despesa, $linha)) {
                    continue;
                }

                foreach ($linha->colunas as $coluna) {
                    if ($colunaPeriodo !== $coluna->coluna) {
                        continue;
                    }
                    // caso não tenha configurado a variável do balancete, ignora a coluna
                    if (!array_key_exists($coluna->coluna, $this->colunaMesDespesa)) {
                        continue;
                    }

                    $colunaDespena = $this->colunaMesDespesa[$coluna->coluna];
                    if (empty($colunaDespena)) {
                        continue;
                    }
                    $coluna->valor += $despesa->{$colunaDespena};
                }
            }
        }
    }

    /**
     * Soma o valor dos 12 meses na variável total_meses.
     * Obs.: Para essa coluna ser executada ela deve existir no relatório.
     *
     * @throws Exception
     */
    public function calcularTotalUltimos12Meses()
    {
        $mesesProcessar = $this->getMesesProcessar();
        foreach ($this->linhas as $linha) {
            if (!in_array($linha->ordem, $this->linhasMensais)) {
                continue;
            }
            foreach ($mesesProcessar as $mesProcessar) {
                $linha->total_meses += $linha->{$mesProcessar->coluna};
            }
        }
    }

    /**
     * Executa o calculo das linhas totalizadora mensais
     * @throws Exception
     */
    public function calcularLinhasTotalizadorasMensais()
    {
        $mesesProcessar = $this->getMesesProcessar();
        // realiza a soma das linhas
        foreach ($this->totalizar as $linha => $somar) {
            $linhaTotalizar = $this->linhas[$linha];
            foreach ($somar as $idLinhaSoma) {
                $linhaSomar = $this->linhas[$idLinhaSoma];
                // totaliza os meses
                foreach ($mesesProcessar as $mesProcessar) {
                    $linhaTotalizar->{$mesProcessar->coluna} += $linhaSomar->{$mesProcessar->coluna};
                }
                $linhaTotalizar->total_meses += $linhaSomar->total_meses;
            }
        }
    }
}
