<?php

namespace App\Domain\Tributario\Arrecadacao\Services;

use App\Domain\Tributario\Arrecadacao\Repositories\CadTipoRepository;
use App\Helpers\Configuracao\BuscaRotulosSyscampo;
use ECidade\Tributario\Arrecadacao\Model\CadTipo;

class CadTipoService
{
    /** @var CadTipoRepository */
    private $cadTipoRepository;

    public function __construct(CadTipoRepository $cadTipoRepository)
    {
        $this->cadTipoRepository = $cadTipoRepository;
    }

    /**
     * @return array<key, object>
     */
    public function getGruposDebito()
    {
        $gruposDebito = $this->cadTipoRepository->getGruposDebito();
        $gruposDebito = array_map(function (CadTipo $grupo) {
            return (object) [
                'sequencial' => $grupo->getSequencial(),
                'descricao' => $grupo->getDescricao(),
                'parelaSomanteAnoAtual' => $grupo->getParelaSomanteAnoAtual(),
                'isParcelamento' => $grupo->getIsParcelamento(),
                'permiteParcelar' => $grupo->getPermiteParcelar(),
            ];
        }, $gruposDebito);
        return $gruposDebito;
    }

    /**
     * @return Array
     */
    public function getRotulos()
    {
        $nomeCampos = [
            "k03_tipo",
            "k03_descr",
            "k03_parcano",
            "k03_parcelamento",
            "k03_permparc",
        ];
        return BuscaRotulosSyscampo::execute($nomeCampos);
    }
}
