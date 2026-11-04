<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DesignadoTransposicao
 * @package App\Domain\Educacao\Escola\Models
 * @property integer $ed354_codigo
 * @property integer $ed354_transposicao
 * @property integer $ed354_aluno
 * @property integer $ed354_escoladestino
 * @property integer $ed354_etapadestino
 * @property integer $ed354_opcao
 * @property integer $ed354_pontuacao
 * @property integer $ed354_deficiente
 *
 * @property Transposicao $transposicao
 * @property Aluno $aluno
 * @property Escola $escolaDestino
 * @property Etapa $etapaDestino
 */
class DesignadoTransposicao extends Model
{
    protected $table = "escola.designadostransposicao";
    protected $primaryKey = 'ed354_codigo';
    public $timestamps = false;

    public function transposicao()
    {
        return $this->belongsTo(Transposicao::class, 'ed354_transposicao', 'ed353_codigo');
    }

    public function aluno()
    {
        return $this->belongsTo(Aluno::class, 'ed354_aluno', 'ed47_i_codigo');
    }

    public function escolaDestino()
    {
        return $this->belongsTo(Escola::class, 'ed354_escoladestino', 'ed18_i_codigo');
    }

    public function etapaDestino()
    {
        return $this->belongsTo(Etapa::class, 'ed354_etapadestino', 'ed11_i_codigo');
    }
}
