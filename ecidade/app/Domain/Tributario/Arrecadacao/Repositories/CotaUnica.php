<?php

namespace App\Domain\Tributario\Arrecadacao\Repositories;

use Exception;

class CotaUnica
{
    /**
     * Data do vencimento
     *
     * @var \DateTime $dataVencimento
     */
    public $dataVencimento;

    /**
     * Valor do Desconto
     *
     * @var int $valorDesconto, 0 ~ 100
     */
    public $valorDesconto;

    /**
     * Valor se existe uma cota unica registrada com esse parametros
     *
     * @return bool $existe
     */
    public $existe;

    public function __construct(
        $dataVencimento = null,
        $valorDesconto = 0,
        $existe = false
    ) {
        $dataVencimento = date_create($dataVencimento);

        if (!$dataVencimento) {
            throw new Exception('A data do vencimento informada e invalida');
        }

        if (!is_integer($valorDesconto) and
            $valorDesconto < 0 ||
            $valorDesconto > 100
        ) {
            throw new Exception(
                'O valor do desconto deve ser um inteiro'.
                ' nao menor que zero ou maior que cem'
            );
        }

        if (is_null($existe) && $valorDesconto !== 0) {
            $existe = true;
        } elseif (!is_bool($existe)) {
            throw new Exception(
                'A verificacao se foi informado uma conta'.
                ' unica deve ser um boleano'
            );
        }

        $this->dataVencimento = $dataVencimento;
        $this->valorDesconto  = $valorDesconto;
        $this->existe         = $existe;
    }

    /**
     * Retorna a data de vencimento da CotaUnica
     *
     * @param string $format Formato do retorno, default Y-m-d
     *
     * @return string
     */
    public function getDataVenc($format = 'Y-m-d')
    {
        return date_format($this->dataVencimento, $format);
    }

    /**
     * Retorna o valor do desconto da CotaUnica
     *
     * @param boolean $format
     *
     * @return string
     */
    public function getDesconto($format = true)
    {
        return (!$format) ?
            (string) (int) $this->valorDesconto :
            str_pad($this->valorDesconto, 2, "0", STR_PAD_LEFT);
    }
}
