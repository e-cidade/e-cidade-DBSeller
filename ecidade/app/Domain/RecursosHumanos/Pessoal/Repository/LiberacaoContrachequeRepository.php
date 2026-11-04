<?php

namespace App\Domain\RecursosHumanos\Pessoal\Repository;

use App\Domain\RecursosHumanos\Pessoal\Model\Contracheque\LiberacaoContrachequeModel;

class LiberacaoContrachequeRepository
{
    /**
     * Busca parametros de configuracao de liberacao de contracheque
     * @param integer $codigoInstituicao
     */
    public function buscaLiberacoes($codigoInstituicao)
    {
        $liberacaoContracheque = new LiberacaoContrachequeModel();

        return $liberacaoContracheque->where('rh301_instituicao', '=', $codigoInstituicao)
            ->orderBy('rh301_ano', 'desc')->orderBy('rh301_mes', 'desc')->get()->toArray();
    }

    /**
     * Busca a liberacao de contracheque por codigo
     * @param integer $codigo
     */
    public function buscaLiberacao($codigo)
    {
        $liberacaoContracheque = new LiberacaoContrachequeModel();

        return $liberacaoContracheque::find($codigo);
    }
}
