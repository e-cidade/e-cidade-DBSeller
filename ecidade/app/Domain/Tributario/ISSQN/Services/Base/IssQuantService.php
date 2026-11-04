<?php

namespace App\Domain\Tributario\ISSQN\Services\Base;

use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use App\Domain\Tributario\ISSQN\Model\Base\IssQuant;

class IssQuantService
{
    /**
     * @throws \Throwable
     */
    public function save(IssBase $issBase, $data)
    {
        $issQuant = new IssQuant();
        $issQuant->q30_inscr = $issBase->q02_inscr;
        $issQuant->q30_anousu = $data->q30_anousu;
        $issQuant->q30_quant = isset($data->q30_quant) ? $data->q30_quant : null;
        $issQuant->q30_mult = isset($data->q30_mult) ? $data->q30_mult : null;
        $issQuant->q30_area = isset($data->q30_area) ? $data->q30_area : null;
        $issQuant->q30_tempofuncionamento = isset($data->q30_tempofuncionamento) ? $data->q30_tempofuncionamento : null;
        $issQuant->q30_areapublicidade = isset($data->q30_areapublicidade) ? $data->q30_areapublicidade : null;
        $issQuant->q30_graurisco = isset($data->q30_graurisco) ? $data->q30_graurisco : null;
        $issQuant->saveOrFail();

        return $issQuant;
    }
}
