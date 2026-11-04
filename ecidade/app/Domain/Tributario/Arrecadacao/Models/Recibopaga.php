<?php

namespace App\Domain\Tributario\Arrecadacao\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Model Recibopaga
 *
 * @property int|null k00_numcgm
 * @property string|null k00_dtoper
 * @property int k00_receit
 * @property int k00_hist
 * @property double|null k00_valor
 * @property string|null k00_dtvenc
 * @property int|null k00_numpre
 * @property int|null k00_numpar
 * @property int|null k00_numtot
 * @property int|null k00_numdig
 * @property int|null k00_conta
 * @property string|null k00_dtpaga
 * @property int|null k00_numnov
 */
class Recibopaga extends Model
{
    protected $table       = "caixa.recibopaga";
    protected $primaryKey  = "k00_numnov";

    protected $fillable = [
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
}
