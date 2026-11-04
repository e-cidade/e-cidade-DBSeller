<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LancamentoInstituicao
 * @package App\Domain\Financeiro\Contabilidade\Models
 * @property integer $c02_sequencial
 * @property integer $c02_codlan
 * @property integer $c02_instit
 */
class LancamentoInstituicao extends Model
{
    protected $table = 'contabilidade.conlancaminstit';
    protected $primaryKey = 'c02_sequencial';
    public $timestamps = false;
}
