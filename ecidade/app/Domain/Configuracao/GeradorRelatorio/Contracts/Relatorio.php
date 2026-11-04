<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Contracts;

interface Relatorio
{
    public function setLayout(array $layout);

    public function setCampos(array $campos);

    public function setDados(array $dados);

    /**
     * @return array
     */
    public function emitir();
}
