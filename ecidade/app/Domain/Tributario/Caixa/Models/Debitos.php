<?php

namespace App\Domain\Tributario\Caixa\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Tributario\Caixa\Models\Arretipo;

use Illuminate\Support\Facades\DB;

/**
 * @property string|null k22_data
 * @property int|null k22_numpre
 * @property int|null k22_numpar
 * @property int|null k22_receit
 * @property string|null k22_dtvenc
 * @property string|null k22_dtoper
 * @property int|null k22_hist
 * @property int|null k22_numcgm
 * @property int|null k22_matric
 * @property int|null k22_inscr
 * @property int|null k22_tipo
 * @property float|null k22_vlrhis
 * @property float|null k22_vlrcor
 * @property float|null k22_juros
 * @property float|null k22_multa
 * @property float|null k22_desconto
 * @property int|null k22_exerc
 * @property int|null k22_instit
 */
class Debitos extends Model
{
    public $timestamp = false;

    protected $table = 'caixa.debitos';
    protected $primaryKey = '';

    public $fillable = [
        'k22_data',
        'k22_numpre',
        'k22_numpar',
        'k22_receit',
        'k22_dtvenc',
        'k22_dtoper',
        'k22_hist',
        'k22_numcgm',
        'k22_matric',
        'k22_inscr',
        'k22_tipo',
        'k22_vlrhis',
        'k22_vlrcor',
        'k22_juros',
        'k22_multa',
        'k22_desconto',
        'k22_exerc',
        'k22_instit'
    ];


    /**
     * Retorna uma lisata de Debitos Ativos
     *
     * @param int $k22_matric
     * @param array<int> $k22_tipo
     *
     * @return Collection
     */
    public static function getDebitosAtivos($k22_matric, $k22_tipo = [])
    {
        $k22_tipo = implode(',', (is_array($k22_tipo) ? $k22_tipo : [$k22_tipo]));

        $sql =<<<SQL
            select
                *
            from
                caixa.debitos d
            where
                d.k22_matric = ($k22_matric) and
                d.k22_tipo not in ($k22_tipo) and
                d.k22_data = (select max(k22_data) from caixa.debitos) and
                exists (
                    select
                        k00_numpre
                    from
                        caixa.arrecad a
                    where
                    a.k00_numpre = d.k22_numpre and
                    a.k00_numpar = d.k22_numpar and 
                    a.k00_receit = d.k22_receit
                )
SQL;

        return new Collection(DB::select($sql));
    }

    /**
     * Retorna a ultima data atualizacao da debitos
     *
     * @return \DateTime
     */
    public static function getMaxData()
    {
        $sql =<<<SQL
            select
                max(k22_data)
            from
                caixa.debitos
            limit 1;
SQL;

        $result = DB::select($sql);

        if (count($result) > 0) {
            return date_create($result[0]->max);
        }

        return date_create('now');
    }
}
