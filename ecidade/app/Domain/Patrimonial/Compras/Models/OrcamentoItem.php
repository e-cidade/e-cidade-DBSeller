<?php

namespace App\Domain\Patrimonial\Compras\Models;

use App\Domain\Patrimonial\Licitacoes\Models\OrcamentoItemLicitacao;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $pc22_orcamitem
 * @property $pc22_codorc
 */
class OrcamentoItem extends Model
{
    public $timestamps = false;
    protected $table = 'compras.pcorcamitem';

    public function orcamento()
    {
        return $this->belongsTo(Orcamento::class, 'pc22_codorc', 'pc20_codorc');
    }

    public function julgamentoVencedor()
    {
        return $this
            ->hasOne(OrcamentoJulgamento::class, 'pc24_orcamitem', 'pc22_orcamitem')
            ->where('pc24_pontuacao', '=', 1)
            ->leftJoin('pcorcamval', function ($join) {
                $join
                    ->on('pc24_orcamitem', '=', 'pc23_orcamitem')
                    ->on('pc24_orcamforne', '=', 'pc23_orcamforne');
            })
            ->leftJoin('pcorcamforne', 'pc21_orcamforne', '=', 'pc23_orcamforne')
            ->leftJoin('cgm', 'pc21_numcgm', '=', 'z01_numcgm');
    }

    public function vinculoItensLicitacao()
    {
        return $this->hasMany(OrcamentoItemLicitacao::class, 'pc26_orcamitem', 'pc22_orcamitem');
    }

    public function valores()
    {
        return $this->hasMany(OrcamentoValor::class, 'pc23_orcamitem', 'pc22_orcamitem');
    }

    public function julgamentos()
    {
        return $this->hasMany(OrcamentoJulgamento::class, 'pc24_orcamitem', 'pc22_orcamitem');
    }
}
