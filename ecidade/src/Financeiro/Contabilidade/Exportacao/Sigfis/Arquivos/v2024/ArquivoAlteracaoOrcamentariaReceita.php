<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;
use db_utils;
use Exception;

class ArquivoAlteracaoOrcamentariaReceita extends ArquivoBase
{

    protected $sNomeArquivo  = 'AlteracaoOrcamentariaReceita';
    /**
    * Busca os dados para gerar o Arquivo de Unidade Orçamentária
    */
    public function gerarDados()
    {

        $daoOrcSuplem  = new \cl_orcsuplem;

        $sCampos  = " distinct o46_codsup,o49_data as data_alteracao, o39_codproj, o85_valor,";
        $sCampos .= "o45_numlei || ' - ' || o45_descr as lei,";
        $sCampos .= "o39_codproj || ' - ' || o39_descr as decreto,";
        $sCampos .= "'1' as tipo_atualizacao,codigo_siconfi,";
        $sCampos .= "substr(planoreceita.conta,1,8) as codigo_item_receita,";
        $sCampos .= "RANK() OVER (ORDER BY substr(planoreceita.conta,1,8),codigo_siconfi) AS grupo_receita,";
        $sCampos .= " case when substr(o57_fonte,1,1) = '4' then 1
                           when substr(o57_fonte,1,1) = '9' and substr(o57_fonte,1,3) <> '917' then 2
                           when  substr(o57_fonte,1,3) = '917' then 3 else 0 end as deducao";

        $sWhere         = "o46_instit in (".implode(",", $this->getListaInstituicoes()).")";
        $sWhere        .= " and o49_data between '{$this->dtDataInicial}' and '{$this->dtDataFinal}'";

        $sSqlOrcSuplem = $daoOrcSuplem->sql_query_suplementacoes_receita(null, $sCampos, null, $sWhere, null);
        $rsOrcSuplem   = db_query($sSqlOrcSuplem);

        if (pg_num_rows($rsOrcSuplem) == 0) {
            throw new \Exception("Nenhum registro encontrado para o período");
        }
        if (empty($this->sCodigoTribunal)) {
            throw new \Exception("O código do tribunal deve ser informado para geração do arquivo");
        }

        $RemessaAlteracaoOrcamentaria = new \stdClass();
        $alteracaoOrcamentariaReceita = [];

        for ($i = 0; $i < pg_num_rows($rsOrcSuplem); $i++) {
            $oDadosQuery = db_utils::fieldsMemory($rsOrcSuplem, $i);

            $identificador = date("m", db_getsession("DB_datausu"));
            $identificador .= $oDadosQuery->grupo_receita;
            $identificador .= $oDadosQuery->deducao;

            $valorReceitaAgrupada = $this->getValorAgrupadoReceitaSigfis(
                $oDadosQuery->codigo_item_receita,
                $oDadosQuery->codigo_siconfi,
                $oDadosQuery->deducao
            );

            $oDadosAlteracaoOrcamentaria = new \stdClass();
            $oDadosAlteracaoOrcamentaria->Identificador = $oDadosQuery->o46_codsup;
            $oDadosAlteracaoOrcamentaria->CodigoUnidadeGestora = $this->sCodigoTribunal;
            $oDadosAlteracaoOrcamentaria->Competencia = $this->competencia;
            $oDadosAlteracaoOrcamentaria->DataAlteracao =$oDadosQuery->data_alteracao ;
            $oDadosAlteracaoOrcamentaria->CodigoItemReceita = $oDadosQuery->codigo_item_receita;
            $oDadosAlteracaoOrcamentaria->TipoAtualizacao = $oDadosQuery->tipo_atualizacao;
            $oDadosAlteracaoOrcamentaria->Valor =  $oDadosQuery->o85_valor;// $valorReceitaAgrupada;
            $lei = Helper::convertAndLimit("<![CDATA[".$oDadosQuery->lei, 259);
            $oDadosAlteracaoOrcamentaria->Lei = $lei;
            $decreto = Helper::convertAndLimit("<![CDATA[".$oDadosQuery->decreto, 259);
            $oDadosAlteracaoOrcamentaria->Decreto = $decreto;
            $oDadosAlteracaoOrcamentaria->FonteRecursos = $oDadosQuery->codigo_siconfi;
            $oDadosAlteracaoOrcamentaria->Deducao = $oDadosQuery->deducao;

            $alteracaoOrcamentariaReceita[] =  (object) [
                'AlteracaoOrcamentariaReceita' => $oDadosAlteracaoOrcamentaria
            ];
        }

            $RemessaAlteracaoOrcamentaria->AlteracoesOrcamentariasReceitas = $alteracaoOrcamentariaReceita;
            $this->aDados= $RemessaAlteracaoOrcamentaria;
    }


    public function getValorAgrupadoReceitaSigfis($codigoItemReceita, $codigoSiconfi, $deducao)
    {

        $daoOrcSuplem  = new \cl_orcsuplem;

        $campos  = "sum(o85_valor) as valor";

        $where  = "substr(planoreceita.conta,1,8) = '$codigoItemReceita'";

        if ($deducao == '1') {
            $where  .= " and substr(o57_fonte,1,1) = '4'";
        } elseif ($deducao == '2') {
            $where  .= " and substr(o57_fonte,1,1) = '9' and substr(o57_fonte,1,3) <> '917'";
        } elseif ($deducao == '3') {
            $where  .= " and substr(o57_fonte,1,3) = '917'";
        }

        $where .= " and codigo_siconfi = '$codigoSiconfi'";
        $where .= " and o46_instit in (".implode(",", $this->getListaInstituicoes()).")";
        $where .= " and o49_data between '{$this->dtDataInicial}' and '{$this->dtDataFinal}'";

        $sSqlOrcSuplem = $daoOrcSuplem->sql_query_suplementacoes_receita(null, $campos, null, $where, null);

        $rsOrcSuplem   = db_query($sSqlOrcSuplem);

        $valor = 0;
        if (pg_num_rows($rsOrcSuplem) > 0) {
            $valor = db_utils::fieldsMemory($rsOrcSuplem, 0)->valor;
        }
        return $valor;
    }
}
