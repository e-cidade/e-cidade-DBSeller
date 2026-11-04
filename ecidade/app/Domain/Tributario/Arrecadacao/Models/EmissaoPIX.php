<?php

namespace App\Domain\Tributario\Arrecadacao\Models;

use Illuminate\Database\Eloquent\Model;

use App\Domain\Tributario\Arrecadacao\Models\EmissaoPIXDetalhe;

/**
 * @property int tr10_sequencial
 * @property string tr10_codigo_emissao
 * @property int tr10_num_jobs
 * @property int tr10_concluidos_jobs
 * @property int tr10_failed_jobs
 * @property string tr10_codban
 * @property int tr10_tipo
 * @property string tr10_data_vencimento  Data format Y-m-d
 * @property bool tr10_cotaunica
 * @property bool tr10_status
 */
class EmissaoPIX extends Model
{
    public $timestamps = false;
    
    protected $table = 'tributario.emissao_pix';
    protected $primaryKey = 'tr10_sequencial';

    public $fillable = [
        'tr10_sequencial',
        'tr10_codigo_emissao',
        'tr10_num_jobs',
        'tr10_concluidos_jobs',
        'tr10_failed_jobs',
        'tr10_codban',
        'tr10_tipo',
        'tr10_data_vencimento',
        'tr10_cotaunica',
        'tr10_status'
    ];

    public function detalhes()
    {
        return $this->hasMany(
            EmissaoPIXDetalhe::class,
            'tr11_tr10_sequencial',
            'tr10_sequencial'
        );
    }
}
