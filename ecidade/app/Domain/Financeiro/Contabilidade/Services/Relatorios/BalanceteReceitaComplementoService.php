<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios;

use App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Receita\BalanceteReceitaCsv;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceitaFormatter;
use Illuminate\Support\Facades\DB;

/**
 * ************************************* ATENÇÃO *************************************************
 * Essa classe busca os valores da receita com base nos LANÇAMENTOS CONTÁBEIS usando o recurso e complemento da execução
 * Caso os valores estejam divergentes com o balancete da receita, tem lançamento contábil errado
 * esta faltando ou com valor errado na tabela "conlancamcomplementorecurso"
 */
class BalanceteReceitaComplementoService extends BalanceteReceitaService
{
    /**
     * Essa função só retorna o label para aplicar no header do relatório
     * @return string
     */
    protected function getHeaderTitulo()
    {
        return 'BALANCETE DA RECEITA POR COMPLEMENTO';
    }

    public function processar()
    {
        $receitas = $this->getDados();
        if ($this->agrupador == 0) {
            $balancete = $this->montaArvore($receitas);
        } else {
            $balancete = $this->montaArvorePorGrupo($receitas);
        }

        /**
         * Altere a propriedade validarQuantidadeDeContasAnaliticas para true se alterar o relatório, para debugar
         * se todas as contas analíticas estão presente em $balancete.
         */
        if ($this->validarQuantidadeDeContasAnaliticas) {
            $this->validarQuantidadeDeContasAnaliticas($receitas, $balancete);
        }

        return $balancete;
    }

    protected function getDados()
    {
        list($where, $dataInicio, $dataFinal) = $this->montaWhere();

        $sql = "
            select balancete_receita_complemento.*,
                   substr(natureza,1,1)::int4 as classe,
                   substr(natureza, 2)::varchar as resto
              from orcreceita
              join balancete_receita_complemento(
                     o70_anousu, o70_codfon, o70_concarpeculiar, '{$dataInicio}', '{$dataFinal}'
                   ) on o70_codfon = fonte and o70_anousu = ano
              where {$where}
              order by resto, natureza;
        ";
        return DB::select($sql);
    }

    protected function mapperReceitaAnalitica($receita, EstruturalReceitaFormatter $estrutural)
    {
        $receita->subrecurso = $receita->fonte_recurso;
        $receita->complemento = $receita->complemento_lancamento;
        $std = parent::mapperReceitaAnalitica($receita, $estrutural);
        $std->recurso_lancamento = $receita->recurso_lancamento;
        $std->ordem = $receita->ordem;
        return $std;
    }

    protected function montaWhereFiltraRecurso()
    {
        $recursos = $this->filtrarRecursos->implode(',');
        return "recurso_lancamento in ($recursos)";
    }


    protected function getInstanciaBalanceteCSV()
    {
        return new BalanceteReceitaCsv();
    }

    /**
     * @param $receita
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
            $receita->complemento_lancamento
        );
    }

    /**
     * @param $receita
     * @return string
     */
    public function montaHashArvore($receita)
    {
        return sprintf(
            '%s#%s#%s#%s#%s#%s',
            $receita->natureza,
            $receita->cp,
            $receita->reduzido,
            $receita->gestao,
            $receita->siconfi,
            $receita->complemento_lancamento
        );
    }
}
