<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use db_utils;
use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;
use Exception;

class ArquivoAlteracaoOrcamentariaDespesa extends ArquivoBase
{

    protected $sNomeArquivo  = 'AlteracaoOrcamentariaDespesa';

    const SUPLEMENTACAO_POR_REDUCAO = '1001';
    const SUPERAVIT_FINANCEIRO = '1003';
    const ARRECADACAO_A_MAIOR = '1004';
    const CREDITOS_ESPECIAIS_POR_REDUCAO = '1006';
    const CREDITOS_ESPECIAIS_POR_SUPERAVIT_FINANCEIRO = '1008';
    const CREDITO_ESPECIAL_ARRECADACAO_A_MAIOR = '1009';
    const CREDITOS_ESPECIAIS_POR_AUXILIOS_E_CONVENIOS = '1010';
    const CREDITOS_EXTRAORDINARIOS  = '1011';
    const REABERTURA_DE_CREDITOS_ESPECIAIS = '1012';
    const REABERTURA_DE_CREDITOS_EXTRAORDINARIOS = '1013';
    const TRANSFERENCIA_DE_RECURSOS = '1014';
    const REMANEJAMENTO_DE_RECURSOS = '1015';
    const TRANSPOSICAO_DE_RECURSOS = '1016';
    const OPERACAO_DE_CREDITO = '1002';
    const CREDITOS_ESPECIAIS_POR_OPERACAO_DE_CREDITO = '1007';
    const AUXILIOS_A_CONVENIOS = '1005';
    const REDUCAO_DO_ORCAMENTO = '1050';

    /**
    * Busca os dados para gerar o Arquivo de Unidade Orçamentária
    */
    public function gerarDados()
    {

        $daoOrcSuplem  = new \cl_orcsuplem;

        $sCampos  = " distinct o46_codsup,";
        $sCampos .= " o47_valor as valor,";
        $sCampos .= " o45_numlei as lei,";
        $sCampos .= " o45_dataini as data_lei_autorizativa,";
        $sCampos .= " o39_numero as numero_instrumento,";
        $sCampos .= " o39_anousu as ano_alteracao,";
        $sCampos .= " o49_data as data_instrumento,";
        $sCampos .= " '1' as tipo_atualizacao,";
        $sCampos .= " o15_codigo,";
        $sCampos .= " o48_tiposup,";
        $sCampos .= " substr(planodespesa.conta,1,6) as natureza_despesa,";
        $sCampos .= " o58_programa,";
        $sCampos .= " o52_siconfi as funcao,";
        $sCampos .= " o53_siconfi as subfuncao,";
        $sCampos .= " o55_projativ,";
        $sCampos .= " o55_tipo,";
        $sCampos .= " o58_orgao,";
        $sCampos .= " o58_unidade,";
        $sCampos .= " codigo_siconfi";

        $sWhere  = "o58_instit in (".implode(",", $this->getListaInstituicoes()).")";
        $sWhere .= " and o49_data between '{$this->dtDataInicial}' and '{$this->dtDataFinal}'";

        $sSqlOrcSuplem = $daoOrcSuplem->sql_query_suplementacoes_despesa(null, $sCampos, null, $sWhere);

        $sSqlOrcSuplem = " select o58_orgao, 
                                  o58_unidade, 
                                  numero_instrumento,
                                  ano_alteracao, 
                                  tipo_atualizacao, 
                                  data_instrumento,
                                  lei,
                                  data_lei_autorizativa,
                                  funcao, 
                                  subfuncao, 
                                  o58_programa, 
                                  o55_tipo, 
                                  o55_projativ, 
                                  natureza_despesa, 
                                  codigo_siconfi,
                                  o48_tiposup,
                                  sum(valor) as valor 
                              from ({$sSqlOrcSuplem}) as dados
                             group by o55_tipo, 
                                      o55_projativ, 
                                      funcao, 
                                      subfuncao, 
                                      o58_programa, 
                                      o58_unidade, 
                                      o58_orgao, 
                                      codigo_siconfi, 
                                      tipo_atualizacao, 
                                      natureza_despesa,
                                      numero_instrumento, 
                                      data_instrumento,
                                      ano_alteracao,
                                      lei, 
                                      data_lei_autorizativa,
                                      o48_tiposup
                             order by o58_orgao, 
                                      o58_unidade,
                                      natureza_despesa,
                                      o58_programa,
                                      o55_projativ,
                                      funcao,
                                      subfuncao,
                                      codigo_siconfi";

        $rsOrcSuplem   = db_query($sSqlOrcSuplem);

        if (pg_num_rows($rsOrcSuplem) == 0) {
            throw new \Exception("Nenhum registro encontrado para o período");
        }

        if (empty($this->sCodigoTribunal)) {
            throw new \Exception("O código do tribunal deve ser informado para geração do arquivo");
        }

            $RemessaAlteracaoOrcamentariaDespesa = new \stdClass();
            $alteracaoOrcamentariaDespesa = array();

            $identificador = 1;
        for ($i = 0; $i < pg_num_rows($rsOrcSuplem); $i++) {
            $oDadosQuery = db_utils::fieldsMemory($rsOrcSuplem, $i);

            $anoInstrumento = explode("-", $oDadosQuery->data_instrumento)[0];
            $mesInstrumento = explode("-", $oDadosQuery->data_instrumento)[1];
            $competencia = $anoInstrumento.$mesInstrumento;

            $deParaSuplemTipo = $this->deParaSuplemTipo($oDadosQuery->o48_tiposup);

            $oDadosAlteracaoOrcamentariaDespesa = new \stdClass();
            $oDadosAlteracaoOrcamentariaDespesa->Identificador = $identificador;
            $oDadosAlteracaoOrcamentariaDespesa->CodigoOrgao = $oDadosQuery->o58_orgao;
            $oDadosAlteracaoOrcamentariaDespesa->CodigoUnidadeOrcamentaria =$oDadosQuery->o58_unidade;
            $oDadosAlteracaoOrcamentariaDespesa->Competencia = $competencia;
            $oDadosAlteracaoOrcamentariaDespesa->CodigoUnidadeGestora = $this->sCodigoTribunal;
            $oDadosAlteracaoOrcamentariaDespesa->NumeroInstrumentoAlteracao = $oDadosQuery->numero_instrumento;
            $oDadosAlteracaoOrcamentariaDespesa->AnoAlteracao = $oDadosQuery->ano_alteracao;
            $oDadosAlteracaoOrcamentariaDespesa->TipoInstrumentoAlteracao = $oDadosQuery->tipo_atualizacao;
            $oDadosAlteracaoOrcamentariaDespesa->DataInstrumentoAlteracao =$oDadosQuery->data_instrumento;
            $oDadosAlteracaoOrcamentariaDespesa->DataPublicacao =$oDadosQuery->data_instrumento;
            $lei = Helper::convertAndLimit("<![CDATA[".$oDadosQuery->lei, 259);
            $oDadosAlteracaoOrcamentariaDespesa->NumeroLeiAutorizativa = $lei;
            $oDadosAlteracaoOrcamentariaDespesa->DataLeiAutorizativa = $oDadosQuery->data_lei_autorizativa;
            $oDadosAlteracaoOrcamentariaDespesa->TipoAlteracao = $deParaSuplemTipo->tipoalteracao;
            $oDadosAlteracaoOrcamentariaDespesa->FonteAbertura = $deParaSuplemTipo->fonteabertura;
            $oDadosAlteracaoOrcamentariaDespesa->CodigoFuncao = $oDadosQuery->funcao;
            $oDadosAlteracaoOrcamentariaDespesa->CodigoSubFuncao = $oDadosQuery->subfuncao;
            $oDadosAlteracaoOrcamentariaDespesa->CodigoPrograma = $oDadosQuery->o58_programa;
            $oDadosAlteracaoOrcamentariaDespesa->TipoAcao = $oDadosQuery->o55_tipo;
            $oDadosAlteracaoOrcamentariaDespesa->CodigoAcao = $oDadosQuery->o55_projativ;
            $oDadosAlteracaoOrcamentariaDespesa->NaturezaDespesa = $oDadosQuery->natureza_despesa;
            $oDadosAlteracaoOrcamentariaDespesa->FonteRecurso = $oDadosQuery->codigo_siconfi;
            $oDadosAlteracaoOrcamentariaDespesa->ValorAlteracao = abs($oDadosQuery->valor);
            $oDadosAlteracaoOrcamentariaDespesa->CodigoCreditoAdicional = $deParaSuplemTipo->creditoadicional;

            $alteracaoOrcamentariaDespesa[] =  (object) [
                'AlteracaoOrcamentariaDespesa' => $oDadosAlteracaoOrcamentariaDespesa
            ];

            $identificador++;
        }

            $RemessaAlteracaoOrcamentariaDespesa->AlteracoesOrcamentariasDespesas = $alteracaoOrcamentariaDespesa;
            $this->aDados= $RemessaAlteracaoOrcamentariaDespesa;
    }

    public function deParaSuplemTipo($tipoSuplementacao)
    {

        switch ($tipoSuplementacao) {
            case static::SUPLEMENTACAO_POR_REDUCAO:
                return (object) [
                    'tipoalteracao' => '2',
                    'fonteabertura' => '1',
                    'creditoadicional' => '1'
                ];

            case static::SUPERAVIT_FINANCEIRO:
                return (object) [
                    'tipoalteracao' => '2',
                    'fonteabertura' => '3',
                    'creditoadicional' => '1'
                ];
            case static::ARRECADACAO_A_MAIOR:
                return (object) [
                    'tipoalteracao' => '2',
                    'fonteabertura' => '2',
                    'creditoadicional' => '1'
                ];
            case static::CREDITOS_ESPECIAIS_POR_REDUCAO:
                return (object) [
                    'tipoalteracao' => '3',
                    'fonteabertura' => '1',
                    'creditoadicional' => '2'
                ];
            case static::CREDITOS_ESPECIAIS_POR_SUPERAVIT_FINANCEIRO:
                return (object) [
                    'tipoalteracao' => '3',
                    'fonteabertura' => '3',
                    'creditoadicional' => '2'
                ];
            case static::CREDITO_ESPECIAL_ARRECADACAO_A_MAIOR:
                return (object) [
                    'tipoalteracao' => '3',
                    'fonteabertura' => '2',
                    'creditoadicional' => '2'
                ];
            case static::CREDITOS_ESPECIAIS_POR_AUXILIOS_E_CONVENIOS:
                return (object) [
                    'tipoalteracao' => '3',
                    'fonteabertura' => '4',
                    'creditoadicional' => '2'
                ];
            case static::CREDITOS_EXTRAORDINARIOS:
                return (object) [
                    'tipoalteracao' => '4',
                    'fonteabertura' => '1',
                    'creditoadicional' => '3'
                ];
            case static::REABERTURA_DE_CREDITOS_ESPECIAIS:
                return (object) [
                    'tipoalteracao' => '3',
                    'fonteabertura' => '3',
                    'creditoadicional' => '2'
                ];
            case static::REABERTURA_DE_CREDITOS_EXTRAORDINARIOS:
                return (object) [
                    'tipoalteracao' => '4',
                    'fonteabertura' => '3',
                    'creditoadicional' => '3'
                ];
            case static::TRANSFERENCIA_DE_RECURSOS:
                return (object) [
                    'tipoalteracao' => '2',
                    'fonteabertura' => '8',
                    'creditoadicional' => '1'
                ];
            case static::REMANEJAMENTO_DE_RECURSOS:
                return (object) [
                    'tipoalteracao' => '2',
                    'fonteabertura' => '7',
                    'creditoadicional' => '2'
                ];
            case static::TRANSPOSICAO_DE_RECURSOS:
                return (object) [
                    'tipoalteracao' => '2',
                    'fonteabertura' => '6',
                    'creditoadicional' => '1'
                ];
            case static::OPERACAO_DE_CREDITO:
                return (object) [
                    'tipoalteracao' => '2',
                    'fonteabertura' => '5',
                    'creditoadicional' => '1'
                ];
            case static::CREDITOS_ESPECIAIS_POR_OPERACAO_DE_CREDITO:
                return (object) [
                    'tipoalteracao' => '3',
                    'fonteabertura' => '5',
                    'creditoadicional' => '2'
                ];
            case static::AUXILIOS_A_CONVENIOS:
                return (object) [
                    'tipoalteracao' => '2',
                    'fonteabertura' => '4',
                    'creditoadicional' => '1'
                ];
            case static::REDUCAO_DO_ORCAMENTO:
                return (object) [
                    'tipoalteracao' => '2',
                    'fonteabertura' => '1',
                    'creditoadicional' => '1'
                ];
        }
    }
}
