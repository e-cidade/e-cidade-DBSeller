<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model\Instituicao;

use Illuminate\Database\Eloquent\Model;

class Instituicao extends Model
{
    protected $table = 'configuracoes.db_config';

    protected $primaryKey = 'codigo';
}
