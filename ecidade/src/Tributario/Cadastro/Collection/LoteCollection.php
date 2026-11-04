<?php

namespace ECidade\Tributario\Cadastro\Collection;

use ECidade\Tributario\Library\ModelCollection;
use Lote;

final class LoteCollection extends ModelCollection
{
    protected function get($index)
    {
        $object = $this->fetchRow($this->resource, $index);

        $lote = new Lote();

        $lote->setCodigoSetor($object->j34_setor);
        $lote->setSetor($object->j30_descr);
        $lote->setQuadra($object->j34_quadra);
        $lote->setLote($object->j34_lote);
        $lote->setAreaLote($object->j34_area);
        $lote->setCodigoBairro($object->j34_bairro);
        $lote->setBairro($object->j13_descr);
        $lote->setAreaMedida($object->j34_areal);
        $lote->setTotalConstruido($object->j34_totcon);
        $lote->setZona($object->j34_zona);
        $lote->setQuantidadeMatriculas($object->j34_quamat);
        $lote->setAreaPreservada($object->j34_areapreservada);
        $lote->setCodigoLote($object->j34_idbql);
        $lote->setCodigoLogradouro($object->j14_codigo);
        $lote->setLogradouro($object->j14_nome);
        $lote->setCep($object->j29_cep);
        $lote->setValorTestadaLote($object->j36_testad);
        $lote->setCodigoLoteamento($object->j34_loteam);
        $lote->setDescricaoLoteamento($object->j34_descr);
        $lote->setCodigoTipoLogradouro($object->j88_codigo);
        $lote->setSiglaTipoLogradouro($object->j88_sigla);

        return $lote;
    }
}
