<?php

namespace App\Domain\Patrimonial\Protocolo\Model;

use Illuminate\Database\Eloquent\Model;

class ProtParam extends Model
{
    protected $table = 'protocolo.protparam';
    //protected $primaryKey = ''; --> faltando primary key na tabela
    public $timestamps = false;
}
