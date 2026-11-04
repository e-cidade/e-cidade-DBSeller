<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use BusinessException;
use db_utils;
use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;

class ArquivoAcaoOrcamentaria extends ArquivoBase
{
    protected $sNomeArquivo  = 'AcaoOrcamentaria';

    /**
    * Busca os dados para gerar o Arquivo de Unidade Orçamentaria
    */
    public function gerarDados()
    {

        $daoOrcProjativ = new \cl_orcprojativ;
        $sCampos = "distinct o55_projativ,o55_anousu, o55_orcproduto, o55_descr,o55_tipo,o52_siconfi,o53_siconfi,";
        $sCampos .= "pl9_orcprograma,o58_programa,pl11_descricao as meta,";
        $sCampos .= "o220_codigo as unidade_medida";
        $sWhere  = "o58_instit in (".implode(",", $this->getListaInstituicoes()).")";
        $sWhere .= " and o55_anousu = {$this->iAnoUsu}";
        $sWhere .= " and o55_datacriacao between '{$this->dtDataInicial}' and '{$this->dtDataFinal}'";
        $sSqlOrcProjativ = $daoOrcProjativ->sql_query_projetoMetas(null, null, $sCampos, null, $sWhere);
        $rsOrcProjativ   = db_query($sSqlOrcProjativ);

        if (pg_num_rows($rsOrcProjativ) <= 0) {
            throw new BusinessException('Não foram encontradas Ações Orçamentarias');
        }

        if (empty($this->sCodigoTribunal)) {
            throw new BusinessException("O código do tribunal deve ser informado para geração do arquivo");
        }

        $RemessaAcaoOrcamentaria= new \stdClass();
        $acaoOrcamentaria = array();

        for ($i = 0; $i < pg_num_rows($rsOrcProjativ); $i++) {
            $oDadosQuery = db_utils::fieldsMemory($rsOrcProjativ, $i);
            $oDadosAcaoOrcamentaria = new \stdClass();
            $oDadosAcaoOrcamentaria->Identificador = $i + 1;
            $oDadosAcaoOrcamentaria->CodigoUnidadeGestora = $this->sCodigoTribunal;
            $oDadosAcaoOrcamentaria->Ano = $oDadosQuery->o55_anousu;
            $tipo = (!in_array($oDadosQuery->o55_tipo, [1,2])) ? 3 : $oDadosQuery->o55_tipo;
            $oDadosAcaoOrcamentaria->TipoAcao = $tipo;
            $oDadosAcaoOrcamentaria->CodigoAcao =$oDadosQuery->o55_projativ ;
            $descricaoAcao = Helper::convertAndLimit("<![CDATA[".$oDadosQuery->o55_descr, 259);
            $oDadosAcaoOrcamentaria->Descricao = $descricaoAcao;
            $oDadosAcaoOrcamentaria->Produto = $oDadosQuery->o55_orcproduto;
            $oDadosAcaoOrcamentaria->Funcao = $oDadosQuery->o52_siconfi;
            $oDadosAcaoOrcamentaria->SubFuncao = $oDadosQuery->o53_siconfi;
            $oDadosAcaoOrcamentaria->Programa = $oDadosQuery->o58_programa;
            $oDadosAcaoOrcamentaria->UnidadeMedida = !empty($oDadosQuery->unidade_medida)
                ? $oDadosQuery->unidade_medida : 29;
            $meta = Helper::convertAndLimit("<![CDATA[".$oDadosQuery->meta, 259);
            $oDadosAcaoOrcamentaria->Meta = $meta;
            $acaoOrcamentaria[] = (object) ['AcaoOrcamentaria' => $oDadosAcaoOrcamentaria];
        }

        $RemessaAcaoOrcamentaria->AcoesOrcamentarias = $acaoOrcamentaria;
        $this->aDados = $RemessaAcaoOrcamentaria;
    }
}
