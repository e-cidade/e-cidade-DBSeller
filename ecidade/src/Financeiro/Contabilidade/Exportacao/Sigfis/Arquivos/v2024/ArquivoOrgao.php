<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use cl_orcorgao;
use db_utils;
use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;

class ArquivoOrgao extends ArquivoBase
{
    protected $sNomeArquivo = 'Orgao';

    public function gerarDados()
    {
        $oDaoOrcorgao = new cl_orcorgao();

        $iAnoSessao = db_getsession('DB_anousu');
        $sSqlBuscaOrgaos = "select distinct
                                   orcorgao.o40_instit,
                                   orcorgao.o40_anousu,
                                   db_config.nomeinst,
                                   orcorgao.o40_orgao,
                                   orcorgao.o40_descr
                              from orcamento.orcorgao
                                   inner join configuracoes.db_config on db_config.codigo = orcorgao.o40_instit
                             where orcorgao.o40_anousu = {$iAnoSessao}
                               and orcorgao.o40_instit in (".implode(",", $this->getListaInstituicoes()).")
                               and orcorgao.o40_datacriacao between '{$this->dtDataInicial}' 
                               and '{$this->dtDataInicial}' ";
        $rsSqlBuscaOrgaos = $oDaoOrcorgao->sql_record($sSqlBuscaOrgaos);

        if (!$rsSqlBuscaOrgaos) {
            throw new \Exception("Erro ao consultar Orgao");
        }

        $aOrgaos = db_utils::getCollectionByRecord($rsSqlBuscaOrgaos);

        if (count($aOrgaos) > 0) {
            if (empty($this->sCodigoTribunal)) {
                throw new \Exception("O código do tribunal deve ser informado para geração do arquivo");
            }

            $RemessaOrgao = new \stdClass();
            $RemessaOrgao->Orgaos = [];

            foreach ($aOrgaos as $oOrgao) {
                $id = $oOrgao->o40_anousu . $oOrgao->o40_instit . $oOrgao->o40_orgao;
                $Orgao = new \stdClass();
                $Orgao->Identificador = $id;
                $Orgao->CodigoUnidadeGestora = $this->sCodigoTribunal;
                $Orgao->Ano = $oOrgao->o40_anousu;
                $Orgao->CodigoOrgao = $oOrgao->o40_orgao;
                $Orgao->DescricaoOrgao = Helper::convertAndLimit("<![CDATA[".$oOrgao->o40_descr);

                $RemessaOrgao->Orgaos[] = (object)['Orgao' => $Orgao];
            }

            $this->aDados = $RemessaOrgao;
        }
    }
}
