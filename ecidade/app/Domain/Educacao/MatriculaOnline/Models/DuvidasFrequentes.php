<?php

namespace App\Domain\Educacao\MatriculaOnline\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DuvidasFrequentes extends Model
{
    protected $table = 'matriculaonline.duvidas_frequentes';
    public $timestamps = false;
    protected $primaryKey = 'mo15_id';
    public $incrementing = true;
    protected $fillable = [
        'mo15_pergunta',
        'mo15_ativo',
        'mo15_ordem'
    ];


    public function respostas()
    {
        return $this->hasMany(RespostaDuvidaFrequente::class, 'mo25_pergunta', 'mo15_id');
    }
}
