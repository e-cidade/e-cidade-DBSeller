<?php

namespace App\Domain\Tributario\Cadastro\Models;

use Illuminate\Database\Eloquent\Model;

class TipoRua extends Model
{
    protected $table = "cadastro.ruastipo";
    protected $primaryKey = 'j88_codigo';
    public $timestamps = false;
}
