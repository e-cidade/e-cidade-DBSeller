<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use App\Domain\Financeiro\Contabilidade\Builder\EstruturalPadraoReceita;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $exercicio
 * @property $uniao
 * @property $conta
 * @property $natureza
 * @property $nome
 * @property $funcao
 * @property $sintetica
 * @property $classe
 * @property $categoria
 * @property $origem
 * @property $especie
 * @property $desdobramento1
 * @property $desdobramento2
 * @property $desdobramento3
 * @property $tipo
 * @property $desdobramento4
 * @property $desdobramento5
 * @property $desdobramento6
 */
class PlanoReceita extends Model
{
    protected $table = 'contabilidade.planoreceita';

    protected $casts = [
        'exercicio' => 'integer',
        'uniao' => 'boolean',
        'conta' => 'string',
        'nome' => 'string',
        'funcao' => 'string',
        'sintetica' => 'boolean',
        'classe' => 'integer',
        'categoria' => 'integer',
        'origem' => 'integer',
        'especie' => 'integer',
        'desdobramento1' => 'string',
        'desdobramento2' => 'string',
        'desdobramento3' => 'string',
        'tipo' => 'integer',
        'desdobramento4' => 'string',
        'desdobramento5' => 'string',
        'desdobramento6' => 'string',
    ];

    public function toArray()
    {
        $estrutural = new EstruturalPadraoReceita($this->conta, $this->classe);
        $data = parent::toArray();
        $data['mascara'] = $estrutural->estruturalComMascara();
        $data['estruturalAteNivel'] = $estrutural->estruturalAteNivel();

        return $data;
    }

    public function contasEcidade()
    {
        return $this->belongsToMany(
            ConplanoOrcamento::class,
            'contabilidade.planoreceitaconplanoorcamento',
            'planoreceita_id',
            'conplanoorcamento_codigo'
        );
    }

    /**
     * @return string
     */
    public function getNaturezaAttribute()
    {
        return $this->conta;
    }
}
