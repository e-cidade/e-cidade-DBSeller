<?php

namespace App\Domain\Tributario\Arrecadacao\Models;

use App\Domain\Tributario\Caixa\Models\Arrematric;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int tr11_sequencial
 * @property int tr11_tr10_sequencial
 * @property int tr11_numpre_origem
 * @property int tr11_numpre_receita
 * @property int tr11_valores
 */
class EmissaoPIXDetalhe extends Model
{
    public $timestamps = false;
    
    protected $table = 'tributario.emissao_pix_detalhe';
    protected $primaryKey = 'tr11_sequencial';

    public $fillable = [
        'tr11_sequencial',
        'tr11_tr10_sequencial',
        'tr11_numpre_origem',
        'tr11_numpre_receita',
        'tr11_valores'
    ];

    public function arrematric()
    {
        return $this->hasOne(Arrematric::class, 'k00_numpre', 'tr11_numpre_origem');
    }
}
