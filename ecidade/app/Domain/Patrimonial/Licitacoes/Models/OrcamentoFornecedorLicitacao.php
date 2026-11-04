<?php

namespace App\Domain\Patrimonial\Licitacoes\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $pc31_orcamforne,
 * @property $pc31_nomeretira,
 * @property $pc31_dtretira,
 * @property $pc31_horaretira,
 * @property $pc31_liclicitatipoempresa,
 * @property $pc31_tipocondicao
 */
class OrcamentoFornecedorLicitacao extends Model
{
    public $timestamps = false;
    protected $table = 'licitacao.pcorcamfornelic';
}
