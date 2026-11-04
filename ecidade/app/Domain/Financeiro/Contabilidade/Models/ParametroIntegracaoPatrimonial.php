<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use Illuminate\Database\Eloquent\Model;

class ParametroIntegracaoPatrimonial extends Model
{
    protected $table = 'contabilidade.parametrointegracaopatrimonial';
    protected $primaryKey = 'c01_sequencial';
    public $timestamps = false;

    protected $dates = ['c01_data'];
}
