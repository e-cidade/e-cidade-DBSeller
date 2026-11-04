<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use App\Domain\Financeiro\Orcamento\Models\Orgao;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ConplanoExeContaCorrente
 * Saldo inicial de uma conta por conta corrente
 *
 * @package App\Domain\Financeiro\Contabilidade\Models
 * @property integer $id
 * @property integer $c143_conplanoreduz
 * @property integer $c143_exercicio
 * @property integer $c143_conplanosistema
 * @property float $c143_saldo
 * @property string $c143_natureza
 */
class ConplanoExeContaCorrente extends Model
{
    protected $table = 'contabilidade.conplanoexecontacorrente';
    public $timestamps = false;

    /**
     * Armazena a instancia da classe
     * @var array
     */
    private $storage = [];

    public function getReduzido()
    {
        if (!array_key_exists('reduzido', $this->storage)) {
            $this->storage['reduzido'] = ConplanoReduzido::where('c61_reduz', '=', $this->c143_conplanoreduz)
                ->where('c61_anousu', '=', $this->c143_exercicio)
                ->first();
        }

        return $this->storage['reduzido'];
    }

    public function contaCorrente()
    {
        return $this->belongsTo(ConplanoSistema::class, 'c143_conplanosistema', 'c122_sequencial');
    }

    public function atributos()
    {
        return $this->hasMany(ConplanoExeContaCorrenteAtributos::class, 'c144_conplanoexecontacorrente', 'id');
    }
}
