<?php

namespace App\Domain\Tributario\ISSQN\Model\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Base\CertBaixaNumero
 *
 * @property int $q79_sequencial
 * @property int|null $q79_anousu
 * @property int|null $q79_ultcodcertbaixa
 * @mixin \Eloquent
 */
class CertBaixaNumero extends Model
{
    protected $table = "certbaixanumero";
    protected $primaryKey = "q79_sequencial";
}
