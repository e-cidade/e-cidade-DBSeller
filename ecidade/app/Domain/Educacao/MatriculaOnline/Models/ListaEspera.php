<?php

namespace App\Domain\Educacao\MatriculaOnline\Models;

use Illuminate\Database\Eloquent\Model;

class ListaEspera extends Model
{
    protected $table = 'plugins.listaespera';
    public $timestamps = false;
    protected $primaryKey = 'mo18_sequencial';
}
