<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use db_utils;
use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;
use Exception;

class ArquivoUnidadeOrcamentaria extends ArquivoBase
{
    protected $iCodigoLayout = 113;
    protected $sNomeArquivo  = 'UnidadeOrcamentaria';

    /**
    * Busca os dados para gerar o Arquivo de Unidade Orçamentária
    */
    public function gerarDados()
    {
        if (empty($this->sCodigoTribunal)) {
            throw new \Exception("O código do tribunal deve ser informado para geração do arquivo");
        }
  
        $sCampos  = "distinct orcunidade.o41_anousu, orcunidade.o41_unidade, orcunidade.o41_descr,";
        $sCampos .= "orcunidade.o41_orgao";

        $sWhere  = " orcunidade.o41_instit in (".implode(",", $this->getListaInstituicoes()).")";
        $sWhere .= " and orcunidade.o41_datacriacao between '{$this->dtDataInicial}' and '{$this->dtDataFinal}'";
        $sWhere .= " and orcunidade.o41_anousu = {$this->iAnoUsu}";

        $sSqlOrcUnidade = "select {$sCampos}
                             from orcamento.orcunidade
                            where {$sWhere}";
        $rsOrcUnidade   = db_query($sSqlOrcUnidade);

        if (pg_num_rows($rsOrcUnidade) == 0) {
            throw new \Exception("Nenhuma informação encontrada");
        }

        $RemessaPPAProgramaIndicador = new \stdClass();
        $unidadeOrcamentaria = array();

        for ($i = 0; $i < pg_num_rows($rsOrcUnidade); $i++) {
            $oDadosQuery = db_utils::fieldsMemory($rsOrcUnidade, $i);

            $identificador = date("Y", db_getsession("DB_datausu"));
            $identificador .= date("m", db_getsession("DB_datausu"));
            $identificador .= $oDadosQuery->o41_orgao.$oDadosQuery->o41_unidade;

            $oDadosUnidadeOrcamentaria = new \stdClass();
            $oDadosUnidadeOrcamentaria->Identificador = $identificador;
            $oDadosUnidadeOrcamentaria->CodigoUnidadeGestora = $this->sCodigoTribunal;
            $oDadosUnidadeOrcamentaria->Ano = $oDadosQuery->o41_anousu;
            $oDadosUnidadeOrcamentaria->CodigoOrgao =$oDadosQuery->o41_orgao ;
            $oDadosUnidadeOrcamentaria->CodigoUnidadeOrcamentaria = $oDadosQuery->o41_unidade;
            $descricaoUnidade = Helper::convertAndLimit("<![CDATA[".$oDadosQuery->o41_descr, 259);
            $oDadosUnidadeOrcamentaria->DescricaoUnidadeOrcamentaria = $descricaoUnidade;

            $unidadeOrcamentaria[] =  (object) ['UnidadeOrcamentaria' => $oDadosUnidadeOrcamentaria];
        }

        $RemessaPPAProgramaIndicador->UnidadesOrcamentarias = $unidadeOrcamentaria;
        $this->aDados= $RemessaPPAProgramaIndicador;
    }
}
