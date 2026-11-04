<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CandidatoTransposicao
 * @package App\Domain\Educacao\Escola\Models
 * @property integer $ed349_codigo
 * @property integer $ed349_aluno
 * @property integer $ed349_etapa
 * @property integer $ed349_etapaorigem
 * @property integer $ed349_ano
 * @property integer $ed349_escolaorigem
 * @property integer $ed349_escola1
 * @property integer $ed349_irmao1
 * @property integer $ed349_escola2
 * @property integer $ed349_irmao2
 * @property integer $ed349_escola3
 * @property integer $ed349_irmao3
 * @property integer $ed349_servidor1
 * @property integer $ed349_servidor2
 * @property integer $ed349_servidor3
 * @property integer $ed349_matricula
 * @property Escola $escolaOrigem
 * @property Escola $escola1
 * @property Escola $escola2
 * @property Escola $escola3
 * @property Aluno $aluno
 * @property Aluno $irmao1
 * @property Aluno $irmao2
 * @property Aluno $irmao3
 * @property Etapa $etapaDestino
 * @property Etapa $etapaOrigem
 */
class CandidatoTransposicao extends Model
{
    protected $table = "escola.candidatotransposicao";
    protected $primaryKey = 'ed349_codigo';

    public function scopeAno($query, $ano)
    {
        return $query->where('ed349_ano', $ano);
    }

    public function escolaOrigem()
    {
        return $this->belongsTo(Escola::class, 'ed349_escolaorigem', 'ed18_i_codigo');
    }
    public function escola1()
    {
        return $this->belongsTo(Escola::class, 'ed349_escola1', 'ed18_i_codigo');
    }
    public function escola2()
    {
        return $this->belongsTo(Escola::class, 'ed349_escola2', 'ed18_i_codigo');
    }
    public function escola3()
    {
        return $this->belongsTo(Escola::class, 'ed349_escola3', 'ed18_i_codigo');
    }
    public function aluno()
    {
        return $this->belongsTo(Aluno::class, 'ed349_aluno', 'ed47_i_codigo');
    }
    public function etapaDestino()
    {
        return $this->belongsTo(Etapa::class, 'ed349_etapa', 'ed11_i_codigo');
    }

    public function etapaOrigem()
    {
        return $this->belongsTo(Etapa::class, 'ed349_etapaorigem', 'ed11_i_codigo');
    }

    public function irmao1()
    {
        return $this->belongsTo(Aluno::class, 'ed349_irmao1', 'ed47_i_codigo');
    }
    public function irmao2()
    {
        return $this->belongsTo(Aluno::class, 'ed349_irmao2', 'ed47_i_codigo');
    }
    public function irmao3()
    {
        return $this->belongsTo(Aluno::class, 'ed349_irmao3', 'ed47_i_codigo');
    }
}
