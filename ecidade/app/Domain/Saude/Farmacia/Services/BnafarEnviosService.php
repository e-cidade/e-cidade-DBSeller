<?php

namespace App\Domain\Saude\Farmacia\Services;

use App\Domain\Saude\Farmacia\Models\BnafarEnvio;
use Illuminate\Support\Facades\DB;

class BnafarEnviosService
{
    public static function salvar($method, $uri, $body, $protocolo = null)
    {
        if (is_array($body)) {
            foreach ($body as $dados) {
                static::salvar($method, $uri, $dados, $protocolo);
            }
            return;
        }

        if (DB::transactionLevel() <= 0) {
            DB::beginTransaction();
        }

        $model = new BnafarEnvio();
        $model->fa70_matestoqueini = $body->caracterizacao->codigoOrigem;
        $model->fa70_data = new \DateTime();
        $model->fa70_uri = $uri;
        $model->fa70_method = $method;
        $model->fa70_protocolo = $protocolo;
        $model->fa70_body = json_encode($body);
        $model->save();

        DB::commit();
    }

    /**
     * @param integer $codigoBnafar
     * @param integer $idEstoqueMovimentacao
     * @param integer $protocolo
     * @return void
     * @throws \Exception
     */
    public static function vincular($codigoBnafar, $idEstoqueMovimentacao, $protocolo = null)
    {
        $query = BnafarEnvio::where('fa70_matestoqueini', $idEstoqueMovimentacao);
        if ($protocolo) {
            $query->where('fa70_protocolo', $protocolo);
        } else {
            $query->whereNull('fa70_protocolo');
        }

        $model = $query->first();
        if (!$model) {
            throw new \Exception('Erro ao buscar dados do envio para o BNAFAR no sistema.');
        }

        if (DB::transactionLevel() <= 0) {
            DB::beginTransaction();
        }
        $model->fa70_codigobnafar = $codigoBnafar;
        $model->save();

        DB::commit();
    }
}
