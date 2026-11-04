<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use App\Traits\DeepReplicates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class Lancamento
 * @package App\Domain\Financeiro\Contabilidade\Models
 * @property integer $c70_codlan
 * @property integer $c70_anousu
 * @property string $c70_data
 * @property float $c70_valor
 * @property DocumentoLancamento $documentoLancamento
 * @property LancamentoInstituicao $instituicao
 * @property LancamentoContas[]|Collection $contasLancamento
 * @property LancamentoComplemento observacao
 * @property LancamentoRecurso recurso
 */
class Lancamento extends Model
{
    protected $table = 'contabilidade.conlancam';
    protected $primaryKey = 'c70_codlan';
    public $timestamps = false;

    use DeepReplicates;

    protected $dates = [
        'c70_data'
    ];

    public function documentoLancamento()
    {
        return $this->hasOne(DocumentoLancamento::class, 'c71_codlan', 'c70_codlan');
    }

    public function instituicao()
    {
        return $this->hasOne(LancamentoInstituicao::class, 'c02_codlan', 'c70_codlan');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @deprecated use contasLancamento
     */
    public function valorLancamento()
    {
        return $this->hasMany(LancamentoContas::class, 'c69_codlan', 'c70_codlan');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contasLancamento()
    {
        return $this->hasMany(LancamentoContas::class, 'c69_codlan', 'c70_codlan');
    }

    public function observacao()
    {
        return $this->hasOne(LancamentoComplemento::class, 'c72_codlan', 'c70_codlan');
    }

    public function recurso()
    {
        return $this->hasOne(LancamentoRecurso::class, 'o201_codlan', 'c70_codlan');
    }

    public function lote()
    {
        return $this->belongsToMany(
            Lote::class,
            'lotelancamentoconlancam',
            'c161_conlancam',
            'c161_lotelancamento'
        );
    }
}
