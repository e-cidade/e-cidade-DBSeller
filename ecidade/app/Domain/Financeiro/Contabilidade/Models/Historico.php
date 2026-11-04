<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use Illuminate\Database\Eloquent\Model;

class Historico extends Model
{
    protected $table = 'contabilidade.conhist';
    protected $primaryKey = 'c50_codhist';
    public $timestamps = false;
}
