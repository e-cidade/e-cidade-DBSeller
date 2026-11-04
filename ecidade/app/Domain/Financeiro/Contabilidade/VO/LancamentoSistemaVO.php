<?php

namespace App\Domain\Financeiro\Contabilidade\VO;

/**
 * Armazena o sistema de atributos
 */
class LancamentoSistemaVO
{
    public $codigoSistema;
    public $reduzido;
    private $descricao;

    public function __construct($reduzido, $codigo)
    {
        $codigoSistema = $codigo;
        $reduzido = $reduzido;
    }

    /**
     * @var LancamentoAtributosContasVO[]
     */
    private $atributos = [];
}
