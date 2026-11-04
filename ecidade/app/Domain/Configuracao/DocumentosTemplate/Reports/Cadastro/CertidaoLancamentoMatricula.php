<?php

namespace App\Domain\Configuracao\DocumentosTemplate\Reports\Cadastro;

use App\Domain\Configuracao\DocumentosTemplate\ProcessaDocumentoTemplate;
use App\Domain\Configuracao\DocumentosTemplate\Reports\Cadastro\Helpers\FiltroDadosCertidoesHelper;
use \cl_certlancimov;
use \db_utils;

class CertidaoLancamentoMatricula extends ProcessaDocumentoTemplate
{
    private $sequencialCertidao;
    const GRUPOTEMPLATE = 64;

    public function __construct($sequencialCertidao)
    {
        parent::__construct(self::GRUPOTEMPLATE);
        $this->sequencialCertidao = $sequencialCertidao;
        $this->setNomeDocumento('CertidaoLancamentoMatricula');
    }

    /**
     * @return array
     */
    public function configuraDadosVariaveis()
    {
        $oDaoCertLancImov = new cl_certlancimov();

        $sqlVariaveisCertidao = $oDaoCertLancImov->sql_query_certidaoLancamentoMatriculaTemplate(
            $this->sequencialCertidao
        );

        $rsVariaveisCertidao = db_query(
            $sqlVariaveisCertidao
        );

        $aDadosVariaveisCertidao = (array) db_utils::fieldsMemory(
            $rsVariaveisCertidao,
            0
        );

        return $this->adicionaFiltros($aDadosVariaveisCertidao);
    }

    private function adicionaFiltros($dados)
    {
        $filtros = new FiltroDadosCertidoesHelper();

        return $filtros->adicionaFiltros($dados);
    }
}
