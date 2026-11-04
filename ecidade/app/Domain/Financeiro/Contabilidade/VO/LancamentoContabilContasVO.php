<?php

namespace App\Domain\Financeiro\Contabilidade\VO;

class LancamentoContabilContasVO
{
    /**
     * @var integer
     */
    public $id;
    /**
     * @var integer
     */
    public $lancamento;
    /**
     * @var integer
     */
    public $exercicio;
    /**
     * @var integer
     */
    public $historico;
    /**
     * @var integer
     */
    public $contaCredito;
    /**
     * @var integer
     */
    public $contaDebito;
    /**
     * @var float
     */
    public $valor;
    /**
     * @var string
     */
    public $data;
    /**
     * @var integer
     */
    public $ordem;
}
