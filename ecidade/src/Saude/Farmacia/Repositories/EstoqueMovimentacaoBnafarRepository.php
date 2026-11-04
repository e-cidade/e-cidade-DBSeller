<?php

namespace ECidade\Saude\Farmacia\Repositories;

use ECidade\Educacao\Escola\Repository\Repository;
use ECidade\Saude\Farmacia\Models\EstoqueMovimentacaoBnafar;
use Exception;

class EstoqueMovimentacaoBnafarRepository extends Repository
{
    /**
     * @param EstoqueMovimentacaoBnafar $model
     * @return EstoqueMovimentacaoBnafar
     * @throws Exception
     */
    public function salvar(EstoqueMovimentacaoBnafar $model)
    {
        $dao = new \cl_estoquemovimentacaobnafar;
        $dao->fa69_codigo = $model->getCodigo();
        $dao->fa69_matestoqueini = $model->getEstoqueMovimentacao()->getCodigo();
        $dao->fa69_tipomovimentacao = $model->getTipoMovimentacao()->getCodigo();
        $dao->fa69_cgm = $model->getCgm() ? $model->getCgm()->getCodigo() : null;
        $dao->fa69_unidade = $model->getUnidade() ? $model->getUnidade()->getCodigo() : null;

        if ($dao->fa69_codigo) {
            $dao->alterar($dao->fa69_codigo);
        } else {
            $dao->incluir(null);
        }

        if ($dao->erro_status == 0) {
            throw new Exception($dao->erro_msg);
        }

        return $model->setCodigo($dao->fa69_codigo);
    }

    /**
     * @param integer $idEstoqueMovimentacao
     * @return EstoqueMovimentacaoBnafar|null
     */
    public function getByEstoqueMovimentacao($idEstoqueMovimentacao)
    {
        return $this->resetScopes()->scopeEstoqueMovimentacao($idEstoqueMovimentacao)->first();
    }

    /**
     * @return EstoqueMovimentacaoBnafar[]
     */
    public function get()
    {
        $dao = new \cl_estoquemovimentacaobnafar;
        $sql = $dao->sql_query_file('', '*', '', implode(' AND ', $this->scopes));
        $rs = $dao->sql_record($sql);
        if (!$rs) {
            return [];
        }

        $dados = [];
        while ($state = pg_fetch_assoc($rs)) {
            $dados[] = EstoqueMovimentacaoBnafar::fromState($state);
        }

        return $dados;
    }

    /**
     * @return EstoqueMovimentacaoBnafar|null
     */
    public function first()
    {
        $movimentacoes = $this->get();
        return count($movimentacoes) ? $movimentacoes[0] : null;
    }

    /**
     * @param integer $idEstoqueMovimentacao
     * @return EstoqueMovimentacaoBnafarRepository
     */
    public function scopeEstoqueMovimentacao($idEstoqueMovimentacao)
    {
        $this->scopes['fa69_matestoqueini'] = "fa69_matestoqueini = {$idEstoqueMovimentacao}";
        return $this;
    }
}
