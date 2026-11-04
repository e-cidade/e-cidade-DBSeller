<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DocumentoLancamento
 * @package App\Domain\Financeiro\Contabilidade\Models
 * @property integer $c71_codlan
 * @property integer $c71_coddoc
 * @property string $c71_data
 */
class DocumentoLancamento extends Model
{
    protected $table = 'contabilidade.conlancamdoc';
    protected $primaryKey = 'c71_codlan';
    public $timestamps = false;

    public function documento()
    {
        return $this->belongsTo(DocumentosContabeis::class, 'c71_coddoc', 'c53_coddoc');
    }

    public function lancamento()
    {
        return $this->belongsTo(Lancamento::class, 'c71_codlan', 'c70_codlan');
    }
}
