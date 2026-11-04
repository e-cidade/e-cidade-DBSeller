<?php

namespace App\Domain\Tributario\ISSQN\Model\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Base\QualificacaoSocio
 *
 * @property int $q180_sequencial
 * @property int $q180_codigo
 * @property string $q180_descricao
 * @mixin \Eloquent
 */
class QualificacaoSocio extends Model
{
    protected $table = 'qualificacaosocio';
    protected $primaryKey = 'q180_sequencial';
    public $timestamps = false;
}
