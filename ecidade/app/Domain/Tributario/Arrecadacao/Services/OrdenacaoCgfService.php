<?php

namespace App\Domain\Tributario\Arrecadacao\Services;

use App\Domain\Tributario\Arrecadacao\Repositories\OrdenacaoCgfRepository;
use ECidade\Tributario\Arrecadacao\Model\OrdenacaoCgf;

class OrdenacaoCgfService
{
    /** @var OrdenacaoCgfRepository */
    private $ordenacaoCgfRepository;

    public function __construct(OrdenacaoCgfRepository $ordenacaoCgfRepository)
    {
        $this->ordenacaoCgfRepository = $ordenacaoCgfRepository;
    }

    /**
     * @param integer $cadtipo
     * @return array<key, object>
     */
    public function getOrdenacoesPorGrupo($cadtipo)
    {
        $ordenacoes = $this->ordenacaoCgfRepository->getByGrupoDebito(
            $cadtipo
        );
        $ordenacoes = array_map(function (OrdenacaoCgf $grupo) {
            return (object) [
                'sequencial' => $grupo->getSequencial(),
                'grupo' => $grupo->getGrupo(),
                'tipo' => $grupo->getTipo(),
            ];
        }, $ordenacoes);
        return $ordenacoes;
    }

    /**
     * @param integer $cadtipo
     * @return void
     */
    public function apagarOrdenacoesPorGrupo($cadtipo)
    {
        $this->ordenacaoCgfRepository->apagarPorGrupo($cadtipo);
    }

    /**
     * @param OrdenacaoCgf $ordenacao
     * @return void
     */
    public function adicionarOrdenacao(OrdenacaoCgf $ordenacao)
    {
        $entity = new OrdenacaoCgf();
        $entity->setGrupo($ordenacao->getGrupo());
        $entity->setTipo($ordenacao->getTipo());
        $this->ordenacaoCgfRepository->persist($entity);
    }
}
