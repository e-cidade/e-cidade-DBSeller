<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $c115_sequencial
 * @property $c115_conhistdocinclusao
 * @property $c115_conhistdocestorno
 */
class VinculoEventosContabeis extends Model
{
    protected $table = 'contabilidade.vinculoeventoscontabeis';
    protected $primaryKey = 'c115_sequencial';
    public $timestamps = false;
}
