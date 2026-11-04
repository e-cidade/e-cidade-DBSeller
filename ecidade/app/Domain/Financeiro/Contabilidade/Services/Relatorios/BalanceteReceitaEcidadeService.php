<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios;

use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * ********************************************* ATENÇÃO *************************************************
 * Essa classe IGNORA O RECURSO DA EXECUÇÃO. Ela apenas totaliza os valores onde a receita possui lançamento.
 * Para analisar os valores por recurso, usar BalanceteReceitaComplementoService
 */
class BalanceteReceitaEcidadeService extends BalanceteReceitaService
{
    /**
     * @return array
     */
    public function processar()
    {
        $receitas = $this->getDados();
        if ($this->agrupador == 0) {
            $balancete = $this->montaArvore($receitas);
        } else {
            $balancete = $this->montaArvorePorGrupo($receitas);
        }

        return $balancete;
    }

    /**
     * @return array
     */
    protected function getDados()
    {
        list($where, $dataInicio, $dataFinal) = $this->montaWhere();
        $sql = "
            select bl.*,
                   substr(natureza,1,1)::int4 as classe,
                   substr(natureza, 2)::varchar as resto,
                   codigo_siconfi as siconfi,
                   gestao,
                   o15_recurso as subrecurso,
                   o15_complemento as complemento
              from balancete_receita_exercicio($this->ano, '{$dataInicio}', '{$dataFinal}') as bl
              join orctiporec on o15_codigo = recurso_receita
              join fonterecurso fr on fr.orctiporec_id = o15_codigo and fr.exercicio = bl.ano
              where {$where}
              order by resto, natureza ;
        ";

        return DB::select($sql);
    }

    /**
     * @param stdClass $receita
     * @return string
     */
    protected function montaHashArvore($receita)
    {
        return sprintf(
            '%s#%s#%s#%s#%s',
            $receita->natureza,
            $receita->cp,
            $receita->gestao,
            $receita->siconfi,
            $receita->complemento
        );
    }

    /**
     * @param stdClass $receita
     * @return string
     */
    protected function montaHashGrupo($receita)
    {
        return sprintf(
            '%s#%s#%s#%s#%s',
            $receita->resto,
            $receita->cp,
            $receita->gestao,
            $receita->siconfi,
            $receita->complemento
        );
    }
}
