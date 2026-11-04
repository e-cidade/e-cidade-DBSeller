<?php

namespace App\Domain\Financeiro\Planejamento\Services\Relatorios;

use App\Domain\Financeiro\Planejamento\Relatorios\ConferenciaRecursoProjecaoReceitaPdf;
use App\Domain\Financeiro\Planejamento\Relatorios\ConferenciaRecursoProjecaoReceitaPorRecursoPdf;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceita;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceitaFormatter;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ConferenciaRecursoProjecaoReceitaService extends ReceitaService
{
    public function __construct(array $filtros)
    {
        $this->filtros = $filtros;
        $this->processar();
    }

    public function emitir()
    {
        return $this->emitirPdf();
    }

    /**
     * @return array
     */
    public function emitirPdf()
    {
        $relatorio = new ConferenciaRecursoProjecaoReceitaPdf();
        if ($this->filtros['agruparPorRecurso'] == 1) {
            $relatorio = new ConferenciaRecursoProjecaoReceitaPorRecursoPdf();
        }
        $relatorio->setDados($this->dados);
        return $relatorio->emitir();
    }

    /**
     * @throws Exception
     */
    private function processar()
    {
        $this->processarFiltros();

        if (!empty($this->filtros['natureza'])) {
            $fonte = str_pad($this->filtros['natureza'], 15, '0', STR_PAD_RIGHT);
            $estrutural = new EstruturalReceita($fonte);
            $this->nivel = $estrutural->getNivel();
        }

        $dados = $this->projecao();
        $this->organizaDados($dados);
    }

    private function projecao()
    {
        $campos = [
            'orcfontes_id',
            'o70_codrec',
            'o57_fonte as fonte',
            'o57_descr as descricao',
            'o15_recurso as recurso_original',
            'o15_descr as descricao_recurso_original',
            'fonterecurso.codigo_siconfi as recurso',
            'fonterecurso.descricao as descricao_recurso',
            'o15_complemento as complemento',
            'valorbase as valor_base',
        ];

        $outrosCampos = [
            DB::raw("
            (select json_agg(
                          json_build_object(
                            'ano', x.pl10_ano,
                            'valor', x.pl10_valor
                          )
                       )
                  from (select valores.pl10_ano, valores.pl10_valor
                         from planejamento.valores
                        where pl10_origem = 'RECEITA'
                          and pl10_chave = estimativareceita.id
                        order by pl10_ano
                     ) as x
            ) as valores ")
        ];

        $estimativas = $this->buscarProjecao(array_merge($campos, $outrosCampos));

        if ($estimativas->count() === 0) {
            throw new Exception("Nenhuma receita encontrada para o filtro encontrado.", 403);
        }
        if ($this->filtros['agruparPorRecurso'] == 0) {
            return $this->montaArvoreEstrutural($estimativas);
        }
        return $this->agrupaPorRecurso($estimativas);
    }


    protected function builder(EstruturalReceitaFormatter $estrutural, $descricao)
    {
        $std = (object)[
            'orcfontes_id' => null,
            'o70_codrec' => null,
            'sintetico' => true,
            'nivel' => $estrutural->getNivel(),
            'fonte' => $estrutural->getEstrutural(),
            'estrutural' => $estrutural->getEstruturalComMascara(),
            'descricao' => $descricao,
            'recurso' => null,
            'recurso_original' => null,
            'complemento' => null,
            'valor_base' => 0,
        ];

        foreach ($this->exerciciosAnteriores as $exercicio) {
            $std->{"arrecadado_{$exercicio}"} = 0;
        }
        foreach ($this->planejamento->execiciosPlanejamento() as $exercicio) {
            $std->{"valor_{$exercicio}"} = 0;
        }
        return $std;
    }

    /**
     * @param $dadosEstimativa
     * @param EstruturalReceitaFormatter $estrutural
     * @return object
     */
    protected function builderAnalitico($dadosEstimativa, EstruturalReceitaFormatter $estrutural)
    {
        $estimativa = $this->builder($estrutural, $dadosEstimativa->descricao);
        $estimativa->sintetico = false;
        $estimativa->orcfontes_id = $dadosEstimativa->orcfontes_id;
        $estimativa->o70_codrec = $dadosEstimativa->o70_codrec;
        $estimativa->recurso = $dadosEstimativa->recurso;
        $estimativa->recurso_original = $dadosEstimativa->recurso_original;
        $estimativa->complemento = $dadosEstimativa->complemento;
        $estimativa->valor_base = $dadosEstimativa->valor_base;

        $valores = \JSON::create()->parse($dadosEstimativa->valores);
        foreach ($valores as $valor) {
            $estimativa->{"valor_{$valor->ano}"} = (float)$valor->valor;
            $this->totalizador[$valor->ano] += (float)$valor->valor;
        }

        return $estimativa;
    }

    /**
     * @param array $dados
     */
    protected function organizaDados(array $dados)
    {
        parent::organizaDados($dados);
    }

    /**
     * @param Collection $dadosEstimativas
     * @return array
     */
    protected function agrupaPorRecurso(Collection $dadosEstimativas)
    {
        $recursos = [];

        foreach ($dadosEstimativas as $dadosEstimativa) {
            $hash = "$dadosEstimativa->recurso#$dadosEstimativa->recurso_original#$dadosEstimativa->complemento";
            if (!array_key_exists($hash, $recursos)) {
                $recursos[$hash] = $this->builderRecurso(
                    $dadosEstimativa->recurso,
                    $dadosEstimativa->recurso_original,
                    $dadosEstimativa->descricao_recurso,
                    $dadosEstimativa->descricao_recurso_original,
                    $dadosEstimativa->complemento
                );
            }

            $recursos[$hash]->valor_base += $dadosEstimativa->valor_base;

            $valores = \JSON::create()->parse($dadosEstimativa->valores);
            foreach ($valores as $valor) {
                $recursos[$hash]->{"valor_{$valor->ano}"} += (float)$valor->valor;
                $this->totalizador[$valor->ano] += (float)$valor->valor;
            }
        }
        ksort($recursos);
        return $recursos;
    }

    protected function builderRecurso($recurso, $recursoOriginal, $descricao, $descricaoOriginal, $complemento)
    {
        $std = (object)[
            'recurso' => "$recurso",
            'complemento' => "$complemento",
            'descricao' => $descricao,
            'recurso_original' => $recursoOriginal,
            'descricao_recurso_original' => $descricaoOriginal,
            'sintetico' => false,
            'valor_base' => 0,
        ];

        foreach ($this->planejamento->execiciosPlanejamento() as $exercicio) {
            $std->{"valor_{$exercicio}"} = 0;
        }
        return $std;
    }
}
