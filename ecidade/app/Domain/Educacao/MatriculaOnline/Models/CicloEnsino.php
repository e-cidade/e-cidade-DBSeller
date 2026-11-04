<?php

namespace App\Domain\Educacao\MatriculaOnline\Models;

use App\Domain\Educacao\Escola\Models\Ensino;
use Illuminate\Database\Eloquent\Model;

class CicloEnsino extends Model
{
    protected $table = 'plugins.ciclosensino';
    public $timestamps = false;
    protected $primaryKey = 'mo14_sequencial';
    public $incrementing = true;
    protected $fillable = ['mo14_sequencial', 'mo14_ciclo', 'mo14_ensino'];

    public function ensino()
    {
        return $this->belongsTo(Ensino::class, 'mo14_ensino', 'ed10_i_codigo');
    }
}
