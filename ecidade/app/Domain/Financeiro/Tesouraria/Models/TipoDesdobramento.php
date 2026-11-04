<?php

namespace App\Domain\Financeiro\Tesouraria\Models;

use App\Domain\Financeiro\Orcamento\Models\Recurso;
use Illuminate\Database\Eloquent\Model;

/**
 * @class TipoDesdobramento
 * @namespace App\Domain\Financeiro\Tesouraria\Models
 *
 * @property int $k222_codigo
 * @property int $k222_descricao
 * @property int $k222_percentual
 */
class TipoDesdobramento extends Model
{
    protected $table = 'caixa.tipodesdobramento';
    protected $primaryKey = 'k222_codigo';

    protected $fillable = [
        'k222_codigo',
        'k222_origem',
        'k222_descricao'
    ];

    public function recursos()
    {
        return $this->belongsToMany(
            Recurso::class,
            'caixa.orctiporectipodesdobramento',
            'k223_tiposdesdobramento',
            'k223_orctiporec'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lancamentos()
    {
        return$this->hasMany(DecendioLancamento::class, 'k231_desdobramento', 'k222_codigo');
    }
}
