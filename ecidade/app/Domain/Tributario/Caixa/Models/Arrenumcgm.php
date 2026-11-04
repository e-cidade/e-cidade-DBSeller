<?php

namespace App\Domain\Tributario\Caixa\Models;

use App\Domain\Tributario\Caixa\Models\Arrecad;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Model Arrenumcgm
 *
 * @property int k00_numpre
 * @property int k00_matric
 */
class Arrenumcgm extends Model
{
    protected $table = 'caixa.arrenumcgm';

    protected $fillable = [
        'k00_numpre',
        'k00_matric'
    ];

    public function arrecad()
    {
        return $this->hasMany(Arrecad::class, 'k00_numpre', 'k00_numpre');
    }
}
