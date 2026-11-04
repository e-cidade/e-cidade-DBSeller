<?php

namespace App\Domain\Saude\Farmacia\Resources;

use Illuminate\Support\Collection;

class InconsistenciaBnafarResource
{
    /**
     * @param Collection $dados
     * @return array
     */
    public function toResponse(Collection $dados)
    {
        $dadosFormatados = [];
        foreach ($dados as $movimentacao) {
            if (!array_key_exists($movimentacao->codigo_origem, $dadosFormatados)) {
                $dadosFormatados[$movimentacao->codigo_origem] = $this->toObject($movimentacao);
            }

            if ($movimentacao->erro_bnafar) {
                $dadosFormatados[$movimentacao->codigo_origem]->erroBnafar = true;
            }

            $dadosFormatados[$movimentacao->codigo_origem]->itens[] = $this->itemToResponse($movimentacao);
        }

        return array_values($dadosFormatados);
    }

    /**
     * @param object $movimentacao
     * @return object
     */
    protected function toObject($movimentacao)
    {
        return (object)[
            'lancamento' => $movimentacao->codigo_origem,
            'descricao' => $movimentacao->m81_descr,
            'tipo' => $movimentacao->tipo,
            'errosGenericos' => self::getErrosGenericos($movimentacao),
            'erroBnafar' => false,
            'itens' => []
        ];
    }
    /**
     * @param object $movimentacao
     * @return object
     */
    protected function itemToResponse($movimentacao)
    {
        $isFabricanteInternacional =
            !empty($movimentacao->nome_fabricante) && empty($movimentacao->cnpj_fabricante);

        return (object)[
            'estoqueItem' => $movimentacao->codigo_origem_item,
            'id' => $movimentacao->fa01_i_codmater,
            'descricao' => $movimentacao->m60_descr,
            'lote' => self::campoToObject($movimentacao, 'lote'),
            'validade' => self::campoToObject($movimentacao, 'data_validade', true),
            'idFabricante' => self::campoToObject($movimentacao, 'id_fabricante'),
            'nomeFabricante' => $movimentacao->nome_fabricante,
            'cnpjFabricante' => $movimentacao->cnpj_fabricante,
            'isFabricanteInternacional' => $isFabricanteInternacional,
            'numeroProduto' => self::campoToObject($movimentacao, 'numero_produto'),
            'idProduto' => self::campoToObject($movimentacao, 'id_produto'),
            'descricaoProduto' => $movimentacao->descricao_produto
        ];
    }

    /**
     * @param object $movimentacao
     * @param string $campo
     * @param boolean $data
     * @return object
     */
    protected function campoToObject($movimentacao, $campo, $data = false)
    {
        return (object)[
            'valor' => !$data ? $movimentacao->{$campo} : db_formatar($movimentacao->{$campo}, 'd'),
            'inconsistente' => array_key_exists($campo, $movimentacao->erros),
            'descricaoErro' => array_key_exists($campo, $movimentacao->erros) ? $movimentacao->erros[$campo] : ''
        ];
    }

    protected function getErrosGenericos($movimentacao)
    {
        $erros = [];

        foreach ($movimentacao->erros as $key => $erro) {
            if (!is_numeric($key)) {
                continue;
            }

            $erros[] = $erro;
        }

        return $erros;
    }
}
