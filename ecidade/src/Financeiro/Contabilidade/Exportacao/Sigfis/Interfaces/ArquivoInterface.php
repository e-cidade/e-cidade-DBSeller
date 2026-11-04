<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Interfaces;

interface ArquivoInterface
{
    public function gerarDados();
    public function gerarXML();
    public function getListaArquivos();
    public function gerarCSV();
}
