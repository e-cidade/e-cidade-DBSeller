<?php

namespace App\Domain\Financeiro\Planejamento\Models;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DetalhamnentoPlanejamento extends Model
{
    protected $table = 'planejamento.detalhamnento_planejamento';
    public $timestamps = false;

    public function iniciativa()
    {
        return $this->belongsTo(Iniciativa::class, 'iniciativaprojativ_id', 'pl12_codigo');
    }

    public function instituicao()
    {
        return $this->belongsTo(DBConfig::class, 'pl20_instituicao', 'codigo');
    }

    /**
     * Esse scope está prevendo os filtrar dos seguintes campos:
     * Programa - orcprograma_id
     * Órgão - orcorgao_id
     * Unidade - orcunidade_id
     * Função - funcao_id
     * Subfunção - subfuncao_id
     * Projeto/Iniciativa/Ação - orcprojativ_id
     * Id do Recuro - recurso_id
     * @param Builder $query
     * @param $dados
     * @return mixed
     */
    public function scopeVinculoOrcamento(Builder $query, $dados = [])
    {
        return $query->join('orcamento.orcorgao', function ($join) {
            $join->on('o40_anousu', 'pl20_anoorcamento')
                ->on('o40_orgao', 'pl20_orcorgao');
        })
        ->join('orcamento.orcunidade', function ($join) {
            $join->on('o41_anousu', 'pl20_anoorcamento')
                ->on('o41_orgao', 'pl20_orcorgao')
                ->on('o41_unidade', 'pl20_orcunidade');
        })
        ->join('orcamento.orcfuncao', 'o52_funcao', 'pl20_orcfuncao')
        ->join('orcamento.orcsubfuncao', 'o53_subfuncao', 'pl20_orcsubfuncao')
        ->join('orcamento.orcprograma', function ($join) {
            $join->on('o54_anousu', 'pl20_anoorcamento')
                ->on('o54_programa', 'pl9_orcprograma');
        })
        ->join('orcamento.orcprojativ', function ($join) {
            $join->on('o55_anousu', 'pl20_anoorcamento')
                ->on('o55_projativ', 'pl12_orcprojativ');
        })
        ->join('orcamento.orcelemento', function ($join) {
            $join->on('o56_anousu', 'pl20_anoorcamento')
                ->on('o56_codele', 'pl20_orcelemento');
        })
        ->join('orcamento.orctiporec', 'o15_codigo', 'pl20_recurso')
        ->join('orcamento.fonterecurso', function ($join) {
            $join->on('orctiporec_id', 'o15_codigo')
                ->on('exercicio', 'pl20_anoorcamento');
        })
        ->when(!empty($dados['orcprograma_id']), function ($query) use ($dados) {
            $query->where('pl9_orcprograma', '=', $dados['orcprograma_id']);
        })
        ->when(!empty($dados['orcorgao_id']), function ($query) use ($dados) {
            $query->where('pl20_orcorgao', '=', $dados['orcorgao_id']);
        })
        ->when(!empty($dados['orcunidade_id']), function ($query) use ($dados) {
            $query->where('pl20_orcunidade', '=', $dados['orcunidade_id']);
        })
        ->when(!empty($dados['funcao_id']), function ($query) use ($dados) {
            $query->where('pl20_orcfuncao', '=', $dados['funcao_id']);
        })
        ->when(!empty($dados['subfuncao_id']), function ($query) use ($dados) {
            $query->where('pl20_orcsubfuncao', '=', $dados['subfuncao_id']);
        })
        ->when(!empty($dados['orcprojativ_id']), function ($query) use ($dados) {
            $query->where('pl12_orcprojativ', '=', $dados['orcprojativ_id']);
        })
        ->when(!empty($dados['recurso_id']), function ($query) use ($dados) {
            $query->where('o15_codigo', '=', $dados['recurso_id']);
        });
    }
}
