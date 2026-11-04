<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use cl_orcdotacao;
use db_utils;
use stdClass;

class ArquivoDotacao extends ArquivoBase
{
    protected $sNomeArquivo  = 'Dotacao';

    public function gerarDados()
    {
        $clOrcDotacao = new cl_orcdotacao;

        $sCampos = " distinct orcdotacao.o58_anousu, min(o58_coddot) as codigo_dotacao, ";
        $sCampos .= " orcdotacao.o58_orgao, orcdotacao.o58_unidade, orcdotacao.o58_projativ, ";
        $sCampos .= " orcdotacao.o58_programa, sum(orcdotacao.o58_valor) as valor,";
        $sCampos .= " orcdotacao.o58_instit, orcprojativ.o55_tipo, ";
        $sCampos .= " substr(planodespesa.conta,1,6) as natureza_despesa,codigo_siconfi,o52_siconfi,o53_siconfi ";

        $sOrder =  "orcdotacao.o58_projativ";
        $sWhere  = " orcdotacao.o58_anousu = {$this->iAnoUsu}";
        $sWhere .= " and orcdotacao.o58_instit in (".implode(",", $this->getListaInstituicoes()).")";
        $sWhere .= " and orcdotacao.o58_datacriacao between '{$this->dtDataInicial}' and '{$this->dtDataFinal}'";

        $groupBy = "o58_anousu,o58_orgao,o58_unidade,o58_projativ,o58_programa,o58_instit,o55_tipo,";
        $groupBy .= "planodespesa.conta,codigo_siconfi,o52_siconfi,o53_siconfi";
        $sSqlOrcDotacao = $clOrcDotacao->sql_dotacao_planodespesa(
            null,
            null,
            $sCampos,
            $sOrder,
            $sWhere,
            $groupBy,
            $this->utilizaPlanoUniao
        );
        $rsOrcDotacao = db_query($sSqlOrcDotacao);
        
        $iNumRowsDotacoes = pg_num_rows($rsOrcDotacao);

        if ($iNumRowsDotacoes == 0) {
            throw new \Exception("Nenhuma dotação encontrada no período informado.");
        }

        $RemessaDotacao = new stdClass();
        $dotacao = array();

        if ($iNumRowsDotacoes > 0) {
            if (empty($this->sCodigoTribunal)) {
                throw new \Exception("O código do tribunal deve ser informado para geração do  arquivo");
            }

            for ($i = 0; $i < pg_num_rows($rsOrcDotacao); $i++) {
                $oDadosQuery = db_utils::fieldsMemory($rsOrcDotacao, $i);

                $oDadosDotacao = new stdClass();

                $identificador = date("y", db_getsession("DB_datausu"));
                $identificador .= date("m", db_getsession("DB_datausu"));
                $identificador .= $oDadosQuery->codigo_dotacao;

                $oDadosDotacao->Identificador = $identificador;
                $oDadosDotacao->CodigoUnidadeGestora = $this->sCodigoTribunal;
                $oDadosDotacao->Ano = $oDadosQuery->o58_anousu;
                $oDadosDotacao->CodigoOrgao = $oDadosQuery->o58_orgao;
                $oDadosDotacao->CodigoUnidadeOrcamentaria = $oDadosQuery->o58_unidade;
                $oDadosDotacao->CodigoPrograma = $oDadosQuery->o58_programa;
                $tipo = (!in_array($oDadosQuery->o55_tipo, [1,2])) ? 3 : $oDadosQuery->o55_tipo;
                $oDadosDotacao->TipoAcao =  $tipo;
                $oDadosDotacao->CodigoAcao = $oDadosQuery->o58_projativ;
                $oDadosDotacao->CodigoFuncao = $oDadosQuery->o52_siconfi;
                $oDadosDotacao->CodigoSubFuncao = $oDadosQuery->o53_siconfi;
                $oDadosDotacao->NaturezaDespesa = $oDadosQuery->natureza_despesa;
                $oDadosDotacao->CodigoFonteRecurso = $oDadosQuery->codigo_siconfi;
                $oDadosDotacao->ValorDotacao = $oDadosQuery->valor;

                $dotacao[] = (object) ['Dotacao' => $oDadosDotacao];
            }
            $RemessaDotacao->Dotacoes = $dotacao;
        }
        $this->aDados= $RemessaDotacao;
    }
}
