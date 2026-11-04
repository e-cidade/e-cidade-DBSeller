<?php

namespace App\Domain\Financeiro\Contabilidade\VO;

use Carbon\Carbon;

/**
 * Class LancamentoContabilVO
 * @package App\Domain\Financeiro\Contabilidade\VO
 */
class LancamentoContabilVO
{
    /**
     * @var Carbon
     */
    public $data;
    /**
     * @var integer
     */
    public $exercicio;
    /**
     * @var integer
     */
    public $valor;
    /**
     * @var integer
     */
    public $instituicao;
    /**
     * @var integer
     */
    public $documento;
    /**
     * conlancamcompl
     * @var string
     */
    public $observacao;

    /**
     * @var LancamentoContabilContasVO[]
     */
    protected $contas = [];

    public function addContas(LancamentoContabilContasVO $contabilContasVO)
    {
        $this->contas[] = $contabilContasVO;
    }

    /**
     * @return LancamentoContabilContasVO[]
     */
    public function getContas()
    {
        return $this->contas;
    }
}
