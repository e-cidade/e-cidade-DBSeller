<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use App\Domain\Financeiro\Orcamento\Models\Elemento;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $c162_codigo
 * @property integer $c162_exercicio
 * @property integer $c162_reduzido
 * @property integer $c162_ai
 * @property integer $c162_funcao
 * @property integer $c162_subfuncao
 * @property integer $c162_elemento
 * @property integer $c162_recurso
 * @property float $c162_valor
 * @property string $c162_natureza
 */
class ConplanoexeConciliaRp extends Model
{
    protected $table = 'contabilidade.conplanoexeconciliarp';
    protected $primaryKey = 'c162_codigo';
    public $timestamps = false;

    protected $fillable = [
        'c162_exercicio',
        'c162_reduzido',
        'c162_ai',
        'c162_funcao',
        'c162_subfuncao',
        'c162_elemento',
        'c162_recurso',
        'c162_valor',
        'c162_natureza',
    ];

    private $storage = [];

    /**
     * @return Elemento|null
     */
    public function getElemento()
    {
        if (!array_key_exists('elemento', $this->storage)) {
            $this->storage['elemento'] = Elemento::query()
                ->where('o56_codele', $this->c162_elemento)
                ->where('o56_anousu', $this->c162_ai)
                ->first();
        }

        return $this->storage['elemento'];
    }
}
