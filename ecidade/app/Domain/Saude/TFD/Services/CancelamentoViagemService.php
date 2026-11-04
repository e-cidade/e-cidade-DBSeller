<?php

namespace App\Domain\Saude\TFD\Services;

use App\Domain\Saude\TFD\Models\CancelamentoViagem;
use App\Domain\Saude\TFD\Requests\CancelaVeiculoRequest;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

class CancelamentoViagemService
{
    /**
     * @param CancelaVeiculoRequest $request
     * @return void
     */
    public function cancelaVeiculo(CancelaVeiculoRequest $request)
    {
        $dataHora = new DateTime();
        $cancelamento = new CancelamentoViagem();
        $cancelamento->tf41_usuario = $request->DB_id_usuario;
        $cancelamento->tf41_datahora = $dataHora->format('Y-m-d H:i');
        $cancelamento->tf41_veiculodestino = $request->codigoAgendamento;
        $cancelamento->tf41_motivocancelamento = $request->motivoCancelamento;
        $cancelamento->save();
        return;
    }

    /**
     * @param $cancelamento
     * @return CancelamentoViagem[]|Collection|Builder[]|\Illuminate\Support\Collection
     */
    public function getCancelamento($cancelamento)
    {
        $cancelamento = CancelamentoViagem::query()
            ->where('tf41_veiculodestino', $cancelamento)
            ->join('db_usuarios', 'id_usuario', '=', 'tf41_usuario')
            ->get();

        return $cancelamento;
    }
}
