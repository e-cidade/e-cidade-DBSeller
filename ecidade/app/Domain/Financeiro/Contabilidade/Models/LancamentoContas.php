<?php


namespace App\Domain\Financeiro\Contabilidade\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ValorLancamento
 * @package App\Domain\Financeiro\Contabilidade\Models
 * @property integer $c69_sequen
 * @property integer $c69_anousu
 * @property integer $c69_codlan
 * @property integer $c69_codhist
 * @property integer $c69_credito
 * @property integer $c69_debito
 * @property float $c69_valor
 * @property string $c69_data
 * @property integer $c69_ordem
 */
class LancamentoContas extends Model
{
    protected $table = 'contabilidade.conlancamval';
    protected $primaryKey = 'c69_sequen';
    public $timestamps = false;

    public function lancamento()
    {
        return $this->belongsTo(Lancamento::class, 'c69_codlan', 'c70_codlan');
    }
}
