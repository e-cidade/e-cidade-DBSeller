<?php

namespace App\Domain\Tributario\Caixa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Model Recibounica
 *
 * @property int|null k00_numpre
 * @property string|null k00_dtvenc
 * @property string|null k00_dtoper
 * @property int|null k00_percdes
 * @property string|null k00_tipoger
 * @property int k00_recibounicageracao
 * @property int k00_sequencial
 */
class Recibounica extends Model
{
    protected $table = "caixa.recibounica";

    protected $fillable = [
        'k00_numpre',
        'k00_dtvenc',
        'k00_dtoper',
        'k00_percdes',
        'k00_tipoger',
        'k00_recibounicageracao',
        'k00_sequencial'
    ];

    /**
     * Campos customizados
     *
     * @var array<string, mixed> $attributesCustom
     */
    protected $attributesCustom = [];

    /**
     * Campos customizados usados no calculo
     *
     * @var array<int, string>
     */
    protected $fillableCalculado = [
        'k00_vlrhis',
        'k00_vlrcor',
        'k00_vlrjuros',
        'k00_vlrmulta',
        'k00_vlrtotal',
        'k00_vlrdesconto'
    ];

    /**
     * Metodo para calcular o valor da cota unica
     *
     * @param bool $receit calcular default false
     *
     * @return mixed
     */
    public function calcular()
    {
        $numpre = $this->attributes['k00_numpre'];
        $data   = $this->attributes['k00_dtvenc'];
        $ano    = date('Y');

        $sql =<<<SQL
            select
                substr(fc_calcula,2,13)::float8  as k00_vlrhis,
				substr(fc_calcula,15,13)::float8 as k00_vlrcor,
				substr(fc_calcula,28,13)::float8 as k00_vlrjuros,
				substr(fc_calcula,41,13)::float8 as k00_vlrmulta,
				substr(fc_calcula,54,13)::float8 as k00_vlrdesconto,
				(
                    substr(fc_calcula,15,13)::float8 +
				    substr(fc_calcula,28,13)::float8 +
				    substr(fc_calcula,41,13)::float8 -
				    substr(fc_calcula,54,13)::float8
                ) as k00_vlrtotal
            from
                (
                    select fc_calcula(
                        {$numpre},
                        0,
                        0,
                        '{$data}'::date,
                        '{$data}'::date,
                        '{$ano}'::int
                    )
                ) as x;
               
SQL;

        $result = DB::selectOne($sql);

        if ($result) {
            $this->attributesCustom = (array) $result;
        } else {
            $this->attributesCustom = array_combine(
                $this->fillableCalculado,
                [0, 0, 0, 0, 0, 0]
            );
        }
    }

    /**
     * Convert the model instance to an array.
     *
     * @override toArray
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge(
            $this->attributesToArray(),
            $this->relationsToArray(),
            $this->attributesCustom
        );
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        if (array_search($key, $this->fillableCalculado) !== false and
            isset($this->attributesCustom[$key])
        ) {
            return $this->attributesCustom[$key];
        } elseif (array_search($key, $this->fillableCalculado) !== false) {
            return null;
        }

        return $this->getAttribute($key);
    }
}
