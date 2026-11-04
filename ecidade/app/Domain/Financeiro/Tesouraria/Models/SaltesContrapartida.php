<?php

namespace App\Domain\Financeiro\Tesouraria\Models;

use Illuminate\Database\Eloquent\Model;

class SaltesContrapartida extends Model
{
    protected $table = 'caixa.saltescontrapartida';
    protected $primaryKey = 'k103_sequencial';
    public $timestamps = false;

    public function conta()
    {
        return $this->hasOne(Saltes::class, 'k13_conta', 'k103_contrapartida');
    }

    public function contaTesouraria()
    {
        return $this->hasOne(Saltes::class, 'k13_conta', 'k103_saltes');
    }
}
