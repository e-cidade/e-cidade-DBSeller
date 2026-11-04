<?php

namespace ECidade\Tributario\Caixa\Collection;

use \DateTime;
use ECidade\Tributario\Library\ModelCollection;
use ECidade\Tributario\Caixa\Model\Recibopaga;

final class RecibopagaCollection extends ModelCollection
{
    public function get($index)
    {
        $recibopaga = new Recibopaga();

        if (pg_num_rows($this->resource) > 0) {
            $object = $this->fetchRow($this->resource, $index);

            $recibopaga->setNumcgm($object->k00_numcgm);
            $recibopaga->setDtoper(new DateTime($object->k00_dtoper));
            $recibopaga->setReceit($object->k00_receit);
            $recibopaga->setHist($object->k00_hist);
            $recibopaga->setValor($object->k00_valor);
            $recibopaga->setDtvenc(new DateTime($object->k00_dtvenc));
            $recibopaga->setNumpre($object->k00_numpre);
            $recibopaga->setNumpar($object->k00_numpar);
            $recibopaga->setNumtot($object->k00_numtot);
            $recibopaga->setNumdig($object->k00_numdig);
            $recibopaga->setConta($object->k00_conta);
            $recibopaga->setDtpaga(new DateTime($object->k00_dtpaga));
            $recibopaga->setNumnov($object->k00_numnov);
        }

        return $recibopaga;
    }
}
