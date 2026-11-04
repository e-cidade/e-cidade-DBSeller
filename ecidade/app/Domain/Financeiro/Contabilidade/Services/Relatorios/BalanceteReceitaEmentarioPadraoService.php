<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios;

use App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Receita\BalanceteReceitaEmentarioPadraoPdf;
use App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Receita\PrevisaoInicialReceitaPdf;
use Illuminate\Support\Facades\DB;

/**
 * ********************************************* ATENÇÃO *************************************************
 * Essa classe IGNORA O RECURSO DA EXECUÇÃO. Ela apenas totaliza os valores onde a receita possui lançamento.
 * Para analisar os valores por recurso, usar BalanceteReceitaComplementoService
 */
class BalanceteReceitaEmentarioPadraoService extends BalanceteReceitaService
{
    public function processar()
    {
        $receitas = $this->getDados();

        return $this->montaArvore($receitas);
    }

    protected function montaHashArvore($receita)
    {
        return sprintf(
            '%s#%s#%s#%s',
            $receita->natureza,
            $receita->reduzido,
            $receita->siconfi,
            $receita->complemento
        );
    }

    protected function montaHashGrupo($receita)
    {
        // TODO: Implement montaHashGrupo() method.
    }

    protected function getDados()
    {
        list($where, $dataInicio, $dataFinal) = $this->montaWhere();

        $pl = 'balancete_receita_exercicio_plano_estadual';
        if ($this->ementario === 'uniao') {
            $pl = 'balancete_receita_exercicio_plano_uniao';
        }

        $sql = "
            select bl.*,
                   substr(natureza,1,1)::int4 as classe,
                   substr(natureza, 2)::varchar as resto,
                   codigo_siconfi as siconfi,
                   gestao,
                   o15_recurso as subrecurso,
                   o15_complemento as complemento
              from $pl($this->ano, '{$dataInicio}', '{$dataFinal}') as bl
              join orctiporec on o15_codigo = recurso_receita
              join fonterecurso fr on fr.orctiporec_id = o15_codigo and fr.exercicio = bl.ano
              where {$where}
              order by resto, natureza ;
        ";

        return DB::select($sql);
    }

    protected function getInstanciaBalancetePdf($apresentar = null)
    {

        if ($this->dadosEmissao === 'orcamento') {
            return new PrevisaoInicialReceitaPdf();
        }

        return new BalanceteReceitaEmentarioPadraoPdf();
    }
}
