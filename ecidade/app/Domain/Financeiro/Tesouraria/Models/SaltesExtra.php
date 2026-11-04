<?php

namespace App\Domain\Financeiro\Tesouraria\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $k109_sequencial
 * @property $k109_saltes
 * @property $k109_contaextra
 */
class SaltesExtra extends Model
{
    protected $table = 'caixa.saltesextra';
    protected $primaryKey = 'k109_sequencial';
    public $timestamps = false;

    public function conta()
    {
        return $this->hasOne(Saltes::class, 'k13_conta', 'k109_contaextra');
    }

    public function contaTesouraria()
    {
        return $this->hasOne(Saltes::class, 'k13_conta', 'k109_saltes');
    }
}
