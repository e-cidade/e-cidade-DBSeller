<?php

namespace App\Domain\Tributario\ISSQN\Services\Base;

use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use App\Domain\Tributario\ISSQN\Model\Base\IssRuas;

class IssRuasService
{
    /**
     * @throws \Throwable
     */
    public function save(IssBase $issBase, $data)
    {
        $issRuas = new IssRuas();
        $issRuas->q02_inscr = $issBase->q02_inscr;
        $issRuas->j14_codigo = $data->j14_codigo;
        $issRuas->q02_numero = isset($data->q02_numero) ? $data->q02_numero : null;
        $issRuas->q02_compl = isset($data->q02_compl) ? $data->q02_compl : null;
        $issRuas->q02_cxpost = isset($data->q02_cxpost) ? $data->q02_cxpost : null;
        $issRuas->z01_cep = isset($data->z01_cep) ? $data->z01_cep : null;
        $issRuas->saveOrFail();

        return $issRuas;
    }

    /**
     * @throws \Throwable
     */
    public function update(IssBase $issBase, $data)
    {
        $updated = IssRuas::query()->where("q02_inscr", $issBase->q02_inscr)->update($data);

        if (!$updated) {
            throw new \Exception("Erro ao atualizar a rua.");
        }
    }
}
