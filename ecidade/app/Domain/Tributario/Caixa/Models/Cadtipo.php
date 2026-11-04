<?php

namespace App\Domain\Tributario\Caixa\Models;

use App\Domain\Tributario\Arrecadacao\Models\Arretipo;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Model Cadtipo
 *
 * @property int k03_tipo
 * @property string|null k03_descr
 * @property bool|null k03_parcano
 * @property bool|null k03_parcelamento
 * @property bool|null k03_permparc
 */
class Cadtipo extends Model
{
    protected $table = 'caixa.cadtipo';

    protected $fillable = [
        'k03_tipo',
        'k03_descr',
        'k03_parcano',
        'k03_parcelamento',
        'k03_permparc'
    ];

    public function arretipo()
    {
        return $this->hasMany(Arretipo::class, 'k03_tipo', 'k03_tipo');
    }
}
