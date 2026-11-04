<?php

namespace App\Domain\Educacao\Escola\Services;

use App\Domain\Educacao\Escola\Models\AtestadoFrequenciaResumo;
use Illuminate\Http\Request;

class AtestadoFrequenciaService
{
    /**
     * @param Request $request
     *
     * @return void
     *
     * @throws \Exception
     */
    public static function updateOrCreateObs(
        $rechumanoescola = null,
        $obs = null,
        $atestado = null
    ) {
        if (!$rechumanoescola) {
            return;
        }

        if ($resumo = AtestadoFrequenciaResumo::where('ed364_rechumanoescola', $rechumanoescola)
                ->where('ed364_atestado', $atestado)
                ->first()
        ) {
            return $resumo->fill([
                'ed364_observacao' => $obs,
            ])->update();
        }

        $resumo = new AtestadoFrequenciaResumo([
            'ed364_observacao' => $obs,
            'ed364_rechumanoescola' => $rechumanoescola,
            'ed364_atestado' => $atestado,
        ]);

        $resumo->save();
    }
}
