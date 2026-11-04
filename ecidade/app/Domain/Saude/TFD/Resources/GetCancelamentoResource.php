<?php

namespace App\Domain\Saude\TFD\Resources;

use App\Domain\Saude\TFD\Models\CancelamentoViagem;
use Illuminate\Database\Eloquent\Collection;

class GetCancelamentoResource
{
    /**
     * @param Collection $dados
     * @return array
     */
    public static function toArray(Collection $dados)
    {
        $retorno = [];

        foreach ($dados as $dado) {
            $retorno[] = self::toObject($dado);
        }

        return $retorno;
    }


    /**
     * @param CancelamentoViagem $cancelamento
     * @return object
     */
    public static function toObject(CancelamentoViagem $cancelamento)
    {
        return (object)[
            'motivoCancelamento' => $cancelamento->tf41_motivocancelamento,
            'dataHora' => date("d/m/Y H:i", strtotime($cancelamento->tf41_datahora)),
            'login' => $cancelamento->login,
        ];
    }
}
