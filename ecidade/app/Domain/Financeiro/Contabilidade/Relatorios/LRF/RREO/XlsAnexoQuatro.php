<?php

namespace App\Domain\Financeiro\Contabilidade\Relatorios\LRF\RREO;

class XlsAnexoQuatro extends XlsRREO
{
    protected $nomeArquivo = "anexo IV";
    protected $saveAs = 'tmp/Anexo_IV.xlsx';
}
