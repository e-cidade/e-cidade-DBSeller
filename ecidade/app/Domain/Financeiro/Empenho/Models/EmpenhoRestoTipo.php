<?php

namespace App\Domain\Financeiro\Empenho\Models;

use Illuminate\Database\Eloquent\Model;

class EmpenhoRestoTipo extends Model
{
    protected $table = 'empenho.emprestotipo';
    protected $primaryKey = 'e90_codigo';
    public $timestamps = false;
}
