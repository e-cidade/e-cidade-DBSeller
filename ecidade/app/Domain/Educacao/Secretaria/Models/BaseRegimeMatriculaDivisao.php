<?php

namespace App\Domain\Educacao\Secretaria\Models;

use App\Domain\Educacao\Escola\Models\RegimeMatriculaDivisao;
use Illuminate\Database\Eloquent\Model;

class BaseRegimeMatriculaDivisao extends Model
{
    protected $table = 'escola.baseregimematdiv';
    protected $primaryKey = 'ed224_i_codigo';
    public $timestamps = false;
    protected $fillable = [
        'ed224_i_codigo',
        'ed224_i_base',
        'ed224_i_regimematdiv'
    ];

    public function regimeMatriculaDivisao()
    {
        return $this->belongsTo(RegimeMatriculaDivisao::class, 'ed224_i_regimematdiv', 'ed219_i_codigo');
    }
}
