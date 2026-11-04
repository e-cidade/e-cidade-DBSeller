<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use Illuminate\Database\Eloquent\Model;

class ConlancamRecurso extends Model
{
    protected $table = 'contabilidade.conlancamrecurso';
    protected $primaryKey = 'c130_sequencial';
    public $timestamps = false;
}
