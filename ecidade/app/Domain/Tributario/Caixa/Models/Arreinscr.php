<?php

namespace App\Domain\Tributario\Caixa\Models;

use App\Domain\Tributario\Caixa\Models\Arrecad;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Model Arreinscr
 *
 * @property int k00_numpre
 * @property int k00_inscr
 * @property double|null k00_perc
 */
class Arreinscr extends Model
{
    protected $table = 'caixa.arreinscr';

    protected $fillable = [
        'k00_numpre',
        'k00_inscr',
        'k00_perc'
    ];

    public function arrecad()
    {
        return $this->hasMany(Arrecad::class, 'k00_numpre', 'k00_numpre');
    }
}
