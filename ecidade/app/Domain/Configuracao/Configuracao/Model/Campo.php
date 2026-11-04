<?php

namespace App\Domain\Configuracao\Configuracao\Model;

use Illuminate\Database\Eloquent\Model;

class Campo extends Model
{
    protected $table = 'configuracoes.db_syscampo';
    protected $primaryKey = 'codcam';
    public $incrementing = true;
    public $timestamps = false;
}
