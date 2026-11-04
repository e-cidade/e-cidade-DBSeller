<?php

namespace App\Domain\Tributario\Caixa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Tributario\Caixa\Models\Tabrec;
use Illuminate\Support\Facades\DB;

/**
 * Class Model Arrecad
 *
 * @property int|null k00_numpre
 * @property int|null k00_numpar
 * @property int k00_numcgm
 * @property string|null k00_dtoper
 * @property int k00_receit
 * @property int k00_hist
 * @property double|null k00_valor
 * @property string|null k00_dtvenc
 * @property int|null k00_numtot
 * @property int|null k00_numdig
 * @property int k00_tipo
 * @property int|null k00_tipojm
 * @property int|null k00_vlrhis
 * @property int|null k00_vlrcor
 * @property int|null k00_vlrjuros
 * @property int|null k00_vlrmulta
 * @property int|null k00_vlrtotal
 * @property int|null k00_vlrdesconto
 */
class Arrecad extends Model
{
    public $timestamps    = false;

    /**
     * Nome da table
     *
     * @var string $table
     */
    protected $table = 'caixa.arrecad';

    /**
     * Campos customizados
     *
     * @var array<string, mixed> $attributesCustom
     */
    protected $attributesCustom = [];

    /**
     * Campos da model
     *
     * @var array<string, mixed>
     */
    protected $fillable = [
        'k00_numpre',
        'k00_numpar',
        'k00_numcgm',
        'k00_dtoper',
        'k00_receit',
        'k00_hist',
        'k00_valor',
        'k00_dtvenc',
        'k00_numtot',
        'k00_numdig',
        'k00_tipo',
        'k00_tipojm'
    ];

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


    public static function getByMatriculaForTipoDebito($j20_matric, $k00_tipo)
    {
        $sql =<<<SQL
            select
                distinct a.k00_numpar,
                a.k00_numpre,
                a.k00_dtvenc
            from
                cadastro.iptunump i
            inner join cadastro.iptucalv ci on ci.j21_matric = i.j20_matric
                and ci.j21_anousu = i.j20_anousu
            inner join caixa.arrecad a on a.k00_numpre = i.j20_numpre
                and a.k00_receit = ci.j21_receit
            where
                i.j20_matric = {$j20_matric}
                and a.k00_tipo = {$k00_tipo}
SQL;
        $result = DB::select(DB::raw($sql));
        $collection = new Collection();

        foreach ($result as $arrecad) {
            $collection->push(new static((array) $arrecad));
        }

        return $collection;
    }

    public function recibounica()
    {
        return $this->hasOne(Recibounica::class, 'k00_numpre', 'k00_numpre');
    }

    public function tabrec()
    {
        return $this->hasOne(Tabrec::class, 'k02_codigo', 'k00_receit');
    }

    /**
     * Metodo para calcular o valor atual, juros e multa do debito
     *
     * @param bool $receit calcular default false
     *
     * @return mixed
     */
    public function calcular($receita = false)
    {
        $numpre  = $this->attributes['k00_numpre'];
        $numpar  = $this->attributes['k00_numpar'];

        if ($receita) {
            $receita = $this->attributes['k00_receit'];
        } else {
            $receita = 0;
        }

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
                        {$numpar},
                        {$receita},
                        current_date,
                        current_date,
                        extract (year from current_date)::integer
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

    public function arrecad()
    {
        $this->arrecad = $this
            ->where('k00_numpre', $this->k00_numpre)
            ->where('k00_numpar', $this->k00_numpar)
            ->with('tabrec')
            ->get();

        $this->arrecad->each(function (Arrecad $arrecad) {
            $arrecad->calcular(true);
        });

        return $this;
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
