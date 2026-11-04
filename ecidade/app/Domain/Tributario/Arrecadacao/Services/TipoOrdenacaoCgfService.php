<?php

namespace App\Domain\Tributario\Arrecadacao\Services;

use App\Domain\Tributario\Arrecadacao\Repositories\TipoOrdenacaoCgfRepository;
use App\Helpers\Configuracao\BuscaRotulosSyscampo;
use ECidade\Tributario\Arrecadacao\Model\TipoOrdenacaoCgf;

class TipoOrdenacaoCgfService
{
    /** @var TipoOrdenacaoCgfRepository */
    private $tipoOrdenacaoCgfRepository;

    public function __construct(TipoOrdenacaoCgfRepository $tipoOrdenacaoCgfRepository)
    {
        $this->tipoOrdenacaoCgfRepository = $tipoOrdenacaoCgfRepository;
    }

    /**
     * @return array<key, object>
     */
    public function getTiposOrdenacao()
    {
        $tiposOrdenacao = $this->tipoOrdenacaoCgfRepository->getTiposDeOrdenacaoCgf();
        $tiposOrdenacao = array_map(function (TipoOrdenacaoCgf $grupo) {
            return (object) [
                'sequencial' => $grupo->getSequencial(),
                'descricao' => $grupo->getDescricao(),
            ];
        }, $tiposOrdenacao);
        return $tiposOrdenacao;
    }

    /**
     * @return Array
     */
    public function getRotulos()
    {
        $nomeCampos = [
            'k240_sequencial',
            'k240_descricao',
        ];
        return BuscaRotulosSyscampo::execute($nomeCampos);
    }
}
