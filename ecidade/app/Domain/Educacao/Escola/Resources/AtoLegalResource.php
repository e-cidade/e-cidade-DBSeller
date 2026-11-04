<?php
namespace App\Domain\Educacao\Escola\Resources;

use App\Domain\Educacao\Escola\Models\AtoLegal;
use Carbon\Carbon;

class AtoLegalResource
{
    const COMPETENCIAS = [
        "M" => "MUNICIPAL",
        "E" => "ESTADUAL",
        "F" => "FEDERAL"
    ];
    public static function toResponse(AtoLegal $ato)
    {
        return (object) [
            'codigo' => $ato->ed05_i_codigo,
            'numero' => $ato->ed05_c_numero,
            'finalidade' => trim($ato->ed05_c_finalidade),
            'tipoAto' =>
                trim(\DB::table('tipoato')
                    ->where('ed83_i_codigo', $ato->ed05_i_tipoato)->get()->first()->ed83_c_descr),
            'competencia' => self::COMPETENCIAS[$ato->ed05_c_competencia],
            'ano' => $ato->ed05_i_ano,
            'orgao' => trim($ato->ed05_c_orgao),
            'dataVigor' => new Carbon($ato->ed05_d_vigora),
            'dataAprovacao' => new Carbon($ato->ed05_d_aprovado),
            'dataPublicacao' => new Carbon($ato->ed05_d_publicado),
            'texto' => trim($ato->ed05_t_texto),
            'apareceHistorico' => $ato->ed05_i_aparecehistorico
        ];
    }
}
