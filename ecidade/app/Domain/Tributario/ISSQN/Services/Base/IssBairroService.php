<?php

namespace App\Domain\Tributario\ISSQN\Services\Base;

use App\Domain\Tributario\ISSQN\Model\Base\IssBairro;
use App\Domain\Tributario\ISSQN\Model\Base\IssBase;

class IssBairroService
{
    /**
     * @throws \Throwable
     */
    public function save(IssBase $issBase, $data)
    {
        $issBairro = new IssBairro();
        $issBairro->q13_inscr = $issBase->q02_inscr;
        $issBairro->q13_bairro = $data->q13_bairro;
        $issBairro->saveOrFail();

        return $issBairro;
    }

    /**
     * @throws \Exception
     */
    public function update(IssBase $issBase, $data)
    {
        $updated = IssBairro::query()->where("q13_inscr", $issBase->q02_inscr)->update($data);

        if (!$updated) {
            throw new \Exception("Erro ao atualizar o bairro.");
        }
    }
}
