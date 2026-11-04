<?php

namespace App\Domain\Financeiro\Empenho\Models;

use Illuminate\Database\Eloquent\Model;

class EmpenhoNota extends Model
{

    protected $table = 'empenho.empnota';
    protected $primaryKey = 'e69_codnota';
    public $timestamps = false;
}
