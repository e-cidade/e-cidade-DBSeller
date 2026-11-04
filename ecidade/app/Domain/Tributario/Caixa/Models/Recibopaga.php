<?php

namespace App\Domain\Tributario\Caixa\Models;

use Illuminate\Database\Eloquent\Model;

use App\Domain\Tributario\Caixa\Models\Recibobarpix;

/**
 * @property int|null k00_numcgm
 * @property string|null k00_dtoper Date formato Y-m-d
 * @property int k00_receit
 * @property int k00_hist
 * @property float|null k00_valor
 * @property string|null k00_dtvenc Date formato Y-m-d
 * @property int|null k00_numpre
 * @property int|null k00_numpar
 * @property int|null k00_numtot
 * @property int|null k00_numdig
 * @property int|null k00_conta
 * @property string|null k00_dtpaga Date formato Y-m-d
 * @property int|null k00_numnov
 */
class Recibopaga extends Model
{
    public $timestamps    = false;

    protected $table = 'caixa.recibopaga';
    protected $primaryKey = 'k00_numnov';

    public $fillable = [
        'k00_numcgm',
        'k00_dtoper',
        'k00_receit',
        'k00_hist',
        'k00_valor',
        'k00_dtvenc',
        'k00_numpre',
        'k00_numpar',
        'k00_numtot',
        'k00_numdig',
        'k00_conta',
        'k00_dtpaga',
        'k00_numnov'
    ];

    public static function getListNumnov($list = [], $k00_receit = null, $k00_hist = null)
    {
        return self::with('recibocodbar', 'recibobarpix')
            ->select('k00_numpar', 'k00_numnov', 'k00_numpre', 'k00_dtvenc', 'k00_dtpaga', 'k00_numnov')
            ->distinct('k00_numpar', 'k00_numnov')
            ->whereIn('k00_numnov', $list)
            ->orderBy('k00_numpar', 'desc')
            ->take(11)
            ->get();
    }

    public static function getReciboCotaUnica($k00_numnov)
    {
        return self::with('recibocodbar', 'recibobarpix')->where([
                ['k00_numnov', $k00_numnov]
            ])
            ->distinct()
            ->first();
    }

    public function valorTotalRecibo()
    {
        $total   = 0;
        $recibos = self::where('k00_numnov', $this->k00_numnov)
            ->select('k00_valor')
            ->get();

        $recibos->each(function ($recibo) use (&$total) {
            $total += $recibo->k00_valor;
        });

        return $total;
    }

    public function recibocodbar()
    {
        return $this->hasOne(Recibocodbar::class, 'k00_numpre', 'k00_numnov');
    }

    public function recibobarpix()
    {
        return $this->hasOne(Recibobarpix::class, 'k00_numpre', 'k00_numnov');
    }
}
