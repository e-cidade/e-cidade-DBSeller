<?php

namespace App\Domain\Patrimonial\PNCP\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $pn06_codigo
 * @property $pn06_planocontratacao
 * @property $pn06_item
 * @property $pn06_codmater
 * @property $pn06_categoriaitem
 * @property $pn06_classificacaocatalogo
 * @property $pn06_classificacaosuperiorcodigo
 * @property $pn06_unidadefornecimentocodigo
 * @property $pn06_quantidade
 * @property $pn06_valorunitario
 * @property $pn06_valortotal
 * @property $pn06_valororcamento
 * @property $pn06_unidaderequisitante
 * @property $pn06_datadesejada
 */
class PlanoContratacaoItem extends Model
{
    protected $primaryKey = 'pn06_codigo';
    protected $table = 'planocontratacaoitens';
    public $timestamps = false;
    protected $fillable = [
        'pn06_codigo',
        'pn06_planocontratacao',
        'pn06_item',
        'pn06_categoriaitem',
        'pn06_classificacaocatalogo',
        'pn06_classificacaosuperiorcodigo',
        'pn06_unidadefornecimentocodigo',
        'pn06_quantidade',
        'pn06_valorunitario',
        'pn06_valortotal',
        'pn06_valororcamento',
        'pn06_unidaderequisitante',
        'pn06_datadesejada',
    ];

    public function planoContratacao()
    {
        return $this->belongsTo(PlanoContratacao::class, 'pn06_planocontratacao', 'pn05_codigo');
    }
}
