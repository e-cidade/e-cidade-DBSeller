<?php

namespace App\Domain\Tributario\ISSQN\Services\Base;

use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use App\Domain\Tributario\ISSQN\Model\Base\Isscadsimples;
use App\Domain\Tributario\ISSQN\Model\Base\Isscadsimplesbaixa;

class IsscadsimplesService
{
    /**
     * @throws \Throwable
     */
    public function save(IssBase $issBase, $data)
    {
        $isscadsimples = new Isscadsimples();
        $isscadsimples->q38_inscr = $issBase->q02_inscr;
        $isscadsimples->q38_dtinicial = $data->q38_dtinicial;
        $isscadsimples->q38_categoria = isset($data->q38_categoria) ? $data->q38_categoria : null;
        $isscadsimples->q38_observacao = isset($data->q38_observacao) ? $data->q38_observacao : null;
        $isscadsimples->saveOrFail();

        return $isscadsimples;
    }

    /**
     * @throws \Throwable
     */
    public function baixa(Isscadsimples $isscadsimples, $data)
    {
        $isscadsimplesbaixa = new Isscadsimplesbaixa();
        $isscadsimplesbaixa->q39_isscadsimples = $isscadsimples->q38_sequencial;
        $isscadsimplesbaixa->q39_dtbaixa = $data->q39_dtbaixa;
        $isscadsimplesbaixa->q39_issmotivobaixa = $data->q39_issmotivobaixa;
        $isscadsimplesbaixa->q39_obs = isset($data->q39_obs) ? $data->q39_obs : null;
        $isscadsimplesbaixa->saveOrFail();

        return $isscadsimplesbaixa;
    }
}
