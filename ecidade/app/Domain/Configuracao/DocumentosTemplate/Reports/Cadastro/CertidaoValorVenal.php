<?php

namespace App\Domain\Configuracao\DocumentosTemplate\Reports\Cadastro;

use App\Domain\Configuracao\DocumentosTemplate\ProcessaDocumentoTemplate;
use App\Domain\Configuracao\DocumentosTemplate\Reports\Cadastro\Helpers\FiltroDadosCertidoesHelper;
use \cl_certvalven;
use \db_utils;

class CertidaoValorVenal extends ProcessaDocumentoTemplate
{
    private $sequencialCertidao;
    const GRUPOTEMPLATE = 65;

    public function __construct($sequencialCertidao)
    {
        parent::__construct(self::GRUPOTEMPLATE);
        $this->sequencialCertidao = $sequencialCertidao;
        $this->setNomeDocumento('CertidaoValorVenal');
    }

    /**
     * @return array
     */
    public function configuraDadosVariaveis()
    {
        $oDaoCertValVen = new cl_certvalven();

        $sqlVariaveisCertidao = $oDaoCertValVen->sql_query_certidaoValorVenalTemplate(
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
