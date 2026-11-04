<?php

namespace App\Domain\Financeiro\Tesouraria\Models;

use App\Domain\Financeiro\Empenho\Models\Empagetipo;
use Illuminate\Database\Eloquent\Model;

/**
 * @class Saltes
 * @property $k13_conta
 * @property $k13_reduz
 * @property $k13_descr
 * @property $k13_saldo
 * @property $k13_ident
 * @property $k13_vlratu
 * @property $k13_datvlr
 * @property $k13_limite
 * @property $k13_dtimplantacao
 */
class Saltes extends Model
{
    protected $table = 'caixa.saltes';
    protected $primaryKey = 'k13_conta';
    public $timestamps = false;

    public function empagetipo()
    {
        return $this->hasOne(Empagetipo::class, 'e83_codtipo', 'k13_conta');
    }

    public function contaExtra()
    {
        return $this->hasOne(SaltesExtra::class, 'k109_saltes', 'k13_conta');
    }

    public function contaContrapartida()
    {
        return $this->hasOne(SaltesContrapartida::class, 'k103_saltes', 'k13_conta');
    }
}
