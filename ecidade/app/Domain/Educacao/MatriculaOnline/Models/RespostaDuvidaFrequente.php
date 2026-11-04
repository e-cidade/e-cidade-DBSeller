<?php

namespace App\Domain\Educacao\MatriculaOnline\Models;

use ECidade\V3\Extension\Model;

class RespostaDuvidaFrequente extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'matriculaonline.respostas_duvidas_frequentes';
    public $timestamps = false;
    protected $primaryKey = 'mo25_id';
    public $incrementing = true;
    protected $fillable = [
        'mo25_pergunta',
        'mo25_resposta'
    ];
}
