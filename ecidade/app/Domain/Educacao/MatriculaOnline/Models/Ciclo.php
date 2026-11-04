<?php

namespace App\Domain\Educacao\MatriculaOnline\Models;

use Illuminate\Database\Eloquent\Model;

class Ciclo extends Model
{
    protected $table = 'plugins.ciclos';
    public $timestamps = false;
    protected $primaryKey = 'mo09_codigo';
    public $incrementing = true;
    protected $fillable = [
        'mo09_codigo',
        'mo09_status',
        'mo09_dtcad',
        'mo09_descricao',
        'mo09_sigla',
        'mo09_eja'
    ];
    protected $dates = ['mo09_dtcad'];

    public function ciclosEnsino()
    {
        return $this->hasMany(CicloEnsino::class, 'mo14_ciclo', 'mo09_codigo');
    }
}
