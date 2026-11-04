<?php

namespace App\Domain\Patrimonial\Compras\Models;

use App\Domain\Patrimonial\Licitacoes\Models\OrcamentoFornecedorLicitacao;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $pc21_orcamforne
 * @property $pc21_codorc
 * @property $pc21_numcgm
 * @property $pc21_importado
 * @property $pc21_prazoent
 * @property $pc21_validadorc
 * @property $pc21_taxaestimadaglobal
 */
class OrcamentoFornecedor extends Model
{
    public $timestamps = false;
    protected $table = 'compras.pcorcamforne';
    public $primaryKey = 'pc21_orcamforne';

    public function orcamentoFornecedorLicitacao()
    {
        return $this->hasMany(OrcamentoFornecedorLicitacao::class, 'pc31_orcamforne', 'pc21_orcamforne');
    }
}
