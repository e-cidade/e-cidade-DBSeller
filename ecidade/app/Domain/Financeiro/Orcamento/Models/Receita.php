<?php


namespace App\Domain\Financeiro\Orcamento\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Receita
 * @package App\Domain\Financeiro\Orcamento\Models
 * @property $o70_anousu
 * @property $o70_codrec
 * @property $o70_codfon
 * @property $o70_codigo
 * @property $o70_valor
 * @property $o70_reclan
 * @property $o70_instit
 * @property $o70_concarpeculiar
 * @property $o70_datacriacao
 * @property $o70_orcorgao
 * @property $o70_orcunidade
 * @property $o70_esferaorcamentaria
 */
class Receita extends Model
{
    protected $table = 'orcamento.orcreceita';

    public function recurso()
    {
        return $this->hasOne(Recurso::class, 'o15_codigo', 'o70_codigo');
    }

    public function fonteRecurso()
    {
        return $this->recurso->fonteRecurso($this->o70_anousu);
    }
}
