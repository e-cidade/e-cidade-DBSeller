<?php

namespace App\Domain\RecursosHumanos\Pessoal\Services\Contracheque;

use App\Domain\RecursosHumanos\Pessoal\Repository\LiberacaoContrachequeRepository;

class LiberacaoContrachequeService
{
    /**
     * @var LiberacaoContrachequeRepository
     */
    private $liberacaoContrachequeRepository;

    public function __construct(LiberacaoContrachequeRepository $liberacaoContrachequeRepository)
    {
        $this->liberacaoContrachequeRepository = $liberacaoContrachequeRepository;
    }

    /**
     * Busca parametros de liberacao de contracheques da instituicao
     * @param integer $codigoInstituicao
     */
    public function buscaLiberacoes($codigoInstituicao)
    {
        return $this->liberacaoContrachequeRepository->buscaLiberacoes($codigoInstituicao);
    }

    /**
     * Busca parametros de liberacao de contracheques por código
     * @param integer $codigo
     */
    public function buscaLiberacao($codigo)
    {
        return $this->liberacaoContrachequeRepository->buscaLiberacao($codigo);
    }
}
