<?php

namespace App\Domain\Integracoes\EFDReinf\Retencao\Interfaces;

interface RetencaoInterface
{
    /**
     * Lista as retencoes do evento
     *
     * @param object $filters
     * @return object
     */
    public function getRetencoes($filters);


    /**
     * Salava/Atualiza dados da retencao
     *
     * @param object $data
     * @return bool|void
     */
    public function saveRetencao($data);
}
