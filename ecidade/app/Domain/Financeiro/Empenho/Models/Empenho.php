<?php

namespace App\Domain\Financeiro\Empenho\Models;

use Illuminate\Database\Eloquent\Model;

class Empenho extends Model
{
    protected $table = 'empenho.empempenho';
    protected $primaryKey = 'e60_numemp';
    public $timestamps = false;
}
