<?php

namespace App\Domain\Tributario\Caixa\Models;

use App\Domain\Tributario\Caixa\Models\Arrecad;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Model Arrematric
 *
 * @property int k00_numpre
 * @property int k00_matric
 * @property double|null k00_perc
 */
class Arrematric extends Model
{
    public $timestamps = false;

    protected $table = 'caixa.arrematric';
    protected $primaryKey = 'k00_numpre';

    public $fillable = [
        'k00_numpre',
        'k00_matric',
        'k00_perc'
    ];

    /**
     * Retorna uma lista de Arrematric com join na arrecad
     * mais o somatorio dos valores
     *
     * @param int $k00_matric
     * @param array<int> $k00_tipo
     *
     * @return mixed
     */
    public static function getTotalDividasAtivas($k00_matric, $k00_tipo = [])
    {
        $k00_tipo = implode(',', $k00_tipo);

        $sql =<<<SQL
            select
                a.*,
                (
                    select
                        sum(round(ad.k00_valor, 2))
                    from
                        caixa.arrecad ad
                    where
                        ad.k00_numpre = a.k00_numpre
                        and k00_valor is not null
                ) as k00_valor
            from
                caixa.arrematric a
            where
                a.k00_matric = {$k00_matric}
                and exists (
                    select
                        k00_numpre
                    from
                        caixa.arrecad ad
                    where
                        ad.k00_numpre = a.k00_numpre
                        and ad.k00_tipo not in ({$k00_tipo})
                )
            group by
                a.k00_matric, a.k00_numpre;
SQL;

        $arrematric = DB::select($sql);

        return $arrematric;
    }

    public function arrecad()
    {
        return $this->hasMany(Arrecad::class, 'k00_numpre', 'k00_numpre');
    }
}
