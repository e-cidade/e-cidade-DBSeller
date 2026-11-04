<?php

namespace App\Domain\Saude\Farmacia\Services;

use App\Domain\Saude\Farmacia\Mappers\InconsistenciaBnafarMapper;
use App\Domain\Saude\Farmacia\Models\BnafarEnvio;
use App\Domain\Saude\Farmacia\Models\BnafarErro;
use App\Domain\Saude\Farmacia\Models\BnafarInconsistencia;
use Illuminate\Support\Facades\DB;

class BnafarInconsistenciasService
{
    /**
     * @param object $item
     * @param integer $protocolo
     * @throws \Exception
     */
    public static function salvar($item, $protocolo)
    {
        $envio = BnafarEnvio::where('fa70_matestoqueini', $item->codigoOrigem)
            ->where('fa70_protocolo', $protocolo)
            ->first();
        if ($envio === null) {
            throw new \Exception('Erro ao buscar dados do envio para o BNAFAR no sistema.');
        }

        $model = BnafarInconsistencia::where('fa71_bnafarenvio', $envio->fa70_id)->first();
        if ($model !== null) {
            return;
        }

        if (DB::transactionLevel() < 0) {
            DB::beginTransaction();
        }

        $mapper = new InconsistenciaBnafarMapper((object)json_decode($envio->fa70_body), $item->inconsistencias);
        foreach ($mapper->get() as $erro) {
            $model = new BnafarErro();
            $model->fa73_matestoqueini = $erro->idMovimentacao;
            $model->fa73_descricao = $erro->descricao;
            $model->fa73_campo = $erro->campo;
            $model->fa73_matestoqueitem = $erro->idEstoqueItem;
            $model->save();
        }

        $model = new BnafarInconsistencia();
        $model->fa71_bnafarenvio = $envio->fa70_id;
        $model->fa71_content = utf8_decode(json_encode($item, JSON_UNESCAPED_UNICODE));
        $model->save();

        DB::commit();
    }
}
