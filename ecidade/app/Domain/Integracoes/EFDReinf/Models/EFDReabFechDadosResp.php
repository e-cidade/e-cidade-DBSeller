<?php

namespace App\Domain\Integracoes\EFDReinf\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $efd10_numcgm
 * @property string $efd10_nome
 * @property string $efd10_telefone
 * @property string $efd10_email
 * @property string $efd10_cpf
 */
class EFDReabFechDadosResp extends Model
{
    protected $table = 'efdreabfechdadosresp';
    protected $primaryKey = 'efd10_sequencial';
    public $timestamps = false;
}
