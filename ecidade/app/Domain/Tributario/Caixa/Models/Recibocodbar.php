<?php

namespace App\Domain\Tributario\Caixa\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Model Recibocodbar
 *
 * @property int k00_numpre
 * @property string|null k00_codbar
 * @property string|null k00_linhadigitavel
 * @property string|null k00_nossonumero
 */
class Recibocodbar extends Model
{
    public $timestamps = false;

    protected $table = 'caixa.recibocodbar';
    protected $primaryKey = 'k00_numnov';

    public $fillable = [
        'k00_numpre',
        'k00_codbar',
        'k00_linhadigitavel',
        'k00_nossonumero'
    ];
}
