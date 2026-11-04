<?php

namespace ECidade\Saude\Farmacia\Repositories;

use Exception;
use ECidade\Educacao\Escola\Repository\Repository;
use ECidade\Saude\Farmacia\Models\TipoMovimentacaoBnafar;

class TipoMovimentacaoBnafarRepository extends Repository
{
    /**
     * @param integer $id
     * @return TipoMovimentacaoBnafar
     * @throws Exception
     */
    public static function find($id)
    {
        $self = new self;
        return $self->scopeCodigo($id)->first();
    }

    /**
     * @return TipoMovimentacaoBnafar[]
     * @throws Exception
     */
    public function get()
    {
        $dao = new \cl_tipomovimentacaobnafar;
        $sql = $dao->sql_query_file('', '*', '', implode(' AND ', $this->scopes));
        $rs = $dao->sql_record($sql);

        if (!$rs) {
            throw new Exception("Erro ao buscar tipo de movimentação.");
        }

        $tipos = [];
        while ($state = pg_fetch_array($rs)) {
            $tipos[] = TipoMovimentacaoBnafar::fromState($state);
        }

        return $tipos;
    }

    /**
     * @return TipoMovimentacaoBnafar|null
     * @throws Exception
     */
    public function first()
    {
        $tipos = $this->get();
        return !empty($tipos) ? $tipos[0] : null;
    }

    /**
     * @param integer $codigo
     * @return $this
     */
    public function scopeCodigo($codigo)
    {
        $this->scopes['fa68_codigo'] = "fa68_codigo = {$codigo}";
        return $this;
    }

    /**
     * @return $this
     */
    public function scopeEntrada()
    {
        $this->scopes['fa68_tipo'] = "fa68_tipo in ('A', 'E')";
        return $this;
    }

    /**
     * @return $this
     */
    public function scopeSaida()
    {
        $this->scopes['fa68_tipo'] = "fa68_tipo in ('A', 'S')";
        return $this;
    }
}
