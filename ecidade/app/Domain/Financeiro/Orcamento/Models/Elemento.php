<?php

namespace App\Domain\Financeiro\Orcamento\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $o56_codele
 * @property int $o56_anousu
 * @property string $o56_elemento
 * @property string $o56_descr
 * @property string $o56_finali
 * @property boolean $o56_orcado
 */
class Elemento extends Model
{
    protected $table = 'orcamento.orcelemento';
}
