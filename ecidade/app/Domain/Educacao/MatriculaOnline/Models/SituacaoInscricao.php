<?php

namespace App\Domain\Educacao\MatriculaOnline\Models;

use Illuminate\Database\Eloquent\Model;

class SituacaoInscricao extends Model
{
    protected $table = 'plugins.situacaoinscricao';
    public $timestamps = false;
    protected $primaryKey = 'mo27_sequencial';
}
