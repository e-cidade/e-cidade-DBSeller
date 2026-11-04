<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ConplanoExeContaCorrenteAtributos
 * Atributos do saldo inicial de uma conta corrente
 *
 * @package App\Domain\Financeiro\Contabilidade\Models
 * @property integer $id
 * @property integer $c144_conplanoexecontacorrente
 * @property integer $c144_conplanoinfocomplementar
 * @property string $c144_valor
 */
class ConplanoExeContaCorrenteAtributos extends Model
{
    protected $table = 'contabilidade.conplanoexecontacorrenteatributo';
    public $timestamps = false;

    protected $fillable = [
        'c144_conplanoexecontacorrente',
        'c144_conplanoinfocomplementar',
        'c144_valor',
    ];

    public function sigla()
    {
        return $this->belongsTo(InformacaoComplementar::class, 'c144_conplanoinfocomplementar', 'c121_sequencial');
    }
}
