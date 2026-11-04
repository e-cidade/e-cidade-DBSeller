<?php

namespace ECidade\Saude\Farmacia\Models;

use MaterialEstoqueMovimentacao;
use CgmBase;
use UnidadeProntoSocorro;
use DateTime;
use ECidade\Patrimonial\Material\Models\Fabricante;
use ECidade\Saude\Farmacia\Repositories\TipoMovimentacaoBnafarRepository;
use CgmFactory;
use ECidade\Patrimonial\Material\Repositories\FabricanteRepository;
use UnidadeProntoSocorroRepository;

class EstoqueMovimentacaoBnafar
{
    /**
     * @var integer
     */
    private $codigo;

    /**
     * @var MaterialEstoqueMovimentacao
     */
    private $estoqueMovimentacao;

    /**
     * @var TipoMovimentacaoBnafar
     */
    private $tipoMovimentacao;

    /**
     * @var CgmBase|null
     */
    private $cgm;

    /**
     * @var UnidadeProntoSocorro|null
     */
    private $unidade;

    /**
     * @param integer $codigo
     * @return EstoqueMovimentacaoBnafar
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
        return $this;
    }

    /**
     * @return integer
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param MaterialEstoqueMovimentacao $estoqueMovimentacao
     * @return EstoqueMovimentacaoBnafar
     */
    public function setEstoqueMovimentacao(MaterialEstoqueMovimentacao $estoqueMovimentacao)
    {
        $this->estoqueMovimentacao = $estoqueMovimentacao;
        return $this;
    }

    /**
     * @return MaterialEstoqueMovimentacao
     */
    public function getEstoqueMovimentacao()
    {
        return $this->estoqueMovimentacao;
    }

    /**
     * @param TipoMovimentacaoBnafar $tipoMovimentacao
     * @return EstoqueMovimentacaoBnafar
     */
    public function setTipoMovimentacao(TipoMovimentacaoBnafar $tipoMovimentacao)
    {
        $this->tipoMovimentacao = $tipoMovimentacao;
        return $this;
    }

    /**
     * @return TipoMovimentacaoBnafar
     */
    public function getTipoMovimentacao()
    {
        return $this->tipoMovimentacao;
    }

    /**
     * @param CgmBase $cgm
     * @return EstoqueMovimentacaoBnafar
     */
    public function setCgm(CgmBase $cgm)
    {
        $this->cgm = $cgm;
        return $this;
    }

    /**
     * @return CgmBase
     */
    public function getCgm()
    {
        return $this->cgm;
    }

    /**
     * @param UnidadeProntoSocorro $unidade
     * @return EstoqueMovimentacaoBnafar
     */
    public function setUnidade(UnidadeProntoSocorro $unidade)
    {
        $this->unidade = $unidade;
        return $this;
    }

    /**
     * @return UnidadeProntoSocorro
     */
    public function getUnidade()
    {
        return $this->unidade;
    }

    /**
     * @param array $state
     * @return EstoqueMovimentacaoBnafar
     */
    public static function fromState(array $state)
    {
        $self = new self;

        if (array_key_exists('fa69_codigo', $state)) {
            $self->setCodigo($state['fa69_codigo']);
        }
        if (array_key_exists('fa69_matestoqueini', $state)) {
            $self->setEstoqueMovimentacao(new MaterialEstoqueMovimentacao($state['fa69_matestoqueini']));
        }
        if (array_key_exists('fa69_tipomovimentacao', $state)) {
            $self->setTipoMovimentacao(TipoMovimentacaoBnafarRepository::find($state['fa69_tipomovimentacao']));
        }
        if (array_key_exists('fa69_cgm', $state) && !empty($state['fa69_cgm'])) {
            $self->setCgm(CgmFactory::getInstanceByCgm($state['fa69_cgm']));
        }
        if (array_key_exists('fa69_unidade', $state) && !empty($state['fa69_unidade'])) {
            $self->setUnidade(
                UnidadeProntoSocorroRepository::getUnidadeProntoSocorroByCodigo($state['fa69_unidade'])
            );
        }

        return $self;
    }
}
