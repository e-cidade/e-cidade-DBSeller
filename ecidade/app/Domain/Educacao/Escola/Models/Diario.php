<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $ed95_i_codigo
 * @property integer $ed95_i_escola
 * @property integer $ed95_i_calendario
 * @property integer $ed95_i_aluno
 * @property integer $ed95_i_serie
 * @property integer $ed95_i_regencia
 * @property string $ed95_c_encerrado
 * @property Calendario $calendario
 */
class Diario extends Model
{
    protected $table = 'escola.diario';
    protected $primaryKey = 'ed95_i_codigo';
    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function calendario()
    {
        return $this->belongsTo(Calendario::class, 'ed95_i_calendario', 'ed52_i_codigo');
    }
}
