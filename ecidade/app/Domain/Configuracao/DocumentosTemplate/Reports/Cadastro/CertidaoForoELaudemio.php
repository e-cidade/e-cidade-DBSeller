<?php

namespace App\Domain\Configuracao\DocumentosTemplate\Reports\Cadastro;

use App\Domain\Configuracao\DocumentosTemplate\ProcessaDocumentoTemplate;
use App\Domain\Configuracao\DocumentosTemplate\Reports\Cadastro\Helpers\FiltroDadosCertidoesHelper;
use \cl_certforlaud;
use \db_utils;

class CertidaoForoELaudemio extends ProcessaDocumentoTemplate
{
    private $sequencialCertidao;
    const GRUPOTEMPLATE = 66;

    public function __construct($sequencialCertidao)
    {
        parent::__construct(self::GRUPOTEMPLATE);
        $this->sequencialCertidao = $sequencialCertidao;
        $this->setNomeDocumento('CertidaoForoELaudemio');
    }

    /**
     * @return array
     */
    public function configuraDadosVariaveis()
    {
        $oDaoCertForLaud = new cl_certforlaud();

        $sqlVariaveisCertidao = $oDaoCertForLaud->sql_query_certidaoForoLaudemioTemplate(
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
