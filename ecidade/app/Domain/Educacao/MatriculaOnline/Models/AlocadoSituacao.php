<?php

namespace App\Domain\Educacao\MatriculaOnline\Models;

use Illuminate\Database\Eloquent\Model;

class AlocadoSituacao extends Model
{
    protected $table = 'plugins.alocadossituacao';
    public $timestamps = false;
    protected $primaryKey = 'mo28_sequencial';

    public function situacao()
    {
        return $this->belongsTo(SituacaoInscricao::class, 'mo28_situacao', 'mo27_sequencial');
    }
}
