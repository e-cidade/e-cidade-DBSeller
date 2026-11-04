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

namespace ECidade\Financeiro\Contabilidade\Relatorio\RREO\V2018;

use ECidade\Financeiro\Contabilidade\Relatorio\RREO\InterfaceRelatorioLegal;

/**
 * Class AnexoVIII
 * @package ECidade\Financeiro\Contabilidade\Relatorio\RREO\V2017
 */
class AnexoVIII extends \RelatoriosLegaisBase implements InterfaceRelatorioLegal
{
    /**
     * Código Padrão do Relatório
     * @var integer
     */
    const CODIGO_RELATORIO = 179;

    /**
     * AnexoVII constructor.
     *
     * @param int $iAnoUsu
     * @param int $iCodigoRelatorio
     * @param int $iCodigoPeriodo
     */
    public function __construct($iAnoUsu, $iCodigoPeriodo)
    {
        parent::__construct($iAnoUsu, static::CODIGO_RELATORIO, $iCodigoPeriodo);
    }

    /**
     * Retorna um array contendo as linhas do relatório já processadas.
     * @return \stdClass[]
     */
    public function getLinhas()
    {
        if (count($this->aLinhasConsistencia) == 0) {
            $this->processar();
        }
        return $this->aLinhasConsistencia;
    }

    /**
     * Processa a busca e cálculo necessários para emissão do relatório
     */
    private function processar()
    {
        $this->getDados();

        /**
         * Array associativo onde o indice é o código da linha e os valores em 'inclusao' e 'exclusao' são os documentos
         */
        $aLinhasOrcamento = array(
            //  47- (+) INGRESSO DE RECURSOS ATÉ O BIMESTRE
            107 => array(
                'inclusao' => array(130, 150, 100, 160),
                'exclusao' => array(131, 152, 101)
            ),
            118 => array(
                'inclusao' => array(130, 100),
                'exclusao' => array(131, 101)
            ),
            // 48.1 Orçamento do Exercício
            109 => array(
                'inclusao' => array(5, 161, 151, 120, 140),
                'exclusao' => array(6)
            ),
            120 => array(
                'inclusao' => array(5),
                'exclusao' => array(6)
            ),
            // 48.2 Restos a Pagar
            110 => array(
                'inclusao' => array(35, 37),
                'exclusao' => array(36, 38)
            ),
            121 => array(
                'inclusao' => array(35, 37),
                'exclusao' => array(36, 38)
            ),
            // 51.1 Retenções
            114 => array(
                'inclusao' => array(160),
                'exclusao' => array(162)
            ),
            125 => array(
                'inclusao' => array(150, 160),
                'exclusao' => array(153, 163)
            ),
        );

        // Caso ultimo bimestre, sao alteradas algumas colunas de formulas
        if ($this->verificaUltimoBimestre()) {
            $this->aLinhasConsistencia[95]
            ->colunas[0]->o116_formula = "(L[73]->empenhado_atebim+L[80]->empenhado_atebim)-L[94]->valor";
            
            $this->aLinhasConsistencia[67]
            ->colunas[0]->o116_formula = "L[59]->empenhado_atebim - L[66]->valor";
            
            $this->aLinhasConsistencia[68]
            ->colunas[0]->o116_formula = "((L[53]->empenhado_atebim-(L[61]->valor+L[64]->valor))/L[48]->recatebim)*100";
            
            $this->aLinhasConsistencia[69]
            ->colunas[0]->o116_formula = "((L[56]->empenhado_atebim-(L[62]->valor+L[65]->valor))/L[48]->recatebim)*100";

            $this->processarFormasDasLinhas(array(95, 67, 68, 69));
        }


        foreach ($aLinhasOrcamento as $linha => $aConfiguracao) {
            $this->processarOrcamentoExercicio($linha, $aConfiguracao);
        }

        /**
         * Ajustes dos Ingressos linha 107 - descontamos todas as receitas a rendimento e recebimentos de retenção.
         */
        $this->aLinhasConsistencia[107]->valor -= $this->aLinhasConsistencia[111]->valor;
        $this->aLinhasConsistencia[107]->valor -= $this->aLinhasConsistencia[114]->valor;

        $this->aLinhasConsistencia[118]->valor -= $this->aLinhasConsistencia[122]->valor;

        $aLinhasProcessar = array(
            52,
            60,
            61,
            62,
            63,
            64,
            65,
            66,
            67,
            68,
            69,
            70,
            108,
            112,
            113,
            116,
            119,
            123,
            124,
            127
        );
        $this->processarFormasDasLinhas($aLinhasProcessar);

        $this->arredondarValores();
    }

    /**
     * Processa os dados das contas bancárias no exercício
     * @param $iLinha
     * @param $aConfiguracao
     * @throws \DBException
     */
    protected function processarOrcamentoExercicio($iLinha, $aConfiguracao)
    {
        $iCodigoRecurso = '';
        // Verifica se existe recurso configurado
        if (!empty($this->aLinhasConsistencia[$iLinha]->parametros->orcamento->recurso->valor)) {
            $iCodigoRecurso = $this->aLinhasConsistencia[$iLinha]->parametros->orcamento->recurso->valor[0];
        }

        $sInclusao = implode(",", $aConfiguracao['inclusao']);
        $sDocumentos = implode(",", array_merge($aConfiguracao['inclusao'], $aConfiguracao['exclusao']));
        $aContas = array();

        foreach ($this->aLinhasConsistencia[$iLinha]->parametros->contas as $oStdConta) {
            $aContas[$oStdConta->estrutural] = $oStdConta->nivel;
        }

        $oValor = new \stdClass();
        $oValor->valor = 0;

        if (!in_array($iLinha, array(114, 125))) {
            $oDaoLancamento = new \cl_conlancam();
            $sCampos = "sum(case 
                              when c71_coddoc in(" . $sInclusao . ") then c70_valor 
                              else c70_valor * -1 
                            end) as valor";
            $aWhere = array(
                "c70_data between '{$this->getDataInicial()->getDate()}' and '{$this->getDataFinal()->getDate()}'",
                "c71_coddoc in (" . $sDocumentos . ")",
                "(c60_estrut ilike '111%' or c60_estrut ilike '114%')",
                "c02_instit in ({$this->getInstituicoes()})"
            );
            // Caso exista recurso, adiciona na busca o recurso configurado
            if (!empty($iCodigoRecurso)) {
                $aWhere[] = "c61_codigo = {$iCodigoRecurso}";
            }

            $sWhere = implode(" and ", $aWhere);
            $sSql = $oDaoLancamento->sql_query_conta($sCampos, null, $sWhere);
            $rsValor = db_query($sSql);

            if (!$rsValor) {
                throw new \DBException("Ocorreu algum erro na consulta da linha {$iLinha}");
            }

            if (pg_num_rows($rsValor) <= 0) {
                throw new \DBException("Ocorreu algum erro ao buscar informações da linha {$iLinha}");
            }

            $oValor->valor += \db_utils::fieldsMemory($rsValor, 0)->valor;
        } else {
            $oValor->valor += $this->processarOrcamentoExercicioLinhaRetencoes($iCodigoRecurso);
        }
        //Busca configuracao de valor manual e soma na linha
        $aValorManual = $this->aLinhasConsistencia[$iLinha]->oLinhaRelatorio->getValoresColunas();

        if ($aValorManual > 0) {
            foreach ($aValorManual as $oValorManual) {
                $oValor->valor += $oValorManual->colunas[0]->o117_valor;
            }
        }

        if (in_array($iLinha, array(109, 120))) {
            $oValor->valor = $this->processarOrcamentoExercicioLinhaOrcamentoExercicio($iCodigoRecurso);
        }

        $this->aLinhasConsistencia[$iLinha]->valor = $oValor->valor;
    }

    /**
     * Calcula o valor das retenções no quadro ONTROLE DA DISPONIBILIDADE FINANCEIRA
     * @param $recurso
     * @return mixed
     */
    protected function processarOrcamentoExercicioLinhaOrcamentoExercicio($recurso)
    {
        $aWhere = array(
            "c70_data between '{$this->getDataInicial()->getDate()}' and '{$this->getDataFinal()->getDate()}'",
            "c71_coddoc in (140, 161,151, 120,5,121,163,153,141,6)",
            "(c60_estrut ilike '111%' or c60_estrut ilike '114%')",
            "c02_instit in ({$this->getInstituicoes()})"
        );
        if (!empty($recurso)) {
            $aWhere[] = "c61_codigo = {$recurso}";
        }
        $oDaoLancamento = new \cl_conlancam();
        $sCampos = "coalesce(sum(case 
                                   when c61_reduz = c69_credito 
                                     then c70_valor 
                                     else c70_valor * -1 
                                end), 0) as valor_credito";
        $sSqlCredito = $oDaoLancamento->sql_query_conta($sCampos, null, implode(' and ', $aWhere));
        $rsBuscaCredito = db_query($sSqlCredito);
        if (!$rsBuscaCredito || pg_num_rows($rsBuscaCredito) === 0) {
            throw new Exception("Ocorreu um erro ao buscar o valor a débito para o documento 140.");
        }
        return \db_utils::fieldsMemory($rsBuscaCredito, 0)->valor_credito;
    }

    /**
     * Calcula o valor da linha retenções
     * @param $recurso
     * @return mixed
     */
    protected function processarOrcamentoExercicioLinhaRetencoes($recurso)
    {
        $sqlRetencoes = "select  coalesce(sum(case when c71_coddoc in(160, 150) then c69_valor 
                                                   when c71_coddoc in(152, 162) then c69_valor * -1 end ), 0) as valor
                           from conlancamval
                          inner join conplanoreduz on c69_debito = c61_reduz
                                                  and c69_anousu = c61_anousu
                          inner join conplano     ON conplano.c60_anousu = conplanoreduz.c61_anousu
                                                  and conplano.c60_codcon = conplanoreduz.c61_codcon
 
                          inner join conplanoreduz as reduzcredito on c69_credito = reduzcredito.c61_reduz
                                                  and c69_anousu = reduzcredito.c61_anousu
                          inner join conplano as cp_credito   ON cp_credito.c60_anousu = reduzcredito.c61_anousu
                                                  and cp_credito.c60_codcon = reduzcredito.c61_codcon
                                                  
                            
                          inner join conlancam on conlancam.c70_codlan = conlancamval.c69_codlan
                          inner join conlancaminstit on c02_codlan = c70_codlan
                          inner join conlancamdoc on conlancamdoc.c71_codlan = conlancam.c70_codlan
                    where (conplano.c60_estrut like '111%' or conplano.c60_estrut like '114%')
                      and (cp_credito.c60_estrut like '21881%')
                      and c70_data between '{$this->getDataInicial()->getDate()}' 
                                       and '{$this->getDataFinal()->getDate()}'
                      and c02_instit in ({$this->getInstituicoes()})
                      and c71_coddoc in (160,150, 152,162) ";


        $sqlRetencoes2 = "select  (coalesce(sum(case when c71_coddoc in(160, 150) then c69_valor
                     when c71_coddoc in(152, 162) then c69_valor * -1 end ), 0)) as valor
                    from conlancamval
                      inner join conplanoreduz on c69_credito = c61_reduz
                                                  and c69_anousu = c61_anousu
                      inner join conplano     ON conplano.c60_anousu = conplanoreduz.c61_anousu
                                                 and conplano.c60_codcon = conplanoreduz.c61_codcon
                    
                      inner join conplanoreduz as reduzdebito on c69_debito = reduzdebito.c61_reduz
                                                                  and c69_anousu = reduzdebito.c61_anousu
                      inner join conplano as cp_debito ON cp_debito.c60_anousu = reduzdebito.c61_anousu
                                                      and cp_debito.c60_codcon = reduzdebito.c61_codcon
                    
                      inner join conlancam on conlancam.c70_codlan = conlancamval.c69_codlan
                      inner join conlancaminstit on c02_codlan = c70_codlan
                      inner join conlancamdoc on conlancamdoc.c71_codlan = conlancam.c70_codlan
                    where (conplano.c60_estrut like '111%' or conplano.c60_estrut like '114%')
                      and (cp_debito.c60_estrut like '21881%')
                      and c70_data between '{$this->getDataInicial()->getDate()}' 
                                       and '{$this->getDataFinal()->getDate()}'
                      and c02_instit in ({$this->getInstituicoes()})
                      and c71_coddoc in (160,150, 152,162)";


        if (!empty($recurso)) {
            $sqlRetencoes .= " and conplanoreduz.c61_codigo = {$recurso}";
            $sqlRetencoes2 .= " and conplanoreduz.c61_codigo = {$recurso}";
        }

        $sqlRetencoesUnion =  $sqlRetencoes." union all " . $sqlRetencoes2;

        $sqlRetencoes =  "select sum(valor) as valor from  ($sqlRetencoesUnion) as x";
        
        $rsOrcamentoRetencoes = db_query($sqlRetencoes);
        if (!$rsOrcamentoRetencoes) {
            throw new Exception("Ocorreu um erro ao buscar o valor a débito para o documento 140.");
        }
        return \db_utils::fieldsMemory($rsOrcamentoRetencoes, 0)->valor;
    }


    /**
     * Retorna os dados para Demonstrativo Simplificado
     * @return \stdClass
     */
    public function getDadosSimplificado()
    {
        $aDados = $this->getLinhas();
        $oDados = new \stdClass();
        $oDados->nMinimoAtualMDEAteBimestre = $aDados[95]->valor;
        $oDados->nPercentualAplicadoComMDE = $aDados[96]->valor;

        $oDados->nMinimoAtualFUNDEBAteBimestre = $aDados[53]->liquidado_atebim
        - ($aDados[61]->valor + $aDados[64]->valor);
        // Caso ultimo bimestre altera a coluna da formula
        if ($this->verificaUltimoBimestre()) {
            $oDados->nMinimoAtualFUNDEBAteBimestre = $aDados[53]->empenhado_atebim
            - ($aDados[61]->valor + $aDados[64]->valor);
        }
        $oDados->nPercentualAplicadoComFUNDEB = $aDados[68]->valor;
        return $oDados;
    }

    /**
     * verifica se é 6 bimestre retorna true se sim, caso contrario retorna false
     * @return bool
     */
    public function verificaUltimoBimestre()
    {
        if ($this->oPeriodo->getCodigo() == 11) {
            return true;
        }
        return false;
    }
}
