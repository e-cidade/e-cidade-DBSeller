<?php

namespace App\Domain\Saude\Farmacia\Services;

use ECidade\Saude\Farmacia\Models\EstoqueMovimentacaoBnafar;
use ECidade\Saude\Farmacia\Repositories\EstoqueMovimentacaoBnafarRepository;
use App\Domain\Saude\Farmacia\Helpers\FarmaciaHelper;
use ECidade\Saude\Farmacia\Repositories\TipoMovimentacaoBnafarRepository;
use Exception;
use stdClass;

class EstoqueMovimentacaoBnafarService
{
    /**
     * @param stdClass $dados
     * @param integer $idMatEstoqueIni
     * @return void
     * @throws Exception
     */
    public static function salvar($dados, $idMatEstoqueIni)
    {
        if (!FarmaciaHelper::utilizaIntegracaoBnafar()) {
            return;
        }

        $repository = new EstoqueMovimentacaoBnafarRepository();

        $model = $repository->getByEstoqueMovimentacao($idMatEstoqueIni) ?: new EstoqueMovimentacaoBnafar;
        $model->setEstoqueMovimentacao(new \MaterialEstoqueMovimentacao($idMatEstoqueIni));
        $model->setTipoMovimentacao(TipoMovimentacaoBnafarRepository::find($dados->tipoMovimentacao));
        if (!empty($dados->cgm)) {
            $model->setCgm(\CgmFactory::getInstanceByCgm($dados->cgm));
        }
        if (!empty($dados->unidade)) {
            $model->setUnidade(
                \UnidadeProntoSocorroRepository::getUnidadeProntoSocorroByCodigo($dados->unidade)
            );
        }

        $repository->salvar($model);
    }

    /**
     * @param integer $idMovimentacaoSaida
     * @param integer $idDepartamentoSaida
     * @param integer $idMovimentacaoEntrada
     * @param integer $idDepartamentoEntrada
     * @return void
     * @throws Exception
     */
    public static function salvarTransferencia(
        $idMovimentacaoSaida,
        $idDepartamentoSaida,
        $idMovimentacaoEntrada,
        $idDepartamentoEntrada
    ) {
        // registro da saída com unidade de destino
        self::salvar((object)[
            'tipoMovimentacao' => 7, // Transferência/remanejamento
            'cgm' => null,
            'unidade' => FarmaciaHelper::isUnidadeSaude($idDepartamentoEntrada) ? $idDepartamentoEntrada : null
        ], $idMovimentacaoSaida);

        // registro da entrada com unidade de origem
        self::salvar((object)[
            'tipoMovimentacao' => 7, // Transferência/remanejamento
            'cgm' => null,
            'unidade' => FarmaciaHelper::isUnidadeSaude($idDepartamentoSaida) ? $idDepartamentoSaida : null
        ], $idMovimentacaoEntrada);
    }
}
