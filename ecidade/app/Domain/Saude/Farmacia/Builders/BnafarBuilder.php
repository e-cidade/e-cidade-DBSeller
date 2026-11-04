<?php

namespace App\Domain\Saude\Farmacia\Builders;

use Generator;
use Illuminate\Support\Collection;

abstract class BnafarBuilder
{
    /**
     * Conforme documentação v1.8 do dia 10/11/2022
     * Para os envios assíncronos, o tamanho máximo permitido para cada transmissão será de 1.000 registros.
     */
    const MAX_REGISTROS = 1000;

    /**
     * @var integer
     */
    protected $cnes;

    /**
     * @var string
     */
    protected $tipoEstabelecimento;

    /**
     * @var Collection
     */
    protected $dados;

    public function setCnes($cnes)
    {
        $this->cnes = $cnes;
    }

    public function setTipoEstabelecimento($tipoEstabelecimento)
    {
        $this->tipoEstabelecimento = $tipoEstabelecimento;
    }

    /**
     * @param Collection $dados
     */
    public function setDados(Collection $dados)
    {
        $this->dados = $dados;
    }

    /**
     * @return Generator
     */
    final public function build()
    {
        $request = [];
        $contador = 0;
        while (count($this->dados) > 0) {
            $request[] = $this->buildBody();
            $contador++;
            if ($contador === self::MAX_REGISTROS) {
                $contador = 0;
                $generator = $request;
                $request = [];
                yield $generator;
            }
        }

        if (count($request) > 0) {
            yield $request;
        }
    }

    abstract protected function buildCaracterizacao();

    protected function buildEstabelecimento()
    {
        return (object)[
            'cnes' => $this->cnes,
            'tipo' => $this->tipoEstabelecimento
        ];
    }

    protected function buildBody()
    {
        return (object)[
            'codigo' => null,
            'estabelecimento' =>  $this->buildEstabelecimento(),
            'caracterizacao' => $this->buildCaracterizacao(),
            'itens' => $this->buildItens()
        ];
    }

    protected function buildItens($maxItens = 60)
    {
        $itens = [];
        $idEstoqueMovimentacao = $this->dados->first()->codigo_origem;
        foreach ($this->dados as $key => $item) {
            if ($item->codigo_origem != $idEstoqueMovimentacao) {
                continue;
            }
            if (count($itens) == $maxItens) {
                break;
            }
            $itens[] = $this->buildItem($item);
            unset($this->dados[$key]);
        }

        return $itens;
    }

    protected function buildItem($item)
    {
        $nomeFabricanteInternacional = '';
        if (empty($item->cnpj_fabricante)) {
            $nomeFabricanteInternacional = $item->nome_fabricante;
        }
        return (object)[
            'cnpjFabricante' => $item->cnpj_fabricante,
            'dataValidade' => $item->data_validade,
            'lote' => $item->lote,
            'nomeFabricanteInternacional' => $nomeFabricanteInternacional,
            'numero' => $item->numero_produto,
            'tipoProduto' => $item->tipo_produto,
            'codigoOrigem' => $item->codigo_origem_item,
            'siglaProgramaSaude' => '',
            'quantidade' => (int)$item->quantidade
        ];
    }
}
