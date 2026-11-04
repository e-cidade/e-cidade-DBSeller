<?php

namespace App\Domain\Financeiro\Orcamento\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 *
 * @property integer $id
 * @property integer $orctiporec_id
 * @property integer $exercicio
 * @property string $codigo_siconfi
 * @property string $gestao
 * @property integer $classificacaofr_id
 * @property string $tipo_detalhamento
 * @property string $descricao
 *
 * @method FonteRecurso fonteRecurso($fonteGestao, $exercicio, $subrecurso, $complemento = null)
 * @method FonteRecurso recursoExiste(Recurso $recurso)
 * @method FonteRecurso recursoAtivo($data)
 */
class FonteRecurso extends Model
{
    protected $table = 'orcamento.fonterecurso';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = false;

    public function recurso()
    {
        return $this->belongsTo(Recurso::class, 'orctiporec_id', 'o15_codigo');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classificacao()
    {
        return $this->belongsTo(ClassificacaoFonteRecurso::class, 'classificacaofr_id', 'id');
    }

    /**
     * @param Builder $query
     * @param string $fonteGestao
     * @param integer $exercicio
     * @param string $subrecurso
     * @param integer $complemento
     * @return Builder
     */
    public function scopeFonteRecurso(Builder $query, $fonteGestao, $exercicio, $subrecurso, $complemento = null)
    {
        return $query->join('orcamento.orctiporec', 'o15_codigo', 'orctiporec_id')
            ->where('gestao', $fonteGestao)
            ->where('exercicio', $exercicio)
            ->where('o15_recurso', $subrecurso)
            ->when(!is_null($complemento), function ($query) use ($complemento) {
                return $query->where('o15_complemento', $complemento);
            });
    }

    /**
     * @param Builder $query
     * @param Recurso $recurso
     * @return Builder
     */
    public function scopeRecursoExiste(Builder $query, Recurso $recurso)
    {
        $query = $query->join('orcamento.orctiporec', 'o15_codigo', 'orctiporec_id')
            ->when($recurso->getCodigo(), function ($query) use ($recurso) {
                $query->where('o15_codigo', '!=', $recurso->getCodigo());
            })->where('o15_complemento', '=', $recurso->getComplemento()->o200_sequencial)
            ->where('o15_recurso', '=', $recurso->getRecurso());
        return $query;
    }

    /**
     * @param Builder $query
     * @param string $data no formato Y-m-d
     * @return Builder
     */
    public function scopeRecursoAtivo(Builder $query, $data)
    {
        return $query->whereExists(function ($query) use ($data) {
            $query->select(DB::raw(1))
                ->from('orctiporec')
                ->whereRaw('orctiporec.o15_codigo = fonterecurso.orctiporec_id')
                ->where(function ($query) use ($data) {
                    $query->whereNull('o15_datalimite');
                    $query->orWhere('o15_datalimite', '>=', $data);
                });
        });
    }
}
