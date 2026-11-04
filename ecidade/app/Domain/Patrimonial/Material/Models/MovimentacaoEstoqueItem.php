<?php

namespace App\Domain\Patrimonial\Material\Models;

use Illuminate\Database\Eloquent\Model;

class MovimentacaoEstoqueItem extends Model
{
    protected $table = 'material.matestoqueinimei';
    protected $primaryKey = 'm82_codigo';
    public $timestamps = false;
    protected $fillable = [
        'm82_codigo',
        'm82_matestoqueitem',
        'm82_matestoqueini',
        'm82_quant'
    ];

    public function lancamento()
    {
        return $this->belongsTo(LancamentoMovimentacao::class, 'm82_matestoqueini', 'm80_codigo')
            ->with("usuario")
            ->with("tipoLancamento")
            ->with("departamentoDestino");
    }
    public function valores()
    {
        return $this->hasOne(ValorMovimentacaoEstoque::class, 'm89_matestoqueinimei', 'm82_codigo');
    }

    public function estoqueItem()
    {
        return $this->belongsTo(MaterialEstoqueItem::class, 'm82_matestoqueitem', 'm71_codlanc');
    }
}
