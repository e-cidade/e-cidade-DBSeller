<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @package App\Domain\Financeiro\Contabilidade\Models
 * @property integer $c180_codigo
 * @property integer $c180_relatorio
 * @property integer $c180_instituicao
 * @property integer $c180_linha
 * @property string $c180_coluna
 * @property integer $c180_exercicio
 * @property integer $c180_mes
 * @property numeric $c180_valor
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class LrfValorManual extends Model
{
    protected $table = 'contabilidade.lrfvalormanual';
    protected $primaryKey = 'c180_codigo';
}
